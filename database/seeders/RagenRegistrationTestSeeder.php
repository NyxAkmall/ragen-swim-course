<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class RagenRegistrationTestSeeder extends Seeder
{
    public function run(): void
    {
        $databaseName = DB::connection()->getDatabaseName();

        if ($databaseName !== 'kelas_renang_testing') {
            throw new RuntimeException(
                'Seeder ini hanya boleh dijalankan pada database kelas_renang_testing.'
            );
        }

        DB::transaction(function () {

            $now = now();

            /*
            |--------------------------------------------------------------------------
            | Customer Testing
            |--------------------------------------------------------------------------
            */

            $email = 'customer.registration.test@example.com';

            $customer = DB::table('users')
                ->where('email', $email)
                ->first();

            if ($customer) {
                $customerId = $customer->id;
            } else {
                $customerId = DB::table('users')->insertGetId([
                    'name' => 'Customer Testing Ragen',
                    'email' => $email,
                    'phone' => '081200000001',
                    'password' => Hash::make(
                        'RagenTest2026!'
                    ),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Peserta Testing
            |--------------------------------------------------------------------------
            */

            $participants = [
                [
                    'full_name' => 'Ragen Test Peserta A',
                    'gender' => 'male',
                    'birth_date' => '2017-05-10',
                ],
                [
                    'full_name' => 'Ragen Test Peserta B',
                    'gender' => 'female',
                    'birth_date' => '2018-07-15',
                ],
                [
                    'full_name' => 'Ragen Test Peserta C',
                    'gender' => 'male',
                    'birth_date' => '2019-03-20',
                ],
            ];

            foreach ($participants as $participant) {

                $exists = DB::table('participants')
                    ->where('user_id', $customerId)
                    ->where(
                        'full_name',
                        $participant['full_name']
                    )
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('participants')->insert([
                    'user_id' => $customerId,
                    'full_name' => $participant['full_name'],
                    'gender' => $participant['gender'],
                    'birth_date' => $participant['birth_date'],
                    'swimming_level' => 'beginner',
                    'participant_type' => 'child',
                    'status' => 'active',
                    'registration_paid_at' => null,
                    'registration_fee_exempt' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Program Testing
            |--------------------------------------------------------------------------
            */

            $programSlug = 'basic-swimming-testing';

            $program = DB::table('programs')
                ->where('slug', $programSlug)
                ->first();

            if ($program) {
                $programId = $program->id;
            } else {
                $programId = DB::table('programs')
                    ->insertGetId([
                        'name' => 'Basic Swimming Testing',
                        'slug' => $programSlug,
                        'short_description' =>
                            'Program simulasi untuk pengujian biaya pendaftaran.',
                        'description' =>
                            'Program ini hanya digunakan pada database testing Ragen Swim Course.',
                        'duration_minutes' => 60,
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Paket Testing
            |--------------------------------------------------------------------------
            */

            $packageName = 'Paket 4 Pertemuan Testing';

            $packageExists = DB::table('packages')
                ->where('program_id', $programId)
                ->where('name', $packageName)
                ->exists();

            if (! $packageExists) {
                DB::table('packages')->insert([
                    'program_id' => $programId,
                    'name' => $packageName,
                    'description' =>
                        'Paket simulasi 4 sesi selama 30 hari.',
                    'total_sessions' => 4,
                    'price' => 400000,
                    'validity_days' => 30,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Informasi
            |--------------------------------------------------------------------------
            */

            $this->command?->info(
                'Data simulasi Ragen Swim Course berhasil disiapkan.'
            );
        });
    }
}
