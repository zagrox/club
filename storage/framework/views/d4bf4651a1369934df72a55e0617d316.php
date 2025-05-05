<?php
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Auth;
?>
<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
        <input
          type="text"
          class="form-control border-0 shadow-none ps-1"
          placeholder="<?php echo e(__('messages.search')); ?>..."
          aria-label="<?php echo e(__('messages.search')); ?>..." />
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">

      <!-- Theme Settings -->
      <li class="nav-item me-2 me-xl-0">
        <a class="nav-link nav-icon" href="<?php echo e(route('theme.settings')); ?>" aria-expanded="false">
          <i class="bx bx-cog bx-sm"></i>
        </a>
      </li>
      <!-- /Theme Settings -->
      
      <!-- Language Selector -->
      <li class="nav-item dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-globe bx-sm"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
              <a class="dropdown-item <?php echo e(App::getLocale() == $localeCode ? 'active' : ''); ?>" 
                href="<?php echo e(LaravelLocalization::getLocalizedURL($localeCode, null, [], true)); ?>" 
                rel="alternate" 
                hreflang="<?php echo e($localeCode); ?>">
                <?php echo e($properties['native']); ?>

              </a>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </li>
      <!-- /Language Selector -->

      <!-- Notification -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
          <i class="bx bx-bell bx-sm"></i>
          <?php
            $standardUnreadCount = Auth::check() ? Auth::user()->unreadNotifications->count() : 0;
            $walletUnreadCount = Auth::check() ? Auth::user()->walletNotifications()->whereNull('read_at')->count() : 0;
            $unreadCount = $standardUnreadCount + $walletUnreadCount;
          ?>
          <?php if($unreadCount > 0): ?>
            <span class="badge bg-danger rounded-pill badge-notifications"><?php echo e($unreadCount); ?></span>
          <?php endif; ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto"><?php echo e(__('messages.notifications')); ?></h5>
              <?php if($unreadCount > 0): ?>
                <a href="<?php echo e(route('inbox.markAllAsRead')); ?>" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('messages.view_all_notifications')); ?>">
                  <i class="bx fs-4 bx-envelope-open"></i>
                </a>
              <?php endif; ?>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
              <?php if(Auth::check() && ($standardNotifications = Auth::user()->notifications->take(3))->isNotEmpty() || 
                  Auth::check() && ($walletNotifications = Auth::user()->walletNotifications()->limit(3)->get())->isNotEmpty()): ?>
                
                
                <?php $__currentLoopData = $standardNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $notificationData = $notification->data;
                    $isRead = $notification->read_at !== null;
                    $icon = $notificationData['icon'] ?? 'bx-bell';
                    $color = $notificationData['color'] ?? 'primary';
                  ?>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item <?php echo e($isRead ? '' : 'marked-as-unread'); ?>">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-<?php echo e($color); ?>">
                            <i class="bx <?php echo e($icon); ?>"></i>
                          </span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1"><?php echo e($notificationData['title'] ?? 'اعلان جدید'); ?></h6>
                        <p class="mb-0"><?php echo e($notificationData['message'] ?? ''); ?></p>
                        <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="<?php echo e(route('inbox.readAndRedirect', $notification->id)); ?>" class="dropdown-notifications-read">
                          <span class="badge badge-dot"></span>
                        </a>
                      </div>
                    </div>
                  </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                
                <?php $__currentLoopData = $walletNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $notificationData = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                    $isRead = $notification->read_at !== null;
                    $icon = $notificationData['icon'] ?? 'bx-wallet';
                    $color = $notificationData['color'] ?? 'primary';
                  ?>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item <?php echo e($isRead ? '' : 'marked-as-unread'); ?>">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-<?php echo e($color); ?>">
                            <i class="bx <?php echo e($icon); ?>"></i>
                          </span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1"><?php echo e($notificationData['title'] ?? 'اعلان کیف پول'); ?></h6>
                        <p class="mb-0"><?php echo e($notificationData['message'] ?? ''); ?></p>
                        <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="<?php echo e(route('inbox.readAndRedirect', ['id' => $notification->id, 'type' => 'wallet'])); ?>" class="dropdown-notifications-read">
                          <span class="badge badge-dot"></span>
                        </a>
                      </div>
                    </div>
                  </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php else: ?>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex">
                    <div class="flex-grow-1 text-center py-2">
                      <p class="mb-0"><?php echo e(__('messages.no_notifications')); ?></p>
                    </div>
                  </div>
                </li>
              <?php endif; ?>
            </ul>
          </li>
          <li class="dropdown-menu-footer border-top">
            <a href="<?php echo e(route('inbox.index')); ?>" class="dropdown-item d-flex justify-content-center p-3">
              <?php echo e(__('messages.view_all_notifications')); ?>

            </a>
          </li>
        </ul>
      </li>
      <!-- /Notification -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-medium d-block"><?php if(auth()->guard()->check()): ?> <?php echo e(Auth::user()->name); ?> <?php else: ?> John Doe <?php endif; ?></span>
                  <small class="text-muted"><?php if(auth()->guard()->check()): ?> <?php echo e(Auth::user()->email); ?> <?php else: ?> admin@example.com <?php endif; ?></small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="<?php echo e(route('settings.account')); ?>">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle"><?php echo e(__('messages.profile')); ?></span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="<?php echo e(route('settings.account')); ?>">
              <i class="bx bx-cog me-2"></i>
              <span class="align-middle"><?php echo e(__('messages.settings')); ?></span>
            </a>
          </li>
          <?php if(Auth::check() && Auth::user()->hasRole('admin')): ?>
          <li>
            <a class="dropdown-item" href="<?php echo e(route('translations.index')); ?>">
              <i class="bx bx-globe me-2"></i>
              <span class="align-middle"><?php echo e(__('messages.manage_translations')); ?></span>
            </a>
          </li>
          <?php endif; ?>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <?php if(auth()->guard()->check()): ?>
              <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle"><?php echo e(__('messages.logout')); ?></span>
              </a>
              <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                <?php echo csrf_field(); ?>
              </form>
            <?php else: ?>
              <a class="dropdown-item" href="<?php echo e(route('login')); ?>">
                <i class="bx bx-log-in me-2"></i>
                <span class="align-middle"><?php echo e(__('messages.login')); ?></span>
              </a>
            <?php endif; ?>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav> <?php /**PATH /Applications/MAMP/htdocs/club/resources/views/layouts/navbar.blade.php ENDPATH**/ ?>