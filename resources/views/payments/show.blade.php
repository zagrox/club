@extends('layouts.app')

@section('title', 'جزئیات پرداخت')

@section('page-css')
<style>
    .payment-stats {
        border-radius: 0.5rem;
    }
    .payment-card {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        transition: all 0.3s ease;
    }
    .payment-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    .transaction-meta-item {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .transaction-meta-item:last-child {
        border-bottom: none;
    }
    .transaction-timeline .transaction-item {
        position: relative;
        padding-left: 3rem;
        padding-bottom: 1.5rem;
    }
    .transaction-timeline .transaction-item:before {
        content: "";
        position: absolute;
        left: 0.85rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .transaction-timeline .transaction-item:last-child:before {
        display: none;
    }
    .transaction-timeline .timeline-dot {
        position: absolute;
        left: 0;
        top: 0;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 50%;
        background: white;
        border: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .metadata-container {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">مالی / پرداخت‌ها /</span> جزئیات پرداخت
    </h4>

    <div class="row">
        <!-- Main Payment Details -->
        <div class="col-lg-8 mb-4">
            <div class="card payment-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">پرداخت <span class="fw-bold">#{{ $payment->id }}</span></h5>
                        <small class="text-muted">{{ $payment->created_at->format('Y/m/d H:i:s') }}</small>
                    </div>
                    <div>
                        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>بازگشت
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                            <i class="bx bx-printer me-1"></i>چاپ
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Payment Status Banner -->
                    <div class="alert 
                        @if($payment->status == 'verified') alert-success 
                        @elseif($payment->status == 'pending') alert-warning 
                        @elseif($payment->status == 'failed' || $payment->status == 'canceled') alert-danger 
                        @else alert-info @endif
                        d-flex align-items-center mb-4">
                        <span class="badge 
                            @if($payment->status == 'verified') bg-success 
                            @elseif($payment->status == 'pending') bg-warning 
                            @elseif($payment->status == 'failed' || $payment->status == 'canceled') bg-danger 
                            @else bg-info @endif
                            me-2">
                            <i class="bx 
                                @if($payment->status == 'verified') bx-check-circle 
                                @elseif($payment->status == 'pending') bx-time-five 
                                @elseif($payment->status == 'failed' || $payment->status == 'canceled') bx-x-circle 
                                @else bx-info-circle @endif
                                me-1"></i>
                            {{ $payment->status_label }}
                        </span>
                        <div>
                            @if($payment->status == 'verified')
                                پرداخت با موفقیت انجام و تایید شده است.
                            @elseif($payment->status == 'pending')
                                پرداخت در انتظار تایید می‌باشد.
                            @elseif($payment->status == 'failed')
                                پرداخت ناموفق بوده است.
                            @elseif($payment->status == 'canceled')
                                پرداخت توسط کاربر لغو شده است.
                            @else
                                وضعیت پرداخت: {{ $payment->status_label }}
                            @endif
                        </div>
                    </div>
                    
                    <!-- Amount & User Info -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card bg-primary text-white payment-stats">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-1 text-white opacity-75">مبلغ پرداخت</h6>
                                    <h2 class="card-title mb-0">{{ number_format($payment->amount) }} <small>ریال</small></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($payment->user)
                                <div class="card bg-dark text-white payment-stats">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-1 text-white opacity-75">پرداخت کننده</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-primary">
                                                    {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-white">{{ $payment->user->name }}</h6>
                                                <small class="text-white opacity-75">{{ $payment->user->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card bg-secondary text-white payment-stats">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-1 text-white opacity-75">پرداخت کننده</h6>
                                        <h6 class="mb-0">کاربر نامشخص</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Transaction Details -->
                    <div class="card mb-4">
                        <div class="card-header border-bottom">
                            <h6 class="mb-0">جزئیات تراکنش</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">شناسه پرداخت</th>
                                        <td>{{ $payment->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>درگاه پرداخت</th>
                                        <td>
                                            <span class="badge bg-label-primary">{{ $payment->gateway ?: 'زیبال' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>تاریخ درخواست</th>
                                        <td>{{ $payment->created_at->format('Y/m/d H:i:s') }}</td>
                                    </tr>
                                    @if($payment->payment_date)
                                    <tr>
                                        <th>تاریخ پرداخت</th>
                                        <td>{{ $payment->payment_date->format('Y/m/d H:i:s') }}</td>
                                    </tr>
                                    @endif
                                    @if($payment->track_id)
                                    <tr>
                                        <th>کد پیگیری</th>
                                        <td><code>{{ $payment->track_id }}</code></td>
                                    </tr>
                                    @endif
                                    @if($payment->ref_id)
                                    <tr>
                                        <th>شماره مرجع</th>
                                        <td><code>{{ $payment->ref_id }}</code></td>
                                    </tr>
                                    @endif
                                    @if($payment->order_id)
                                    <tr>
                                        <th>شماره سفارش</th>
                                        <td>
                                            <a href="{{ route('orders.details', $payment->order_id) }}" class="text-primary">
                                                #{{ $payment->order_id }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($payment->description)
                                    <tr>
                                        <th>توضیحات</th>
                                        <td>{{ $payment->description }}</td>
                                    </tr>
                                    @endif
                                    @if($payment->card_number)
                                    <tr>
                                        <th>شماره کارت</th>
                                        <td><code>{{ $payment->card_number }}</code></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="card mb-0">
                        <div class="card-header border-bottom">
                            <h6 class="mb-0">تاریخچه وضعیت</h6>
                        </div>
                        <div class="card-body">
                            <div class="transaction-timeline">
                                <div class="transaction-item">
                                    <div class="timeline-dot bg-primary">
                                        <i class="bx bx-plus-circle text-primary"></i>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">ایجاد پرداخت</h6>
                                        <small class="text-muted">{{ $payment->created_at->format('Y/m/d H:i:s') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">پرداخت با مبلغ {{ number_format($payment->amount) }} ریال ایجاد شد.</p>
                                </div>
                                
                                @if($payment->status == 'verified' && $payment->payment_date)
                                <div class="transaction-item">
                                    <div class="timeline-dot bg-success">
                                        <i class="bx bx-check-circle text-success"></i>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">پرداخت موفق</h6>
                                        <small class="text-muted">{{ $payment->payment_date->format('Y/m/d H:i:s') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">پرداخت با موفقیت انجام و تایید شد.</p>
                                </div>
                                @elseif($payment->status == 'failed')
                                <div class="transaction-item">
                                    <div class="timeline-dot bg-danger">
                                        <i class="bx bx-x-circle text-danger"></i>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">پرداخت ناموفق</h6>
                                        <small class="text-muted">{{ $payment->updated_at->format('Y/m/d H:i:s') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">تراکنش با خطا مواجه شد.</p>
                                </div>
                                @elseif($payment->status == 'canceled')
                                <div class="transaction-item">
                                    <div class="timeline-dot bg-warning">
                                        <i class="bx bx-x text-warning"></i>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">انصراف از پرداخت</h6>
                                        <small class="text-muted">{{ $payment->updated_at->format('Y/m/d H:i:s') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">پرداخت توسط کاربر لغو شد.</p>
                                </div>
                                @elseif($payment->status == 'pending')
                                <div class="transaction-item">
                                    <div class="timeline-dot bg-warning">
                                        <i class="bx bx-time text-warning"></i>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">در انتظار پرداخت</h6>
                                        <small class="text-muted">{{ $payment->updated_at->format('Y/m/d H:i:s') }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">پرداخت در انتظار تکمیل توسط کاربر می‌باشد.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Information -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card payment-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">اقدامات</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-primary d-grid">
                                <i class="bx bx-list-ul mb-1 d-block fs-4"></i>لیست پرداخت‌ها
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <button type="button" class="btn btn-outline-secondary d-grid" onclick="window.print()">
                                <i class="bx bx-printer mb-1 d-block fs-4"></i>چاپ اطلاعات
                            </button>
                        </div>
                    </div>

                    @if($payment->status == 'pending')
                    <form method="POST" action="{{ route('payments.request') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $payment->amount }}">
                        <input type="hidden" name="description" value="{{ $payment->description }}">
                        <input type="hidden" name="order_id" value="{{ $payment->order_id }}">
                        <button type="submit" class="btn btn-primary d-grid w-100">
                            <i class="bx bx-refresh me-1"></i>تلاش مجدد پرداخت
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Metadata Card -->
            @if(!empty($payment->metadata))
            <div class="card payment-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">اطلاعات تکمیلی</h5>
                </div>
                <div class="card-body p-0">
                    <div class="metadata-container p-3">
                        <pre class="mb-0 p-2 bg-light rounded">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 