@extends('layouts.app')

@section('title', 'جزئیات پرداخت')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">پرداخت‌ها /</span> جزئیات پرداخت
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">جزئیات پرداخت #{{ $payment->id }}</h5>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>بازگشت به لیست
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">شناسه پرداخت</th>
                                    <td>{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <th>مبلغ</th>
                                    <td>{{ number_format($payment->amount) }} ریال</td>
                                </tr>
                                <tr>
                                    <th>وضعیت</th>
                                    <td>
                                        @if($payment->status == 'verified')
                                            <span class="badge bg-success">{{ $payment->status_label }}</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge bg-warning">{{ $payment->status_label }}</span>
                                        @elseif($payment->status == 'failed' || $payment->status == 'canceled')
                                            <span class="badge bg-danger">{{ $payment->status_label }}</span>
                                        @else
                                            <span class="badge bg-info">{{ $payment->status_label }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>درگاه پرداخت</th>
                                    <td>{{ $payment->gateway }}</td>
                                </tr>
                                <tr>
                                    <th>تاریخ ایجاد</th>
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
                                    <td>{{ $payment->track_id }}</td>
                                </tr>
                                @endif
                                @if($payment->ref_id)
                                <tr>
                                    <th>شماره مرجع</th>
                                    <td>{{ $payment->ref_id }}</td>
                                </tr>
                                @endif
                                @if($payment->order_id)
                                <tr>
                                    <th>شماره سفارش</th>
                                    <td>{{ $payment->order_id }}</td>
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
                                    <td>{{ $payment->card_number }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">اقدامات</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-primary d-grid mb-3">
                        <i class="bx bx-list-ul me-1"></i>لیست پرداخت‌ها
                    </a>

                    @if($payment->status == 'pending')
                    <form method="POST" action="{{ route('payments.request') }}">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $payment->amount }}">
                        <input type="hidden" name="description" value="{{ $payment->description }}">
                        <input type="hidden" name="order_id" value="{{ $payment->order_id }}">
                        <button type="submit" class="btn btn-primary d-grid">
                            <i class="bx bx-refresh me-1"></i>تلاش مجدد پرداخت
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            @if(!empty($payment->metadata))
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">اطلاعات تکمیلی</h5>
                </div>
                <div class="card-body">
                    <pre class="mb-0">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 