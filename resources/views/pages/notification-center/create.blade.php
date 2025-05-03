@extends('layouts.app')

@section('title', 'ایجاد اعلان جدید')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('page-css')
<style>
  .notification-card {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    border: none;
  }
  .notification-card:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
  }
  .notification-card .card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 1.25rem 1.5rem;
  }
  .notification-card .card-body {
    padding: 1.5rem;
  }
  .notification-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 0.5rem;
    margin-right: 0.75rem;
    background-color: rgba(105, 108, 255, 0.16);
    color: #696cff;
  }
  .form-label {
    font-weight: 500;
  }
  .file-preview {
    border: 1px solid #eceef1;
    border-radius: 0.375rem;
    padding: 0.5rem;
    transition: all 0.2s ease;
  }
  .file-preview:hover {
    background-color: #f8f9fa;
  }
  .badge-priority {
    border-radius: 50rem;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
  }
  .badge-priority-high {
    background-color: #ff3e1d;
    color: #fff;
  }
  .badge-priority-medium {
    background-color: #ffab00;
    color: #fff;
  }
  .badge-priority-low {
    background-color: #71dd37;
    color: #fff;
  }
  .action-buttons {
    gap: 0.5rem;
  }
</style>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Helper function to show alerts (uses SweetAlert2 if available, otherwise falls back to alert)
      function showAlert(title, message, type) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: title,
            text: message,
            icon: type,
            confirmButtonText: 'باشه'
          });
        } else {
          alert(title + ': ' + message);
        }
      }
      
      // Initialize select2 with improved UI
      try {
        $('.select2').select2({
          dir: 'rtl',
          language: 'fa',
          placeholder: 'انتخاب کنید...',
          allowClear: true,
          width: '100%'
        });
      } catch (error) {
        console.error('Error initializing Select2:', error);
      }
      
      // Initialize flatpickr with Persian calendar
      try {
        $('.flatpickr-date-time').flatpickr({
          enableTime: true,
          dateFormat: "Y-m-d H:i",
          minDate: "today",
          time_24hr: true,
          locale: {
            firstDayOfWeek: 6
          }
        });
      } catch (error) {
        console.error('Error initializing Flatpickr:', error);
      }
      
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
      const audienceSelect = document.getElementById('audience');
      if (audienceSelect) {
        audienceSelect.addEventListener('change', function() {
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
      }
      
      // Preview notification (offcanvas)
      const previewBtn = document.getElementById('preview-btn');
      if (previewBtn) {
        previewBtn.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Get form data
          const title = document.getElementById('title').value;
          const message = document.getElementById('message').value;
          const priority = document.getElementById('priority').value;
          const category = document.getElementById('category').value;
          
          // Validate required fields
          if (!title || !message.trim()) {
            showAlert('خطا!', 'عنوان و متن پیام برای پیش‌نمایش ضروری هستند', 'error');
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
              let errorMessage = 'لطفا خطاهای زیر را اصلاح کنید:';
              for (const key in data.errors) {
                errorMessage += `\n- ${data.errors[key]}`;
              }
              showAlert('خطا!', errorMessage, 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showAlert('خطا!', 'خطایی در ایجاد پیش‌نمایش رخ داد. لطفا دوباره تلاش کنید.', 'error');
          });
        });
      }
      
      // Handle form submissions
      const notificationForm = document.getElementById('notification-form');
      
      // Save Draft
      const saveDraftBtn = document.getElementById('save-draft-btn');
      if (saveDraftBtn && notificationForm) {
        saveDraftBtn.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Validate title and message
          const title = document.getElementById('title').value;
          const message = document.getElementById('message').value;
          
          if (!title || !message.trim()) {
            showAlert('خطا!', 'عنوان و متن پیام برای ذخیره پیش‌نویس ضروری هستند', 'error');
            return;
          }
          
          // Change form action to the draft route
          notificationForm.action = '{{ route("notification-center.store-draft") }}';
          notificationForm.submit();
        });
      }
      
      // Send Notification
      if (notificationForm) {
        notificationForm.addEventListener('submit', function(e) {
          // Prevent default submission to validate first
          e.preventDefault();
          
          // Validate all required fields
          const title = document.getElementById('title').value;
          const message = document.getElementById('message').value;
          
          if (!title || !message.trim()) {
            showAlert('خطا!', 'عنوان و متن پیام برای ارسال اعلان ضروری هستند', 'error');
            return;
          }
          
          // Check if at least one delivery method is selected
          const deliveryMethods = document.querySelectorAll('input[name="delivery_methods[]"]:checked');
          if (deliveryMethods.length === 0) {
            showAlert('خطا!', 'حداقل یک روش ارسال باید انتخاب شود', 'error');
            return;
          }
          
          // If scheduled, validate the date
          const sendNow = document.getElementById('send_now').value === '1';
          if (!sendNow) {
            const scheduledAt = document.getElementById('scheduled_at').value;
            if (!scheduledAt) {
              showAlert('خطا!', 'برای زمان‌بندی، تاریخ و زمان ارسال باید مشخص شود', 'error');
              return;
            }
          }
          
          // Submit the form
          this.submit();
        });
      }
      
      // File upload preview with enhanced UI
      const attachmentsInput = document.getElementById('attachments');
      if (attachmentsInput) {
        attachmentsInput.addEventListener('change', function(e) {
          const fileList = document.getElementById('file-list');
          if (fileList) {
            fileList.innerHTML = '';
            
            if (this.files.length > 0) {
              document.getElementById('attachments-container').classList.remove('d-none');
              
              Array.from(this.files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-preview mb-2 d-flex align-items-center';
                
                // Determine file icon based on type
                let icon = 'bx-file';
                let iconColor = 'text-secondary';
                
                if (file.type.includes('image')) {
                  icon = 'bx-image';
                  iconColor = 'text-success';
                } else if (file.type.includes('pdf')) {
                  icon = 'bx-file-pdf';
                  iconColor = 'text-danger';
                } else if (file.type.includes('excel') || file.type.includes('spreadsheet')) {
                  icon = 'bx-file-excel';
                  iconColor = 'text-success';
                } else if (file.type.includes('word') || file.type.includes('document')) {
                  icon = 'bx-file-doc';
                  iconColor = 'text-primary';
                } else if (file.type.includes('zip') || file.type.includes('rar') || file.type.includes('archive')) {
                  icon = 'bx-archive';
                  iconColor = 'text-warning';
                }
                
                // Format file size
                const fileSize = file.size < 1024 ? `${file.size} B` :
                              file.size < 1048576 ? `${(file.size / 1024).toFixed(2)} KB` :
                              `${(file.size / 1048576).toFixed(2)} MB`;
                
                fileItem.innerHTML = `
                  <i class="bx ${icon} fs-3 me-2 ${iconColor}"></i>
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
          }
        });
      }
      
      // Initialize form with default values
      const sendNowRadio = document.getElementById('send-now');
      if (sendNowRadio) {
        sendNowRadio.checked = true;
      }
      
      const sendNowInput = document.getElementById('send_now');
      if (sendNowInput) {
        sendNowInput.value = 1;
      }
      
      const schedulingOptions = document.getElementById('scheduling-options');
      if (schedulingOptions) {
        schedulingOptions.classList.add('d-none');
      }
      
      const recipientsSection = document.getElementById('recipients-section');
      if (recipientsSection) {
        recipientsSection.classList.add('d-none');
      }
    });
  </script>
@endsection

@section('content')
  <!-- Preview Offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationPreviewOffcanvas" aria-labelledby="notificationPreviewOffcanvasLabel">
    <div class="offcanvas-header">
      <h5 id="notificationPreviewOffcanvasLabel" class="offcanvas-title">پیش‌نمایش اعلان</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="notification-preview-content">
      <!-- Preview content will be inserted here by JavaScript -->
    </div>
  </div>

  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card notification-card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
              <div class="d-flex align-items-center">
                <div class="notification-icon">
                  <i class="bx bx-bell-plus fs-3"></i>
                </div>
                <div>
                  <h4 class="fw-bold mb-1">ایجاد اعلان جدید</h4>
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">خانه</a>
                      </li>
                      <li class="breadcrumb-item">
                        <a href="{{ route('notification-center.index') }}">مرکز اعلان‌ها</a>
                      </li>
                      <li class="breadcrumb-item active">اعلان جدید</li>
                    </ol>
                  </nav>
                </div>
              </div>
              <div>
                <a href="{{ route('notification-center.index') }}" class="btn btn-label-secondary">
                  <i class="bx bx-arrow-back me-1"></i>
                  بازگشت به مرکز اعلان‌ها
                </a>
              </div>
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
        
        <div class="row">
          <!-- Notification Content Section -->
          <div class="col-md-8 col-12 mb-4">
            <div class="card notification-card h-100">
              <div class="card-header d-flex align-items-center">
                <i class="bx bx-edit-alt me-2 text-primary"></i>
                <h5 class="card-title mb-0">محتوای اعلان</h5>
              </div>
              <div class="card-body">
                <!-- Title -->
                <div class="mb-4">
                  <label for="title" class="form-label">عنوان <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-message"></i></span>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="عنوان اعلان را وارد کنید" required>
                  </div>
                  @error('title')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                  @enderror
                </div>
                
                <!-- Message (Rich Text Editor) -->
                <div class="mb-4">
                  <label for="message" class="form-label">متن پیام <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-message-detail"></i></span>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="8" placeholder="متن پیام اعلان خود را اینجا بنویسید..." style="direction: rtl; text-align: right;" required>{{ old('message') }}</textarea>
                  </div>
                  <div class="form-text mt-1">متن پیام اعلان خود را وارد کنید</div>
                  @error('message')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                  @enderror
                </div>
                
                <!-- Attachments -->
                <div class="mb-3">
                  <label for="attachments" class="form-label">پیوست‌ها</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-paperclip"></i></span>
                    <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                  </div>
                  <div class="form-text">حداکثر اندازه فایل: 10 مگابایت. شما می‌توانید چندین فایل را بارگذاری کنید.</div>
                  @error('attachments')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                  @enderror
                </div>
                
                <!-- Attachments Preview -->
                <div id="attachments-container" class="d-none mt-4">
                  <div class="d-flex align-items-center mb-2">
                    <i class="bx bx-file fs-5 me-2 text-primary"></i>
                    <h6 class="mb-0">فایل‌های پیوست شده</h6>
                  </div>
                  <div id="file-list" class="rounded border p-3"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Configuration Section -->
          <div class="col-md-4 col-12">
            <div class="row">
              <!-- Target Audience -->
              <div class="col-12 mb-4">
                <div class="card notification-card">
                  <div class="card-header d-flex align-items-center">
                    <i class="bx bx-target-lock me-2 text-primary"></i>
                    <h5 class="card-title mb-0">مخاطبان هدف</h5>
                  </div>
                  <div class="card-body">
                    <!-- Send To -->
                    <div class="mb-3">
                      <label for="audience" class="form-label">ارسال به <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-user-circle"></i></span>
                        <select class="form-select @error('audience') is-invalid @enderror" id="audience" name="audience" required>
                          <option value="all" {{ old('audience') == 'all' ? 'selected' : '' }}>همه کاربران</option>
                          <option value="roles" {{ old('audience') == 'roles' ? 'selected' : '' }}>نقش‌های خاص</option>
                          <option value="users" {{ old('audience') == 'users' ? 'selected' : '' }}>کاربران فردی</option>
                        </select>
                      </div>
                      @error('audience')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Recipients Selection -->
                    <div id="recipients-section" class="mb-3">
                      <!-- Role Selection -->
                      <div id="roles-selection" class="d-none">
                        <label for="role-recipients" class="form-label">انتخاب نقش‌ها <span class="text-danger">*</span></label>
                        <select class="select2 form-select @error('recipients') is-invalid @enderror" id="role-recipients" name="recipients[]" multiple>
                          @foreach($roles as $role)
                            <option value="{{ $role }}" {{ (is_array(old('recipients')) && in_array($role, old('recipients'))) ? 'selected' : '' }}>
                              {{ ucfirst($role) }}
                            </option>
                          @endforeach
                        </select>
                        @error('recipients')
                          <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <!-- User Selection -->
                      <div id="users-selection" class="d-none">
                        <label for="user-recipients" class="form-label">انتخاب کاربران <span class="text-danger">*</span></label>
                        <select class="select2 form-select @error('recipients') is-invalid @enderror" id="user-recipients" name="recipients[]" multiple>
                          @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (is_array(old('recipients')) && in_array($user->id, old('recipients'))) ? 'selected' : '' }}>
                              {{ $user->name }} ({{ $user->email }})
                            </option>
                          @endforeach
                        </select>
                        @error('recipients')
                          <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    
                    <!-- Priority Level -->
                    <div class="mb-3">
                      <label for="priority" class="form-label">سطح اولویت <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-line-chart"></i></span>
                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                          <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>بالا</option>
                          <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }} selected>متوسط</option>
                          <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>پایین</option>
                        </select>
                      </div>
                      <div class="mt-2">
                        <span class="badge bg-danger badge-priority badge-priority-high">بالا</span>
                        <span class="badge bg-warning badge-priority badge-priority-medium">متوسط</span>
                        <span class="badge bg-success badge-priority badge-priority-low">پایین</span>
                      </div>
                      @error('priority')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Category -->
                    <div class="mb-3">
                      <label for="category" class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-category"></i></span>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                          <option value="system" {{ old('category') == 'system' ? 'selected' : '' }} selected>هشدار سیستم</option>
                          <option value="update" {{ old('category') == 'update' ? 'selected' : '' }}>بروزرسانی</option>
                          <option value="reminder" {{ old('category') == 'reminder' ? 'selected' : '' }}>یادآوری</option>
                          <option value="custom" {{ old('category') == 'custom' ? 'selected' : '' }}>سفارشی</option>
                        </select>
                      </div>
                      @error('category')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Scheduling & Delivery -->
              <div class="col-12 mb-4">
                <div class="card notification-card">
                  <div class="card-header d-flex align-items-center">
                    <i class="bx bx-time-five me-2 text-primary"></i>
                    <h5 class="card-title mb-0">زمان‌بندی و ارسال</h5>
                  </div>
                  <div class="card-body">
                    <!-- Timing Options -->
                    <div class="mb-4">
                      <label class="form-label d-block">زمان ارسال <span class="text-danger">*</span></label>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input @error('send_option') is-invalid @enderror" type="radio" name="send_option" id="send-now" value="now" checked>
                        <label class="form-check-label" for="send-now">ارسال فوری</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input @error('send_option') is-invalid @enderror" type="radio" name="send_option" id="schedule-later" value="schedule">
                        <label class="form-check-label" for="schedule-later">زمان‌بندی</label>
                      </div>
                      @error('send_option')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Scheduling Options -->
                    <div id="scheduling-options" class="mb-4">
                      <label for="scheduled_at" class="form-label">تاریخ و زمان ارسال <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                        <input type="text" class="form-control flatpickr-date-time @error('scheduled_at') is-invalid @enderror" id="scheduled_at" name="scheduled_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('scheduled_at') }}">
                      </div>
                      @error('scheduled_at')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Expiration Date -->
                    <div class="mb-4">
                      <label for="expires_at" class="form-label">تاریخ انقضا (اختیاری)</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-time-five"></i></span>
                        <input type="text" class="form-control flatpickr-date-time @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" placeholder="YYYY-MM-DD HH:MM" value="{{ old('expires_at') }}">
                      </div>
                      <div class="form-text">اعلان پس از این تاریخ به طور خودکار حذف می‌شود</div>
                      @error('expires_at')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                    
                    <!-- Delivery Methods -->
                    <div class="mb-3">
                      <label class="form-label d-block">روش‌های ارسال <span class="text-danger">*</span></label>
                      
                      <div class="d-flex flex-wrap gap-3">
                        <div class="form-check custom-option custom-option-basic">
                          <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="web-alert" value="web" checked>
                          <label class="form-check-label d-flex gap-2 py-1" for="web-alert">
                            <i class="bx bx-globe fs-4"></i>
                            <span>اعلان وب</span>
                          </label>
                        </div>
                        
                        <div class="form-check custom-option custom-option-basic">
                          <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="push-notification" value="push">
                          <label class="form-check-label d-flex gap-2 py-1" for="push-notification">
                            <i class="bx bx-bell fs-4"></i>
                            <span>پوش نوتیفیکیشن</span>
                          </label>
                        </div>
                        
                        <div class="form-check custom-option custom-option-basic">
                          <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="email-notification" value="email">
                          <label class="form-check-label d-flex gap-2 py-1" for="email-notification">
                            <i class="bx bx-envelope fs-4"></i>
                            <span>ایمیل</span>
                          </label>
                        </div>
                        
                        <div class="form-check custom-option custom-option-basic">
                          <input class="form-check-input @error('delivery_methods') is-invalid @enderror" type="checkbox" name="delivery_methods[]" id="sms-notification" value="sms">
                          <label class="form-check-label d-flex gap-2 py-1" for="sms-notification">
                            <i class="bx bx-mobile fs-4"></i>
                            <span>پیامک</span>
                          </label>
                        </div>
                      </div>
                      
                      @error('delivery_methods')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="col-12 mb-4">
                <div class="card notification-card">
                  <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                      <button type="button" id="preview-btn" class="btn btn-outline-primary d-flex align-items-center justify-content-center">
                        <i class="bx bx-show fs-5 me-2"></i> پیش‌نمایش اعلان
                      </button>
                      <button type="button" id="save-draft-btn" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                        <i class="bx bx-save fs-5 me-2"></i> ذخیره پیش‌نویس
                      </button>
                      <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                        <i class="bx bx-paper-plane fs-5 me-2"></i> ارسال اعلان
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