<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('page-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->yieldContent('content'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<?php echo $__env->yieldContent('page-js'); ?>
<?php echo $__env->yieldPushContent('page-scripts'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<?php echo $__env->yieldContent('modals'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/layouts/app.blade.php ENDPATH**/ ?>