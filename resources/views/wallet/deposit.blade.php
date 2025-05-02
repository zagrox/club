@extends('layouts.app')

@section('title', 'شارژ کیف پول')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">کیف پول /</span> شارژ کیف پول
    </h4>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">افزایش موجودی کیف پول</h5>
                    <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>بازگشت به کیف پول
                    </a>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.deposit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">مبلغ (ریال)</label>
                            <div class="input-group">
                                <input id="amount" type="number" min="10000" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', 100000) }}" required autofocus>
                                <span class="input-group-text">ریال</span>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-text">حداقل مبلغ: 10,000 ریال (100 تومان)</div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <i class="bx bx-info-circle me-1"></i>
                                    پس از کلیک روی دکمه پرداخت، به درگاه پرداخت زیبال منتقل خواهید شد.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">انصراف</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-credit-card me-1"></i>پرداخت با زیبال
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">راهنمای افزایش موجودی</h5>
                </div>
                <div class="card-body">
                    <p>برای افزایش موجودی کیف پول، مراحل زیر را دنبال کنید:</p>
                    <ol>
                        <li>مبلغ مورد نظر خود را به ریال وارد کنید.</li>
                        <li>روی دکمه «پرداخت با زیبال» کلیک کنید.</li>
                        <li>به درگاه پرداخت زیبال منتقل خواهید شد.</li>
                        <li>پس از تکمیل پرداخت، به سایت بازگردانده می‌شوید.</li>
                        <li>در صورت موفقیت آمیز بودن پرداخت، مبلغ به کیف پول شما اضافه خواهد شد.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 