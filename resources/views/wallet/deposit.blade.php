@extends('layouts.app')

@section('title', 'شارژ کیف پول')

@section('page-css')
<style>
    .suggestion-btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .amount-display {
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: 10px;
    }
    .currency-toggle {
        cursor: pointer;
        color: #5d87ff;
        text-decoration: underline;
    }
</style>
@endsection

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

                    <form method="POST" action="{{ route('wallet.deposit') }}" id="depositForm">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">مبلغ</label>
                            <div class="input-group">
                                <input id="amount" type="number" min="10000" class="form-control @error('amount') is-invalid @enderror" 
                                       name="amount" value="{{ old('amount', 100000) }}" required autofocus>
                                <span class="input-group-text">ریال</span>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-text">حداقل مبلغ شارژ: 10,000 ریال (معادل 1,000 تومان)</div>
                            
                            <div class="amount-display mt-2">
                                معادل <span id="toman-amount">10,000</span> تومان
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">مبالغ پیشنهادی:</label>
                            <div>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="100000">100,000 ریال</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="200000">200,000 ریال</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="500000">500,000 ریال</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="1000000">1,000,000 ریال</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="2000000">2,000,000 ریال</button>
                            </div>
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
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-credit-card me-1"></i>پرداخت <span id="payment-amount-display">100,000 ریال</span>
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
                        <li>مبلغ مورد نظر خود را به ریال وارد کنید یا یکی از مبالغ پیشنهادی را انتخاب نمایید.</li>
                        <li>روی دکمه «پرداخت» کلیک کنید.</li>
                        <li>به درگاه پرداخت زیبال منتقل خواهید شد.</li>
                        <li>پس از تکمیل پرداخت، به سایت بازگردانده می‌شوید.</li>
                        <li>در صورت موفقیت آمیز بودن پرداخت، مبلغ به کیف پول شما اضافه خواهد شد.</li>
                    </ol>
                    <div class="alert alert-warning mt-3">
                        <i class="bx bx-error-circle me-1"></i>
                        <strong>توجه:</strong> در صورت بروز هرگونه مشکل در فرآیند پرداخت، با پشتیبانی تماس بگیرید.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const amountInput = document.getElementById('amount');
        const tomanAmount = document.getElementById('toman-amount');
        const paymentAmountDisplay = document.getElementById('payment-amount-display');
        const suggestionButtons = document.querySelectorAll('.suggestion-btn');
        
        // Update Toman amount on input change
        function updateTomanAmount() {
            const rialAmount = parseFloat(amountInput.value) || 0;
            const tomanValue = Math.floor(rialAmount / 10).toLocaleString('fa-IR');
            tomanAmount.textContent = tomanValue;
            
            // Also update the payment button amount
            paymentAmountDisplay.textContent = rialAmount.toLocaleString('fa-IR') + ' ریال';
        }
        
        // Set initial values
        updateTomanAmount();
        
        // Add event listener for amount changes
        amountInput.addEventListener('input', updateTomanAmount);
        
        // Add event listeners for suggestion buttons
        suggestionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                amountInput.value = amount;
                updateTomanAmount();
                
                // Remove active class from all buttons
                suggestionButtons.forEach(btn => btn.classList.remove('btn-primary'));
                suggestionButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
                
                // Add active class to clicked button
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            });
        });
        
        // Form validation
        document.getElementById('depositForm').addEventListener('submit', function(e) {
            const amount = parseFloat(amountInput.value);
            if (isNaN(amount) || amount < 10000) {
                e.preventDefault();
                alert('لطفا مبلغی بیشتر از 10,000 ریال وارد کنید.');
                amountInput.focus();
            }
        });
    });
</script>
@endsection 