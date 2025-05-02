@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Transfer to Another User') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.transfer') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Recipient Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="form-text">{{ __('Enter the email address of the user you want to transfer to.') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input id="amount" type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>{{ __('Current Balance') }}:</strong> ${{ number_format(Auth::user()->wallet->balance, 2) }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-info">{{ __('Transfer') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 