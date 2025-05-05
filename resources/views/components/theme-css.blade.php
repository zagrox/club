@props(['additional' => []])

@php
$cssFiles = $themeConfig['css'] ?? [];

// Add additional CSS files if provided
if (is_array($additional) && count($additional) > 0) {
    $cssFiles = array_merge($cssFiles, $additional);
}

// Add theme-specific CSS
if (($themeConfig['mode'] ?? 'light') === 'dark') {
    $cssFiles[] = 'vendor/css/theme-dark.css';
}

// Add RTL support if enabled
if ($themeConfig['is_rtl'] ?? false) {
    $cssFiles[] = 'vendor/css/rtl.css';
}

// Add defaults if empty
if (empty($cssFiles)) {
    $cssFiles = [
        'vendor/fonts/iconify-icons.css',
        'vendor/css/core.css',
        'css/demo.css',
        'vendor/libs/perfect-scrollbar/perfect-scrollbar.css',
    ];
}
@endphp

@foreach($cssFiles as $css)
<link rel="stylesheet" href="{{ asset('assets/' . $css) }}" />
@endforeach 