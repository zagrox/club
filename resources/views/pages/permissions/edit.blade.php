@extends('layouts.app')

@section('title', 'Edit Permission')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    // Initialize Select2 for group dropdown
    $('.select2').select2();
    
    // Make the slug automatically generated
    $('#name').on('input', function() {
      updateSlugPreview();
    });
    
    $('#group, #action').on('change input', function() {
      updateSlugPreview();
    });
    
    function updateSlugPreview() {
      let group = $('#group').val() || '';
      let action = $('#action').val() || '';
      
      if (group && action) {
        let slug = group + '.' + action.toLowerCase().replace(/\s+/g, '-');
        $('#slug-preview').text(slug);
      }
    }
    
    // Add new group option if it doesn't exist
    $('#group').on('select2:close', function() {
      let currentValue = $(this).val();
      if (currentValue === 'add-new-group') {
        // Open modal or prompt for new group name
        let newGroup = prompt('Enter new permission group name:');
        if (newGroup) {
          // Add new option and select it
          let newOption = new Option(newGroup, newGroup, true, true);
          $(this).append(newOption).trigger('change');
          updateSlugPreview();
        } else {
          // If cancelled, revert to previous value
          $(this).val('{{ $group }}').trigger('change');
        }
      }
    });
    
    // Update slug preview on page load
    updateSlugPreview();
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Management / <a href="{{ route('users.permissions.index') }}">Permissions</a> /</span> Edit
</h4>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Permission: {{ $permission->name }}</h5>
      </div>
      <div class="card-body">
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex">
              <i class="bx bx-error-circle me-2"></i>
              <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        
        <form action="{{ route('users.permissions.update', $permission) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="name">Permission Name <span class="text-danger">*</span></label>
              <input 
                type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name', $permission->name) }}"
                placeholder="View Users" 
                required
              />
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">A descriptive name for this permission.</div>
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="group">Permission Group <span class="text-danger">*</span></label>
              <select 
                id="group" 
                name="group" 
                class="select2 form-select @error('group') is-invalid @enderror" 
                required
              >
                @if($groups->count() > 0)
                  @foreach($groups as $existingGroup)
                    <option value="{{ $existingGroup }}" {{ old('group', $group) == $existingGroup ? 'selected' : '' }}>
                      {{ ucfirst($existingGroup) }}
                    </option>
                  @endforeach
                @endif
                <option value="add-new-group">+ Add New Group</option>
              </select>
              @error('group')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">The category this permission belongs to.</div>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="action">Permission Action <span class="text-danger">*</span></label>
              <input 
                type="text" 
                class="form-control @error('action') is-invalid @enderror" 
                id="action" 
                name="action" 
                value="{{ old('action', $action) }}"
                placeholder="view" 
                required
              />
              @error('action')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">The action this permission grants (e.g., view, create, update, delete).</div>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Generated Slug</label>
              <div class="input-group">
                <span class="input-group-text">Slug:</span>
                <span id="slug-preview" class="form-control bg-light text-muted">{{ $permission->slug }}</span>
              </div>
              <div class="form-text">This will be automatically generated based on group and action.</div>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea 
              class="form-control @error('description') is-invalid @enderror" 
              id="description" 
              name="description" 
              rows="3"
              placeholder="Can view list of users in the system"
            >{{ old('description', $permission->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">A clear description of what this permission allows.</div>
          </div>
          
          <div class="mb-4">
            <h6>Used in Roles</h6>
            <div class="d-flex flex-wrap gap-2">
              @forelse($permission->roles as $role)
                <a href="{{ route('users.roles.show', $role) }}" class="badge bg-label-primary rounded-pill me-1">
                  {{ $role->name }}
                </a>
              @empty
                <span class="text-muted">Not assigned to any roles yet</span>
              @endforelse
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary me-2">Update Permission</button>
              <a href="{{ route('users.permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Delete Card -->
    <div class="card mb-4">
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