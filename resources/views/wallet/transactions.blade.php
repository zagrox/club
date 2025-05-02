@extends('layouts.app')

@section('title', 'تاریخچه تراکنش‌ها')

@section('page-css')
<style>
    .transaction-badge {
        padding: 8px 12px;
        font-size: 0.8rem;
        border-radius: 30px;
    }
    .status-badge {
        padding: 7px 12px;
        font-size: 0.8rem;
        border-radius: 30px;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>تاریخچه تراکنش‌ها</span>
                    <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>بازگشت به کیف پول
                    </a>
                </div>

                <div class="card-body">
                    @if ($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>شناسه</th>
                                        <th>نوع تراکنش</th>
                                        <th>مبلغ</th>
                                        <th>توضیحات</th>
                                        <th>تاریخ</th>
                                        <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>
                                                @if ($transaction->type === 'deposit')
                                                    <span class="badge bg-label-success transaction-badge">
                                                        <i class="bx bx-plus-circle me-1"></i>واریز
                                                    </span>
                                                @elseif ($transaction->type === 'withdrawal')
                                                    <span class="badge bg-label-warning transaction-badge">
                                                        <i class="bx bx-minus-circle me-1"></i>برداشت
                                                    </span>
                                                @elseif ($transaction->type === 'transfer')
                                                    <span class="badge bg-label-info transaction-badge">
                                                        <i class="bx bx-transfer-alt me-1"></i>انتقال
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-primary transaction-badge">
                                                        <i class="bx bx-repeat me-1"></i>{{ $transaction->type }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format(abs($transaction->amount)) }} تومان
                                                </span>
                                            </td>
                                            <td>{{ $transaction->description ?: 'بدون توضیحات' }}</td>
                                            <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('completed')
                                                        <span class="badge bg-label-success status-badge">
                                                            <i class="bx bx-check me-1"></i>تکمیل شده
                                                        </span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-label-warning status-badge">
                                                            <i class="bx bx-time me-1"></i>در انتظار
                                                        </span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-label-danger status-badge">
                                                            <i class="bx bx-error me-1"></i>ناموفق
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-label-secondary status-badge">
                                                            {{ $transaction->status }}
                                                        </span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="bx bx-info-circle bx-lg text-primary mb-2"></i>
                            <p>هیچ تراکنشی یافت نشد</p>
                            <a href="{{ route('wallet.showDepositForm') }}" class="btn btn-primary">
                                <i class="bx bx-plus-circle me-1"></i>شارژ کیف پول
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 