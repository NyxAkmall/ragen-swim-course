<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Gallery;
use App\Models\Sponsor;
use App\Models\Setting;
use App\Models\Faq;
use App\Models\Location;
use App\Models\Program;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama Ragen Swim Course.
     */
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Program & Paket Aktif
        |--------------------------------------------------------------------------
        */

        $programs = Program::query()
            ->where('status', 'active')
            ->with([
                'packages' => function ($query) {
                    $query
                        ->where('status', 'active')
                        ->orderBy('price');
                },
            ])
            ->get()
            ->sortBy(function ($program) {
                $name = strtolower(trim($program->name));

                return match (true) {
                    str_starts_with($name, 'level 1 - basic swimming') => 1,
                    str_starts_with($name, 'level 1 - adult basic swimming') => 2,
                    str_starts_with($name, 'level 2') => 3,
                    str_starts_with($name, 'level 3') => 4,
                    str_starts_with($name, 'level 4') => 5,
                    str_starts_with($name, 'special program') => 6,
                    str_starts_with($name, 'personal program') => 7,
                    str_starts_with($name, 'trial class') => 8,
                    default => 99,
                };
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Semua Paket Aktif
        |--------------------------------------------------------------------------
        */

        $packages = $programs
            ->flatMap(function ($program) {
                return $program->packages;
            })
            ->sortBy('price')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Pelatih Aktif
        |--------------------------------------------------------------------------
        */

        $coaches = Coach::query()
            ->where('status', 'active')
            ->whereHas('user', function ($query) {
                $query
                    ->where('role', 'coach')
                    ->where('status', 'active');
            })
            ->with('user')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Lokasi Latihan Aktif
        |--------------------------------------------------------------------------
        |
        | Sementara tampilkan hanya satu kartu untuk
        | lokasi dengan nama dan kota yang sama.
        |
        | Prioritaskan ID terbaru agar data biaya masuk
        | Rawageni ID 31 digunakan.
        |
        | Ini hanya penanganan tampilan sementara.
        | Data database belum dihapus atau digabungkan.
        |
        */

        $locations = Location::query()
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get()
            ->unique(function ($location) {
                return mb_strtolower(trim($location->name))
                    . '|'
                    . mb_strtolower(trim($location->city));
            })
            ->sortBy('name')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | FAQ Publik
        |--------------------------------------------------------------------------
        |
        | Hanya menampilkan FAQ yang telah dipublikasikan.
        | FAQ berstatus draft tidak ditampilkan.
        |
        */

        $faqsQuery = Faq::query()
            ->where('status', 'published');

        if (Schema::hasColumn('faqs', 'sort_order')) {
            $faqsQuery->orderBy('sort_order');
        }

        $faqs = $faqsQuery
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Homepage
        |--------------------------------------------------------------------------
        */

        return view('welcome', [
            'programs' => $programs,
            'packages' => $packages,
            'coaches' => $coaches,
            'locations' => $locations,
            'faqs' => $faqs,
            'galleries' => Gallery::active()->ordered()->limit(6)->get(),
            'sponsors' => Schema::hasTable('sponsors') ? Sponsor::where('is_active',true)->orderBy('sort_order')->orderBy('id')->get() : collect(),
            'websiteLogo' => Setting::getValue('website_logo','branding/ragen-logo.png'),
        ]);
    }
}
