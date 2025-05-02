@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Deposit to Wallet') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.deposit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input id="amount" type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autofocus>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                {{ __('This is a simulation. In a real application, this would connect to a payment processor.') }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-success">{{ __('Deposit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 