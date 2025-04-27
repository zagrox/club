@extends('layouts.app')

@section('title', 'Users List')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-user-list.css') }}" />
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Users /</span> List
</h4>

<!-- Users List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-3">Users</h5>
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4 user_role"></div>
      <div class="col-md-4 user_plan"></div>
      <div class="col-md-4 user_status"></div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
      <div class="row mx-2">
        <div class="col-md-2">
          <div class="me-3">
            <div class="dataTables_length" id="DataTables_Table_0_length">
              <label>
                <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </label>
            </div>
          </div>
        </div>
        <div class="col-md-10">
          <div class="dt-action-buttons text-end pt-3 pt-md-0">
            <div class="dt-buttons">
              <div class="btn-group">
                <button class="btn btn-secondary buttons-collection dropdown-toggle btn-label-primary me-2" 
                        tabindex="0" aria-controls="DataTables_Table_0" type="button" 
                        aria-haspopup="dialog" aria-expanded="false">
                  <span><i class="bx bx-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span></span>
                </button>
              </div>
              <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">
                <span><i class="bx bx-key me-sm-1"></i> <span class="d-none d-sm-inline-block">Manage Roles</span></span>
              </a>
              <button class="btn btn-primary add-new btn-primary" 
                      tabindex="0" aria-controls="DataTables_Table_0" type="button"
                      data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                <span><i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New User</span></span>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Display success message -->
      @if(session('success'))
      <div class="alert alert-success alert-dismissible m-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      <div class="row">
        <div class="col-sm-12">
          <table class="datatables-users table dataTable no-footer dtr-column" id="DataTables_Table_0">
            <thead class="table-light">
              <tr>
                <th class="control sorting_disabled" rowspan="1" colspan="1" style="width: 35px;">
                  <input type="checkbox" class="form-check-input">
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">User</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">Role</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">Plan</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1">Status</th>
                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr class="odd">
                <td class="control" tabindex="0">
                  <input type="checkbox" class="form-check-input">
                </td>
                <td class="sorting_1">
                  <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar-wrapper">
                      <div class="avatar me-2">
                        <span class="avatar-initial rounded-circle bg-label-{{ strtolower(substr($user->name, 0, 1)) == 'a' ? 'primary' : (strtolower(substr($user->name, 0, 1)) == 'b' ? 'success' : (strtolower(substr($user->name, 0, 1)) == 'c' ? 'warning' : (strtolower(substr($user->name, 0, 1)) == 'd' ? 'info' : 'secondary'))) }}">
                          {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                      </div>
                    </div>
                    <div class="d-flex flex-column">
                      <span class="fw-medium">{{ $user->name }}</span>
                      <small class="text-muted">{{ $user->email }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="text-nowrap">
                    @if($loop->index % 3 == 0)
                      <i class="bx bx-cog bx-sm text-primary me-1"></i> Admin
                    @elseif($loop->index % 3 == 1)
                      <i class="bx bx-edit bx-sm text-info me-1"></i> Editor
                    @else
                      <i class="bx bx-user bx-sm text-success me-1"></i> Author
                    @endif
                  </span>
                </td>
                <td>
                  @if($loop->index % 3 == 0)
                    Enterprise
                  @elseif($loop->index % 3 == 1)
                    Team
                  @else
                    Basic
                  @endif
                </td>
                <td>
                  <span class="badge {{ $loop->index % 2 == 0 ? 'bg-label-success' : 'bg-label-secondary' }}">
                    {{ $loop->index % 2 == 0 ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="d-inline-block">
                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a href="javascript:;" class="dropdown-item">Details</a>
                      <a href="javascript:;" class="dropdown-item">Edit</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row mx-2">
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
        <select id="user-role" class="form-select @error('role') is-invalid @enderror" name="role">
          <option value="" selected disabled>Select Role</option>
          <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
          <option value="Editor" {{ old('role') == 'Editor' ? 'selected' : '' }}>Editor</option>
          <option value="Author" {{ old('role') == 'Author' ? 'selected' : '' }}>Author</option>
          <option value="Subscriber" {{ old('role') == 'Subscriber' ? 'selected' : '' }}>Subscriber</option>
        </select>
        @error('role')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label" for="user-plan">Select Plan</label>
        <select id="user-plan" class="form-select @error('plan') is-invalid @enderror" name="plan">
          <option value="" selected disabled>Select Plan</option>
          <option value="Enterprise" {{ old('plan') == 'Enterprise' ? 'selected' : '' }}>Enterprise</option>
          <option value="Team" {{ old('plan') == 'Team' ? 'selected' : '' }}>Team</option>
          <option value="Basic" {{ old('plan') == 'Basic' ? 'selected' : '' }}>Basic</option>
        </select>
        @error('plan')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-4">
        <label class="form-label" for="user-status">Status</label>
        <select id="user-status" class="form-select @error('status') is-invalid @enderror" name="status">
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
    
    // Delete user functionality
    const deleteButtons = document.querySelectorAll('.delete-record');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this user?')) {
          // Delete user code here
          console.log('User deleted');
        }
      });
    });
  });
</script>
@endsection 