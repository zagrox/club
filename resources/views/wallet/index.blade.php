@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Wallet') }}</div>

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

                    <div class="mb-4">
                        <h4>{{ __('Balance') }}</h4>
                        <h2 class="text-primary">{{ number_format($balance, 2) }}</h2>
                    </div>

                    <div class="d-flex mb-4">
                        <a href="{{ route('wallet.showDepositForm') }}" class="btn btn-success me-2">{{ __('Deposit') }}</a>
                        <a href="{{ route('wallet.showWithdrawForm') }}" class="btn btn-warning me-2">{{ __('Withdraw') }}</a>
                        <a href="{{ route('wallet.showTransferForm') }}" class="btn btn-info">{{ __('Transfer') }}</a>
                    </div>

                    <div class="mb-4">
                        <h4>{{ __('Recent Transactions') }}</h4>
                        @if ($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                <td>
                                                    @if ($transaction->type === 'deposit')
                                                        <span class="badge bg-success">{{ __('Deposit') }}</span>
                                                    @elseif ($transaction->type === 'withdraw')
                                                        <span class="badge bg-warning">{{ __('Withdraw') }}</span>
                                                    @elseif ($transaction->type === 'transfer')
                                                        <span class="badge bg-info">{{ __('Transfer In') }}</span>
                                                    @elseif ($transaction->type === 'withdraw_transfer')
                                                        <span class="badge bg-secondary">{{ __('Transfer Out') }}</span>
                                                    @else
                                                        <span class="badge bg-primary">{{ $transaction->type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $transactions->links() }}
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('wallet.transactions') }}" class="btn btn-outline-primary">{{ __('View All Transactions') }}</a>
                            </div>
                        @else
                            <div class="alert alert-info">{{ __('No transactions yet.') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 