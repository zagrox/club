@extends('layouts.app')

@section('title', 'User Roles & Permissions')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
<script>
  $(document).ready(function() {
    // Initialize DataTable
    const rolesTable = $('.datatables-roles').DataTable({
      order: [[1, 'asc']], // Sort by role name
      dom: '<"card-header d-flex flex-wrap py-3"<"me-5"f><"d-flex align-items-center position-relative"l><"dt-action-buttons"B>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      language: {
        search: '',
        searchPlaceholder: 'Search roles...'
      },
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-outline-secondary dropdown-toggle me-2',
          text: '<i class="bx bx-export me-1"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="bx bx-printer me-1"></i>Print',
              className: 'dropdown-item'
            },
            {
              extend: 'csv',
              text: '<i class="bx bx-file me-1"></i>CSV',
              className: 'dropdown-item'
            },
            {
              extend: 'excel',
              text: '<i class="bx bx-file me-1"></i>Excel',
              className: 'dropdown-item'
            },
            {
              extend: 'pdf',
              text: '<i class="bx bx-file me-1"></i>PDF',
              className: 'dropdown-item'
            }
          ]
        },
        {
          text: '<i class="bx bx-plus me-1"></i>Add New Role',
          className: 'btn btn-primary',
          action: function() {
            window.location.href = "{{ route('users.roles.create') }}";
          }
        }
      ]
    });

    // Delete confirmation with SweetAlert
    $(document).on('click', '.delete-role', function(e) {
      e.preventDefault();
      const roleId = $(this).data('id');
      const roleName = $(this).data('name');
      const deleteForm = $(`#delete-role-${roleId}`);
      
      Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the role "${roleName}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-outline-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          deleteForm.submit();
        }
      });
    });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Users /</span> Roles & Permissions
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

<!-- Role Cards -->
<div class="row g-4 mb-4">
  @php
    $roleColors = [
      'admin' => 'primary'
    ];
  @endphp
  
  @foreach($roles as $role)
    @php
      $color = $roleColors[$role->slug] ?? 'primary';
      $iconClass = 'bx-shield';
    @endphp
    <div class="col-xl-3 col-lg-6 col-md-6">
      <div class="card">
        <div class="card-body text-center">
          <div class="avatar avatar-md mx-auto mb-3">
            <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
              <i class="bx {{ $iconClass }} fs-4"></i>
            </span>
          </div>
          <h4 class="mb-1">{{ $role->name }}</h4>
          <span class="text-muted d-block mb-2">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
          <div class="d-flex align-items-center justify-content-center">
            <a href="{{ route('users.roles.edit', $role->id) }}" class="btn btn-primary me-2"><i class="bx bx-edit-alt me-1"></i> Edit</a>
            <a href="{{ route('users.roles.show', $role->id) }}" class="btn btn-outline-secondary"><i class="bx bx-user-check me-1"></i> View</a>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<!-- Role Table -->
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">All Roles</h5>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-roles table">
      <thead class="table-light">
        <tr>
          <th></th>
          <th>Role</th>
          <th>Description</th>
          <th>Users</th>
          <th>System</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $role)
        <tr>
          <td>
            <span class="text-truncate">
              <i class="bx bx-shield-quarter text-primary"></i>
            </span>
          </td>
          <td>
            <span class="fw-medium text-primary">{{ $role->name }}</span>
            <small class="d-block text-muted">{{ $role->slug }}</small>
          </td>
          <td>{{ Str::limit($role->description, 60) }}</td>
          <td>{{ $role->users_count }}</td>
          <td>
            @if($role->is_system)
              <span class="badge bg-label-primary">System</span>
            @else
              <span class="badge bg-label-secondary">Custom</span>
            @endif
          </td>
          <td>
            <div class="d-flex align-items-center">
              <a href="{{ route('users.roles.show', $role->id) }}" class="btn btn-sm btn-icon"><i class="bx bx-show-alt text-primary"></i></a>
              <a href="{{ route('users.roles.edit', $role->id) }}" class="btn btn-sm btn-icon"><i class="bx bx-edit text-info"></i></a>
              
              @if(!$role->is_system)
                <button type="button" class="btn btn-sm btn-icon delete-role" data-id="{{ $role->id }}" data-name="{{ $role->name }}">
                  <i class="bx bx-trash text-danger"></i>
                </button>
                <form id="delete-role-{{ $role->id }}" action="{{ route('users.roles.destroy', $role->id) }}" method="POST" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection 