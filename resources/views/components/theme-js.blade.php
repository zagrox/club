@props(['additional' => []])

@php
$jsFiles = $themeConfig['js'] ?? [];

// Add additional JS files if provided
if (is_array($additional) && count($additional) > 0) {
    $jsFiles = array_merge($jsFiles, $additional);
}

// Add defaults if empty
if (empty($jsFiles)) {
    $jsFiles = [
        'vendor/js/helpers.js',
        'js/config.js',
        'vendor/libs/jquery/jquery.js',
        'vendor/libs/popper/popper.js',
        'vendor/js/bootstrap.js',
        'vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        'vendor/js/menu.js',
        'js/main.js',
    ];
}
@endphp

@foreach($jsFiles as $js)
<script src="{{ asset('assets/' . $js) }}"></script>
@endforeach

<!-- Theme Manager -->
<script src="{{ asset('assets/js/theme-manager.js') }}"></script> 