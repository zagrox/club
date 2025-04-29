@extends('layouts.app')

@section('title', 'Permission Matrix')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Management / <a href="{{ route('users.permissions.index') }}">Permissions</a> /</span> Matrix
</h4>
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Role-Based Permission Matrix</h5>
  </div>
  <div class="card-body table-responsive">
    @if($permissions->isEmpty() || $roles->isEmpty())
      <div class="alert alert-info">
        No permissions or roles found. Please create some first.
      </div>
    @else
      <table class="table table-bordered align-middle text-center">
        <thead>
          <tr>
            <th>Role \ Permission</th>
            @foreach($permissions as $permission)
              <th style="min-width: 120px;">{{ $permission->name }}<br><small class="text-muted">{{ $permission->slug }}</small></th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
            <tr>
              <th class="text-start">{{ $role->name }}</th>
              @foreach($permissions as $permission)
                <td>
                  <input type="checkbox"
                    class="perm-toggle"
                    data-role-id="{{ $role->id }}"
                    data-permission-id="{{ $permission->id }}"
                    @if($role->permissions->contains($permission->id)) checked @endif
                  >
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3">
        <button class="btn btn-outline-primary" id="selectAll">Select All</button>
        <button class="btn btn-outline-secondary" id="deselectAll">Deselect All</button>
      </div>
    @endif
  </div>
</div>
@endsection

@section('page-js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle permission
    document.querySelectorAll('.perm-toggle').forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        const roleId = this.dataset.roleId;
        const permissionId = this.dataset.permissionId;
        const action = this.checked ? 'attach' : 'detach';
        fetch("/matrix/update", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ role_id: roleId, permission_id: permissionId, action: action })
        })
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            alert('Failed to update permission!');
          }
        });
      });
    });
    // Bulk select/deselect
    document.getElementById('selectAll').addEventListener('click', function() {
      document.querySelectorAll('.perm-toggle:not(:checked)').forEach(cb => { cb.checked = true; cb.dispatchEvent(new Event('change')); });
    });
    document.getElementById('deselectAll').addEventListener('click', function() {
      document.querySelectorAll('.perm-toggle:checked').forEach(cb => { cb.checked = false; cb.dispatchEvent(new Event('change')); });
    });
  });
</script>
@endsection 