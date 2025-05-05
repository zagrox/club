@extends('layouts.app')

@section('title', 'Notification Settings')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Initialize select2
      $('.select2').select2();
      
      // Handle form submission
      document.querySelector('#notification-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show success toast
        const toastPlacement = document.querySelector('#toastPlacement');
        toastPlacement.innerHTML = `
          <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <i class="bx bx-check me-2"></i>
              <div class="me-auto fw-semibold">Success</div>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Notification settings saved successfully.</div>
          </div>
        `;
      });
    });
  </script>
@endsection

@section('content')
  <!-- Toast container -->
  <div class="bs-toast toast-placement-ex m-2 position-fixed top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div id="toastPlacement"></div>
  </div>

  <div class="row">
    <!-- Header Section -->
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="fw-bold py-3 mb-0">
              <i class="bx bx-cog me-2"></i>
              Notification Settings
            </h4>
          </div>
          <div>
            <a href="{{ route('notification-center.index') }}" class="btn btn-primary">
              <i class="bx bx-bell me-1"></i>
              Back to Notifications
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Settings Form -->
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form id="notification-settings-form">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-3" role="tablist">
              <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-channels" aria-controls="tab-channels" aria-selected="true">
                  <i class="bx bx-broadcast me-1"></i>
                  Notification Channels
                </button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-preferences" aria-controls="tab-preferences" aria-selected="false">
                  <i class="bx bx-customize me-1"></i>
                  Notification Preferences
                </button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-roles" aria-controls="tab-roles" aria-selected="false">
                  <i class="bx bx-user-pin me-1"></i>
                  Role-Based Settings
                </button>
              </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content">
              <!-- Notification Channels Tab -->
              <div class="tab-pane fade show active" id="tab-channels" role="tabpanel">
                <h5 class="mb-3">Notification Delivery Channels</h5>
                <p class="text-muted mb-4">Configure how notifications are delivered to users</p>
                
                <!-- Email Notifications -->
                <div class="row mb-4">
                  <div class="col-12 col-md-6">
                    <div class="card shadow-none bg-light mb-4">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">Email Notifications</h6>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="email-enabled" checked>
                          <label class="form-check-label" for="email-enabled">Enabled</label>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label for="email-sender" class="form-label">Sender Email</label>
                          <input type="email" class="form-control" id="email-sender" value="notifications@example.com">
                        </div>
                        <div class="mb-3">
                          <label for="email-template" class="form-label">Email Template</label>
                          <select class="form-select" id="email-template">
                            <option value="default">Default Template</option>
                            <option value="minimal">Minimal Template</option>
                            <option value="branded">Branded Template</option>
                          </select>
                        </div>
                        <div class="form-check form-switch mb-2">
                          <input class="form-check-input" type="checkbox" id="email-html" checked>
                          <label class="form-check-label" for="email-html">Send HTML Emails</label>
                        </div>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="email-attachments" checked>
                          <label class="form-check-label" for="email-attachments">Include Attachments</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- SMS Notifications -->
                  <div class="col-12 col-md-6">
                    <div class="card shadow-none bg-light mb-4">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">SMS Notifications</h6>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="sms-enabled">
                          <label class="form-check-label" for="sms-enabled">Enabled</label>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label for="sms-provider" class="form-label">SMS Provider</label>
                          <select class="form-select" id="sms-provider">
                            <option value="twilio">Twilio</option>
                            <option value="nexmo">Nexmo</option>
                            <option value="aws">AWS SNS</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="sms-sender" class="form-label">Sender ID</label>
                          <input type="text" class="form-control" id="sms-sender" value="CompanyName">
                        </div>
                        <div class="mb-3">
                          <label for="sms-length" class="form-label">Max SMS Length</label>
                          <select class="form-select" id="sms-length">
                            <option value="120">120 characters</option>
                            <option value="160" selected>160 characters</option>
                            <option value="640">Multiple SMS (up to 640 chars)</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <!-- Push Notifications -->
                  <div class="col-12 col-md-6">
                    <div class="card shadow-none bg-light mb-4">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">Push Notifications</h6>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="push-enabled" checked>
                          <label class="form-check-label" for="push-enabled">Enabled</label>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label for="push-provider" class="form-label">Push Provider</label>
                          <select class="form-select" id="push-provider">
                            <option value="firebase">Firebase Cloud Messaging</option>
                            <option value="onesignal">OneSignal</option>
                            <option value="pusher">Pusher</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="push-icon" class="form-label">Notification Icon</label>
                          <input type="file" class="form-control" id="push-icon">
                        </div>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="push-sound" checked>
                          <label class="form-check-label" for="push-sound">Enable Notification Sounds</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- In-App Notifications -->
                  <div class="col-12 col-md-6">
                    <div class="card shadow-none bg-light">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">In-App Notifications</h6>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="inapp-enabled" checked>
                          <label class="form-check-label" for="inapp-enabled">Enabled</label>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label for="inapp-max" class="form-label">Maximum Notifications</label>
                          <input type="number" class="form-control" id="inapp-max" value="50">
                        </div>
                        <div class="mb-3">
                          <label for="inapp-expiry" class="form-label">Auto Expire After</label>
                          <select class="form-select" id="inapp-expiry">
                            <option value="0">Never</option>
                            <option value="7" selected>7 days</option>
                            <option value="30">30 days</option>
                            <option value="90">90 days</option>
                          </select>
                        </div>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="inapp-realtime" checked>
                          <label class="form-check-label" for="inapp-realtime">Real-time Updates</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Notification Preferences Tab -->
              <div class="tab-pane fade" id="tab-preferences" role="tabpanel">
                <h5 class="mb-3">Notification Preferences</h5>
                <p class="text-muted mb-4">Customize how and when notifications are delivered</p>
                
                <!-- Notification Types -->
                <div class="card shadow-none bg-light mb-4">
                  <div class="card-header">
                    <h6 class="mb-0">Notification Types</h6>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Notification Type</th>
                            <th>Email</th>
                            <th>SMS</th>
                            <th>Push</th>
                            <th>In-App</th>
                            <th>Priority</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- System Notifications -->
                          <tr>
                            <td>System Alerts</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <select class="form-select form-select-sm">
                                <option value="high" selected>High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                              </select>
                            </td>
                          </tr>
                          
                          <!-- User Notifications -->
                          <tr>
                            <td>User Account Changes</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <select class="form-select form-select-sm">
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                              </select>
                            </td>
                          </tr>
                          
                          <!-- Payment Notifications -->
                          <tr>
                            <td>Payment Activity</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <select class="form-select form-select-sm">
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                              </select>
                            </td>
                          </tr>
                          
                          <!-- Security Notifications -->
                          <tr>
                            <td>Security Alerts</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <select class="form-select form-select-sm">
                                <option value="high" selected>High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                              </select>
                            </td>
                          </tr>
                          
                          <!-- Content Updates -->
                          <tr>
                            <td>Content Updates</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <select class="form-select form-select-sm">
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low" selected>Low</option>
                              </select>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                
                <!-- Delivery Settings -->
                <div class="card shadow-none bg-light">
                  <div class="card-header">
                    <h6 class="mb-0">Delivery Settings</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 col-12 mb-3">
                        <label for="notification-frequency" class="form-label">Notification Frequency</label>
                        <select class="form-select" id="notification-frequency">
                          <option value="immediate">Immediate</option>
                          <option value="hourly">Hourly Digest</option>
                          <option value="daily">Daily Digest</option>
                          <option value="weekly">Weekly Summary</option>
                        </select>
                      </div>
                      <div class="col-md-6 col-12 mb-3">
                        <label for="digest-time" class="form-label">Digest Delivery Time</label>
                        <input type="time" class="form-control" id="digest-time" value="08:00">
                      </div>
                      <div class="col-md-6 col-12 mb-3">
                        <label for="digest-day" class="form-label">Weekly Digest Day</label>
                        <select class="form-select" id="digest-day">
                          <option value="1">Monday</option>
                          <option value="2">Tuesday</option>
                          <option value="3">Wednesday</option>
                          <option value="4">Thursday</option>
                          <option value="5">Friday</option>
                          <option value="6">Saturday</option>
                          <option value="7">Sunday</option>
                        </select>
                      </div>
                      <div class="col-md-6 col-12 mb-3">
                        <label for="quiet-hours" class="form-label">Quiet Hours</label>
                        <select class="select2 form-select" id="quiet-hours" multiple>
                          <option value="0">12 AM - 1 AM</option>
                          <option value="1">1 AM - 2 AM</option>
                          <option value="2">2 AM - 3 AM</option>
                          <option value="3">3 AM - 4 AM</option>
                          <option value="4">4 AM - 5 AM</option>
                          <option value="5">5 AM - 6 AM</option>
                          <option value="6">6 AM - 7 AM</option>
                          <option value="22">10 PM - 11 PM</option>
                          <option value="23">11 PM - 12 AM</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" id="override-high-priority" checked>
                      <label class="form-check-label" for="override-high-priority">Always send high priority notifications immediately</label>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Role-Based Settings Tab -->
              <div class="tab-pane fade" id="tab-roles" role="tabpanel">
                <h5 class="mb-3">Role-Based Notification Settings</h5>
                <p class="text-muted mb-4">Configure notification permissions for different user roles</p>
                
                <!-- Role Access -->
                <div class="card shadow-none bg-light mb-4">
                  <div class="card-header">
                    <h6 class="mb-0">Role Access Control</h6>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Role</th>
                            <th>System Alerts</th>
                            <th>User Notifications</th>
                            <th>Payment Notifications</th>
                            <th>Security Alerts</th>
                            <th>Content Updates</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Admin Role -->
                          <tr>
                            <td>Admin</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                          </tr>
                          
                          <!-- Moderator Role -->
                          <tr>
                            <td>Moderator</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                          </tr>
                          
                          <!-- User Role -->
                          <tr>
                            <td>User</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                          </tr>
                          
                          <!-- Subscriber Role -->
                          <tr>
                            <td>Subscriber</td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                            <td>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                
                <!-- Add Custom Role -->
                <div class="card shadow-none bg-light">
                  <div class="card-header">
                    <h6 class="mb-0">Custom Role Configuration</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 col-12">
                        <div class="mb-3">
                          <label for="role-name" class="form-label">Role Name</label>
                          <input type="text" class="form-control" id="role-name" placeholder="Enter role name">
                        </div>
                        <div class="mb-3">
                          <label for="role-description" class="form-label">Description</label>
                          <textarea class="form-control" id="role-description" rows="3" placeholder="Enter role description"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary">Add Role</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
              <button type="button" class="btn btn-secondary me-2">Reset to Defaults</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection 