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
  <span class="text-muted fw-light">User Management / <a href="{{ route('users.permissions.index') }}">Permissions</a> /</span> View
</h4>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Permission Details</h5>
        <div>
          <a href="{{ route('users.permissions.edit', $permission) }}" class="btn btn-primary me-2">
            <i class="bx bx-edit me-1"></i> Edit
          </a>
          <a href="{{ route('users.permissions.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-6">
            <h6 class="text-muted mb-2">Permission Name</h6>
            <h5>{{ $permission->name }}</h5>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-2">Permission Identifier</h6>
            <div class="badge bg-label-primary fs-6 mb-1">{{ $permission->name }}</div>
          </div>
        </div>
        
        @if($permission->description)
        <div class="row mb-4">
          <div class="col-12">
            <h6 class="text-muted mb-2">Description</h6>
            <p>{{ $permission->description }}</p>
          </div>
        </div>
        @endif
        
        <div class="row mb-0">
          <div class="col-12">
            <h6 class="text-muted mb-3">Assigned Roles</h6>
            <div class="d-flex flex-wrap gap-2">
              @forelse($permission->roles as $role)
                <a href="{{ route('users.roles.show', $role) }}" class="badge bg-label-primary rounded-pill p-2">
                  {{ $role->name }}
                </a>
              @empty
                <span class="text-muted">This permission is not assigned to any roles yet.</span>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Users with this Permission</h5>
      </div>
      <div class="card-body">
        <p class="text-muted mb-3">The following users have this permission through their assigned roles:</p>
        <div class="d-flex flex-wrap gap-2">
          @php 
            // Get users with this permission through roles
            $usersWithPermission = collect(); 
            foreach($permission->roles as $role) {
                $usersWithPermission = $usersWithPermission->merge($role->users);
            }
            $usersWithPermission = $usersWithPermission->unique('id');
          @endphp
          
          @forelse($usersWithPermission as $user)
            <a href="{{ route('users.details', $user) }}" class="badge bg-label-info rounded-pill p-2">
              {{ $user->name }}
            </a>
          @empty
            <span class="text-muted">No users have this permission yet.</span>
          @endforelse
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="text-danger mb-0">Delete Permission</h5>
            <p class="mb-0">Once deleted, this permission will be removed from all roles.</p>
          </div>
          <form action="{{ route('users.permissions.destroy', $permission) }}" method="POST">
            @csrf
            @method('DELETE')
            <button 
              type="submit" 
              class="btn btn-danger" 
              onclick="return confirm('Are you sure you want to delete this permission? This will remove it from all roles.')"
            >
              <i class="bx bx-trash-alt me-1"></i> Delete Permission
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 