@extends('layouts.app')

@section('title', trans('messages.Transfer to Another User'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('messages.Transfer to Another User') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.transfer') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="recipient_email" class="form-label">{{ trans('messages.Recipient Email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input id="recipient_email" type="email" class="form-control @error('recipient_email') is-invalid @enderror" name="recipient_email" value="{{ old('recipient_email') }}" required>
                                @error('recipient_email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="text-muted">{{ trans('messages.Enter the email address of the user you want to transfer to.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ trans('messages.Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ trans('messages.currency_unit') }}</span>
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
                                <strong>{{ trans('messages.Current Balance') }}:</strong> {{ trans('messages.currency_unit') }}{{ number_format(Auth::user()->wallet->balance, 2) }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">{{ trans('messages.Cancel') }}</a>
                            <button type="submit" class="btn btn-info">{{ trans('messages.Transfer') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 