@extends('layouts.app')

@section('title', isset($user) ? __('messages.Manage User Account - ') . $user->name : __('messages.Account Settings - Account'))

@section('content')
<h4 class="py-3 mb-4">
  @if(isset($user))
    <span class="text-muted fw-light">{{ __('messages.User Management') }} /</span> {{ $user->name }}
  @else
    <span class="text-muted fw-light">{{ __('messages.Account Settings') }} /</span> {{ __('messages.Account') }}
  @endif
</h4>

@if(isset($user))
<div class="alert alert-primary alert-dismissible mb-4" role="alert">
  <h4 class="alert-heading d-flex align-items-center"><i class="bx bx-user-circle me-2"></i>{{ __('messages.Admin Mode') }}</h4>
  <p class="mb-0">{{ __("messages.You are currently managing :name's account as an administrator.", ['name' => $user->name]) }}</p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
      <li class="nav-item">
        <a class="nav-link active" href="{{ isset($user) ? route('settings.account', ['manage_user_id' => $user->id]) : route('settings.account') }}">
          <i class="bx bx-user me-1"></i> {{ __('messages.Account') }}
        </a>
      </li>
      @if(!isset($user))
      <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.security') }}">
          <i class="bx bx-lock-alt me-1"></i> {{ __('messages.Security') }}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.notifications') }}">
          <i class="bx bx-bell me-1"></i> {{ __('messages.Notifications') }}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.connections') }}">
          <i class="bx bx-link-alt me-1"></i> {{ __('messages.Connections') }}
        </a>
      </li>
      @endif
    </ul>
    <div class="card mb-4">
      <h5 class="card-header">{{ __('messages.Profile Details') }}</h5>
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-4">
          <img
            src="{{ asset('assets/img/avatars/1.png') }}"
            alt="{{ __('messages.user-avatar') }}"
            class="d-block w-px-100 h-px-100 rounded"
            id="uploadedAvatar" />
          <div class="button-wrapper">
            <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
              <span class="d-none d-sm-block">{{ __('messages.Upload new photo') }}</span>
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
              <span class="d-none d-sm-block">{{ __('messages.Reset') }}</span>
            </button>

            <div class="text-muted small">{{ __('messages.Allowed JPG, GIF or PNG. Max size of 800K') }}</div>
          </div>
        </div>
      </div>
      <div class="card-body pt-2 pb-2">
        <form action="{{ isset($user) ? route('users.update', $user->id) : '#' }}" method="{{ isset($user) ? 'POST' : 'GET' }}">
          @if(isset($user))
            @csrf
            @method('PUT')
          @endif
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="firstName" class="form-label">{{ __('messages.First Name') }}</label>
              <input
                class="form-control"
                type="text"
                id="firstName"
                name="name"
                value="{{ isset($user) ? $user->name : (Auth::user() ? Auth::user()->name : 'John') }}"
                autofocus />
            </div>
            <div class="mb-3 col-md-6">
              <label for="lastName" class="form-label">{{ __('messages.Last Name') }}</label>
              <input class="form-control" type="text" name="lastName" id="lastName" value="Doe" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="email" class="form-label">{{ __('messages.E-mail') }}</label>
              <input
                class="form-control"
                type="text"
                id="email"
                name="email"
                value="{{ isset($user) ? $user->email : (Auth::user() ? Auth::user()->email : 'john.doe@example.com') }}"
                placeholder="john.doe@example.com" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="organization" class="form-label">{{ __('messages.Organization') }}</label>
              <input
                type="text"
                class="form-control"
                id="organization"
                name="organization"
                value="ThemeSelection" />
            </div>
            
            @if(isset($user))
            <div class="mb-3 col-md-6">
              <label for="role" class="form-label">{{ __('messages.Role') }}</label>
              <select id="role" class="form-select" name="role">
                <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>{{ __('messages.Admin') }}</option>
                <option value="Editor" {{ $user->role == 'Editor' ? 'selected' : '' }}>{{ __('messages.Editor') }}</option>
                <option value="Author" {{ $user->role == 'Author' ? 'selected' : '' }}>{{ __('messages.Author') }}</option>
                <option value="Subscriber" {{ $user->role == 'Subscriber' ? 'selected' : '' }}>{{ __('messages.Subscriber') }}</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="status" class="form-label">{{ __('messages.Status') }}</label>
              <select id="status" class="form-select" name="status">
                <option value="Active" {{ $user->status == 'Active' ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                <option value="Inactive" {{ $user->status == 'Inactive' ? 'selected' : '' }}>{{ __('messages.Inactive') }}</option>
                <option value="Pending" {{ $user->status == 'Pending' ? 'selected' : '' }}>{{ __('messages.Pending') }}</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="password" class="form-label">{{ __('messages.New Password') }}</label>
              <input
                type="password"
                id="password"
                class="form-control"
                name="password"
                placeholder="{{ __('messages.Leave blank to keep current password') }}" />
            </div>
            @endif
            
            <div class="mb-3 col-md-6">
              <label class="form-label" for="phoneNumber">{{ __('messages.Phone Number') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text">{{ __('messages.US (+1)') }}</span>
                <input
                  type="text"
                  id="phoneNumber"
                  name="phoneNumber"
                  class="form-control"
                  placeholder="202 555 0111" />
              </div>
            </div>
            <div class="mb-3 col-md-6">
              <label for="address" class="form-label">{{ __('messages.Address') }}</label>
              <input type="text" class="form-control" id="address" name="address" placeholder="{{ __('messages.Address') }}" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="state" class="form-label">{{ __('messages.State') }}</label>
              <input class="form-control" type="text" id="state" name="state" placeholder="{{ __('messages.California') }}" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="zipCode" class="form-label">{{ __('messages.Zip Code') }}</label>
              <input
                type="text"
                class="form-control"
                id="zipCode"
                name="zipCode"
                placeholder="231465"
                maxlength="6" />
            </div>
            <div class="mb-3 col-md-6">
              <label class="form-label" for="country">{{ __('messages.Country') }}</label>
              <select id="country" class="select2 form-select">
                <option value="">{{ __('messages.Select') }}</option>
                <option value="Australia">{{ __('messages.Australia') }}</option>
                <option value="Bangladesh">{{ __('messages.Bangladesh') }}</option>
                <option value="Belarus">{{ __('messages.Belarus') }}</option>
                <option value="Brazil">{{ __('messages.Brazil') }}</option>
                <option value="Canada">{{ __('messages.Canada') }}</option>
                <option value="China">{{ __('messages.China') }}</option>
                <option value="France">{{ __('messages.France') }}</option>
                <option value="Germany">{{ __('messages.Germany') }}</option>
                <option value="India">{{ __('messages.India') }}</option>
                <option value="Indonesia">{{ __('messages.Indonesia') }}</option>
                <option value="Israel">{{ __('messages.Israel') }}</option>
                <option value="Italy">{{ __('messages.Italy') }}</option>
                <option value="Japan">{{ __('messages.Japan') }}</option>
                <option value="Korea">{{ __('messages.Korea, Republic of') }}</option>
                <option value="Mexico">{{ __('messages.Mexico') }}</option>
                <option value="Philippines">{{ __('messages.Philippines') }}</option>
                <option value="Russia">{{ __('messages.Russian Federation') }}</option>
                <option value="South Africa">{{ __('messages.South Africa') }}</option>
                <option value="Thailand">{{ __('messages.Thailand') }}</option>
                <option value="Turkey">{{ __('messages.Turkey') }}</option>
                <option value="Ukraine">{{ __('messages.Ukraine') }}</option>
                <option value="United Arab Emirates">{{ __('messages.United Arab Emirates') }}</option>
                <option value="United Kingdom">{{ __('messages.United Kingdom') }}</option>
                <option value="United States" selected>{{ __('messages.United States') }}</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="language" class="form-label">{{ __('messages.Language') }}</label>
              <select id="language" class="select2 form-select">
                <option value="">{{ __('messages.Select Language') }}</option>
                <option value="en" selected>{{ __('messages.English') }}</option>
                <option value="fr">{{ __('messages.French') }}</option>
                <option value="de">{{ __('messages.German') }}</option>
                <option value="pt">{{ __('messages.Portuguese') }}</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="timeZones" class="form-label">{{ __('messages.Timezone') }}</label>
              <select id="timeZones" class="select2 form-select">
                <option value="">{{ __('messages.Select Timezone') }}</option>
                <option value="-12">{{ __('messages.(GMT-12:00) International Date Line West') }}</option>
                <option value="-11">{{ __('messages.(GMT-11:00) Midway Island, Samoa') }}</option>
                <option value="-10">{{ __('messages.(GMT-10:00) Hawaii') }}</option>
                <option value="-9">{{ __('messages.(GMT-09:00) Alaska') }}</option>
                <option value="-8">{{ __('messages.(GMT-08:00) Pacific Time (US & Canada)') }}</option>
                <option value="-8">{{ __('messages.(GMT-08:00) Tijuana, Baja California') }}</option>
                <option value="-7">{{ __('messages.(GMT-07:00) Arizona') }}</option>
                <option value="-7">{{ __('messages.(GMT-07:00) Chihuahua, La Paz, Mazatlan') }}</option>
                <option value="-7">{{ __('messages.(GMT-07:00) Mountain Time (US & Canada)') }}</option>
                <option value="-6">{{ __('messages.(GMT-06:00) Central America') }}</option>
                <option value="-6">{{ __('messages.(GMT-06:00) Central Time (US & Canada)') }}</option>
                <option value="-6">{{ __('messages.(GMT-06:00) Guadalajara, Mexico City, Monterrey') }}</option>
                <option value="-6">{{ __('messages.(GMT-06:00) Saskatchewan') }}</option>
                <option value="-5">{{ __('messages.(GMT-05:00) Bogota, Lima, Quito, Rio Branco') }}</option>
                <option value="-5">{{ __('messages.(GMT-05:00) Eastern Time (US & Canada)') }}</option>
                <option value="-5">{{ __('messages.(GMT-05:00) Indiana (East)') }}</option>
                <option value="-4">{{ __('messages.(GMT-04:00) Atlantic Time (Canada)') }}</option>
                <option value="-4">{{ __('messages.(GMT-04:00) Caracas, La Paz') }}</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="currency" class="form-label">{{ __('messages.Currency') }}</label>
              <select id="currency" class="select2 form-select">
                <option value="">{{ __('messages.Select Currency') }}</option>
                <option value="usd" selected>{{ __('messages.USD') }}</option>
                <option value="euro">{{ __('messages.Euro') }}</option>
                <option value="pound">{{ __('messages.Pound') }}</option>
                <option value="bitcoin">{{ __('messages.Bitcoin') }}</option>
              </select>
            </div>
          </div>
          <div class="mt-2">
            <button type="submit" class="btn btn-primary me-2">{{ __('messages.Save changes') }}</button>
            <button type="reset" class="btn btn-outline-secondary">{{ __('messages.Cancel') }}</button>
          </div>
        </form>
      </div>
    </div>
    <div class="card">
      <h5 class="card-header">{{ __('messages.Delete Account') }}</h5>
      <div class="card-body">
        <div class="mb-3 col-12 mb-0">
          <div class="alert alert-warning">
            <h6 class="alert-heading mb-1">{{ __('messages.Are you sure you want to delete your account?') }}</h6>
            <p class="mb-0">{{ __('messages.Once you delete your account, there is no going back. Please be certain.') }}</p>
          </div>
        </div>
        <form id="formAccountDeactivation" onsubmit="return false">
          <div class="form-check mb-3">
            <input
              class="form-check-input"
              type="checkbox"
              name="accountActivation"
              id="accountActivation" />
            <label class="form-check-label" for="accountActivation">{{ __('messages.I confirm my account deactivation') }}</label>
          </div>
          <button type="submit" class="btn btn-danger deactivate-account">{{ __('messages.Deactivate Account') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection 