@extends('layouts.app')

@section('title', 'تنظیمات پرداخت')

@section('page-css')
<style>
    .toggle-wrapper {
        position: relative;
    }
    .form-switch .form-check-input {
        height: 1.5rem;
        width: 3rem;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: #696cff;
        border-color: #696cff;
    }
    .form-switch .form-check-label {
        cursor: pointer;
    }
    .settings-card {
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        transition: all 0.3s ease-in-out;
    }
    .settings-card:hover {
        box-shadow: 0 4px 8px 0 rgba(67, 89, 113, 0.2);
    }
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    .field-container {
        margin-bottom: 1.5rem;
    }
    .tab-content {
        padding: 1.5rem 0;
    }
    .form-text {
        margin-top: 0.25rem;
        font-size: 0.8125rem;
        color: #a1acb8;
    }
    .btn-save {
        min-width: 120px;
    }
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        justify-content: flex-start;
        gap: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">تنظیمات /</span> گزینه‌های پرداخت
    </h4>
    
    <!-- Toast container for notifications -->
    <div class="toast-container">
        <!-- Success Toast -->
        <div class="bs-toast toast fade" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
            <div class="toast-header bg-success text-white">
                <i class="bx bx-check me-2"></i>
                <div class="me-auto fw-semibold">موفقیت</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="successToastMessage"></div>
        </div>
        
        <!-- Error Toast -->
        <div class="bs-toast toast fade" role="alert" aria-live="assertive" aria-atomic="true" id="errorToast">
            <div class="toast-header bg-danger text-white">
                <i class="bx bx-error me-2"></i>
                <div class="me-auto fw-semibold">خطا</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="errorToastMessage"></div>
        </div>
    </div>

    <!-- Server-side Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('paymentUrl'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <p>برای تست درگاه پرداخت، روی لینک زیر کلیک کنید:</p>
            <a href="{{ session('paymentUrl') }}" target="_blank" class="btn btn-sm btn-primary">
                <i class="bx bx-link-external me-1"></i>باز کردن درگاه پرداخت تست
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-light mb-2" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">در حال بارگذاری...</span>
        </div>
        <div class="text-light mt-2 fs-5">در حال ذخیره تنظیمات...</div>
    </div>

    <!-- Nav tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-pills nav-fill mb-4" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-zibal" aria-controls="tab-zibal" aria-selected="true">
                        <i class="bx bx-credit-card me-1"></i>
                        <span class="d-none d-sm-block">درگاه پرداخت زیبال</span>
                        <span class="d-block d-sm-none">زیبال</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-general" aria-controls="tab-general" aria-selected="false">
                        <i class="bx bx-cog me-1"></i>
                        <span class="d-none d-sm-block">تنظیمات عمومی</span>
                        <span class="d-block d-sm-none">عمومی</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-logs" aria-controls="tab-logs" aria-selected="false">
                        <i class="bx bx-history me-1"></i>
                        <span class="d-none d-sm-block">گزارش‌های پرداخت</span>
                        <span class="d-block d-sm-none">گزارش‌ها</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab content -->
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <!-- Zibal Tab -->
                <div class="tab-pane fade show active" id="tab-zibal" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card settings-card mb-4">
                                <h5 class="card-header d-flex align-items-center">
                                    <i class="bx bx-credit-card me-2 text-primary"></i>
                                    تنظیمات درگاه پرداخت زیبال
                                </h5>
                                <div class="card-body">
                                    <form id="zibalSettingsForm">
                                        @csrf
                                        
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="field-container">
                                                    <label for="merchant" class="form-label fw-semibold">شناسه پذیرنده (Merchant ID)</label>
                                                    <input type="text" class="form-control" id="merchant" name="merchant" value="{{ $zibalConfig['merchant'] }}">
                                                    <div class="form-text">این شناسه را از پنل کاربری زیبال دریافت کنید.</div>
                                                    <div class="invalid-feedback" id="merchant-error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="field-container">
                                                    <label for="log_channel" class="form-label fw-semibold">کانال لاگ</label>
                                                    <select class="form-select" id="log_channel" name="log_channel">
                                                        <option value="daily" {{ $zibalConfig['log_channel'] == 'daily' ? 'selected' : '' }}>روزانه</option>
                                                        <option value="single" {{ $zibalConfig['log_channel'] == 'single' ? 'selected' : '' }}>تک فایل</option>
                                                        <option value="stack" {{ $zibalConfig['log_channel'] == 'stack' ? 'selected' : '' }}>مجموعه</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="log_channel-error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-container">
                                            <label for="callback_url" class="form-label fw-semibold">آدرس بازگشت (Callback URL)</label>
                                            <input type="text" class="form-control" id="callback_url" name="callback_url" value="{{ $zibalConfig['callback_url'] }}">
                                            <div class="form-text">آدرس بازگشت از درگاه پرداخت، به طور پیش‌فرض /payments/callback</div>
                                            <div class="invalid-feedback" id="callback_url-error"></div>
                                        </div>

                                        <div class="field-container">
                                            <label for="description_prefix" class="form-label fw-semibold">پیشوند توضیحات</label>
                                            <input type="text" class="form-control" id="description_prefix" name="description_prefix" value="{{ $zibalConfig['description_prefix'] }}">
                                            <div class="form-text">این متن قبل از توضیحات هر پرداخت اضافه می‌شود.</div>
                                            <div class="invalid-feedback" id="description_prefix-error"></div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-md-4">
                                                <div class="toggle-wrapper mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="sandbox" name="sandbox" {{ $zibalConfig['sandbox'] ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold" for="sandbox">
                                                            حالت آزمایشی (Sandbox)
                                                        </label>
                                                    </div>
                                                    <div class="form-text">برای تست در محیط غیرتولید</div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="toggle-wrapper mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="mock" name="mock" {{ $zibalConfig['mock'] ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold" for="mock">
                                                            حالت شبیه‌سازی (Mock)
                                                        </label>
                                                    </div>
                                                    <div class="form-text">شبیه‌سازی بدون تماس با زیبال</div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="toggle-wrapper mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="log_enabled" name="log_enabled" {{ $zibalConfig['log_enabled'] ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold" for="log_enabled">
                                                            فعال‌سازی لاگ
                                                        </label>
                                                    </div>
                                                    <div class="form-text">ثبت گزارش‌های تراکنش در لاگ</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="action-buttons">
                                            <button type="button" id="saveZibalSettings" class="btn btn-primary btn-save">
                                                <i class="bx bx-save me-1"></i>ذخیره تنظیمات
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="bx bx-reset me-1"></i>بازنشانی
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card settings-card mb-4">
                                <h5 class="card-header d-flex align-items-center">
                                    <i class="bx bx-test-tube me-2 text-success"></i>
                                    تست درگاه پرداخت
                                </h5>
                                <div class="card-body">
                                    <p>برای اطمینان از صحت تنظیمات، یک پرداخت آزمایشی انجام دهید.</p>
                                    <div class="d-flex justify-content-between align-items-center my-3">
                                        <span class="fw-semibold">مبلغ تست:</span>
                                        <span class="badge bg-label-info">10,000 ریال</span>
                                    </div>
                                    
                                    <form id="testZibalForm" action="{{ route('payment-options.zibal.test') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success d-grid w-100">
                                            <i class="bx bx-check-circle me-1"></i>
                                            <span>تست درگاه پرداخت</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="card settings-card mb-4">
                                <h5 class="card-header d-flex align-items-center">
                                    <i class="bx bx-question-mark me-2 text-info"></i>
                                    راهنمای درگاه زیبال
                                </h5>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="https://docs.zibal.ir/" target="_blank" class="btn btn-outline-primary">
                                            <i class="bx bx-link-external me-1"></i>مستندات رسمی زیبال
                                        </a>
                                        <a href="{{ url('docs/zibal-integration.md') }}" target="_blank" class="btn btn-outline-info">
                                            <i class="bx bx-file me-1"></i>راهنمای یکپارچه‌سازی
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- General Tab -->
                <div class="tab-pane fade" id="tab-general" role="tabpanel">
                    <div class="card settings-card mt-2">
                        <h5 class="card-header d-flex align-items-center">
                            <i class="bx bx-cog me-2 text-primary"></i>
                            تنظیمات عمومی پرداخت
                        </h5>
                        <div class="card-body">
                            <p class="text-muted">این بخش در حال تکمیل است...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Logs Tab -->
                <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                    <div class="card settings-card mt-2">
                        <h5 class="card-header d-flex align-items-center">
                            <i class="bx bx-history me-2 text-primary"></i>
                            گزارش‌های پرداخت
                        </h5>
                        <div class="card-body">
                            <p class="text-muted">این بخش در حال تکمیل است...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const successToast = document.getElementById('successToast');
    const errorToast = document.getElementById('errorToast');
    const successToastMessage = document.getElementById('successToastMessage');
    const errorToastMessage = document.getElementById('errorToastMessage');

    // Initialize toasts
    const toastOptions = {
        animation: true,
        delay: 5000
    };
    
    // Test form submission (regular form)
    const testZibalForm = document.getElementById('testZibalForm');
    if (testZibalForm) {
        testZibalForm.addEventListener('submit', function() {
            loadingOverlay.style.display = 'flex';
        });
    }

    // Zibal settings form submission with AJAX
    const saveZibalSettings = document.getElementById('saveZibalSettings');
    if (saveZibalSettings) {
        saveZibalSettings.addEventListener('click', function(e) {
            e.preventDefault();
            submitZibalSettings();
        });
    }

    function submitZibalSettings() {
        // Clear previous errors
        clearFormErrors();
        
        // Show loading overlay
        loadingOverlay.style.display = 'flex';
        
        // Get form data
        const form = document.getElementById('zibalSettingsForm');
        const formData = new FormData(form);
        
        // Add checkbox values correctly
        formData.append('sandbox', document.getElementById('sandbox').checked ? '1' : '0');
        formData.append('mock', document.getElementById('mock').checked ? '1' : '0');
        formData.append('log_enabled', document.getElementById('log_enabled').checked ? '1' : '0');
        
        // Send AJAX request
        fetch('{{ route('payment-options.zibal.update') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingOverlay.style.display = 'none';
            
            if (data.success) {
                // Show success message
                successToastMessage.textContent = data.message;
                const bsSuccessToast = new bootstrap.Toast(successToast, toastOptions);
                bsSuccessToast.show();
            } else {
                // Show error messages
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById(`${field}-error`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                            document.getElementById(field).classList.add('is-invalid');
                        }
                    });
                }
                
                // Show general error message
                errorToastMessage.textContent = data.message || 'خطا در ذخیره تنظیمات';
                const bsErrorToast = new bootstrap.Toast(errorToast, toastOptions);
                bsErrorToast.show();
            }
        })
        .catch(error => {
            loadingOverlay.style.display = 'none';
            
            // Show error message
            errorToastMessage.textContent = 'خطا در برقراری ارتباط با سرور';
            const bsErrorToast = new bootstrap.Toast(errorToast, toastOptions);
            bsErrorToast.show();
            
            console.error('Error:', error);
        });
    }

    function clearFormErrors() {
        const invalidInputs = document.querySelectorAll('.is-invalid');
        invalidInputs.forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        const errorMessages = document.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(message => {
            message.textContent = '';
        });
    }
    
    // Form reset handler
    const formResetButton = document.querySelector('button[type="reset"]');
    if (formResetButton) {
        formResetButton.addEventListener('click', function() {
            // Reset form to initial values
            document.getElementById('sandbox').checked = {{ $zibalConfig['sandbox'] ? 'true' : 'false' }};
            document.getElementById('mock').checked = {{ $zibalConfig['mock'] ? 'true' : 'false' }};
            document.getElementById('log_enabled').checked = {{ $zibalConfig['log_enabled'] ? 'true' : 'false' }};
            
            // Clear any validation errors
            clearFormErrors();
        });
    }
});
</script>
@endpush 