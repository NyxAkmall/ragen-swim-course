<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Daftar kategori FAQ yang diperbolehkan.
     */
    private const CATEGORIES = [
        'general',
        'registration',
        'program',
        'schedule',
        'booking',
        'payment',
        'reschedule',
        'package',
        'trial',
    ];

    /**
     * Menampilkan daftar FAQ.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $category = trim((string) $request->query('category', ''));

        $faqs = Faq::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('question', 'like', '%' . $search . '%')
                        ->orWhere('answer', 'like', '%' . $search . '%');
                });
            })
            ->when(
                in_array($status, ['draft', 'published'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                in_array($category, self::CATEGORIES, true),
                fn ($query) => $query->where('category', $category)
            )
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            'total' => Faq::query()->count(),

            'published' => Faq::query()
                ->where('status', 'published')
                ->count(),

            'draft' => Faq::query()
                ->where('status', 'draft')
                ->count(),
        ];

        return view('admin.faqs.index', [
            'faqs' => $faqs,
            'statistics' => $statistics,
            'search' => $search,
            'status' => $status,
            'category' => $category,
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * Form tambah FAQ.
     */
    public function create(): View
    {
        return view('admin.faqs.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * Menyimpan FAQ baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateFaq($request);

        Faq::query()->create($validated);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Form edit FAQ.
     */
    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', [
            'faq' => $faq,
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * Memperbarui FAQ.
     */
    public function update(
        Request $request,
        Faq $faq
    ): RedirectResponse {
        $validated = $this->validateFaq($request);

        $faq->update($validated);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Mengubah status publikasi FAQ.
     */
    public function updateStatus(
        Request $request,
        Faq $faq
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],
        ]);

        if ($faq->status === $validated['status']) {
            return back()->with(
                'warning',
                'FAQ sudah memiliki status tersebut.'
            );
        }

        $faq->update([
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'published'
            ? 'FAQ berhasil dipublikasikan.'
            : 'FAQ berhasil dijadikan draf.';

        return back()->with('success', $message);
    }

    /**
     * Menghapus FAQ.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Validasi data FAQ untuk tambah dan edit.
     */
    private function validateFaq(Request $request): array
    {
        return $request->validate(
            [
                'question' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'answer' => [
                    'required',
                    'string',
                    'max:10000',
                ],

                'category' => [
                    'required',
                    Rule::in(self::CATEGORIES),
                ],

                'status' => [
                    'required',
                    Rule::in(['draft', 'published']),
                ],

                'sort_order' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:999999',
                ],
            ],
            [
                'question.required' =>
                    'Pertanyaan FAQ wajib diisi.',

                'question.max' =>
                    'Pertanyaan maksimal 255 karakter.',

                'answer.required' =>
                    'Jawaban FAQ wajib diisi.',

                'answer.max' =>
                    'Jawaban maksimal 10.000 karakter.',

                'category.required' =>
                    'Kategori FAQ wajib dipilih.',

                'category.in' =>
                    'Kategori FAQ tidak valid.',

                'status.required' =>
                    'Status FAQ wajib dipilih.',

                'status.in' =>
                    'Status FAQ tidak valid.',

                'sort_order.required' =>
                    'Urutan tampil wajib diisi.',

                'sort_order.integer' =>
                    'Urutan tampil harus berupa angka.',

                'sort_order.min' =>
                    'Urutan tampil tidak boleh negatif.',

                'sort_order.max' =>
                    'Urutan tampil maksimal 999999.',
            ]
        );
    }
}
