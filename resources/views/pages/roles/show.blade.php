@extends('layouts.app')

@section('title', 'Role Details')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    // Initialize DataTable
    const usersTable = $('.datatables-users').DataTable({
      order: [[1, 'asc']], // Sort by name
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      language: {
        search: '',
        searchPlaceholder: 'Search users...'
      },
      lengthMenu: [5, 10, 25, 50, 100]
    });
    
    // Initialize Select2
    $('#user-select').select2({
      placeholder: 'Select users to assign this role',
      dropdownParent: $('#assignUsersModal')
    });
    
    // Confirm user removal with SweetAlert
    $(document).on('click', '.remove-user-btn', function(e) {
      e.preventDefault();
      const userId = $(this).data('user-id');
      const userName = $(this).data('user-name');
      const roleName = $(this).data('role-name');
      const removeUrl = $(this).attr('href');
      
      Swal.fire({
        title: 'Remove User from Role?',
        text: `Are you sure you want to remove ${userName} from the ${roleName} role?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'No, cancel',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-outline-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          const form = $('<form></form>').attr({
            method: 'POST',
            action: removeUrl
          }).appendTo('body');
          
          $('<input>').attr({
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
          }).appendTo(form);
          
          $('<input>').attr({
            type: 'hidden',
            name: '_method',
            value: 'DELETE'
          }).appendTo(form);
          
          form.submit();
        }
      });
    });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Users / Roles /</span> Role Details
</h4>

<!-- Alert Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible mb-4" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <!-- Role Information -->
  <div class="col-xl-4 col-lg-5 col-md-12">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">Role Information</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-start mb-4">
          <div class="d-flex align-items-start">
            <div class="avatar me-2">
              <span class="avatar-initial rounded bg-label-{{ $role->slug === 'admin' ? 'primary' : ($role->slug === 'moderator' ? 'info' : 'success') }}">
                <i class="bx {{ $role->slug === 'admin' ? 'bx-shield' : ($role->slug === 'moderator' ? 'bx-crown' : 'bx-user') }} fs-3"></i>
              </span>
            </div>
            <div class="me-2 ms-1">
              <h4 class="mb-1">{{ $role->name }}</h4>
              <span class="badge bg-label-{{ $role->is_system ? 'primary' : 'secondary' }} me-1">{{ $role->is_system ? 'System' : 'Custom' }}</span>
            </div>
          </div>
        </div>
        
        <div class="info-container">
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Role Slug</small>
            <h6>{{ $role->slug }}</h6>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Description</small>
            <h6>{{ $role->description ?? 'No description provided' }}</h6>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Users with this role</small>
            <h6>{{ count($role->users) }}</h6>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Created At</small>
            <h6>{{ $role->created_at->format('M d, Y') }}</h6>
          </div>
        </div>
        
        <div class="d-flex justify-content-center pt-3">
          <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary me-3">
            <i class="bx bx-edit-alt me-1"></i> Edit Role
          </a>
          
          @if(!$role->is_system)
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal">
              <i class="bx bx-trash me-1"></i> Delete Role
            </button>
          @endif
        </div>
      </div>
    </div>
    
    <!-- Permissions Card -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">Role Permissions</h5>
      </div>
      <div class="card-body">
        @if(empty($role->permissions))
          <div class="text-center p-4">
            <i class="bx bx-shield-x bx-lg text-muted mb-2"></i>
            <p class="mb-0">No permissions assigned to this role</p>
          </div>
        @else
          <div class="d-grid gap-2">
            @php
              $permissionGroups = [];
              
              foreach ($role->permissions as $permission) {
                $group = explode('.', $permission)[0];
                if (!isset($permissionGroups[$group])) {
                  $permissionGroups[$group] = [];
                }
                $permissionGroups[$group][] = $permission;
              }
            @endphp
            
            @foreach($permissionGroups as $group => $perms)
              <div class="card shadow-none bg-light mb-0">
                <div class="card-header py-2 border-bottom">
                  <h6 class="card-title mb-0 text-capitalize">{{ $group }}</h6>
                </div>
                <div class="card-body">
                  <div class="d-flex flex-wrap gap-2">
                    @foreach($perms as $perm)
                      <span class="badge bg-primary">{{ str_replace($group . '.', '', $perm) }}</span>
                    @endforeach
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
  
  <!-- Users with this role -->
  <div class="col-xl-8 col-lg-7 col-md-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Users with this role</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUsersModal">
          <i class="bx bx-user-plus me-1"></i> Assign Users
        </button>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-users table border-top">
          <thead>
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($role->users as $user)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar me-2">
                      <span class="avatar-initial rounded-circle bg-label-{{ strtolower(substr($user->name, 0, 1)) == 'a' ? 'primary' : (strtolower(substr($user->name, 0, 1)) == 'b' ? 'success' : (strtolower(substr($user->name, 0, 1)) == 'c' ? 'warning' : (strtolower(substr($user->name, 0, 1)) == 'd' ? 'info' : 'secondary'))) }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                      </span>
                    </div>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $user->name }}</span>
                    </div>
                  </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                  <span class="badge {{ $user->status === 'active' ? 'bg-label-success' : 'bg-label-secondary' }}">
                    {{ ucfirst($user->status ?? 'N/A') }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('roles.remove-user', ['role' => $role->id, 'user' => $user->id]) }}" 
                     class="btn btn-sm btn-icon btn-text-danger remove-user-btn"
                     data-user-id="{{ $user->id }}" 
                     data-user-name="{{ $user->name }}"
                     data-role-name="{{ $role->name }}">
                    <i class="bx bx-trash"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4">
                  <div class="text-center">
                    <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                    <p class="mb-0">No users have been assigned this role</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Delete Role Modal -->
@if(!$role->is_system)
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title text-white">Delete Role</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <div class="text-center mb-4">
            <i class="bx bx-error-circle bx-lg text-danger"></i>
            <h4 class="mt-2">Are you sure you want to delete this role?</h4>
            <p class="mb-0">This action cannot be undone. This will remove the role from all assigned users.</p>
          </div>
          <div class="alert alert-warning">
            <div class="d-flex">
              <i class="bx bx-error me-2"></i>
              <div>Users assigned to this role will lose all permissions associated with it.</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<!-- Assign Users Modal -->
<div class="modal fade" id="assignUsersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign Users to "{{ $role->name }}" Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.assign-users', $role->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="user-select" class="form-label">Select Users</label>
            <select id="user-select" class="form-select select2" name="user_ids[]" multiple required>
              @foreach(App\Models\User::whereDoesntHave('roles', function($query) use ($role) {
                $query->where('role_id', $role->id);
              })->get() as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
              @endforeach
            </select>
            <small class="text-muted">Only users who don't already have this role are shown.</small>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="notify-users">
            <label class="form-check-label" for="notify-users">
              Notify users about role assignment
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Assign Users</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection 