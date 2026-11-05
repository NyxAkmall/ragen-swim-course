@php
    $faviconPath = \App\Models\Setting::getValue('website_favicon', 'favicon.ico');

    $faviconUrl = $faviconPath === 'favicon.ico'
        ? asset('favicon.ico')
        : asset('storage/' . ltrim($faviconPath, '/'));

    $faviconExtension = strtolower(pathinfo($faviconPath, PATHINFO_EXTENSION));

    $faviconType = match ($faviconExtension) {
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        default => 'image/x-icon',
    };
@endphp

<link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
