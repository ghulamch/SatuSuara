@php
    $logo = config('election.logo_instansi');
    $hasLogo = $logo && file_exists(public_path($logo));
@endphp

@if ($hasLogo)
    @php
        $ext = strtolower(pathinfo($logo, PATHINFO_EXTENSION));
        $mime = match($ext) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/x-icon',
        };
        $version = @filemtime(public_path($logo)) ?: time();
    @endphp
    <link rel="icon" type="{{ $mime }}" href="{{ asset($logo) }}?v={{ $version }}">
    <link rel="shortcut icon" type="{{ $mime }}" href="{{ asset($logo) }}?v={{ $version }}">
    <link rel="apple-touch-icon" href="{{ asset($logo) }}?v={{ $version }}">
@else
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231E3A8A' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/%3E%3C/svg%3E">
@endif

