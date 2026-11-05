<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\ProgressRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    /**
     * Menampilkan seluruh perkembangan peserta
     * yang dimiliki customer yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Ambil peserta milik customer
        |--------------------------------------------------------------------------
        |
        | Satu customer dapat mempunyai lebih dari satu peserta/anak.
        |
        */
        $participants = Participant::query()
            ->where('user_id', $user->id)
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Peserta yang sedang dipilih
        |--------------------------------------------------------------------------
        |
        | Jika URL memiliki ?participant=1, maka peserta tersebut yang
        | ditampilkan.
        |
        | Jika belum ada pilihan, otomatis menggunakan peserta pertama.
        |
        */
        $selectedParticipant = null;

        if ($participants->isNotEmpty()) {
            $requestedParticipantId = $request->integer('participant');

            if ($requestedParticipantId) {
                $selectedParticipant = $participants->firstWhere(
                    'id',
                    $requestedParticipantId
                );
            }

            if ($selectedParticipant === null) {
                $selectedParticipant = $participants->first();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Data perkembangan
        |--------------------------------------------------------------------------
        */
        $progressRecords = collect();

        $latestProgress = null;

        if ($selectedParticipant !== null) {
            $progressRecords = ProgressRecord::query()
                ->with([
                    'coach.user',
                    'booking.classSchedule.program',
                    'booking.classSchedule.location',
                ])
                ->where(
                    'participant_id',
                    $selectedParticipant->id
                )
                ->orderByDesc('assessment_date')
                ->orderByDesc('id')
                ->get();

            $latestProgress = $progressRecords->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik sederhana
        |--------------------------------------------------------------------------
        */
        $statistics = [
            'total_assessments' => $progressRecords->count(),
            'latest_average' => $latestProgress
                ? $latestProgress->average_score
                : 0,
            'latest_level' => $latestProgress
                ? $latestProgress->level_label
                : 'Belum Dinilai',
        ];

        return view('customer.progress.index', [
            'participants' => $participants,
            'selectedParticipant' => $selectedParticipant,
            'progressRecords' => $progressRecords,
            'latestProgress' => $latestProgress,
            'statistics' => $statistics,
        ]);
    }
}
