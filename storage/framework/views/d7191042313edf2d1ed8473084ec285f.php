<!DOCTYPE html>
<html
  lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
  class="layout-<?php echo e(($themeConfig['menu_collapsed'] ?? false) ? 'compact' : 'expanded'); ?>"
  data-assets-path="<?php echo e($themeConfig['assets_path'] ?? 'assets'); ?>/"
  data-template="<?php echo e($themeConfig['template'] ?? 'vertical-menu-template-free'); ?>"
  data-theme="<?php echo e($themeConfig['mode'] ?? 'light'); ?>"
  <?php if($themeConfig['is_rtl'] ?? false): ?> dir="rtl" <?php endif; ?>>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> | <?php echo e(config('app.name', 'Laravel')); ?></title>

    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', ''); ?>" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon/favicon.ico')); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <!-- Core CSS Files -->
    <?php if (isset($component)) { $__componentOriginal84e31ca33f95b830e4e38ff1e67983af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal84e31ca33f95b830e4e38ff1e67983af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.theme-css','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('theme-css'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal84e31ca33f95b830e4e38ff1e67983af)): ?>
<?php $attributes = $__attributesOriginal84e31ca33f95b830e4e38ff1e67983af; ?>
<?php unset($__attributesOriginal84e31ca33f95b830e4e38ff1e67983af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal84e31ca33f95b830e4e38ff1e67983af)): ?>
<?php $component = $__componentOriginal84e31ca33f95b830e4e38ff1e67983af; ?>
<?php unset($__componentOriginal84e31ca33f95b830e4e38ff1e67983af); ?>
<?php endif; ?>

    <!-- Page CSS -->
    <?php echo $__env->yieldContent('page-css'); ?>

    <!-- Helpers -->
    <script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>" defer></script>
    <script src="<?php echo e(asset('assets/js/config.js')); ?>" defer></script>
    
    <!-- Fix for 'kl' variable conflict -->
    <script>
      // Clean up any existing variables that might cause conflicts
      if (typeof window.kl !== 'undefined') {
        delete window.kl;
      }
    </script>
    
    <!-- Additional Head Content -->
    <?php echo $__env->yieldContent('head'); ?>
  </head>

  <body>
    <?php if(View::hasSection('full-content')): ?>
      <?php echo $__env->yieldContent('full-content'); ?>
    <?php else: ?>
      <!-- Layout wrapper -->
      <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
          <!-- Menu -->
          <?php if ($__env->exists('layouts.sidebar')) echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          <!-- / Menu -->

          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
            <?php if ($__env->exists('layouts.navbar')) echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
              <div class="container-xxl flex-grow-1 container-p-y">
                <?php echo $__env->yieldContent('content'); ?>
              </div>
              <!-- / Content -->

              <!-- Footer -->
              <?php if ($__env->exists('layouts.footer')) echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <!-- / Footer -->

              <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <!-- / Layout wrapper -->
    <?php endif; ?>

    <!-- Modals -->
    <?php echo $__env->yieldContent('modals'); ?>
    <!-- / Modals -->

    <!-- Core JS Files -->
    <?php if (isset($component)) { $__componentOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.theme-js','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('theme-js'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b)): ?>
<?php $attributes = $__attributesOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b; ?>
<?php unset($__attributesOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b)): ?>
<?php $component = $__componentOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b; ?>
<?php unset($__componentOriginal1acd4d9f5d22ce0efbee9ef5151c6a5b); ?>
<?php endif; ?>

    <!-- Page JS -->
    <?php echo $__env->yieldContent('page-js'); ?>
  </body>
</html> <?php /**PATH /Applications/MAMP/htdocs/club/resources/views/layouts/base.blade.php ENDPATH**/ ?>