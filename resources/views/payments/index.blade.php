@extends('layouts.app')

@section('title', 'پرداخت‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">مدیریت پرداخت‌ها</h4>

    <div class="row">
        <!-- Payment Form Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">پرداخت جدید</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('payments.request') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">مبلغ (ریال)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 100000) }}" required min="10000">
                            <div class="form-text">حداقل مبلغ: 10,000 ریال (100 تومان)</div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">توضیحات</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">پرداخت با زیبال</button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">راهنمای پرداخت</h5>
                </div>
                <div class="card-body">
                    <p>برای انجام پرداخت، مراحل زیر را دنبال کنید:</p>
                    <ol>
                        <li>مبلغ مورد نظر خود را وارد کنید.</li>
                        <li>در صورت نیاز توضیحات تراکنش را وارد کنید.</li>
                        <li>روی دکمه پرداخت کلیک کنید.</li>
                        <li>به درگاه پرداخت منتقل خواهید شد.</li>
                        <li>پس از تکمیل پرداخت، به سایت بازگردانده می‌شوید.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Payment History Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">تاریخچه پرداخت‌ها</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>شناسه</th>
                                <th>تاریخ</th>
                                <th>مبلغ (ریال)</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                                    <td>{{ number_format($payment->amount) }}</td>
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
                                    <td>
                                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-primary">جزئیات</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">هیچ پرداختی یافت نشد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                    <div class="card-footer">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 