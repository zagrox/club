@extends('layouts.app')

@section('title', trans('messages.Charge Wallet'))

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
        <span class="text-muted fw-light">{{ trans('messages.my_wallet') }} /</span> {{ trans('messages.Charge Wallet') }}
    </h4>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ trans('messages.Increase Wallet Balance') }}</h5>
                    <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>{{ trans('messages.Back to Wallet') }}
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
                            <label for="amount" class="form-label">{{ trans('messages.Amount') }}</label>
                            <div class="input-group">
                                <input id="amount" type="number" min="10000" class="form-control @error('amount') is-invalid @enderror" 
                                       name="amount" value="{{ old('amount', 100000) }}" required autofocus>
                                <span class="input-group-text">{{ trans('messages.currency_code') }}</span>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-text">{{ trans('messages.Minimum charge amount') }}: 10,000 {{ trans('messages.currency_code') }} ({{ trans('messages.Equal to') }} 1,000 {{ trans('messages.currency_unit') }})</div>
                            
                            <div class="amount-display mt-2">
                                {{ trans('messages.Equal to') }} <span id="toman-amount">10,000</span> {{ trans('messages.currency_unit') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('messages.Suggested amounts') }}:</label>
                            <div>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="100000">100,000 {{ trans('messages.currency_code') }}</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="200000">200,000 {{ trans('messages.currency_code') }}</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="500000">500,000 {{ trans('messages.currency_code') }}</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="1000000">1,000,000 {{ trans('messages.currency_code') }}</button>
                                <button type="button" class="btn btn-outline-primary suggestion-btn" data-amount="2000000">2,000,000 {{ trans('messages.currency_code') }}</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <i class="bx bx-info-circle me-1"></i>
                                    {{ trans('messages.payment_gateway_info') }}
                                </p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">{{ trans('messages.Cancel') }}</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-credit-card me-1"></i>{{ trans('messages.Pay') }} <span id="payment-amount-display">100,000 {{ trans('messages.currency_code') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ trans('messages.How to Increase Balance') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ trans('messages.To increase your wallet balance, follow these steps') }}:</p>
                    <ol>
                        <li>{{ trans('messages.Enter the desired amount or select one of the suggested amounts') }}.</li>
                        <li>{{ trans('messages.Click on the "Pay" button') }}.</li>
                        <li>{{ trans('messages.You will be redirected to the payment gateway') }}.</li>
                        <li>{{ trans('messages.After completing the payment, you will be redirected back to the site') }}.</li>
                        <li>{{ trans('messages.If the payment is successful, the amount will be added to your wallet') }}.</li>
                    </ol>
                    <div class="alert alert-warning mt-3">
                        <i class="bx bx-error-circle me-1"></i>
                        <strong>{{ trans('messages.Note') }}:</strong> {{ trans('messages.If you encounter any problem during the payment process, please contact support') }}.
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
            paymentAmountDisplay.textContent = rialAmount.toLocaleString('fa-IR') + ' {{ trans('messages.currency_code') }}';
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
                alert('{{ trans('messages.Please enter an amount greater than 10,000') }}');
                amountInput.focus();
            }
        });
    });
</script>
@endsection 