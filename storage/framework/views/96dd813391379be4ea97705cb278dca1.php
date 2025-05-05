<?php $__env->startSection('title', 'Change Logs'); ?>

<?php $__env->startSection('page-css'); ?>
<style>
  .release-date {
    font-size: 0.875rem;
    color: #a1acb8;
  }
  .release-tag {
    font-weight: 600;
    color: #5d87ff;
  }
  .release-body {
    margin-top: 1rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eaeaec;
  }
  .github-link {
    text-decoration: none;
  }
  .github-link:hover {
    text-decoration: underline;
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Admin Notice for Backups -->
<?php if(Auth::check() && Auth::user()->hasRole('admin')): ?>
<div class="alert alert-info mb-4">
  <div class="d-flex align-items-center">
    <i class="bx bx-archive bx-sm me-2"></i>
    <div>
      <h6 class="alert-heading mb-1">Backup Management</h6>
      <p class="mb-2">
        System backups are now managed on a dedicated page for better organization.
      </p>
      <a href="<?php echo e(route('backup.index')); ?>" class="btn btn-sm btn-info">
        <i class="bx bx-right-arrow-alt me-1"></i>Go to Backup Management
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Change Logs Section -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Change Logs</h5>
    <a href="https://github.com/zagrox/club" target="_blank" class="btn btn-primary">
      <i class="bx bxl-github me-1"></i>View on GitHub
    </a>
  </div>
  <div class="card-body">
    <div class="alert alert-primary">
      <div class="d-flex">
        <i class="bx bx-info-circle bx-sm me-2"></i>
        <div>
          <h6 class="alert-heading mb-1">Project Information</h6>
          <p class="mb-0">
            Track all the changes and updates to our application. This page shows the latest tags and versions
            from our GitHub repository.
          </p>
        </div>
      </div>
    </div>
    
    <?php if(empty($releases)): ?>
      <div class="text-center p-5">
        <i class="bx bx-error-circle bx-lg text-muted mb-3"></i>
        <h5>No Tags Found</h5>
        <p class="text-muted">Unable to fetch tag information at this time.</p>
      </div>
    <?php else: ?>
      <div class="timeline">
        <?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="release-tag"><?php echo e($release['name'] ?? $release['tag_name'] ?? 'Unknown'); ?></h5>
              <span class="release-date"><?php echo e(isset($release['published_at']) ? \Carbon\Carbon::parse($release['published_at'])->format('F d, Y') : 'Unknown date'); ?></span>
            </div>
            <div class="release-body">
              <?php echo isset($release['body']) ? nl2br(e($release['body'])) : 'No description available'; ?>

            </div>
            <?php if(isset($release['html_url'])): ?>
              <div class="mt-2">
                <a href="<?php echo e($release['html_url']); ?>" target="_blank" class="github-link">
                  <i class="bx bx-link-external me-1"></i>View tag details
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script>
  // Any specific JavaScript for the Change Logs page can be added here
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/pages/change-logs.blade.php ENDPATH**/ ?>