<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Coach;
use App\Models\ProgressRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgressController extends Controller
{
    /**
     * Menampilkan riwayat perkembangan peserta.
     */
    public function index(Request $request): View
    {
        $coach = $this->getAuthenticatedCoach($request);

        $progressRecords = ProgressRecord::query()
            ->with([
                'participant',
                'booking',
                'booking.classSchedule',
                'booking.classSchedule.program',
                'booking.classSchedule.location',
            ])
            ->where(
                'coach_id',
                $coach->id
            )
            ->orderByDesc(
                'assessment_date'
            )
            ->orderByDesc(
                'created_at'
            )
            ->get();

        $totalAssessments =
            $progressRecords->count();

        $totalParticipants =
            $progressRecords
                ->pluck('participant_id')
                ->filter()
                ->unique()
                ->count();

        $averageScore =
            $progressRecords->isNotEmpty()
                ? (float) $progressRecords
                    ->avg('average_score')
                : 0;

        $advancedCount =
            $progressRecords
                ->where(
                    'level',
                    'advanced'
                )
                ->count();

        $statistics = [
            'total_assessments' =>
                $totalAssessments,

            'total_participants' =>
                $totalParticipants,

            'average_score' =>
                $averageScore,

            'advanced_count' =>
                $advancedCount,
        ];

        return view(
            'coach.progress.index',
            [
                'coach' =>
                    $coach,

                'progressRecords' =>
                    $progressRecords,

                'statistics' =>
                    $statistics,
            ]
        );
    }


    /**
     * Menampilkan form penilaian perkembangan.
     */
    public function edit(
        Request $request,
        Booking $booking
    ): View {
        $coach =
            $this->getAuthenticatedCoach(
                $request
            );

        $booking->load([
            'participant',
            'participantPackage',
            'classSchedule',
            'classSchedule.program',
            'classSchedule.location',
            'classSchedule.coach',
            'classSchedule.coach.user',
            'attendance',
            'progressRecord',
        ]);

        $this->ensureCoachOwnsBooking(
            $coach,
            $booking
        );

        $this->ensureBookingCanBeAssessed(
            $booking
        );

        $progress =
            $booking->progressRecord;

        return view(
            'coach.progress.edit',
            [
                'coach' =>
                    $coach,

                'booking' =>
                    $booking,

                'progress' =>
                    $progress,

                'progressRecord' =>
                    $progress,
            ]
        );
    }


    /**
     * Menyimpan atau memperbarui penilaian.
     */
    public function update(
        Request $request,
        Booking $booking
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'level' => [
                    'required',
                    Rule::in([
                        'beginner',
                        'basic',
                        'intermediate',
                        'advanced',
                    ]),
                ],

                'water_adaptation' => [
                    'nullable',
                    'integer',
                    'between:1,5',
                ],

                'floating' => [
                    'nullable',
                    'integer',
                    'between:1,5',
                ],

                'breathing' => [
                    'nullable',
                    'integer',
                    'between:1,5',
                ],

                'kicking' => [
                    'nullable',
                    'integer',
                    'between:1,5',
                ],

                'freestyle' => [
                    'nullable',
                    'integer',
                    'between:1,5',
                ],

                'breaststroke' => [
                    'nullable',
                    'integer',
                    'between:1,5',
                ],

                'coach_notes' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ],
            [
                'level.required' =>
                    'Level peserta wajib dipilih.',

                'level.in' =>
                    'Level peserta tidak valid.',

                'water_adaptation.integer' =>
                    'Nilai adaptasi air harus berupa angka.',

                'water_adaptation.between' =>
                    'Nilai adaptasi air harus antara 1 sampai 5.',

                'floating.integer' =>
                    'Nilai mengapung harus berupa angka.',

                'floating.between' =>
                    'Nilai mengapung harus antara 1 sampai 5.',

                'breathing.integer' =>
                    'Nilai pernapasan harus berupa angka.',

                'breathing.between' =>
                    'Nilai pernapasan harus antara 1 sampai 5.',

                'kicking.integer' =>
                    'Nilai gerakan kaki harus berupa angka.',

                'kicking.between' =>
                    'Nilai gerakan kaki harus antara 1 sampai 5.',

                'freestyle.integer' =>
                    'Nilai gaya bebas harus berupa angka.',

                'freestyle.between' =>
                    'Nilai gaya bebas harus antara 1 sampai 5.',

                'breaststroke.integer' =>
                    'Nilai gaya dada harus berupa angka.',

                'breaststroke.between' =>
                    'Nilai gaya dada harus antara 1 sampai 5.',

                'coach_notes.max' =>
                    'Catatan pelatih maksimal 3000 karakter.',
            ]
        );

        $coach =
            $this->getAuthenticatedCoach(
                $request
            );

        DB::transaction(
            function () use (
                $booking,
                $coach,
                $validated
            ) {
                $lockedBooking =
                    Booking::query()
                        ->whereKey(
                            $booking->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $lockedBooking->load([
                    'participant',
                    'classSchedule',
                    'attendance',
                ]);

                $this->ensureCoachOwnsBooking(
                    $coach,
                    $lockedBooking
                );

                $this->ensureBookingCanBeAssessed(
                    $lockedBooking
                );

                ProgressRecord::updateOrCreate(
                    [
                        'booking_id' =>
                            $lockedBooking->id,
                    ],
                    [
                        'participant_id' =>
                            $lockedBooking
                                ->participant_id,

                        'coach_id' =>
                            $coach->id,

                        'level' =>
                            $validated['level'],

                        'water_adaptation' =>
                            $validated[
                                'water_adaptation'
                            ] ?? null,

                        'floating' =>
                            $validated[
                                'floating'
                            ] ?? null,

                        'breathing' =>
                            $validated[
                                'breathing'
                            ] ?? null,

                        'kicking' =>
                            $validated[
                                'kicking'
                            ] ?? null,

                        'freestyle' =>
                            $validated[
                                'freestyle'
                            ] ?? null,

                        'breaststroke' =>
                            $validated[
                                'breaststroke'
                            ] ?? null,

                        'coach_notes' =>
                            $validated[
                                'coach_notes'
                            ] ?? null,

                        'assessment_date' =>
                            $lockedBooking
                                ->classSchedule
                                ?->class_date
                                ?? now()->toDateString(),
                    ]
                );
            }
        );

        return redirect()
            ->route(
                'coach.schedules.show',
                $booking->class_schedule_id
            )
            ->with(
                'success',
                'Penilaian perkembangan peserta berhasil disimpan.'
            );
    }


    /**
     * Mendapatkan coach berdasarkan user yang sedang login.
     */
    private function getAuthenticatedCoach(
        Request $request
    ): Coach {
        $coach =
            Coach::query()
                ->with('user')
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->first();

        abort_if(
            $coach === null,
            403,
            'Profil coach tidak ditemukan.'
        );

        abort_unless(
            $coach->status === 'active',
            403,
            'Profil coach sedang tidak aktif.'
        );

        return $coach;
    }


    /**
     * Memastikan booking dimiliki oleh coach login.
     */
    private function ensureCoachOwnsBooking(
        Coach $coach,
        Booking $booking
    ): void {
        $schedule =
            $booking->classSchedule;

        abort_if(
            $schedule === null,
            404,
            'Jadwal kelas tidak ditemukan.'
        );

        abort_unless(
            (int) $schedule->coach_id ===
                (int) $coach->id,
            403,
            'Anda tidak memiliki akses ke peserta pada kelas ini.'
        );
    }


    /**
     * Memastikan peserta dapat diberikan penilaian.
     */
    private function ensureBookingCanBeAssessed(
        Booking $booking
    ): void {
        abort_unless(
            in_array(
                $booking->status,
                [
                    'confirmed',
                    'completed',
                ],
                true
            ),
            422,
            'Booking ini tidak dapat diberikan penilaian perkembangan.'
        );

        abort_if(
            $booking->attendance === null,
            422,
            'Kehadiran peserta harus dicatat terlebih dahulu.'
        );

        abort_unless(
            $booking->attendance->status === 'present',
            422,
            'Perkembangan hanya dapat dicatat untuk peserta yang hadir.'
        );
    }
}
