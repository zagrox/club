@extends('layouts.app')

@section('title', 'Orders List')

@section('page-css')
<style>
  .card-statistics .card-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .card-statistics h5 {
    font-size: 1.125rem;
    margin-bottom: 0;
  }
  .card-statistics .card-info {
    display: flex;
    flex-direction: column;
  }
  .order-search {
    width: 100%;
    max-width: 250px;
  }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">eCommerce /</span> Orders
  </h4>

  <!-- Orders Statistics -->
  <div class="row">
    <!-- Pending Orders -->
    <div class="col-md-3 col-sm-6 col-12 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="card-info">
              <h5 class="mb-0">{{ $pendingCount }}</h5>
              <small>Pending Payment</small>
            </div>
            <div class="card-icon bg-label-warning">
              <i class="bx bx-time-five text-warning"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Completed Orders -->
    <div class="col-md-3 col-sm-6 col-12 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="card-info">
              <h5 class="mb-0">{{ $completedCount }}</h5>
              <small>Completed</small>
            </div>
            <div class="card-icon bg-label-success">
              <i class="bx bx-check text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Refunded Orders -->
    <div class="col-md-3 col-sm-6 col-12 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="card-info">
              <h5 class="mb-0">{{ $refundedCount }}</h5>
              <small>Refunded</small>
            </div>
            <div class="card-icon bg-label-info">
              <i class="bx bx-revision text-info"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Failed Orders -->
    <div class="col-md-3 col-sm-6 col-12 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="card-info">
              <h5 class="mb-0">{{ $failedCount }}</h5>
              <small>Failed</small>
            </div>
            <div class="card-icon bg-label-danger">
              <i class="bx bx-error-circle text-danger"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Orders List -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Orders</h5>
    </div>
    <div class="card-datatable table-responsive">
      <div class="p-3 pb-0 d-flex justify-content-between align-items-center flex-wrap">
        <div class="order-search me-2">
          <form>
            <div class="input-group input-group-merge">
              <span class="input-group-text" id="basic-addon-search"><i class="bx bx-search"></i></span>
              <input type="text" class="form-control" placeholder="Search Order" aria-label="Search Order" aria-describedby="basic-addon-search">
            </div>
          </form>
        </div>
        <div class="d-flex align-items-center">
          <div class="me-2">
            <select class="form-select form-select-sm">
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
          <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bx bx-export me-1"></i> Export
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="javascript:void(0);">Excel</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);">PDF</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);">Print</a></li>
            </ul>
          </div>
        </div>
      </div>
      <table class="table border-top">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label" for="selectAll"></label>
              </div>
            </th>
            <th>ORDER</th>
            <th>DATE</th>
            <th>CUSTOMER</th>
            <th>PAYMENT</th>
            <th>STATUS</th>
            <th>METHOD</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $order)
          <tr>
            <td class="text-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="order-{{ $order->id }}">
                <label class="form-check-label" for="order-{{ $order->id }}"></label>
              </div>
            </td>
            <td>
              <a href="{{ route('orders.details', $order->id) }}">#{{ $order->order_number }}</a>
            </td>
            <td>{{ $order->created_at->format('M d, Y, H:i') }}</td>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-2">
                  <span class="avatar-initial rounded-circle bg-label-primary">
                    {{ strtoupper(substr($order->user->name, 0, 1)) }}
                  </span>
                </div>
                <div>
                  <strong>{{ $order->user->name }}</strong>
                  <small class="d-block text-muted">{{ $order->user->email }}</small>
                </div>
              </div>
            </td>
            <td>{!! $order->payment_badge !!}</td>
            <td>{!! $order->delivery_badge !!}</td>
            <td>
              @if($order->payment_method)
                <div class="d-flex align-items-center">
                  @if(strtolower($order->payment_method) == 'mastercard')
                    <i class="bx bxl-mastercard text-danger me-1 fs-5"></i>
                  @elseif(strtolower($order->payment_method) == 'visa')
                    <i class="bx bxl-visa text-primary me-1 fs-5"></i>
                  @elseif(strtolower($order->payment_method) == 'paypal')
                    <i class="bx bxl-paypal text-primary me-1 fs-5"></i>
                  @else
                    <i class="bx bx-credit-card me-1 fs-5"></i>
                  @endif
                  <span>{{ str_pad(substr($order->payment_method, -4), 8, '*', STR_PAD_LEFT) }}</span>
                </div>
              @else
                N/A
              @endif
            </td>
            <td>
              <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="{{ route('orders.details', $order->id) }}">
                    <i class="bx bx-edit-alt me-1"></i> View Details
                  </a>
                  <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $order->id }}').submit();">
                    <i class="bx bx-trash me-1"></i> Delete
                  </a>
                  <form id="delete-form-{{ $order->id }}" action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                  </form>
                </div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="pagination-container p-3">
        {{ $orders->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  document.getElementById('selectAll').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('tbody .form-check-input');
    for (let checkbox of checkboxes) {
      checkbox.checked = this.checked;
    }
  });
</script>
@endsection 