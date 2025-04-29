@extends('layouts.app')

@section('title', 'Create Permission')

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
      let group = $('#group').val() || '';
      let action = $('#action').val() || '';
      
      if (group && action) {
        let slug = group + '.' + action.toLowerCase().replace(/\s+/g, '-');
        $('#slug-preview').text(slug);
      }
    });
    
    $('#group, #action').on('change input', function() {
      let group = $('#group').val() || '';
      let action = $('#action').val() || '';
      
      if (group && action) {
        let slug = group + '.' + action.toLowerCase().replace(/\s+/g, '-');
        $('#slug-preview').text(slug);
      }
    });
    
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
        } else {
          // If cancelled, revert to first option or empty
          $(this).val($(this).find('option:first').val()).trigger('change');
        }
      }
    });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Management / <a href="{{ route('users.permissions.index') }}">Permissions</a> /</span> Create
</h4>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Create New Permission</h5>
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
        
        <form action="{{ route('users.permissions.store') }}" method="POST">
          @csrf
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="name">Permission Name <span class="text-danger">*</span></label>
              <input 
                type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name') }}"
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
                    <option value="{{ $existingGroup }}" {{ old('group') == $existingGroup ? 'selected' : '' }}>
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
                value="{{ old('action') }}"
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
                <span id="slug-preview" class="form-control bg-light text-muted">group.action</span>
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
            >{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">A clear description of what this permission allows.</div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary me-2">Create Permission</button>
              <a href="{{ route('users.permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Help Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">About Permissions</h5>
      </div>
      <div class="card-body">
        <p>Permissions define what actions users can perform in the system. Best practices when creating permissions:</p>
        
        <div class="mb-3">
          <h6><i class="bx bx-check-circle me-2 text-success"></i>Naming Conventions</h6>
          <ul>
            <li>Use clear, descriptive names that explain the permission's purpose.</li>
            <li>Group related permissions together (e.g., users, roles, notifications).</li>
            <li>Use consistent action verbs (view, create, edit, delete).</li>
          </ul>
        </div>
        
        <div class="mb-3">
          <h6><i class="bx bx-check-circle me-2 text-success"></i>Permission Structure</h6>
          <ul>
            <li>Group: The resource or module (e.g., users, roles, reports)</li>
            <li>Action: The operation on that resource (e.g., view, create, edit)</li>
            <li>Slug: Automatically formatted as group.action (e.g., users.view)</li>
          </ul>
        </div>
        
        <div class="alert alert-primary mb-0">
          <div class="d-flex align-items-center">
            <i class="bx bx-info-circle bx-sm me-2"></i>
            <div>
              Once created, permissions can be assigned to roles, and roles are assigned to users. This creates a flexible permission system.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 