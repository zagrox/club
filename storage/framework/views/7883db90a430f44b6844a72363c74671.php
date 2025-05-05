<?php $__env->startSection('title', __('messages.Account Settings - Security')); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light"><?php echo e(__('messages.Account Settings')); ?> /</span> <?php echo e(__('messages.Security')); ?>

</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.account')); ?>">
          <i class="bx bx-user me-1"></i> <?php echo e(__('messages.Account')); ?>

        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="<?php echo e(route('settings.security')); ?>">
          <i class="bx bx-lock-alt me-1"></i> <?php echo e(__('messages.Security')); ?>

        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.notifications')); ?>">
          <i class="bx bx-bell me-1"></i> <?php echo e(__('messages.Notifications')); ?>

        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.connections')); ?>">
          <i class="bx bx-link-alt me-1"></i> <?php echo e(__('messages.Connections')); ?>

        </a>
      </li>
    </ul>

    <!-- Change Password -->
    <div class="card mb-4">
      <h5 class="card-header"><?php echo e(__('messages.Change Password')); ?></h5>
      <div class="card-body">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible mb-3" role="alert">
              <?php echo e(session('success')); ?>

              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible mb-3" role="alert">
              <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('settings.change-password')); ?>">
          <?php echo csrf_field(); ?>
          
          <!-- Password Requirements Alert -->
          <div class="alert alert-warning mb-4" role="alert">
            <div class="d-flex">
              <span class="alert-icon text-warning me-2">
                <i class="bx bx-error-circle"></i>
              </span>
              <div>
                <h6 class="alert-heading mb-1"><?php echo e(__('messages.Ensure that these requirements are met')); ?></h6>
                <span><?php echo e(__('messages.Minimum 8 characters long, uppercase & symbol')); ?></span>
              </div>
              <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
          
          <div class="row">
            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="password"><?php echo e(__('messages.New Password')); ?></label>
              <div class="input-group input-group-merge">
                <input
                  class="form-control"
                  type="password"
                  id="password"
                  name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
            </div>
            
            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="password_confirmation"><?php echo e(__('messages.Confirm New Password')); ?></label>
              <div class="input-group input-group-merge">
                <input
                  class="form-control"
                  type="password"
                  name="password_confirmation"
                  id="password_confirmation"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password_confirmation" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
            </div>
            
            <div>
              <button type="submit" class="btn btn-primary"><?php echo e(__('messages.Change Password')); ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Two-steps verification -->
    <div class="card mb-4">
      <h5 class="card-header"><?php echo e(__('messages.Two-steps verification (Soon)')); ?></h5>
      
      <div class="card-body">
        <p><?php echo e(__('messages.Keep your account secure with authentication step.')); ?></p>
        <h6><?php echo e(__('messages.SMS')); ?></h6>
        <div class="d-flex align-items-center justify-content-between border-bottom pb-3">
          <div class="d-flex align-items-center flex-grow-1">
            <input class="form-control w-75 me-2" type="text" value="+1(968) 945-8832" placeholder="+1(XXX) XXX-XXXX" readonly />
            <div>
              <button type="button" class="btn btn-icon btn-outline-primary me-1">
                <i class="bx bx-pencil"></i>
              </button>
              <button type="button" class="btn btn-icon btn-outline-primary">
                <i class="bx bx-user-plus"></i>
              </button>
            </div>
          </div>
        </div>
        <p class="mt-3 mb-0">
          <?php echo e(__('messages.Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.')); ?>

          <a href="javascript:void(0)"><?php echo e(__('messages.Learn more')); ?></a>.
        </p>
      </div>
    </div>
    
    <!-- Recent devices -->
    <div class="card">
      <h5 class="card-header"><?php echo e(__('messages.Recent Devices')); ?></h5>
      <div class="table-responsive">
        <table class="table border-top">
          <thead>
            <tr>
              <th class="text-truncate"><?php echo e(__('messages.BROWSER')); ?></th>
              <th class="text-truncate"><?php echo e(__('messages.DEVICE')); ?></th>
              <th class="text-truncate"><?php echo e(__('messages.LOCATION')); ?></th>
              <th class="text-truncate"><?php echo e(__('messages.RECENT ACTIVITIES')); ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-truncate"><i class="bx bxl-windows text-info me-3"></i> <?php echo e(__('messages.Chrome on Windows')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.HP Spectre 360')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.Switzerland')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.10, July 2021 20:07')); ?></td>
            </tr>
            <tr>
              <td class="text-truncate"><i class="bx bx-mobile-alt text-danger me-3"></i> <?php echo e(__('messages.Chrome on iPhone')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.iPhone 12x')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.Australia')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.13, July 2021 10:10')); ?></td>
            </tr>
            <tr>
              <td class="text-truncate"><i class="bx bxl-android text-success me-3"></i> <?php echo e(__('messages.Chrome on Android')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.Oneplus 9 Pro')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.Dubai')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.14, July 2021 15:15')); ?></td>
            </tr>
            <tr>
              <td class="text-truncate"><i class="bx bxl-apple text-secondary me-3"></i> <?php echo e(__('messages.Chrome on MacOS')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.Apple iMac')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.India')); ?></td>
              <td class="text-truncate"><?php echo e(__('messages.16, July 2021 16:17')); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle password visibility
  const togglePasswordButtons = document.querySelectorAll('.input-group-text');
  
  togglePasswordButtons.forEach(button => {
    button.addEventListener('click', function() {
      const input = this.previousElementSibling;
      const icon = this.querySelector('i');
      
      if (input.getAttribute('type') === 'password') {
        input.setAttribute('type', 'text');
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
      } else {
        input.setAttribute('type', 'password');
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
      }
    });
  });
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/pages/settings/security.blade.php ENDPATH**/ ?>