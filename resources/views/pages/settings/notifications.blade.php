@extends('layouts.app')

@section('title', 'Account Settings - Notifications')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Notifications
</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.account') }}">
          <i class="bx bx-user me-1"></i> Account
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.security') }}">
          <i class="bx bx-lock-alt me-1"></i> Security
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="{{ route('settings.notifications') }}">
          <i class="bx bx-bell me-1"></i> Notifications
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.connections') }}">
          <i class="bx bx-link-alt me-1"></i> Connections
        </a>
      </li>
    </ul>
    <div class="card">
      <h5 class="card-header">Notifications</h5>
      <div class="card-body">
        <span>We need permission from your browser to show notifications. <span class="notificationRequest"><strong>Request Permission</strong></span></span>
        <div class="error"></div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-borderless border-bottom">
          <thead>
            <tr>
              <th class="text-nowrap">Type</th>
              <th class="text-nowrap text-center">Email</th>
              <th class="text-nowrap text-center">Browser</th>
              <th class="text-nowrap text-center">App</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-nowrap">New for you</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck1" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck2" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck3" checked />
                </div>
              </td>
            </tr>
            <tr>
              <td class="text-nowrap">Account activity</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck4" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck5" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck6" checked />
                </div>
              </td>
            </tr>
            <tr>
              <td class="text-nowrap">A new browser used to sign in</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck7" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck8" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck9" />
                </div>
              </td>
            </tr>
            <tr>
              <td class="text-nowrap">A new device is linked</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck10" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck11" />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck12" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary mt-1 me-3">Save changes</button>
            <button type="reset" class="btn btn-outline-secondary mt-1">Discard</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 