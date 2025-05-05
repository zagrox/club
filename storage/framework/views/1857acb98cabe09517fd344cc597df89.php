<?php $__env->startSection('title', 'Account Settings - Connections'); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Connections
</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.account')); ?>">
          <i class="bx bx-user me-1"></i> Account
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.security')); ?>">
          <i class="bx bx-lock-alt me-1"></i> Security
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.notifications')); ?>">
          <i class="bx bx-bell me-1"></i> Notifications
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="<?php echo e(route('settings.connections')); ?>">
          <i class="bx bx-link-alt me-1"></i> Connections
        </a>
      </li>
    </ul>
    <div class="card">
      <h5 class="card-header">Connected accounts</h5>
      <div class="card-body">
        <p>Display content from your connected accounts on your site</p>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/google.png')); ?>" alt="google" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-9 mb-sm-0 mb-2">
              <h6 class="mb-0">Google</h6>
              <small class="text-muted">Not Connected</small>
            </div>
            <div class="col-3 text-end">
              <button class="btn btn-label-secondary btn-icon waves-effect"><i class="bx bx-link-alt"></i></button>
            </div>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/slack.png')); ?>" alt="slack" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-9 mb-sm-0 mb-2">
              <h6 class="mb-0">Slack</h6>
              <small class="text-muted">Connected</small>
            </div>
            <div class="col-3 text-end">
              <button class="btn btn-label-danger btn-icon waves-effect"><i class="bx bx-trash-alt"></i></button>
            </div>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/github.png')); ?>" alt="github" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-9 mb-sm-0 mb-2">
              <h6 class="mb-0">Github</h6>
              <small class="text-muted">Not Connected</small>
            </div>
            <div class="col-3 text-end">
              <button class="btn btn-label-secondary btn-icon waves-effect"><i class="bx bx-link-alt"></i></button>
            </div>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/mailchimp.png')); ?>" alt="mailchimp" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-9 mb-sm-0 mb-2">
              <h6 class="mb-0">Mailchimp</h6>
              <small class="text-muted">Connected</small>
            </div>
            <div class="col-3 text-end">
              <button class="btn btn-label-danger btn-icon waves-effect"><i class="bx bx-trash-alt"></i></button>
            </div>
          </div>
        </div>
        <div class="d-flex">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/asana.png')); ?>" alt="asana" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-9 mb-sm-0 mb-2">
              <h6 class="mb-0">Asana</h6>
              <small class="text-muted">Not Connected</small>
            </div>
            <div class="col-3 text-end">
              <button class="btn btn-label-secondary btn-icon waves-effect"><i class="bx bx-link-alt"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card mt-4">
      <h5 class="card-header">Social accounts</h5>
      <div class="card-body">
        <p>Display content from social accounts on your site</p>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/facebook.png')); ?>" alt="facebook" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-8 col-sm-7 mb-sm-0 mb-2">
              <h6 class="mb-0">Facebook</h6>
              <small class="text-muted">Not Connected</small>
            </div>
            <div class="col-4 col-sm-5 text-end">
              <button type="button" class="btn btn-icon btn-label-secondary">
                <i class='bx bx-link-alt'></i>
              </button>
            </div>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/twitter.png')); ?>" alt="twitter" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-8 col-sm-7 mb-sm-0 mb-2">
              <h6 class="mb-0">Twitter</h6>
              <a href="https://twitter.com/johndoe" target="_blank">@johndoe</a>
            </div>
            <div class="col-4 col-sm-5 text-end">
              <button type="button" class="btn btn-icon btn-label-danger">
                <i class='bx bx-trash-alt'></i>
              </button>
            </div>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/instagram.png')); ?>" alt="instagram" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-8 col-sm-7 mb-sm-0 mb-2">
              <h6 class="mb-0">instagram</h6>
              <a href="https://www.instagram.com/john.doe/" target="_blank">@john.doe</a>
            </div>
            <div class="col-4 col-sm-5 text-end">
              <button type="button" class="btn btn-icon btn-label-danger">
                <i class='bx bx-trash-alt'></i>
              </button>
            </div>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/dribbble.png')); ?>" alt="dribbble" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-8 col-sm-7 mb-sm-0 mb-2">
              <h6 class="mb-0">Dribbble</h6>
              <small class="text-muted">Not Connected</small>
            </div>
            <div class="col-4 col-sm-5 text-end">
              <button type="button" class="btn btn-icon btn-label-secondary">
                <i class='bx bx-link-alt'></i>
              </button>
            </div>
          </div>
        </div>
        <div class="d-flex">
          <div class="flex-shrink-0">
            <img src="<?php echo e(asset('assets/img/icons/brands/behance.png')); ?>" alt="behance" class="me-3" height="30">
          </div>
          <div class="flex-grow-1 row">
            <div class="col-8 col-sm-7 mb-sm-0 mb-2">
              <h6 class="mb-0">Behance</h6>
              <small class="text-muted">Not Connected</small>
            </div>
            <div class="col-4 col-sm-5 text-end">
              <button type="button" class="btn btn-icon btn-label-secondary">
                <i class='bx bx-link-alt'></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/pages/settings/connections.blade.php ENDPATH**/ ?>