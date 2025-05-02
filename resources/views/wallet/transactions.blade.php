@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Transaction History') }}</span>
                    <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back to Wallet') }}</a>
                </div>

                <div class="card-body">
                    @if ($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
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
                                            <td>${{ number_format($transaction->amount, 2) }}</td>
                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if ($transaction->confirmed)
                                                    <span class="badge bg-success">{{ __('Confirmed') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">{{ __('No transactions found.') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 