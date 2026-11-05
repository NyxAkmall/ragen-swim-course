<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    /*
    |--------------------------------------------------------------------------
    | Payment Setting Keys
    |--------------------------------------------------------------------------
    */

    public const BANK_NAME_KEY = 'payment_bank_name';

    public const BANK_ACCOUNT_NUMBER_KEY = 'payment_bank_account_number';

    public const BANK_ACCOUNT_HOLDER_KEY = 'payment_bank_account_holder';

    public const REGISTRATION_FEE_KEY = 'registration_fee';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
    ];

    /**
     * Mengambil nilai setting berdasarkan key.
     */
    public static function getValue(
        string $key,
        mixed $default = null,
        string $type = 'string'
    ): mixed {
        $setting = static::query()
            ->where('setting_key', $key)
            ->first();

        if ($setting === null) {
            return $default;
        }

        if ($setting->setting_value === null) {
            return null;
        }

        return static::convertValue(
            $setting->setting_value,
            $type
        );
    }

    /**
     * Membuat atau memperbarui setting.
     */
    public static function setValue(
        string $key,
        mixed $value,
        string $type = 'string',
        ?string $description = null,
        string $group = 'general'
    ): self {
        $storedValue = static::prepareValueForStorage(
            $value,
            $type
        );

        return static::updateOrCreate(
            [
                'setting_key' => $key,
            ],
            [
                'setting_value' => $storedValue,
                'setting_group' => $group,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bank Account Settings
    |--------------------------------------------------------------------------
    */

    public static function paymentBankAccount(): array
    {
        return [
            'bank_name' => trim(
                (string) static::getValue(
                    static::BANK_NAME_KEY,
                    ''
                )
            ),

            'account_number' => trim(
                (string) static::getValue(
                    static::BANK_ACCOUNT_NUMBER_KEY,
                    ''
                )
            ),

            'account_holder' => trim(
                (string) static::getValue(
                    static::BANK_ACCOUNT_HOLDER_KEY,
                    ''
                )
            ),
        ];
    }

    public static function hasPaymentBankAccount(): bool
    {
        $bankAccount = static::paymentBankAccount();

        return $bankAccount['bank_name'] !== ''
            && $bankAccount['account_number'] !== ''
            && $bankAccount['account_holder'] !== '';
    }

    public static function setPaymentBankAccount(
        string $bankName,
        string $accountNumber,
        string $accountHolder
    ): void {
        static::setValue(
            static::BANK_NAME_KEY,
            trim($bankName),
            'string',
            null,
            'payment'
        );

        static::setValue(
            static::BANK_ACCOUNT_NUMBER_KEY,
            trim($accountNumber),
            'string',
            null,
            'payment'
        );

        static::setValue(
            static::BANK_ACCOUNT_HOLDER_KEY,
            trim($accountHolder),
            'string',
            null,
            'payment'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Fee Settings
    |--------------------------------------------------------------------------
    */

    /**
     * Nominal biaya pendaftaran satu kali per peserta.
     *
     * Default Rp0 sampai admin menetapkan tarif.
     */
    public static function registrationFee(): int
    {
        $value = static::getValue(
            static::REGISTRATION_FEE_KEY,
            0,
            'integer'
        );

        return max(0, (int) $value);
    }

    /**
     * Menyimpan nominal biaya pendaftaran.
     */
    public static function setRegistrationFee(
        int $amount
    ): void {
        if ($amount < 0) {
            throw new \InvalidArgumentException(
                'Biaya pendaftaran tidak boleh negatif.'
            );
        }

        static::setValue(
            static::REGISTRATION_FEE_KEY,
            $amount,
            'integer',
            null,
            'payment'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Value Conversion
    |--------------------------------------------------------------------------
    */

    protected static function convertValue(
        mixed $value,
        ?string $type
    ): mixed {
        return match ($type) {
            'integer' => (int) $value,

            'float' => (float) $value,

            'boolean' => filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN
            ),

            'json' => json_decode(
                $value ?? 'null',
                true
            ),

            default => $value,
        };
    }

    protected static function prepareValueForStorage(
        mixed $value,
        string $type
    ): ?string {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',

            'json' => json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            ),

            default => (string) $value,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeKey(
        Builder $query,
        string $key
    ): Builder {
        return $query->where(
            'setting_key',
            $key
        );
    }

    public function scopeGroup(
        Builder $query,
        string $group
    ): Builder {
        return $query->where(
            'setting_group',
            $group
        );
    }
}
