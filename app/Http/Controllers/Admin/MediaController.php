<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Sponsor;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class MediaController extends Controller
{
    public function index(): View
    {
        return view('admin.media.index', [
            'galleries' => Gallery::ordered()->get(),
            'sponsors' => Sponsor::orderBy('sort_order')->orderBy('id')->get(),
            'logo' => Setting::getValue(
                'website_logo',
                'branding/ragen-logo.png'
            ),
            'favicon' => Setting::getValue(
                'website_favicon',
                'favicon.ico'
            ),
        ]);
    }

    private function deleteManagedFile(?string $path, string $directory): void
    {
        if (
            $path &&
            str_starts_with($path, $directory . '/') &&
            !str_contains($path, '..')
        ) {
            Storage::disk('public')->delete($path);
        }
    }

    public function galleryStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:3000',
            'category' => 'nullable|in:regular,private,special_needs,trial,event,facility,coach',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|in:published,draft',
            'image' => 'required|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $path = $request->file('image')->store('galleries', 'public');

        if (!$path) {
            return back()->withErrors([
                'image' => 'Gambar galeri gagal disimpan.',
            ]);
        }

        $data['image'] = $path;

        try {
            Gallery::create($data);
        } catch (Throwable $exception) {
            $this->deleteManagedFile($path, 'galleries');
            throw $exception;
        }

        return back()->with('success', 'Foto gallery ditambahkan.');
    }

    public function galleryUpdate(Request $request, Gallery $gallery): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:3000',
            'category' => 'nullable|in:regular,private,special_needs,trial,event,facility,coach',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|in:published,draft',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $oldPath = $gallery->image;
        $newPath = null;

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('galleries', 'public');

            if (!$newPath) {
                return back()->withErrors([
                    'image' => 'Gambar galeri baru gagal disimpan.',
                ]);
            }

            $data['image'] = $newPath;
        } else {
            unset($data['image']);
        }

        try {
            $gallery->update($data);
        } catch (Throwable $exception) {
            if ($newPath) {
                $this->deleteManagedFile($newPath, 'galleries');
            }

            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            $this->deleteManagedFile($oldPath, 'galleries');
        }

        return back()->with('success', 'Gallery diperbarui.');
    }

    public function galleryDestroy(Gallery $gallery): RedirectResponse
    {
        $oldPath = $gallery->image;

        $gallery->delete();

        $this->deleteManagedFile($oldPath, 'galleries');

        return back()->with('success', 'Foto dihapus.');
    }

    public function sponsorStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'website_url' => 'nullable|url:http,https|max:500',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'logo' => 'required|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $path = $request->file('logo')->store('sponsors', 'public');

        if (!$path) {
            return back()->withErrors([
                'logo' => 'Logo sponsor gagal disimpan.',
            ]);
        }

        $data['logo'] = $path;

        try {
            Sponsor::create($data);
        } catch (Throwable $exception) {
            $this->deleteManagedFile($path, 'sponsors');
            throw $exception;
        }

        return back()->with('success', 'Sponsor ditambahkan.');
    }

    public function sponsorUpdate(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'website_url' => 'nullable|url:http,https|max:500',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $oldPath = $sponsor->logo;
        $newPath = null;

        if ($request->hasFile('logo')) {
            $newPath = $request->file('logo')->store('sponsors', 'public');

            if (!$newPath) {
                return back()->withErrors([
                    'logo' => 'Logo sponsor baru gagal disimpan.',
                ]);
            }

            $data['logo'] = $newPath;
        } else {
            unset($data['logo']);
        }

        try {
            $sponsor->update($data);
        } catch (Throwable $exception) {
            if ($newPath) {
                $this->deleteManagedFile($newPath, 'sponsors');
            }

            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            $this->deleteManagedFile($oldPath, 'sponsors');
        }

        return back()->with('success', 'Sponsor diperbarui.');
    }

    public function sponsorDestroy(Sponsor $sponsor): RedirectResponse
    {
        $oldPath = $sponsor->logo;

        $sponsor->delete();

        $this->deleteManagedFile($oldPath, 'sponsors');

        return back()->with('success', 'Sponsor dihapus.');
    }

    public function logoUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpeg,webp|max:5120',
        ]);

        $oldPath = Setting::getValue('website_logo');
        $newPath = $request->file('logo')->store('branding', 'public');

        if (!$newPath) {
            return back()->withErrors([
                'logo' => 'Logo website gagal disimpan.',
            ]);
        }

        try {
            Setting::setValue(
                'website_logo',
                $newPath,
                'string',
                null,
                'branding'
            );
        } catch (Throwable $exception) {
            $this->deleteManagedFile($newPath, 'branding');
            throw $exception;
        }

        if (
            $oldPath &&
            $oldPath !== 'branding/ragen-logo.png' &&
            $oldPath !== $newPath
        ) {
            $this->deleteManagedFile($oldPath, 'branding');
        }

        return back()->with('success', 'Logo website diperbarui.');
    }

    public function faviconUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'favicon' => 'required|file|mimes:png,jpeg,webp,ico|max:2048',
        ]);

        $oldPath = Setting::getValue('website_favicon');
        $newPath = $request->file('favicon')
            ->store('branding/favicons', 'public');

        if (!$newPath) {
            return back()->withErrors([
                'favicon' => 'Favicon website gagal disimpan.',
            ]);
        }

        try {
            Setting::setValue(
                'website_favicon',
                $newPath,
                'string',
                null,
                'branding'
            );
        } catch (Throwable $exception) {
            $this->deleteManagedFile($newPath, 'branding/favicons');
            throw $exception;
        }

        if (
            $oldPath &&
            $oldPath !== 'favicon.ico' &&
            $oldPath !== $newPath
        ) {
            $this->deleteManagedFile($oldPath, 'branding/favicons');
        }

        return back()->with('success', 'Favicon website berhasil diperbarui.');
    }
}
