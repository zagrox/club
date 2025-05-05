@extends('layouts.app')

@section('title', 'Notification Center')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
  <script src="{{ asset('assets/js/ui-toasts.js') }}"></script>
  <script>
    // Initialize notification stats chart
    document.addEventListener("DOMContentLoaded", function() {
      const options = {
        series: [75, 25],
        labels: ['Read', 'Unread'],
        chart: {
          type: 'donut',
          height: 200,
          toolbar: { show: false }
        },
        legend: { show: false },
        dataLabels: { enabled: false },
        colors: ['#696cff', '#03c3ec']
      };
      
      const notificationStatsChart = new ApexCharts(document.querySelector('#notificationStatsChart'), options);
      notificationStatsChart.render();
      
      // Initialize notification trends chart
      const trendsOptions = {
        series: [{
          name: 'Notifications',
          data: [28, 45, 35, 50, 32, 55, 23]
        }],
        chart: {
          height: 200,
          type: 'area',
          toolbar: { show: false },
          sparkline: { enabled: true }
        },
        colors: ['#696cff'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
          type: 'category',
          categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        },
        tooltip: { x: { show: false } }
      };
      
      const trendsChart = new ApexCharts(document.querySelector('#notificationTrendsChart'), options);
      trendsChart.render();
      
      // Handle mark as read
      document.querySelectorAll('.mark-as-read').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const notificationId = this.dataset.id;
          fetch('{{ route("notification-center.mark-as-read") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: notificationId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const notificationItem = this.closest('.notification-item');
              notificationItem.classList.remove('unread');
              notificationItem.classList.add('read');
              this.style.display = 'none';
            }
          });
        });
      });
      
      // Handle dismiss notification
      document.querySelectorAll('.dismiss-notification').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const notificationId = this.dataset.id;
          fetch('{{ route("notification-center.dismiss") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: notificationId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const notificationItem = this.closest('.notification-item');
              notificationItem.remove();
              // Show toast notification
              const toastContainer = document.querySelector('.toast-container');
              const toastPlacement = document.querySelector('#toastPlacement');
              toastPlacement.innerHTML = `
                <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                  <div class="toast-header">
                    <i class="bx bx-check me-2"></i>
                    <div class="me-auto fw-semibold">Success</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                  </div>
                  <div class="toast-body">Notification dismissed successfully.</div>
                </div>
              `;
            }
          });
        });
      });
      
      // Handle archive notification
      document.querySelectorAll('.archive-notification').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const notificationId = this.dataset.id;
          fetch('{{ route("notification-center.archive") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: notificationId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const notificationItem = this.closest('.notification-item');
              notificationItem.remove();
              // Show toast notification
              const toastContainer = document.querySelector('.toast-container');
              const toastPlacement = document.querySelector('#toastPlacement');
              toastPlacement.innerHTML = `
                <div class="bs-toast toast fade show bg-primary" role="alert" aria-live="assertive" aria-atomic="true">
                  <div class="toast-header">
                    <i class="bx bx-archive me-2"></i>
                    <div class="me-auto fw-semibold">Info</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                  </div>
                  <div class="toast-body">Notification archived successfully.</div>
                </div>
              `;
            }
          });
        });
      });
      
      // Handle search
      const searchInput = document.querySelector('#search-notifications');
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
      
      // Handle filters
      document.querySelectorAll('.notification-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
          e.preventDefault();
          const filterType = this.dataset.filter;
          
          // Update active filter
          document.querySelectorAll('.notification-filter').forEach(f => {
            f.classList.remove('active');
          });
          this.classList.add('active');
          
          // Filter notifications
          document.querySelectorAll('.notification-item').forEach(item => {
            if (filterType === 'all') {
              item.style.display = 'flex';
            } else if (filterType === 'unread' && item.classList.contains('unread')) {
              item.style.display = 'flex';
            } else if (filterType === 'read' && !item.classList.contains('unread')) {
              item.style.display = 'flex';
            } else if (filterType === item.dataset.category) {
              item.style.display = 'flex';
            } else {
              item.style.display = 'none';
            }
          });
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
              <i class="bx bx-bell me-2"></i>
              Notification Center
            </h4>
          </div>
          <div class="d-flex align-items-center">
            <div class="input-group input-group-merge me-2" style="width: 250px;">
              <span class="input-group-text"><i class="bx bx-search"></i></span>
              <input type="text" id="search-notifications" class="form-control" placeholder="Search notifications..." />
            </div>
            <div class="btn-group me-2">
              <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-filter-alt me-1"></i>
                Filter
              </button>
              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item notification-filter active" href="javascript:void(0);" data-filter="all">
                    All Notifications
                  </a>
                </li>
                <li>
                  <a class="dropdown-item notification-filter" href="javascript:void(0);" data-filter="unread">
                    Unread
                  </a>
                </li>
                <li>
                  <a class="dropdown-item notification-filter" href="javascript:void(0);" data-filter="read">
                    Read
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item notification-filter" href="javascript:void(0);" data-filter="system">
                    System
                  </a>
                </li>
                <li>
                  <a class="dropdown-item notification-filter" href="javascript:void(0);" data-filter="user">
                    User
                  </a>
                </li>
                <li>
                  <a class="dropdown-item notification-filter" href="javascript:void(0);" data-filter="payment">
                    Payment
                  </a>
                </li>
              </ul>
            </div>
            <a href="{{ route('notification-center.create') }}" class="btn btn-success me-2">
              <i class="bx bx-plus me-1"></i>
              Add New
            </a>
            <a href="{{ route('notification-center.settings') }}" class="btn btn-primary">
              <i class="bx bx-cog me-1"></i>
              Settings
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main Section -->
    <div class="col-md-8 col-12 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0">Notification Feed</h5>
          <a href="{{ route('notification-center.archived') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-archive me-1"></i> Archived
          </a>
        </div>
        <div class="card-body">
          <!-- Notification Items -->
          <div class="notification-list">
            <!-- Unread High Priority Notification -->
            <div class="notification-item unread d-flex align-items-start p-3 mb-3 border rounded" data-category="system">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-danger">
                  <i class="bx bx-error"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">System Alert</span>
                  <span class="text-muted small">5 min ago</span>
                </div>
                <p class="notification-description mb-2">Database server at 90% capacity. Immediate attention required.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-danger me-2">High Priority</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 mark-as-read" data-id="1">
                      <i class="bx bx-check"></i> Mark as Read
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-1 dismiss-notification" data-id="1">
                      <i class="bx bx-x"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info archive-notification" data-id="1">
                      <i class="bx bx-archive"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Unread Medium Priority Notification -->
            <div class="notification-item unread d-flex align-items-start p-3 mb-3 border rounded" data-category="user">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="bx bx-user"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">New User Registration</span>
                  <span class="text-muted small">1 hour ago</span>
                </div>
                <p class="notification-description mb-2">5 new users have registered today. Review and approve pending accounts.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-warning me-2">Medium Priority</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 mark-as-read" data-id="2">
                      <i class="bx bx-check"></i> Mark as Read
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-1 dismiss-notification" data-id="2">
                      <i class="bx bx-x"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info archive-notification" data-id="2">
                      <i class="bx bx-archive"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Read Low Priority Notification -->
            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded" data-category="payment">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="bx bx-dollar"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">Payment Reconciliation</span>
                  <span class="text-muted small">Yesterday</span>
                </div>
                <p class="notification-description mb-2">Monthly payment reconciliation report is now available. All transactions processed successfully.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-success me-2">Low Priority</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-secondary me-1 dismiss-notification" data-id="3">
                      <i class="bx bx-x"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info archive-notification" data-id="3">
                      <i class="bx bx-archive"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Unread Medium Priority Notification -->
            <div class="notification-item unread d-flex align-items-start p-3 mb-3 border rounded" data-category="system">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-info">
                  <i class="bx bx-cloud-download"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">System Update Available</span>
                  <span class="text-muted small">2 days ago</span>
                </div>
                <p class="notification-description mb-2">A new system update (v2.3.1) is available. Includes security patches and performance improvements.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-warning me-2">Medium Priority</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-primary me-1 mark-as-read" data-id="4">
                      <i class="bx bx-check"></i> Mark as Read
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-1 dismiss-notification" data-id="4">
                      <i class="bx bx-x"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info archive-notification" data-id="4">
                      <i class="bx bx-archive"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Read Low Priority Notification -->
            <div class="notification-item d-flex align-items-start p-3 mb-3 border rounded" data-category="user">
              <div class="notification-icon me-3">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="bx bx-calendar"></i>
                </span>
              </div>
              <div class="notification-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="notification-title fw-semibold">Weekly Maintenance Scheduled</span>
                  <span class="text-muted small">3 days ago</span>
                </div>
                <p class="notification-description mb-2">Regular system maintenance scheduled for Sunday, 2:00 AM - 4:00 AM UTC. Plan accordingly.</p>
                <div class="notification-meta d-flex align-items-center">
                  <span class="badge bg-success me-2">Low Priority</span>
                  <div class="notification-actions ms-auto">
                    <button class="btn btn-sm btn-outline-secondary me-1 dismiss-notification" data-id="5">
                      <i class="bx bx-x"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info archive-notification" data-id="5">
                      <i class="bx bx-archive"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Analytics & Settings Summary Panel -->
    <div class="col-md-4 col-12">
      <div class="row">
        <!-- Analytics Card -->
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title m-0">Notification Analytics</h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <h6 class="mb-2">Notification Status</h6>
                <div id="notificationStatsChart"></div>
                <div class="d-flex justify-content-center mt-3">
                  <div class="d-flex align-items-center me-4">
                    <i class="bx bxs-circle text-primary me-1"></i>
                    <span>Read (75%)</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="bx bxs-circle text-info me-1"></i>
                    <span>Unread (25%)</span>
                  </div>
                </div>
              </div>
              <div>
                <h6 class="mb-2">Activity Trends</h6>
                <div id="notificationTrendsChart"></div>
                <p class="text-center mt-2 text-muted small">Weekly notification activity</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Recent Actions Card -->
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title m-0">Recent Actions</h5>
            </div>
            <div class="card-body">
              <ul class="timeline pb-0 mb-0">
                <li class="timeline-item pb-3 border-left-dashed">
                  <span class="timeline-indicator timeline-indicator-primary">
                    <i class="bx bx-check"></i>
                  </span>
                  <div class="timeline-event">
                    <div class="timeline-header">
                      <h6 class="mb-0">Marked 3 notifications as read</h6>
                      <small class="text-muted">Today</small>
                    </div>
                    <p class="mb-0">System update, user registrations</p>
                  </div>
                </li>
                <li class="timeline-item pb-3 border-left-dashed">
                  <span class="timeline-indicator timeline-indicator-warning">
                    <i class="bx bx-bell-off"></i>
                  </span>
                  <div class="timeline-event">
                    <div class="timeline-header">
                      <h6 class="mb-0">Dismissed 2 notifications</h6>
                      <small class="text-muted">Yesterday</small>
                    </div>
                    <p class="mb-0">Low priority alerts</p>
                  </div>
                </li>
                <li class="timeline-item">
                  <span class="timeline-indicator timeline-indicator-info">
                    <i class="bx bx-archive"></i>
                  </span>
                  <div class="timeline-event">
                    <div class="timeline-header">
                      <h6 class="mb-0">Archived 5 notifications</h6>
                      <small class="text-muted">3 days ago</small>
                    </div>
                    <p class="mb-0">Completed tasks, system reports</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Quick Settings Card -->
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title m-0">Quick Settings</h5>
              <a href="{{ route('notification-center.settings') }}" class="btn btn-text-primary p-0">View All</a>
            </div>
            <div class="card-body">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                <label class="form-check-label" for="emailNotifications">Email Notifications</label>
              </div>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="smsNotifications">
                <label class="form-check-label" for="smsNotifications">SMS Notifications</label>
              </div>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="browserNotifications" checked>
                <label class="form-check-label" for="browserNotifications">Browser Notifications</label>
              </div>
              <div class="mt-3">
                <label for="notificationFrequency" class="form-label">Frequency</label>
                <select id="notificationFrequency" class="form-select">
                  <option value="immediate">Immediate</option>
                  <option value="daily">Daily Digest</option>
                  <option value="weekly">Weekly Summary</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection 