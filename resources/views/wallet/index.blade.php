@extends('layouts.app')

@section('title', 'کیف پول')

@section('page-css')
<style>
    .wallet-balance {
        font-size: 2.5rem;
        font-weight: bold;
        color: #5d87ff;
    }
    .wallet-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .wallet-icon {
        font-size: 1.5rem;
        padding: 12px;
        background-color: rgba(93, 135, 255, 0.1);
        border-radius: 50%;
        margin-bottom: 15px;
    }
    .transaction-badge {
        padding: 8px 12px;
        font-size: 0.8rem;
        border-radius: 30px;
    }
    .action-button {
        margin-right: 10px;
        border-radius: 8px;
        padding: 10px 20px;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">حساب کاربری /</span> کیف پول
    </h4>

    <div class="row">
        <!-- Wallet Balance Card -->
        <div class="col-md-6 mb-4">
            <div class="card wallet-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h5 class="card-title">موجودی کیف پول</h5>
                            <div class="wallet-balance mt-3">{{ number_format($balance) }} <small class="text-muted">تومان</small></div>
                        </div>
                        <div class="wallet-icon">
                            <i class="bx bx-wallet text-primary"></i>
                        </div>
                    </div>
                    
                    @if ($balance < 10000)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-error-circle me-1"></i>
                        <span>موجودی کیف پول شما کم است. برای ادامه استفاده از خدمات، لطفا کیف پول خود را شارژ کنید.</span>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light d-flex justify-content-center py-3">
                    <a href="{{ route('wallet.showDepositForm') }}" class="btn btn-primary action-button">
                        <i class="bx bx-plus-circle me-1"></i>شارژ کیف پول
                    </a>
                    <a href="{{ route('wallet.showWithdrawForm') }}" class="btn btn-outline-warning action-button">
                        <i class="bx bx-minus-circle me-1"></i>برداشت
                    </a>
                    <a href="{{ route('wallet.showTransferForm') }}" class="btn btn-outline-info action-button">
                        <i class="bx bx-transfer me-1"></i>انتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-md-6 mb-4">
            <div class="card wallet-card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">عملیات سریع</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <i class="bx bx-history mb-2 text-primary" style="font-size: 2rem;"></i>
                                    <h6>تاریخچه تراکنش‌ها</h6>
                                    <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        مشاهده همه
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <i class="bx bx-credit-card mb-2 text-success" style="font-size: 2rem;"></i>
                                    <h6>پرداخت با کیف پول</h6>
                                    <a href="#" class="btn btn-sm btn-outline-success mt-2">
                                        پرداخت سریع
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card wallet-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تراکنش‌های اخیر</h5>
                    <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
                </div>
                <div class="table-responsive text-nowrap">
                    @if ($transactions->count() > 0)
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>نوع تراکنش</th>
                                    <th>مبلغ</th>
                                    <th>توضیحات</th>
                                    <th>تاریخ</th>
                                    <th>وضعیت</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($transactions as $transaction)
                                    <tr>
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
                                            <span class="badge bg-label-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ $transaction->status == 'completed' ? 'تکمیل شده' : $transaction->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3 mb-3">
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