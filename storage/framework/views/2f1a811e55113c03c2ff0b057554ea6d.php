<?php $__env->startSection('title', 'Edit Translations'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / <a href="<?php echo e(url('admin/translations')); ?>">Translations</a> /</span> Edit <?php echo e($file); ?>

  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header d-flex justify-content-between align-items-center">
          <span>Edit Translations: <?php echo e($file); ?>.php</span>
          <div class="btn-group">
            <a href="<?php echo e(url('admin/translations')); ?>" class="btn btn-outline-secondary btn-sm">
              <i class="bx bx-arrow-back me-1"></i> Back
            </a>
          </div>
        </h5>
        <div class="card-body">
          <?php if(session('success')): ?>
            <div class="alert alert-success">
              <?php echo e(session('success')); ?>

            </div>
          <?php endif; ?>

          <form action="<?php echo e(url("admin/translations/{$file}")); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="30%">Key</th>
                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <th><?php echo e(strtoupper($language)); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $translations['keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e($key); ?></td>
                      <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                          <input 
                            type="text" 
                            name="translations[<?php echo e($language); ?>][<?php echo e($key); ?>]" 
                            value="<?php echo e($translations['data'][$language][$key] ?? ''); ?>" 
                            class="form-control"
                            <?php if($language === 'fa'): ?> dir="rtl" <?php endif; ?>
                          >
                        </td>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Save Translations
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add a click handler to copy English text to other languages
    const enInputs = document.querySelectorAll('input[name^="translations[en]"]');
    
    enInputs.forEach(input => {
      input.addEventListener('dblclick', function() {
        const key = this.name.match(/\[([^\]]+)\]$/)[1];
        const value = this.value;
        
        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($language !== 'en'): ?>
            document.querySelector(`input[name="translations[<?php echo e($language); ?>][${key}]"]`).value = value;
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      });
    });
  });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/admin/translations/edit.blade.php ENDPATH**/ ?>