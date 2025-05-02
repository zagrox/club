@extends('layouts.app')

@section('title', 'Mock Payment Gateway')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">درگاه پرداخت شبیه‌سازی شده</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <p><i class="bx bx-info-circle me-1"></i> این یک درگاه پرداخت شبیه‌سازی شده است و برای آزمایش استفاده می‌شود.</p>
                    </div>

                    <form action="{{ route('mock.payment.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="trackId" value="{{ $trackId }}">
                        
                        <div class="mb-3">
                            <label class="form-label">شماره پیگیری</label>
                            <input type="text" class="form-control" value="{{ $trackId }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">مبلغ</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="10,000" readonly>
                                <span class="input-group-text">ریال</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">شماره کارت</label>
                            <input type="text" class="form-control" value="6104****1234" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="success" name="success" checked>
                                <label class="form-check-label" for="success">پرداخت موفق</label>
                            </div>
                            <div class="form-text">با غیرفعال کردن این گزینه می‌توانید یک پرداخت ناموفق را شبیه‌سازی کنید.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">آدرس بازگشت</label>
                            <input type="text" class="form-control" name="callback_url" value="{{ request()->query('callbackUrl', '') }}">
                            <div class="form-text">اگر خالی باشد، به مسیر پیش‌فرض پرداخت‌ها هدایت می‌شود.</div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">تکمیل پرداخت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 