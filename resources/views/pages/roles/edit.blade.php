@extends('layouts.app')

@section('title', 'Edit Role')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    // Initialize Select2 for multiple select
    $('.select2').select2();
    
    // Toggle all permissions in a group
    $('.permission-group-toggle').on('change', function() {
      const groupId = $(this).data('group');
      const isChecked = $(this).prop('checked');
      
      $(`.permission-checkbox[data-group="${groupId}"]`).prop('checked', isChecked);
    });
    
    // Update group toggle when individual permissions change
    $('.permission-checkbox').on('change', function() {
      const groupId = $(this).data('group');
      const groupCheckboxes = $(`.permission-checkbox[data-group="${groupId}"]`);
      const checkedCount = groupCheckboxes.filter(':checked').length;
      
      $(`.permission-group-toggle[data-group="${groupId}"]`).prop('checked', checkedCount === groupCheckboxes.length);
    });
    
    // Set initial state of group toggles
    $('.permission-group-toggle').each(function() {
      const groupId = $(this).data('group');
      const groupCheckboxes = $(`.permission-checkbox[data-group="${groupId}"]`);
      const checkedCount = groupCheckboxes.filter(':checked').length;
      
      $(this).prop('checked', checkedCount === groupCheckboxes.length && groupCheckboxes.length > 0);
    });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Users / Roles /</span> Edit Role
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Edit Role: {{ $role->name }}</h5>
      <div class="card-body">
        @if($role->is_system)
          <div class="alert alert-warning mb-4">
            <div class="d-flex">
              <i class="bx bx-error-circle bx-sm me-2"></i>
              <div>
                <h6 class="alert-heading mb-1">System Role</h6>
                <p class="mb-0">
                  This is a system role. You can view its permissions but cannot modify the role.
                </p>
              </div>
            </div>
          </div>
          
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" class="form-control" id="name" value="{{ $role->name }}" readonly disabled>
              </div>
              
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="3" readonly disabled>{{ $role->description }}</textarea>
              </div>
            </div>
          </div>
          
          <h6 class="mb-3">Role Permissions</h6>
          <p class="text-muted mb-4">These permissions define what actions users with this role can perform.</p>
          
          <div class="row">
            @foreach($permissions as $groupName => $groupPermissions)
              <div class="col-md-6 mb-4">
                <div class="card">
                  <div class="card-header py-2">
                    <h6 class="mb-0">{{ $groupName }}</h6>
                  </div>
                  <div class="card-body pt-2">
                    <div class="row">
                      @foreach($groupPermissions as $permissionKey => $permissionLabel)
                        <div class="col-md-6">
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="{{ $permissionKey }}" 
                                {{ in_array($permissionKey, $role->permissions ?? []) ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="{{ $permissionKey }}">
                              {{ $permissionLabel }}
                            </label>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          
          <div class="mt-4">
            <a href="{{ route('roles.index') }}" class="btn btn-primary">
              <i class="bx bx-arrow-back me-1"></i> Back to Roles
            </a>
          </div>
        @else
          <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter role name" value="{{ old('name', $role->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Role description">{{ old('description', $role->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="alert alert-primary mb-4">
                  <div class="d-flex">
                    <i class="bx bx-info-circle bx-sm me-2"></i>
                    <div>
                      <h6 class="alert-heading mb-1">Role Information</h6>
                      <p class="mb-0">
                        Roles define access levels within the system. Assign permissions to control what users can do.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <h6 class="mb-3">Role Permissions</h6>
            <p class="text-muted mb-4">Assign permissions to define what actions users with this role can perform.</p>
            
            <div class="row">
              @foreach($permissions as $groupName => $groupPermissions)
                <div class="col-md-6 mb-4">
                  <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                      <h6 class="mb-0">{{ $groupName }}</h6>
                      <div class="form-check">
                        <input class="form-check-input permission-group-toggle" type="checkbox" id="group-{{ Str::slug($groupName) }}" data-group="{{ Str::slug($groupName) }}">
                        <label class="form-check-label" for="group-{{ Str::slug($groupName) }}">
                          Select All
                        </label>
                      </div>
                    </div>
                    <div class="card-body pt-2">
                      <div class="row">
                        @foreach($groupPermissions as $permissionKey => $permissionLabel)
                          <div class="col-md-6">
                            <div class="form-check mb-2">
                              <input class="form-check-input permission-checkbox" type="checkbox" id="{{ $permissionKey }}" name="permissions[]" value="{{ $permissionKey }}" data-group="{{ Str::slug($groupName) }}" {{ (is_array(old('permissions')) && in_array($permissionKey, old('permissions'))) || (old('permissions') === null && in_array($permissionKey, $role->permissions ?? [])) ? 'checked' : '' }}>
                              <label class="form-check-label" for="{{ $permissionKey }}">
                                {{ $permissionLabel }}
                              </label>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary me-3">
                <i class="bx bx-save me-1"></i> Update Role
              </button>
              <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-x me-1"></i> Cancel
              </a>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection 