@extends('layouts.app')

@section('title', trans('messages.Withdraw from Wallet'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('messages.Withdraw from Wallet') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.withdraw') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ trans('messages.Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ trans('messages.currency_unit') }}</span>
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
                                <strong>{{ trans('messages.Current Balance') }}:</strong> {{ trans('messages.currency_unit') }}{{ number_format(Auth::user()->wallet->balance, 2) }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">{{ trans('messages.Cancel') }}</a>
                            <button type="submit" class="btn btn-warning">{{ trans('messages.Withdraw') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 