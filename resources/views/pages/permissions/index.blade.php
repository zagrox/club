@extends('layouts.app')

@section('title', 'Permissions Management')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(function () {
    $('.datatables-permissions').DataTable({
      dom: 
        "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      lengthMenu: [ 10, 25, 50, 75, 100 ],
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data[0];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' ?
                '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                  '<td>' + col.title + ':' + '</td> ' +
                  '<td>' + col.data + '</td>' +
                '</tr>' :
                '';
            }).join('');
            
            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Management /</span> Permissions
</h4>

<!-- Permission Cards -->
<div class="row g-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Permissions</h5>
        <div>
          <a href="{{ route('matrix') }}" class="btn btn-secondary me-2">
            <i class="bx bx-grid-alt me-0 me-sm-1"></i>
            <span class="d-none d-sm-inline-block">Permission Matrix</span>
          </a>
          <a href="{{ route('users.permissions.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-0 me-sm-1"></i>
            <span class="d-none d-sm-inline-block">Add New Permission</span>
          </a>
        </div>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex">
              <i class="bx bx-check me-2"></i>
              <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <div class="d-flex">
              <i class="bx bx-error-circle me-2"></i>
              <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        
        <div class="nav-align-top mb-3">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-grouped" aria-controls="tab-grouped" aria-selected="true">
                <i class="bx bx-category me-1"></i> Grouped View
              </button>
            </li>
            <li class="nav-item">
              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-table" aria-controls="tab-table" aria-selected="false">
                <i class="bx bx-table me-1"></i> Table View
              </button>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-grouped" role="tabpanel">
              <div class="row">
                @forelse($groupedPermissions as $group => $permissions)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-capitalize">{{ $group }}</h5>
                        <span class="badge bg-label-primary rounded-pill">{{ $permissions->count() }}</span>
                      </div>
                      <div class="card-body">
                        <div class="list-group list-group-flush">
                          @foreach($permissions as $permission)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                              <div>
                                <h6 class="mb-1">{{ $permission->name }}</h6>
                                <small class="text-muted">{{ $permission->slug }}</small>
                                @if($permission->description)
                                  <p class="text-muted mb-0 mt-1">{{ $permission->description }}</p>
                                @endif
                              </div>
                              <div class="dropdown">
                                <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                  <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                  <a class="dropdown-item" href="{{ route('users.permissions.show', $permission) }}">
                                    <i class="bx bx-show me-1"></i> View
                                  </a>
                                  <a class="dropdown-item" href="{{ route('users.permissions.edit', $permission) }}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                  </a>
                                  <div class="dropdown-divider"></div>
                                  <form action="{{ route('users.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this permission?')">
                                      <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="col-12">
                    <div class="card">
                      <div class="card-body text-center p-5">
                        <i class="bx bx-shield-x bx-lg text-secondary mb-2"></i>
                        <h5>No Permissions Found</h5>
                        <p class="mb-3">It looks like there are no permissions set up yet.</p>
                        <a href="{{ route('users.permissions.create') }}" class="btn btn-primary">
                          <i class="bx bx-plus me-1"></i> Create Permission
                        </a>
                      </div>
                    </div>
                  </div>
                @endforelse
              </div>
            </div>
            
            <div class="tab-pane fade" id="tab-table" role="tabpanel">
              <div class="table-responsive">
                <table class="table table-striped datatables-permissions">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Slug</th>
                      <th>Group</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($groupedPermissions->flatten() as $permission)
                      <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->slug }}</td>
                        <td>
                          <span class="badge bg-label-primary">{{ explode('.', $permission->slug)[0] }}</span>
                        </td>
                        <td>{{ $permission->description }}</td>
                        <td>
                          <div class="d-inline-block">
                            <a href="{{ route('users.permissions.show', $permission) }}" class="btn btn-sm btn-icon">
                              <i class="bx bx-show text-primary"></i>
                            </a>
                            <a href="{{ route('users.permissions.edit', $permission) }}" class="btn btn-sm btn-icon">
                              <i class="bx bx-edit-alt text-primary"></i>
                            </a>
                            <form action="{{ route('users.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-icon" onclick="return confirm('Are you sure you want to delete this permission?')">
                                <i class="bx bx-trash text-danger"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center">No permissions found</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 