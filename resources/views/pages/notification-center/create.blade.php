@extends('layouts.app')

@section('title', 'Create New Notification')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/quill.snow.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Initialize Rich Text Editor
      const quill = new Quill('#message-editor', {
        modules: {
          toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
          ]
        },
        placeholder: 'Write your notification message here...',
        theme: 'snow'
      });
      
      // Initialize select2
      $('.select2').select2();
      
      // Initialize datepickers
      $('.flatpickr-date-time').flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today"
      });
      
      // Toggle scheduling options visibility
      document.querySelectorAll('input[name="send_option"]').forEach(radio => {
        radio.addEventListener('change', function() {
          const schedulingSection = document.getElementById('scheduling-options');
          if (this.value === 'schedule') {
            schedulingSection.classList.remove('d-none');
            document.getElementById('send_now').value = 0;
          } else {
            schedulingSection.classList.add('d-none');
            document.getElementById('send_now').value = 1;
          }
        });
      });
      
      // Toggle recipients selection based on audience
      document.getElementById('audience').addEventListener('change', function() {
        const recipientsSection = document.getElementById('recipients-section');
        if (this.value === 'all') {
          recipientsSection.classList.add('d-none');
        } else {
          recipientsSection.classList.remove('d-none');
          
          // Switch between role and user selection based on audience
          document.getElementById('roles-selection').classList.toggle('d-none', this.value !== 'roles');
          document.getElementById('users-selection').classList.toggle('d-none', this.value !== 'users');
        }
      });
      
      // Preview notification (offcanvas)
      document.getElementById('preview-btn').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get form data
        const title = document.getElementById('title').value;
        const message = quill.root.innerHTML;
        const priority = document.getElementById('priority').value;
        const category = document.getElementById('category').value;
        
        // Validate required fields
        if (!title || !message) {
          alert('Title and message are required for preview');
          return;
        }
        
        // Send AJAX request to preview endpoint
        fetch('{{ route("notification-center.preview") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            title: title,
            message: message,
            priority: priority,
            category: category
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('notification-preview-content').innerHTML = data.preview;
            
            // Show the offcanvas
            const offcanvasElement = document.getElementById('notificationPreviewOffcanvas');
            const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
            offcanvas.show();
          } else {
            let errorMessage = 'Please correct the following errors:';
            for (const key in data.errors) {
              errorMessage += `\n- ${data.errors[key]}`;
            }
            alert(errorMessage);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while generating the preview. Please try again.');
        });
      });
      
      // Handle form submissions
      const notificationForm = document.getElementById('notification-form');
      
      // Save Draft
      document.getElementById('save-draft-btn').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get the message from the editor and set it in the hidden field
        document.getElementById('message').value = quill.root.innerHTML;
        
        // Change form action to the draft route
        notificationForm.action = '{{ route("notification-center.store-draft") }}';
        notificationForm.submit();
      });
      
      // Send Notification
      notificationForm.addEventListener('submit', function(e) {
        // Get the message from the editor and set it in the hidden field
        document.getElementById('message').value = quill.root.innerHTML;
        
        // Form will submit to the default action (store)
      });
      
      // File upload preview
      document.getElementById('attachments').addEventListener('change', function(e) {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        
        if (this.files.length > 0) {
          document.getElementById('attachments-container').classList.remove('d-none');
          
          Array.from(this.files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'p-2 border rounded mb-2 d-flex align-items-center';
            
            // Determine file icon based on type
            let icon = 'bx-file';
            if (file.type.includes('image')) icon = 'bx-image';
            else if (file.type.includes('pdf')) icon = 'bx-file-pdf';
            else if (file.type.includes('excel') || file.type.includes('spreadsheet')) icon = 'bx-file-excel';
            else if (file.type.includes('word') || file.type.includes('document')) icon = 'bx-file-doc';
            
            // Format file size
            const fileSize = file.size < 1024 ? `${file.size} B` :
                           file.size < 1048576 ? `${(file.size / 1024).toFixed(2)} KB` :
                           `${(file.size / 1048576).toFixed(2)} MB`;
            
            fileItem.innerHTML = `
              <i class="bx ${icon} fs-3 me-2"></i>
              <div class="flex-grow-1">
                <div class="fw-semibold">${file.name}</div>
                <div class="small text-muted">${fileSize}</div>
              </div>
            `;
            
            fileList.appendChild(fileItem);
          });
        } else {
          document.getElementById('attachments-container').classList.add('d-none');
        }
      });
      
      // Initialize form with default values
      document.getElementById('send-now').checked = true;
      document.getElementById('send_now').value = 1;
      document.getElementById('scheduling-options').classList.add('d-none');
      document.getElementById('recipients-section').classList.add('d-none');
    });
  </script>
@endsection

@section('content')
  <!-- Preview Offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationPreviewOffcanvas" aria-labelledby="notificationPreviewOffcanvasLabel">
    <div class="offcanvas-header">
      <h5 id="notificationPreviewOffcanvasLabel" class="offcanvas-title">Notification Preview</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="notification-preview-content">
      <!-- Preview content will be inserted here by JavaScript -->
    </div>
  </div>

  <div class="row">
    <!-- Header Section -->
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <h4 class="fw-bold py-3 mb-0">
                <i class="bx bx-bell-plus me-2"></i>
                Create New Notification
              </h4>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                  </li>
                  <li class="breadcrumb-item">
                    <a href="{{ route('notification-center.index') }}">Notifications</a>
                  </li>
                  <li class="breadcrumb-item active">New Notification</li>
                </ol>
              </nav>
            </div>
            <div>
              <a href="{{ route('notification-center.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i>
                Back to Notification Center
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Notification Form -->
    <div class="col-12">
      <form id="notification-form" action="{{ route('notification-center.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="send_now" id="send_now" value="1">
        <input type="hidden" name="message" id="message">
        
        <div class="row">
          <!-- Notification Content Section -->
          <div class="col-md-8 col-12 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">Notification Content</h5>
              </div>
              <div class="card-body">
                <!-- Title -->
                <div class="mb-3">
                  <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Enter notification title" required>
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <!-- Message (Rich Text Editor) -->
                <div class="mb-3">
                  <label for="message-editor" class="form-label">Message <span class="text-danger">*</span></label>
                  <div id="message-editor" style="height: 250px;">{{ old('message') }}</div>
                  @error('message')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                
                <!-- Attachments -->
                <div class="mb-3">
                  <label for="attachments" class="form-label">Attachments</label>
                  <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                  <div class="text-muted small mt-1">Max file size: 10MB. You can upload multiple files.</div>
                  @error('attachments')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <!-- Attachments Preview -->
                <div id="attachments-container" class="d-none">
                  <h6 class="mt-3 mb-2">Attached Files</h6>
                  <div id="file-list" class="mb-3"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Configuration Section -->
          <div class="col-md-4 col-12">
            <div class="row">
              <!-- Target Audience -->
              <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title mb-0">Target Audience</h5>
                  </div>
                  <div class="card-body">
                    <!-- Send To -->
                    <div class="mb-3">
                      <label for="audience" class="form-label">Send To <span class="text-danger">*</span></label>
                      <select class="form-select @error('audience') is-invalid @enderror" id="audience" name="audience" required>
                        <option value="all" {{ old('audience') == 'all' ? 'selected' : '' }}>All Users</option>
                        <option value="roles" {{ old('audience') == 'roles' ? 'selected' : '' }}>Specific Roles</option>
                        <option value="users" {{ old('audience') == 'users' ? 'selected' : '' }}>Individual Users</option>
                      </select>
                      @error('audience')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Recipients Selection -->
                    <div id="recipients-section" class="mb-3">
                      <!-- Role Selection -->
                      <div id="roles-selection" class="d-none">
                        <label for="role-recipients" class="form-label">Select Roles <span class="text-danger">*</span></label>
                        <select class="select2 form-select @error('recipients') is-invalid @enderror" id="role-recipients" name="recipients[]" multiple>
                          @foreach($roles as $role)
                            <option value="{{ $role }}" {{ (is_array(old('recipients')) && in_array($role, old('recipients'))) ? 'selected' : '' }}>
                              {{ ucfirst($role) }}
                            </option>
                          @endforeach
                        </select>
                        @error('recipients')
                          <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <!-- User Selection -->
                      <div id="users-selection" class="d-none">
                        <label for="user-recipients" class="form-label">Select Users <span class="text-danger">*</span></label>
                        <select class="select2 form-select @error('recipients') is-invalid @enderror" id="user-recipients" name="recipients[]" multiple>
                          @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (is_array(old('recipients')) && in_array($user->id, old('recipients'))) ? 'selected' : '' }}>
                              {{ $user->name }} ({{ $user->email }})
                            </option>
                          @endforeach
                        </select>
                        @error('recipients')
                          <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    
                    <!-- Priority Level -->
                    <div class="mb-3">
                      <label for="priority" class="form-label">Priority Level <span class="text-danger">*</span></label>
                      <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }} selected>Medium</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                      </select>
                      @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Category -->
                    <div class="mb-3">
                      <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                      <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                        <option value="system" {{ old('category') == 'system' ? 'selected' : '' }} selected>System Alert</option>
                        <option value="update" {{ old('category') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="reminder" {{ old('category') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                        <option value="custom" {{ old('category') == 'custom' ? 'selected' : '' }}>Custom</option>
                      </select>
                      @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Scheduling & Delivery -->
              <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title mb-0">Scheduling & Delivery</h5>
                  </div>
                  <div class="card-body">
                    <!-- Timing Options -->
                    <div class="mb-3">
                      <label class="form-label d-block">Timing <span class="text-danger">*</span></label>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input @error('send_option') is-invalid @enderror" type="radio" name="send_option" id="send-now" value="now" checked>
                        <label class="form-check-label" for="send-now">Send Now</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input @error('send_option') is-invalid @enderror" type="radio" name="send_option" id="schedule-later" value="schedule">
                        <label class="form-check-label" for="schedule-later">Schedule Later</label>
                      </div>
                      @error('send_option')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Scheduling Options -->
                    <div id="scheduling-options" class="mb-3">
                      <label for="scheduled_at" class="form-label">Schedule Date & Time <span class="text-danger">*</span></label>
                      <input type="text" class="form-control flatpickr-date-time @error('scheduled_at') is-invalid @enderror" id="scheduled_at" name="scheduled_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('scheduled_at') }}">
                      @error('scheduled_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Expiration Date -->
                    <div class="mb-3">
                      <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                      <input type="text" class="form-control flatpickr-date-time @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('expires_at') }}">
                      <div class="text-muted small mt-1">Notification will be automatically removed after this date</div>
                      @error('expires_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Delivery Methods -->
                    <div class="mb-3">
                      <label class="form-label d-block">Delivery Methods <span class="text-danger">*</span></label>
                      <div class="form-check mb-2">
                        <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="web-alert" value="web" checked>
                        <label class="form-check-label" for="web-alert">Web Alert</label>
                      </div>
                      <div class="form-check mb-2">
                        <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="push-notification" value="push">
                        <label class="form-check-label" for="push-notification">Push Notification</label>
                      </div>
                      <div class="form-check mb-2">
                        <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="email-notification" value="email">
                        <label class="form-check-label" for="email-notification">Email</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="sms-notification" value="sms">
                        <label class="form-check-label" for="sms-notification">SMS</label>
                      </div>
                      @error('delivery_methods')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-column">
                      <button type="button" id="preview-btn" class="btn btn-outline-primary mb-2">
                        <i class="bx bx-show me-1"></i> Preview Notification
                      </button>
                      <button type="button" id="save-draft-btn" class="btn btn-outline-secondary mb-2">
                        <i class="bx bx-save me-1"></i> Save Draft
                      </button>
                      <button type="submit" class="btn btn-primary">
                        <i class="bx bx-paper-plane me-1"></i> Send Notification
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection 