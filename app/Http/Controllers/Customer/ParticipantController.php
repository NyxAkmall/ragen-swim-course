<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    /**
     * Menampilkan daftar peserta milik customer yang sedang login.
     */
    public function index(Request $request): View
    {
        $participants = $request->user()
            ->participants()
            ->orderByRaw("
                CASE
                    WHEN status = 'active' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('full_name')
            ->get();

        return view(
            'customer.participants.index',
            compact('participants')
        );
    }

    /**
     * Menampilkan form tambah peserta.
     */
    public function create(): View
    {
        return view('customer.participants.create');
    }

    /**
     * Menyimpan peserta baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'full_name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'gender' => [
                    'required',
                    Rule::in([
                        'male',
                        'female',
                    ]),
                ],

                'birth_date' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],

                'swimming_level' => [
                    'required',
                    Rule::in([
                        'beginner',
                        'basic',
                        'intermediate',
                        'advanced',
                    ]),
                ],

                'participant_type' => [
                    'required',
                    Rule::in([
                        'self',
                        'child',
                    ]),
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'emergency_contact_name' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'emergency_contact_phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],
            ],
            [
                'full_name.required' =>
                    'Nama peserta wajib diisi.',

                'full_name.max' =>
                    'Nama peserta maksimal 150 karakter.',

                'gender.required' =>
                    'Jenis kelamin wajib dipilih.',

                'gender.in' =>
                    'Jenis kelamin yang dipilih tidak valid.',

                'birth_date.required' =>
                    'Tanggal lahir wajib diisi.',

                'birth_date.date' =>
                    'Tanggal lahir tidak valid.',

                'birth_date.before_or_equal' =>
                    'Tanggal lahir tidak boleh melebihi hari ini.',

                'swimming_level.required' =>
                    'Level kemampuan berenang wajib dipilih.',

                'swimming_level.in' =>
                    'Level kemampuan berenang tidak valid.',

                'participant_type.required' =>
                    'Jenis peserta wajib dipilih.',

                'participant_type.in' =>
                    'Jenis peserta tidak valid.',

                'notes.max' =>
                    'Catatan maksimal 2000 karakter.',

                'emergency_contact_name.max' =>
                    'Nama kontak darurat maksimal 150 karakter.',

                'emergency_contact_phone.max' =>
                    'Nomor kontak darurat maksimal 30 karakter.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Keamanan kepemilikan peserta
        |--------------------------------------------------------------------------
        |
        | user_id tidak diambil dari input form.
        | Peserta selalu dihubungkan dengan customer yang sedang login.
        |
        */

        $request->user()
            ->participants()
            ->create([
                'full_name' =>
                    $validated['full_name'],

                'gender' =>
                    $validated['gender'],

                'birth_date' =>
                    $validated['birth_date'],

                'swimming_level' =>
                    $validated['swimming_level'],

                'participant_type' =>
                    $validated['participant_type'],

                'notes' =>
                    $validated['notes'] ?? null,

                'emergency_contact_name' =>
                    $validated['emergency_contact_name'] ?? null,

                'emergency_contact_phone' =>
                    $validated['emergency_contact_phone'] ?? null,

                'status' => 'active',
            ]);

        return redirect()
            ->route('customer.participants.index')
            ->with(
                'success',
                'Peserta berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit peserta.
     */
    public function edit(
        Request $request,
        Participant $participant
    ): View {
        $this->ensureOwnership(
            $request,
            $participant
        );

        return view(
            'customer.participants.edit',
            compact('participant')
        );
    }

    /**
     * Memperbarui data peserta.
     */
    public function update(
        Request $request,
        Participant $participant
    ): RedirectResponse {
        $this->ensureOwnership(
            $request,
            $participant
        );

        $validated = $request->validate(
            [
                'full_name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'gender' => [
                    'required',
                    Rule::in([
                        'male',
                        'female',
                    ]),
                ],

                'birth_date' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],

                'swimming_level' => [
                    'required',
                    Rule::in([
                        'beginner',
                        'basic',
                        'intermediate',
                        'advanced',
                    ]),
                ],

                'participant_type' => [
                    'required',
                    Rule::in([
                        'self',
                        'child',
                    ]),
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'emergency_contact_name' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'emergency_contact_phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],
            ],
            [
                'full_name.required' =>
                    'Nama peserta wajib diisi.',

                'full_name.max' =>
                    'Nama peserta maksimal 150 karakter.',

                'gender.required' =>
                    'Jenis kelamin wajib dipilih.',

                'gender.in' =>
                    'Jenis kelamin yang dipilih tidak valid.',

                'birth_date.required' =>
                    'Tanggal lahir wajib diisi.',

                'birth_date.date' =>
                    'Tanggal lahir tidak valid.',

                'birth_date.before_or_equal' =>
                    'Tanggal lahir tidak boleh melebihi hari ini.',

                'swimming_level.required' =>
                    'Level kemampuan berenang wajib dipilih.',

                'swimming_level.in' =>
                    'Level kemampuan berenang tidak valid.',

                'participant_type.required' =>
                    'Jenis peserta wajib dipilih.',

                'participant_type.in' =>
                    'Jenis peserta tidak valid.',

                'notes.max' =>
                    'Catatan maksimal 2000 karakter.',

                'emergency_contact_name.max' =>
                    'Nama kontak darurat maksimal 150 karakter.',

                'emergency_contact_phone.max' =>
                    'Nomor kontak darurat maksimal 30 karakter.',
            ]
        );

        $participant->update([
            'full_name' =>
                $validated['full_name'],

            'gender' =>
                $validated['gender'],

            'birth_date' =>
                $validated['birth_date'],

            'swimming_level' =>
                $validated['swimming_level'],

            'participant_type' =>
                $validated['participant_type'],

            'notes' =>
                $validated['notes'] ?? null,

            'emergency_contact_name' =>
                $validated['emergency_contact_name'] ?? null,

            'emergency_contact_phone' =>
                $validated['emergency_contact_phone'] ?? null,
        ]);

        return redirect()
            ->route('customer.participants.index')
            ->with(
                'success',
                'Data peserta berhasil diperbarui.'
            );
    }

    /**
     * Menonaktifkan peserta.
     *
     * Kita tidak menghapus data peserta secara permanen
     * karena nantinya peserta dapat memiliki histori
     * paket, booking, absensi, dan progress.
     */
    public function destroy(
        Request $request,
        Participant $participant
    ): RedirectResponse {
        $this->ensureOwnership(
            $request,
            $participant
        );

        if ($participant->status === 'inactive') {
            return redirect()
                ->route('customer.participants.index')
                ->with(
                    'success',
                    'Peserta sudah dalam status tidak aktif.'
                );
        }

        $participant->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('customer.participants.index')
            ->with(
                'success',
                'Peserta berhasil dinonaktifkan.'
            );
    }

    /**
     * Memastikan peserta benar-benar milik
     * customer yang sedang login.
     */
    private function ensureOwnership(
        Request $request,
        Participant $participant
    ): void {
        abort_unless(
            (int) $participant->user_id ===
            (int) $request->user()->id,
            403
        );
    }
}
