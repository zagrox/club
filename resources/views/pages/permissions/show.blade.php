@extends('layouts.app')

@section('title', 'Permission Details')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(function () {
    $('.datatables-roles').DataTable({
      paging: true,
      lengthChange: false,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
      pageLength: 5
    });
    
    $('.datatables-users').DataTable({
      paging: true,
      lengthChange: false,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
      pageLength: 5
    });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Management / <a href="{{ route('permissions.index') }}">Permissions</a> /</span> View
</h4>

<div class="row">
  <!-- Permission Details Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Permission Details</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="d-flex align-items-center mb-1">
            <div class="badge bg-label-primary p-2 rounded me-2">
              <i class="bx bx-key bx-sm"></i>
            </div>
            <h5 class="mb-0">{{ $permission->name }}</h5>
          </div>
          <span class="badge bg-label-secondary">{{ $permission->slug }}</span>
        </div>
        
        @if($permission->description)
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Description</small>
            <p>{{ $permission->description }}</p>
          </div>
        @endif
        
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Group</small>
          <h6 class="text-capitalize">{{ explode('.', $permission->slug)[0] }}</h6>
        </div>
        
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Action</small>
          <h6>{{ count(explode('.', $permission->slug)) > 1 ? explode('.', $permission->slug)[1] : '' }}</h6>
        </div>
        
        <div class="mb-3">
          <small class="text-muted d-block mb-1">Created At</small>
          <h6>{{ $permission->created_at->format('M d, Y') }}</h6>
        </div>
        
        <div class="d-flex justify-content-center pt-3">
          <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary me-3">
            <i class="bx bx-edit-alt me-1"></i> Edit Permission
          </a>
          
          <form action="{{ route('permissions.destroy', $permission) }}" method="POST">
            @csrf
            @method('DELETE')
            <button 
              type="submit" 
              class="btn btn-danger" 
              onclick="return confirm('Are you sure you want to delete this permission? This will remove it from all roles.')"
            >
              <i class="bx bx-trash me-1"></i> Delete
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Roles with this permission -->
  <div class="col-md-6 col-lg-8 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Roles with this permission</h5>
      </div>
      <div class="card-body">
        @if($permission->roles->count() > 0)
          <div class="table-responsive">
            <table class="table table-striped datatables-roles">
              <thead>
                <tr>
                  <th>Role</th>
                  <th>Description</th>
                  <th>Users</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($permission->roles as $role)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <span class="fw-bold">{{ $role->name }}</span>
                        @if($role->is_default)
                          <span class="badge bg-label-info ms-2">Default</span>
                        @endif
                      </div>
                      <small class="text-muted">{{ $role->slug }}</small>
                    </td>
                    <td>{{ Str::limit($role->description, 50) }}</td>
                    <td>{{ $role->users->count() }}</td>
                    <td>
                      <div class="d-inline-block">
                        <a href="{{ route('users.roles.show', $role->id) }}" class="btn btn-sm btn-icon">
                          <i class="bx bx-show text-primary"></i>
                        </a>
                        <a href="{{ route('users.roles.edit', $role->id) }}" class="btn btn-sm btn-icon">
                          <i class="bx bx-edit-alt text-primary"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center p-4">
            <i class="bx bx-user-x bx-lg text-secondary mb-2"></i>
            <p class="mb-0">No roles have been assigned this permission</p>
          </div>
        @endif
      </div>
    </div>
  </div>
  
  <!-- Users with this permission -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Users with this permission (via roles)</h5>
      </div>
      <div class="card-body">
        @php
          $users = $permission->users();
        @endphp
        
        @if($users->count() > 0)
          <div class="table-responsive">
            <table class="table table-striped datatables-users">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Roles</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
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
                      @foreach($user->roles as $role)
                        <span class="badge bg-label-primary me-1">{{ $role->name }}</span>
                      @endforeach
                    </td>
                    <td>
                      <span class="badge {{ $user->status === 'active' ? 'bg-label-success' : 'bg-label-secondary' }}">
                        {{ ucfirst($user->status ?? 'N/A') }}
                      </span>
                    </td>
                    <td>
                      <div class="d-inline-block">
                        <a href="{{ route('users.roles.show', $role->id) }}" class="btn btn-sm btn-icon">
                          <i class="bx bx-show text-primary"></i>
                        </a>
                        <a href="{{ route('users.roles.edit', $role->id) }}" class="btn btn-sm btn-icon">
                          <i class="bx bx-edit-alt text-primary"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center p-4">
            <i class="bx bx-user-x bx-lg text-secondary mb-2"></i>
            <p class="mb-0">No users have this permission</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection 