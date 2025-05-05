<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['additional' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['additional' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
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
?>

<?php $__currentLoopData = $cssFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $css): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/' . $css)); ?>" />
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php /**PATH /Applications/MAMP/htdocs/club/resources/views/components/theme-css.blade.php ENDPATH**/ ?>