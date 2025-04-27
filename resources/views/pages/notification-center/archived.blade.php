@extends('layouts.app')

@section('title', 'Archived Notifications')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
  <script src="{{ asset('assets/js/ui-toasts.js') }}"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Handle restore notification
      document.querySelectorAll('.restore-notification').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const notificationId = this.dataset.id;
          // Here would be the fetch call to restore a notification
          // For now we just remove it from the UI
          const notificationItem = this.closest('.notification-item');
          notificationItem.remove();
          
          // Show toast notification
          const toastPlacement = document.querySelector('#toastPlacement');
          toastPlacement.innerHTML = `
            <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="toast-header">
                <i class="bx bx-check me-2"></i>
                <div class="me-auto fw-semibold">Success</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
              <div class="toast-body">Notification restored successfully.</div>
            </div>
          `;
        });
      });
      
      // Handle delete notification
      document.querySelectorAll('.delete-notification').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const notificationId = this.dataset.id;
          
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
              confirmButton: 'btn btn-danger me-3',
              cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
          }).then((result) => {
            if (result.isConfirmed) {
              // Here would be the fetch call to delete a notification
              // For now we just remove it from the UI
              const notificationItem = this.closest('.notification-item');
              notificationItem.remove();
              
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Notification has been deleted.',
                customClass: {
                  confirmButton: 'btn btn-success'
                },
                buttonsStyling: false
              });
            }
          });
        });
      });
      
      // Handle search
      const searchInput = document.querySelector('#search-archived');
      searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.notification-item').forEach(item => {
          const title = item.querySelector('.notification-title').textContent.toLowerCase();
          const description = item.querySelector('.notification-description').textContent.toLowerCase();
          if (title.includes(searchTerm) || description.includes(searchTerm)) {
            item.style.display = 'flex';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  </script>
@endsection

@section('content')
  <!-- Toast container -->
  <div class="bs-toast toast-placement-ex m-2 position-fixed top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div id="toastPlacement"></div>
  </div>

  <div class="row">
    <!-- Header Section -->
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="fw-bold py-3 mb-0">
              <i class="bx bx-archive me-2"></i>
              Archived Notifications
            </h4>
          </div>
          <div class="d-flex align-items-center">
            <div class="input-group input-group-merge me-2" style="width: 250px;">
              <span class="input-group-text"><i class="bx bx-search"></i></span>
              <input type="text" id="search-archived" class="form-control" placeholder="Search archived..." />
            </div>
            <a href="{{ route('notification-center.index') }}" class="btn btn-primary">
              <i class="bx bx-bell me-1"></i>
              Back to Notifications
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Archived Notifications List -->
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0">Archived Notifications</h5>
          <div class="text-muted small">
            <i class="bx bx-info-circle me-1"></i>
            Items older than 90 days will be automatically deleted
          </div>
        </div>
        <div class="card-body">
          <!-- Notification Items -->
          <div class="notification-list">
            <!-- Archived Notification 1 -->
            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded" data-category="system">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-secondary">
                  <i class="bx bx-server"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">Server Maintenance Completed</span>
                  <span class="text-muted small">Aug 15, 2023</span>
                </div>
                <p class="notification-description mb-2">Scheduled server maintenance has been completed successfully. All services are now operating normally.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-secondary me-2">Archived</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 restore-notification" data-id="101">
                      <i class="bx bx-revision"></i> Restore
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-notification" data-id="101">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Archived Notification 2 -->
            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded" data-category="user">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-secondary">
                  <i class="bx bx-user-check"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">User Onboarding Complete</span>
                  <span class="text-muted small">Aug 10, 2023</span>
                </div>
                <p class="notification-description mb-2">All new users have completed the onboarding process. 12 users successfully onboarded.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-secondary me-2">Archived</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 restore-notification" data-id="102">
                      <i class="bx bx-revision"></i> Restore
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-notification" data-id="102">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Archived Notification 3 -->
            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded" data-category="payment">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-secondary">
                  <i class="bx bx-credit-card"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">Payment Processing Issue Resolved</span>
                  <span class="text-muted small">Jul 28, 2023</span>
                </div>
                <p class="notification-description mb-2">The payment gateway issue has been resolved. All pending transactions have been processed successfully.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-secondary me-2">Archived</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 restore-notification" data-id="103">
                      <i class="bx bx-revision"></i> Restore
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-notification" data-id="103">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Archived Notification 4 -->
            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded" data-category="system">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-secondary">
                  <i class="bx bx-shield"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">Security Audit Completed</span>
                  <span class="text-muted small">Jul 15, 2023</span>
                </div>
                <p class="notification-description mb-2">The quarterly security audit has been completed. All systems passed with no critical vulnerabilities detected.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-secondary me-2">Archived</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 restore-notification" data-id="104">
                      <i class="bx bx-revision"></i> Restore
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-notification" data-id="104">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- No Notifications Message (hidden by default) -->
            <div id="no-notifications" class="text-center py-5 d-none">
              <img src="{{ asset('assets/img/illustrations/empty-box.png') }}" class="mb-3" width="150" alt="No notifications">
              <h6 class="text-muted">No archived notifications found</h6>
              <p>All archived notifications will appear here</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection 