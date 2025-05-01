@extends('layouts.app')

@section('title', 'Users List')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-user-list.css') }}" />
<style>
  .user-avatar {
    height: 40px;
    width: 40px;
    object-fit: cover;
  }
  .user-name {
    font-size: 0.95rem;
    font-weight: 600;
  }
  .user-email {
    font-size: 0.8rem;
    color: #697a8d;
  }
  .role-badge {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
  }
  .role-admin {
    background-color: #e7e7ff;
    color: #696cff;
  }
  .role-user {
    background-color: #e8fadf;
    color: #71dd37;
  }
  .filter-panel {
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #eaeaec;
    border-radius: 0.375rem;
    background-color: #fff;
  }
  .action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
  }
  .status-active {
    background-color: #71dd37;
  }
  .status-inactive {
    background-color: #a1acb8;
  }
  .status-pending {
    background-color: #ffab00;
  }
  .column-toggle-menu {
    max-height: 200px;
    overflow-y: auto;
  }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Users /</span> List
</h4>

<!-- Users List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-3">Users</h5>
    
    <!-- Search and filters row -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
      <!-- Search input -->
      <div class="d-flex align-items-center flex-grow-1 me-2 mb-2">
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="bx bx-search"></i></span>
          <input type="text" id="userSearch" class="form-control" placeholder="Search users..." aria-label="Search...">
          <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-filter-alt me-1"></i>Filters
          </button>
          <div class="dropdown-menu p-3" style="width: 20rem">
            <h6 class="dropdown-header">Filter Options</h6>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select id="filterRole" class="form-select form-select-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select id="filterStatus" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Pending">Pending</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Registration Date</label>
              <div class="d-flex">
                <input type="date" id="dateFrom" class="form-control form-control-sm me-2" placeholder="From">
                <input type="date" id="dateTo" class="form-control form-control-sm" placeholder="To">
              </div>
            </div>
            <div class="pt-1 d-flex justify-content-end">
              <button id="resetFilters" class="btn btn-sm btn-label-secondary me-2">Reset</button>
              <button id="applyFilters" class="btn btn-sm btn-primary">Apply</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Column visibility toggle -->
      <div class="dropdown mb-2">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="columnSelector" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-columns me-1"></i>Columns
        </button>
        <ul class="dropdown-menu column-toggle-menu p-2" aria-labelledby="columnSelector">
          <li>
            <div class="form-check">
              <input class="form-check-input toggle-column" type="checkbox" value="name" id="col-name" checked>
              <label class="form-check-label" for="col-name">Name/Email</label>
            </div>
          </li>
          <li>
            <div class="form-check">
              <input class="form-check-input toggle-column" type="checkbox" value="role" id="col-role" checked>
              <label class="form-check-label" for="col-role">Role</label>
            </div>
          </li>
          <li>
            <div class="form-check">
              <input class="form-check-input toggle-column" type="checkbox" value="status" id="col-status" checked>
              <label class="form-check-label" for="col-status">Status</label>
            </div>
          </li>
          <li>
            <div class="form-check">
              <input class="form-check-input toggle-column" type="checkbox" value="date" id="col-date" checked>
              <label class="form-check-label" for="col-date">Registration Date</label>
            </div>
          </li>
        </ul>
      </div>
      
      <!-- Action buttons -->
      <div class="d-flex mb-2">
        <a href="{{ route('users.roles.index') }}" class="btn btn-outline-primary me-2">
          <i class="bx bx-key me-sm-1"></i> <span class="d-none d-sm-inline-block">Roles</span>
        </a>
        <a href="{{ route('matrix') }}" class="btn btn-outline-primary me-2">
          <i class="bx bx-shield me-sm-1"></i> <span class="d-none d-sm-inline-block">Permissions</span>
        </a>
        <button class="btn btn-primary" 
                tabindex="0" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
          <i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add User</span>
        </button>
      </div>
    </div>
    
    <!-- Active filters badges -->
    <div id="activeFilters" class="d-flex flex-wrap mb-2" style="display: none !important;">
      <!-- Active filters will be shown here -->
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
      <!-- Display success message -->
      @if(session('success'))
      <div class="alert alert-success alert-dismissible m-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      <div class="row">
        <div class="col-sm-12">
          <table class="datatables-users table dataTable no-footer dtr-column" id="usersTable">
            <thead class="table-light">
              <tr>
                <th class="selection-cell" style="width: 35px;">
                  <input type="checkbox" class="form-check-input select-all">
                </th>
                <th class="col-name">User</th>
                <th class="col-role">Role</th>
                <th class="col-status">Status</th>
                <th class="col-date">Registration Date</th>
                <th class="col-actions text-center" style="width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr class="user-row">
                <td class="selection-cell">
                  <input type="checkbox" class="form-check-input user-select" value="{{ $user->id }}">
                </td>
                <td class="col-name">
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar-wrapper me-3">
                      <!-- Profile picture with fallback to initials -->
                      @if(isset($user->profile_image))
                        <img src="{{ asset('storage/profile-images/' . $user->profile_image) }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle user-avatar">
                      @else
                        <div class="avatar rounded-circle" style="background-color: {{ '#' . substr(md5($user->name), 0, 6) }}; height: 40px; width: 40px; display: flex; align-items: center; justify-content: center;">
                          <span style="color: white; font-weight: 600;">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                      @endif
                    </div>
                    <div class="d-flex flex-column">
                      <span class="user-name">{{ $user->name }}</span>
                      <span class="user-email">{{ $user->email }}</span>
                    </div>
                  </div>
                </td>
                <td class="col-role">
                  @if($user->roles->count() > 0)
                    @foreach($user->roles as $role)
                      <span class="role-badge {{ strtolower($role->name) === 'admin' ? 'role-admin' : 'role-user' }}">
                        <i class="bx {{ strtolower($role->name) === 'admin' ? 'bx-shield-quarter' : 'bx-user' }} me-1"></i>
                        {{ $role->name }}
                      </span>
                      {{ !$loop->last ? ' ' : '' }}
                    @endforeach
                  @else
                    <span class="role-badge role-user">
                      <i class="bx bx-user me-1"></i>
                      {{ $user->role ?? 'User' }}
                    </span>
                  @endif
                </td>
                <td class="col-status">
                  <span class="d-flex align-items-center">
                    <span class="status-dot 
                      @if($user->status === 'Active') status-active
                      @elseif($user->status === 'Inactive') status-inactive
                      @elseif($user->status === 'Pending') status-pending
                      @endif">
                    </span>
                    {{ $user->status ?? 'N/A' }}
                  </span>
                </td>
                <td class="col-date">
                  {{ $user->created_at->format('M d, Y') }}
                </td>
                <td class="col-actions text-center">
                  <div class="d-inline-flex">
                    <button type="button" class="btn btn-sm action-btn btn-outline-primary view-details me-1" 
                            data-user-id="{{ $user->id }}" title="Details">
                      <i class="bx bx-show-alt"></i>
                    </button>
                    <a href="{{ route('settings.account', ['manage_user_id' => $user->id]) }}" 
                       class="btn btn-sm action-btn btn-outline-info me-1" title="Edit">
                      <i class="bx bx-edit"></i>
                    </a>
                    <form method="POST" action="/users/delete/{{ $user->id }}" class="d-inline delete-user-form" data-user-id="{{ $user->id }}">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn btn-sm action-btn btn-outline-danger delete-record" title="Delete">
                        <i class="bx bx-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row mx-2 mt-2">
        <div class="col-sm-12 col-md-6">
          <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
          </div>
        </div>
        <div class="col-sm-12 col-md-6">
          <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            {{ $users->links('vendor.pagination.bootstrap-5') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Users List Table -->

<!-- Offcanvas to add new user -->
<div
  class="offcanvas offcanvas-end"
  tabindex="-1"
  id="offcanvasAddUser"
  aria-labelledby="offcanvasAddUserLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add User</h5>
    <button
      type="button"
      class="btn-close text-reset"
      data-bs-dismiss="offcanvas"
      aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0">
    <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('users.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label" for="add-user-fullname">Full Name</label>
        <input
          type="text"
          class="form-control @error('name') is-invalid @enderror"
          id="add-user-fullname"
          placeholder="John Doe"
          name="name"
          value="{{ old('name') }}"
          aria-label="John Doe" />
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label" for="add-user-email">Email</label>
        <input
          type="text"
          id="add-user-email"
          class="form-control @error('email') is-invalid @enderror"
          placeholder="john.doe@example.com"
          aria-label="john.doe@example.com"
          name="email"
          value="{{ old('email') }}" />
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label" for="add-user-password">Password</label>
        <input
          type="password"
          id="add-user-password"
          class="form-control @error('password') is-invalid @enderror"
          placeholder="••••••••"
          aria-label="••••••••"
          name="password" />
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label" for="user-role">User Role</label>
        <select id="user-role" class="form-select @error('role') is-invalid @enderror" name="role" required>
          <option value="" selected disabled>Select Role</option>
          @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
          @endforeach
        </select>
        @error('role')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-4">
        <label class="form-label" for="user-status">Status</label>
        <select id="user-status" class="form-select @error('status') is-invalid @enderror" name="status" required>
          <option value="" selected disabled>Select Status</option>
          <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
          <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
        </select>
        @error('status')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
      <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
    </form>
  </div>
</div>
<!-- / Add User Offcanvas -->
@endsection

@section('page-js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Show offcanvas if there are validation errors
    @if($errors->any())
      var offcanvasAddUser = new bootstrap.Offcanvas(document.getElementById('offcanvasAddUser'));
      offcanvasAddUser.show();
    @endif

    // =========== Live Search Functionality ===========
    const userSearch = document.getElementById('userSearch');
    const userRows = document.querySelectorAll('tr.user-row');
    
    userSearch.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase().trim();
      
      userRows.forEach(row => {
        const name = row.querySelector('.user-name').textContent.toLowerCase();
        const email = row.querySelector('.user-email').textContent.toLowerCase();
        const role = row.querySelector('.col-role').textContent.toLowerCase();
        const status = row.querySelector('.col-status').textContent.toLowerCase();
        
        // Show row if any field contains the search term
        if (name.includes(searchTerm) || 
            email.includes(searchTerm) || 
            role.includes(searchTerm) || 
            status.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
      
      updateVisibleRowsCount();
    });
    
    // =========== Filter Functionality ===========
    const filterRole = document.getElementById('filterRole');
    const filterStatus = document.getElementById('filterStatus');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const applyFilters = document.getElementById('applyFilters');
    const resetFilters = document.getElementById('resetFilters');
    const activeFilters = document.getElementById('activeFilters');
    
    // Apply filters button click
    applyFilters.addEventListener('click', function() {
      applyAllFilters();
      updateActiveFiltersDisplay();
    });
    
    // Reset filters button click
    resetFilters.addEventListener('click', function() {
      filterRole.value = '';
      filterStatus.value = '';
      dateFrom.value = '';
      dateTo.value = '';
      
      // Show all rows
      userRows.forEach(row => {
        row.style.display = '';
      });
      
      // Hide active filters
      activeFilters.innerHTML = '';
      activeFilters.style.display = 'none';
      
      updateVisibleRowsCount();
    });
    
    function applyAllFilters() {
      const selectedRole = filterRole.value.toLowerCase();
      const selectedStatus = filterStatus.value.toLowerCase();
      const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
      const toDate = dateTo.value ? new Date(dateTo.value) : null;
      
      userRows.forEach(row => {
        let showRow = true;
        
        // Filter by role
        if (selectedRole) {
          const roleCell = row.querySelector('.col-role').textContent.toLowerCase();
          if (!roleCell.includes(selectedRole)) {
            showRow = false;
          }
        }
        
        // Filter by status
        if (showRow && selectedStatus) {
          const statusCell = row.querySelector('.col-status').textContent.toLowerCase().trim();
          if (!statusCell.includes(selectedStatus)) {
            showRow = false;
          }
        }
        
        // Filter by date range
        if (showRow && (fromDate || toDate)) {
          const dateCell = row.querySelector('.col-date').textContent.trim();
          const rowDate = new Date(dateCell);
          
          if (fromDate && rowDate < fromDate) {
            showRow = false;
          }
          
          if (toDate && rowDate > toDate) {
            showRow = false;
          }
        }
        
        row.style.display = showRow ? '' : 'none';
      });
      
      updateVisibleRowsCount();
    }
    
    function updateActiveFiltersDisplay() {
      activeFilters.innerHTML = '';
      let hasFilter = false;
      
      if (filterRole.value) {
        addFilterBadge('Role: ' + filterRole.value);
        hasFilter = true;
      }
      
      if (filterStatus.value) {
        addFilterBadge('Status: ' + filterStatus.value);
        hasFilter = true;
      }
      
      if (dateFrom.value) {
        addFilterBadge('From: ' + dateFrom.value);
        hasFilter = true;
      }
      
      if (dateTo.value) {
        addFilterBadge('To: ' + dateTo.value);
        hasFilter = true;
      }
      
      activeFilters.style.display = hasFilter ? 'flex' : 'none';
    }
    
    function addFilterBadge(text) {
      const badge = document.createElement('span');
      badge.className = 'badge bg-label-primary me-1 mb-1';
      badge.innerHTML = text + ' <i class="bx bx-x"></i>';
      badge.style.cursor = 'pointer';
      
      badge.addEventListener('click', function() {
        if (text.includes('Role:')) {
          filterRole.value = '';
        } else if (text.includes('Status:')) {
          filterStatus.value = '';
        } else if (text.includes('From:')) {
          dateFrom.value = '';
        } else if (text.includes('To:')) {
          dateTo.value = '';
        }
        
        applyAllFilters();
        updateActiveFiltersDisplay();
      });
      
      activeFilters.appendChild(badge);
    }
    
    function updateVisibleRowsCount() {
      const visibleRows = document.querySelectorAll('tr.user-row[style=""]').length;
      document.getElementById('DataTables_Table_0_info').textContent = `Showing ${visibleRows} of ${userRows.length} entries`;
    }
    
    // =========== Column Visibility Toggle ===========
    const toggleColumnCheckboxes = document.querySelectorAll('.toggle-column');
    
    toggleColumnCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const columnName = this.value;
        const isVisible = this.checked;
        
        // Toggle visibility of the column
        document.querySelectorAll(`.col-${columnName}`).forEach(cell => {
          cell.style.display = isVisible ? '' : 'none';
        });
      });
    });
    
    // =========== Row selection ===========
    const selectAllCheckbox = document.querySelector('.select-all');
    const userSelectCheckboxes = document.querySelectorAll('.user-select');
    
    selectAllCheckbox.addEventListener('change', function() {
      const isChecked = this.checked;
      
      userSelectCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
      });
    });
    
    userSelectCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const allChecked = [...userSelectCheckboxes].every(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
      });
    });

    // Delete user functionality
    document.querySelectorAll('.delete-record').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this user?')) {
          const form = this.closest('form');
          form.submit();
        }
      });
    });

    // View details in modal
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    if (viewDetailsButtons.length > 0) {
      viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
          const userId = this.getAttribute('data-user-id');
          // Show modal with loading state
          const userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
          userDetailsModal.show();
          // Set loading state
          document.getElementById('userDetailsContent').innerHTML = `
            <div class="modal-body text-center p-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading user details...</p>
            </div>
          `;
          // Fetch user details
          fetch(`/users/details/${userId}`)
            .then(response => response.text())
            .then(html => {
              document.getElementById('userDetailsContent').innerHTML = html;
            })
            .catch(error => {
              document.getElementById('userDetailsContent').innerHTML = `
                <div class="modal-header">
                  <h5 class="modal-title">Error</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="alert alert-danger" role="alert">
                    Failed to load user details: ${error.message}
                  </div>
                </div>
              `;
            });
        });
      });
    }
  });
</script>
@endsection

@section('modals')
<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div id="userDetailsContent">
        <!-- Content will be loaded here -->
        <div class="modal-body text-center p-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading user details...</p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- / User Details Modal -->
@endsection 