<?php $__env->startSection('title', 'Translation Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Translation Management
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">Translation Files</h5>
        <div class="card-body">
          <div class="alert alert-info">
            <p>
              <i class="bx bx-info-circle"></i> 
              This page shows all translation files in your application. You can edit translations for all supported languages:
              <strong><?php echo e(implode(', ', $languages)); ?></strong>
            </p>
          </div>
          
          <?php if(session('extraction_stats')): ?>
          <div class="alert alert-success">
            <h6><i class="bx bx-check-circle"></i> Translation Extraction Report</h6>
            <p>
              Successfully extracted <strong><?php echo e(session('extraction_stats')['unique']); ?></strong> unique translatable strings
              across <strong><?php echo e(session('extraction_stats')['languages']); ?></strong> languages.
              Total translations: <strong><?php echo e(session('extraction_stats')['total']); ?></strong>
            </p>
            
            <?php if(isset(session('extraction_stats')['details'])): ?>
            <div class="mt-2">
              <strong>Translation files:</strong>
              <ul class="mb-0">
                <?php $__currentLoopData = session('extraction_stats')['details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $files): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <li>
                    <?php echo e(strtoupper($lang)); ?>:
                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <span class="badge bg-label-primary"><?php echo e($file); ?>.php (<?php echo e($count); ?>)</span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          
          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <a href="<?php echo e(url('admin/translations/extract')); ?>" class="btn btn-primary">
                  <i class="bx bx-refresh me-1"></i> Extract Translations
                </a>
              </div>
              
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th width="50%">File</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $translationFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                      <tr>
                        <td><?php echo e($file); ?>.php</td>
                        <td>
                          <a href="<?php echo e(url("admin/translations/{$file}")); ?>" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit-alt"></i> Edit
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                      <tr>
                        <td colspan="2" class="text-center">No translation files found.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/admin/translations/index.blade.php ENDPATH**/ ?>