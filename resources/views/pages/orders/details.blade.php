@extends('layouts.app')

@section('title', 'Order Details')

@section('page-css')
<style>
  .timeline {
    margin-top: 1rem;
    margin-bottom: 1rem;
    padding-left: 1.5rem;
    border-left: 2px solid #d9dee3;
  }
  .timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
  }
  .timeline-item:last-child {
    padding-bottom: 0;
  }
  .timeline-item:before {
    content: '';
    position: absolute;
    left: -2.19rem;
    top: 0;
    height: 12px;
    width: 12px;
    border-radius: 50%;
    background-color: #fff;
    border: 2px solid #696cff;
  }
  .timeline-item-current:before {
    background-color: #696cff;
  }
  .timeline-item-future:before {
    border-color: #d9dee3;
  }
  .timeline-item-header {
    font-weight: 600;
    margin-bottom: 0.2rem;
  }
  .timeline-item-text {
    color: #697a8d;
    margin-bottom: 0;
  }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">eCommerce / Orders /</span> Order Details
  </h4>

  <div class="row">
    <!-- Order Header -->
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Order #{{ $order->order_number }}</h5>
          <div class="d-flex">
            <span class="badge bg-label-{{ $order->payment_status == 'Paid' ? 'success' : ($order->payment_status == 'Pending' ? 'warning' : 'danger') }} me-2">
              {{ $order->payment_status }}
            </span>
            <span class="badge bg-label-{{ $order->delivery_status == 'Delivered' ? 'success' : 'info' }}">
              {{ $order->delivery_status }}
            </span>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <div>
              <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('M d, Y, H:i') }}</p>
              <p class="mb-1"><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
            </div>
            <div>
              <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
                <i class="bx bx-trash me-1"></i> Delete Order
              </button>
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  Actions
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <h6 class="dropdown-header">Payment Status</h6>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="payment_status" value="Pending">
                      <button type="submit" class="dropdown-item {{ $order->payment_status == 'Pending' ? 'active' : '' }}">
                        Pending
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="payment_status" value="Paid">
                      <button type="submit" class="dropdown-item {{ $order->payment_status == 'Paid' ? 'active' : '' }}">
                        Paid
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="payment_status" value="Failed">
                      <button type="submit" class="dropdown-item {{ $order->payment_status == 'Failed' ? 'active' : '' }}">
                        Failed
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="payment_status" value="Refunded">
                      <button type="submit" class="dropdown-item {{ $order->payment_status == 'Refunded' ? 'active' : '' }}">
                        Refunded
                      </button>
                    </form>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <h6 class="dropdown-header">Delivery Status</h6>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="delivery_status" value="Pending">
                      <button type="submit" class="dropdown-item {{ $order->delivery_status == 'Pending' ? 'active' : '' }}">
                        Pending
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="delivery_status" value="Processing">
                      <button type="submit" class="dropdown-item {{ $order->delivery_status == 'Processing' ? 'active' : '' }}">
                        Processing
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="delivery_status" value="Shipped">
                      <button type="submit" class="dropdown-item {{ $order->delivery_status == 'Shipped' ? 'active' : '' }}">
                        Shipped
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="delivery_status" value="Out for Delivery">
                      <button type="submit" class="dropdown-item {{ $order->delivery_status == 'Out for Delivery' ? 'active' : '' }}">
                        Out for Delivery
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="delivery_status" value="Delivered">
                      <button type="submit" class="dropdown-item {{ $order->delivery_status == 'Delivered' ? 'active' : '' }}">
                        Delivered
                      </button>
                    </form>
                  </li>
                  <li>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                      @csrf
                      <input type="hidden" name="delivery_status" value="Cancelled">
                      <button type="submit" class="dropdown-item {{ $order->delivery_status == 'Cancelled' ? 'active' : '' }}">
                        Cancelled
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details and Summary -->
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Order Summary</h5>
        </div>
        <div class="card-body">
          <!-- In a real implementation, this would list actual order items -->
          <div class="table-responsive">
            <table class="table table-borderless">
              <thead class="border-bottom">
                <tr>
                  <th>Item</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-lg me-2">
                        <span class="avatar-initial rounded bg-label-primary">
                          <i class="bx bx-package"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="mb-0">Sample Product</h6>
                        <small class="text-muted">SKU: PRD-001</small>
                      </div>
                    </div>
                  </td>
                  <td>${{ number_format($order->subtotal_amount / 2, 2) }}</td>
                  <td>2</td>
                  <td class="text-end">${{ number_format($order->subtotal_amount, 2) }}</td>
                </tr>
              </tbody>
              <tfoot class="border-top">
                <tr>
                  <td colspan="3" class="text-end fw-medium">Subtotal:</td>
                  <td class="text-end">${{ number_format($order->subtotal_amount, 2) }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-medium">Tax:</td>
                  <td class="text-end">${{ number_format($order->tax_amount, 2) }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-medium">Discount:</td>
                  <td class="text-end">${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Total:</td>
                  <td class="text-end fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Details and Shipping -->
    <div class="col-md-4">
      <!-- Customer Info -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Customer Details</h5>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="avatar avatar-md me-2">
              <span class="avatar-initial rounded-circle bg-label-primary">
                {{ strtoupper(substr($order->user->name, 0, 1)) }}
              </span>
            </div>
            <div>
              <h6 class="mb-0">{{ $order->user->name }}</h6>
              <small class="text-muted">Customer ID: #{{ $order->user->id }}</small>
            </div>
          </div>
          <div class="mb-3">
            <h6 class="mb-1">Contact Info</h6>
            <p class="mb-1">Email: {{ $order->user->email }}</p>
            <p class="mb-0">Phone: {{ $order->user->phone ?? 'N/A' }}</p>
          </div>
        </div>
      </div>

      <!-- Shipping Activity -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Shipping Activity</h5>
        </div>
        <div class="card-body">
          <div class="timeline">
            <div class="timeline-item {{ in_array($order->delivery_status, ['Pending', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered']) ? 'timeline-item-current' : 'timeline-item-future' }}">
              <h6 class="timeline-item-header">Order was placed</h6>
              <p class="timeline-item-text">Your order has been placed successfully</p>
            </div>
            <div class="timeline-item {{ in_array($order->delivery_status, ['Processing', 'Shipped', 'Out for Delivery', 'Delivered']) ? 'timeline-item-current' : 'timeline-item-future' }}">
              <h6 class="timeline-item-header">Processing</h6>
              <p class="timeline-item-text">Order is being processed</p>
            </div>
            <div class="timeline-item {{ in_array($order->delivery_status, ['Shipped', 'Out for Delivery', 'Delivered']) ? 'timeline-item-current' : 'timeline-item-future' }}">
              <h6 class="timeline-item-header">Shipped</h6>
              <p class="timeline-item-text">Item has been shipped</p>
            </div>
            <div class="timeline-item {{ in_array($order->delivery_status, ['Out for Delivery', 'Delivered']) ? 'timeline-item-current' : 'timeline-item-future' }}">
              <h6 class="timeline-item-header">Out for Delivery</h6>
              <p class="timeline-item-text">Package is out for delivery</p>
            </div>
            <div class="timeline-item {{ $order->delivery_status == 'Delivered' ? 'timeline-item-current' : 'timeline-item-future' }}">
              <h6 class="timeline-item-header">Delivery</h6>
              <p class="timeline-item-text">Package has been delivered</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Order Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel5">Delete Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-3">
            <p>Are you sure you want to delete order #{{ $order->order_number }}? This action cannot be undone.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('orders.destroy', $order->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection 