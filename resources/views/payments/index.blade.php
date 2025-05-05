@extends('layouts.app')

@section('title', 'مدیریت پرداخت‌ها')

@section('page-css')
<style>
    .status-badge {
        min-width: 80px;
    }
    .payment-card {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        transition: all 0.3s ease;
    }
    .payment-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    .payment-stats {
        border-radius: 0.5rem;
    }
    .stats-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">مالی /</span> مدیریت پرداخت‌ها
    </h4>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card payment-stats bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">کل پرداخت‌ها</h5>
                            <h2 class="fw-bold mt-2 mb-0">{{ number_format($payments->total()) }}</h2>
                        </div>
                        <div class="stats-icon bg-white text-primary">
                            <i class="bx bx-credit-card fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card payment-stats bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">پرداخت‌های موفق</h5>
                            <h2 class="fw-bold mt-2 mb-0">{{ number_format($payments->where('status', 'verified')->count()) }}</h2>
                        </div>
                        <div class="stats-icon bg-white text-success">
                            <i class="bx bx-check-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card payment-stats bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">در انتظار پرداخت</h5>
                            <h2 class="fw-bold mt-2 mb-0">{{ number_format($payments->where('status', 'pending')->count()) }}</h2>
                        </div>
                        <div class="stats-icon bg-white text-warning">
                            <i class="bx bx-time fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card payment-stats bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">پرداخت‌های ناموفق</h5>
                            <h2 class="fw-bold mt-2 mb-0">{{ number_format($payments->whereIn('status', ['failed', 'canceled'])->count()) }}</h2>
                        </div>
                        <div class="stats-icon bg-white text-danger">
                            <i class="bx bx-x-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label">وضعیت</label>
                    <select id="filter-status" name="status" class="form-select">
                        <option value="">همه</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>تایید شده</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار پرداخت</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>ناموفق</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>لغو شده</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-date-from" class="form-label">از تاریخ</label>
                    <input type="date" id="filter-date-from" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter-date-to" class="form-label">تا تاریخ</label>
                    <input type="date" id="filter-date-to" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter-search" class="form-label">جستجو</label>
                    <div class="input-group">
                        <input type="text" id="filter-search" name="search" class="form-control" placeholder="شناسه، کاربر، مبلغ..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment History Card -->
    <div class="card payment-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">تاریخچه پرداخت‌ها</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i>چاپ
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="exportToExcel()">
                    <i class="bx bx-export me-1"></i>خروجی اکسل
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center">شناسه</th>
                        <th>تاریخ</th>
                        <th>کاربر</th>
                        <th>مبلغ (ریال)</th>
                        <th>وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($payments as $payment)
                        <tr>
                            <td class="text-center">{{ $payment->id }}</td>
                            <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                @if($payment->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span>{{ $payment->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">کاربر نامشخص</span>
                                @endif
                            </td>
                            <td>{{ number_format($payment->amount) }}</td>
                            <td>
                                @if($payment->status == 'verified')
                                    <span class="badge bg-success status-badge">{{ $payment->status_label }}</span>
                                @elseif($payment->status == 'pending')
                                    <span class="badge bg-warning status-badge">{{ $payment->status_label }}</span>
                                @elseif($payment->status == 'failed' || $payment->status == 'canceled')
                                    <span class="badge bg-danger status-badge">{{ $payment->status_label }}</span>
                                @else
                                    <span class="badge bg-info status-badge">{{ $payment->status_label }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-block">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-detail me-1"></i>جزئیات
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-center">
                                    <i class="bx bx-credit-card-front fs-1 text-muted mb-2"></i>
                                    <p class="mb-0">هیچ پرداختی یافت نشد</p>
                                </div>
                            </td>
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
@endsection

@section('page-js')
<script>
    function exportToExcel() {
        // This is a placeholder for Excel export functionality
        // You would implement this with a proper Excel export library or endpoint
        alert('این قابلیت در حال توسعه می‌باشد.');
    }
</script>
@endsection 