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
?>

<?php $__currentLoopData = $jsFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<script src="<?php echo e(asset('assets/' . $js)); ?>"></script>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Theme Manager -->
<script src="<?php echo e(asset('assets/js/theme-manager.js')); ?>"></script> <?php /**PATH /Applications/MAMP/htdocs/club/resources/views/components/theme-js.blade.php ENDPATH**/ ?>