<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $programs = Program::query()->where('status', 'active')->orderBy('name')->get();
        $programInput = $request->query('program', '');
        $programFilter = is_string($programInput) ? trim($programInput) : '__invalid__';
        $hasProgramFilter = $programFilter !== '';
        $validProgramId = ctype_digit($programFilter) && (int) $programFilter > 0;
        $selectedProgram = $validProgramId
            ? $programs->firstWhere('id', (int) $programFilter)
            : null;
        $searchInput = $request->query('q', '');
        $search = is_string($searchInput) ? trim($searchInput) : '';
        $search = mb_substr($search, 0, 100);

        $packages = Package::query()
            ->where('status', 'active')
            ->whereHas('program', fn ($query) => $query->where('status', 'active'))
            ->with('program')
            ->when($hasProgramFilter, fn ($query) => $query->where('program_id', $selectedProgram?->id ?? -1))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            }))
            ->orderBy('price')
            ->orderBy('id')
            ->paginate(9)
            ->withQueryString();

        return view('catalog.index', compact('programs', 'selectedProgram', 'hasProgramFilter', 'packages', 'search'));
    }
}
