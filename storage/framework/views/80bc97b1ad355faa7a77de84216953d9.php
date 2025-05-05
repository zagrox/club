<?php $__env->startSection('title', isset($user) ? __('messages.Manage User Account - ') . $user->name : __('messages.Account Settings - Account')); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 mb-4">
  <?php if(isset($user)): ?>
    <span class="text-muted fw-light"><?php echo e(__('messages.User Management')); ?> /</span> <?php echo e($user->name); ?>

  <?php else: ?>
    <span class="text-muted fw-light"><?php echo e(__('messages.Account Settings')); ?> /</span> <?php echo e(__('messages.Account')); ?>

  <?php endif; ?>
</h4>

<?php if(isset($user)): ?>
<div class="alert alert-primary alert-dismissible mb-4" role="alert">
  <h4 class="alert-heading d-flex align-items-center"><i class="bx bx-user-circle me-2"></i><?php echo e(__('messages.Admin Mode')); ?></h4>
  <p class="mb-0"><?php echo e(__("messages.You are currently managing :name's account as an administrator.", ['name' => $user->name])); ?></p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
      <li class="nav-item">
        <a class="nav-link active" href="<?php echo e(isset($user) ? route('settings.account', ['manage_user_id' => $user->id]) : route('settings.account')); ?>">
          <i class="bx bx-user me-1"></i> <?php echo e(__('messages.Account')); ?>

        </a>
      </li>
      <?php if(!isset($user)): ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('settings.security')); ?>">
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
      <?php endif; ?>
    </ul>
    <div class="card mb-4">
      <h5 class="card-header"><?php echo e(__('messages.Profile Details')); ?></h5>
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-4">
          <img
            src="<?php echo e(asset('assets/img/avatars/1.png')); ?>"
            alt="<?php echo e(__('messages.user-avatar')); ?>"
            class="d-block w-px-100 h-px-100 rounded"
            id="uploadedAvatar" />
          <div class="button-wrapper">
            <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
              <span class="d-none d-sm-block"><?php echo e(__('messages.Upload new photo')); ?></span>
              <i class="bx bx-upload d-block d-sm-none"></i>
              <input
                type="file"
                id="upload"
                class="account-file-input"
                hidden
                accept="image/png, image/jpeg" />
            </label>
            <button type="button" class="btn btn-outline-secondary mb-3">
              <i class="bx bx-reset d-block d-sm-none"></i>
              <span class="d-none d-sm-block"><?php echo e(__('messages.Reset')); ?></span>
            </button>

            <div class="text-muted small"><?php echo e(__('messages.Allowed JPG, GIF or PNG. Max size of 800K')); ?></div>
          </div>
        </div>
      </div>
      <div class="card-body pt-2 pb-2">
        <form action="<?php echo e(isset($user) ? route('users.update', $user->id) : '#'); ?>" method="<?php echo e(isset($user) ? 'POST' : 'GET'); ?>">
          <?php if(isset($user)): ?>
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
          <?php endif; ?>
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="firstName" class="form-label"><?php echo e(__('messages.First Name')); ?></label>
              <input
                class="form-control"
                type="text"
                id="firstName"
                name="name"
                value="<?php echo e(isset($user) ? $user->name : (Auth::user() ? Auth::user()->name : 'John')); ?>"
                autofocus />
            </div>
            <div class="mb-3 col-md-6">
              <label for="lastName" class="form-label"><?php echo e(__('messages.Last Name')); ?></label>
              <input class="form-control" type="text" name="lastName" id="lastName" value="Doe" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="email" class="form-label"><?php echo e(__('messages.E-mail')); ?></label>
              <input
                class="form-control"
                type="text"
                id="email"
                name="email"
                value="<?php echo e(isset($user) ? $user->email : (Auth::user() ? Auth::user()->email : 'john.doe@example.com')); ?>"
                placeholder="john.doe@example.com" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="organization" class="form-label"><?php echo e(__('messages.Organization')); ?></label>
              <input
                type="text"
                class="form-control"
                id="organization"
                name="organization"
                value="ThemeSelection" />
            </div>
            
            <?php if(isset($user)): ?>
            <div class="mb-3 col-md-6">
              <label for="role" class="form-label"><?php echo e(__('messages.Role')); ?></label>
              <select id="role" class="form-select" name="role">
                <option value="Admin" <?php echo e($user->role == 'Admin' ? 'selected' : ''); ?>><?php echo e(__('messages.Admin')); ?></option>
                <option value="Editor" <?php echo e($user->role == 'Editor' ? 'selected' : ''); ?>><?php echo e(__('messages.Editor')); ?></option>
                <option value="Author" <?php echo e($user->role == 'Author' ? 'selected' : ''); ?>><?php echo e(__('messages.Author')); ?></option>
                <option value="Subscriber" <?php echo e($user->role == 'Subscriber' ? 'selected' : ''); ?>><?php echo e(__('messages.Subscriber')); ?></option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="status" class="form-label"><?php echo e(__('messages.Status')); ?></label>
              <select id="status" class="form-select" name="status">
                <option value="Active" <?php echo e($user->status == 'Active' ? 'selected' : ''); ?>><?php echo e(__('messages.Active')); ?></option>
                <option value="Inactive" <?php echo e($user->status == 'Inactive' ? 'selected' : ''); ?>><?php echo e(__('messages.Inactive')); ?></option>
                <option value="Pending" <?php echo e($user->status == 'Pending' ? 'selected' : ''); ?>><?php echo e(__('messages.Pending')); ?></option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="password" class="form-label"><?php echo e(__('messages.New Password')); ?></label>
              <input
                type="password"
                id="password"
                class="form-control"
                name="password"
                placeholder="<?php echo e(__('messages.Leave blank to keep current password')); ?>" />
            </div>
            <?php endif; ?>
            
            <div class="mb-3 col-md-6">
              <label class="form-label" for="phoneNumber"><?php echo e(__('messages.Phone Number')); ?></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><?php echo e(__('messages.US (+1)')); ?></span>
                <input
                  type="text"
                  id="phoneNumber"
                  name="phoneNumber"
                  class="form-control"
                  placeholder="202 555 0111" />
              </div>
            </div>
            <div class="mb-3 col-md-6">
              <label for="address" class="form-label"><?php echo e(__('messages.Address')); ?></label>
              <input type="text" class="form-control" id="address" name="address" placeholder="<?php echo e(__('messages.Address')); ?>" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="state" class="form-label"><?php echo e(__('messages.State')); ?></label>
              <input class="form-control" type="text" id="state" name="state" placeholder="<?php echo e(__('messages.California')); ?>" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="zipCode" class="form-label"><?php echo e(__('messages.Zip Code')); ?></label>
              <input
                type="text"
                class="form-control"
                id="zipCode"
                name="zipCode"
                placeholder="231465"
                maxlength="6" />
            </div>
            <div class="mb-3 col-md-6">
              <label class="form-label" for="country"><?php echo e(__('messages.Country')); ?></label>
              <select id="country" class="select2 form-select">
                <option value=""><?php echo e(__('messages.Select')); ?></option>
                <option value="Australia"><?php echo e(__('messages.Australia')); ?></option>
                <option value="Bangladesh"><?php echo e(__('messages.Bangladesh')); ?></option>
                <option value="Belarus"><?php echo e(__('messages.Belarus')); ?></option>
                <option value="Brazil"><?php echo e(__('messages.Brazil')); ?></option>
                <option value="Canada"><?php echo e(__('messages.Canada')); ?></option>
                <option value="China"><?php echo e(__('messages.China')); ?></option>
                <option value="France"><?php echo e(__('messages.France')); ?></option>
                <option value="Germany"><?php echo e(__('messages.Germany')); ?></option>
                <option value="India"><?php echo e(__('messages.India')); ?></option>
                <option value="Indonesia"><?php echo e(__('messages.Indonesia')); ?></option>
                <option value="Israel"><?php echo e(__('messages.Israel')); ?></option>
                <option value="Italy"><?php echo e(__('messages.Italy')); ?></option>
                <option value="Japan"><?php echo e(__('messages.Japan')); ?></option>
                <option value="Korea"><?php echo e(__('messages.Korea, Republic of')); ?></option>
                <option value="Mexico"><?php echo e(__('messages.Mexico')); ?></option>
                <option value="Philippines"><?php echo e(__('messages.Philippines')); ?></option>
                <option value="Russia"><?php echo e(__('messages.Russian Federation')); ?></option>
                <option value="South Africa"><?php echo e(__('messages.South Africa')); ?></option>
                <option value="Thailand"><?php echo e(__('messages.Thailand')); ?></option>
                <option value="Turkey"><?php echo e(__('messages.Turkey')); ?></option>
                <option value="Ukraine"><?php echo e(__('messages.Ukraine')); ?></option>
                <option value="United Arab Emirates"><?php echo e(__('messages.United Arab Emirates')); ?></option>
                <option value="United Kingdom"><?php echo e(__('messages.United Kingdom')); ?></option>
                <option value="United States" selected><?php echo e(__('messages.United States')); ?></option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="language" class="form-label"><?php echo e(__('messages.Language')); ?></label>
              <select id="language" class="select2 form-select">
                <option value=""><?php echo e(__('messages.Select Language')); ?></option>
                <option value="en" selected><?php echo e(__('messages.English')); ?></option>
                <option value="fr"><?php echo e(__('messages.French')); ?></option>
                <option value="de"><?php echo e(__('messages.German')); ?></option>
                <option value="pt"><?php echo e(__('messages.Portuguese')); ?></option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="timeZones" class="form-label"><?php echo e(__('messages.Timezone')); ?></label>
              <select id="timeZones" class="select2 form-select">
                <option value=""><?php echo e(__('messages.Select Timezone')); ?></option>
                <option value="-12"><?php echo e(__('messages.(GMT-12:00) International Date Line West')); ?></option>
                <option value="-11"><?php echo e(__('messages.(GMT-11:00) Midway Island, Samoa')); ?></option>
                <option value="-10"><?php echo e(__('messages.(GMT-10:00) Hawaii')); ?></option>
                <option value="-9"><?php echo e(__('messages.(GMT-09:00) Alaska')); ?></option>
                <option value="-8"><?php echo e(__('messages.(GMT-08:00) Pacific Time (US & Canada)')); ?></option>
                <option value="-8"><?php echo e(__('messages.(GMT-08:00) Tijuana, Baja California')); ?></option>
                <option value="-7"><?php echo e(__('messages.(GMT-07:00) Arizona')); ?></option>
                <option value="-7"><?php echo e(__('messages.(GMT-07:00) Chihuahua, La Paz, Mazatlan')); ?></option>
                <option value="-7"><?php echo e(__('messages.(GMT-07:00) Mountain Time (US & Canada)')); ?></option>
                <option value="-6"><?php echo e(__('messages.(GMT-06:00) Central America')); ?></option>
                <option value="-6"><?php echo e(__('messages.(GMT-06:00) Central Time (US & Canada)')); ?></option>
                <option value="-6"><?php echo e(__('messages.(GMT-06:00) Guadalajara, Mexico City, Monterrey')); ?></option>
                <option value="-6"><?php echo e(__('messages.(GMT-06:00) Saskatchewan')); ?></option>
                <option value="-5"><?php echo e(__('messages.(GMT-05:00) Bogota, Lima, Quito, Rio Branco')); ?></option>
                <option value="-5"><?php echo e(__('messages.(GMT-05:00) Eastern Time (US & Canada)')); ?></option>
                <option value="-5"><?php echo e(__('messages.(GMT-05:00) Indiana (East)')); ?></option>
                <option value="-4"><?php echo e(__('messages.(GMT-04:00) Atlantic Time (Canada)')); ?></option>
                <option value="-4"><?php echo e(__('messages.(GMT-04:00) Caracas, La Paz')); ?></option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="currency" class="form-label"><?php echo e(__('messages.Currency')); ?></label>
              <select id="currency" class="select2 form-select">
                <option value=""><?php echo e(__('messages.Select Currency')); ?></option>
                <option value="usd" selected><?php echo e(__('messages.USD')); ?></option>
                <option value="euro"><?php echo e(__('messages.Euro')); ?></option>
                <option value="pound"><?php echo e(__('messages.Pound')); ?></option>
                <option value="bitcoin"><?php echo e(__('messages.Bitcoin')); ?></option>
              </select>
            </div>
          </div>
          <div class="mt-2">
            <button type="submit" class="btn btn-primary me-2"><?php echo e(__('messages.Save changes')); ?></button>
            <button type="reset" class="btn btn-outline-secondary"><?php echo e(__('messages.Cancel')); ?></button>
          </div>
        </form>
      </div>
    </div>
    <div class="card">
      <h5 class="card-header"><?php echo e(__('messages.Delete Account')); ?></h5>
      <div class="card-body">
        <div class="mb-3 col-12 mb-0">
          <div class="alert alert-warning">
            <h6 class="alert-heading mb-1"><?php echo e(__('messages.Are you sure you want to delete your account?')); ?></h6>
            <p class="mb-0"><?php echo e(__('messages.Once you delete your account, there is no going back. Please be certain.')); ?></p>
          </div>
        </div>
        <form id="formAccountDeactivation" onsubmit="return false">
          <div class="form-check mb-3">
            <input
              class="form-check-input"
              type="checkbox"
              name="accountActivation"
              id="accountActivation" />
            <label class="form-check-label" for="accountActivation"><?php echo e(__('messages.I confirm my account deactivation')); ?></label>
          </div>
          <button type="submit" class="btn btn-danger deactivate-account"><?php echo e(__('messages.Deactivate Account')); ?></button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/pages/settings/account.blade.php ENDPATH**/ ?>