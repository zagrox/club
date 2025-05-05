@extends('layouts.app')

@section('title', 'Permission Matrix')

@section('styles')
<style>
  .matrix-table {
    border-collapse: collapse;
  }
  
  .matrix-table th, .matrix-table td {
    border: 1px solid #dee2e6;
  }
  
  .matrix-table th.header-cell {
    background-color: #f8f9fa;
    font-weight: bold;
    text-transform: uppercase;
  }
  
  .matrix-table .role-column {
    border: 1px solid #dc3545 !important;
  }
  
  .matrix-table .header-row th {
    border: 1px solid #dc3545 !important;
    background-color: #f8f9fa;
  }
  
  .matrix-table .checkbox-red {
    accent-color: #0d6efd;
  }
</style>
@endsection

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
      <table class="table table-bordered align-middle text-center matrix-table">
        <thead>
          <tr class="header-row">
            <th class="header-cell role-column" style="min-width: 150px; vertical-align: middle;">
              ROLE \ <br>PERMISSION
            </th>
            @foreach($roles as $role)
              <th class="header-cell" style="min-width: 120px; text-transform: uppercase;">
                {{ strtoupper($role->name) }}<br>
                <small class="text-muted">{{ strtoupper($role->name) }}</small>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @php
            // Group permissions by their namespace (users, roles, etc.)
            $permissionsByGroup = $permissions->groupBy(function($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0];
            });
            
            // Sort groups to match the order in the image
            $orderedGroups = ['users', 'roles', 'permissions', 'orders', 'notifications', 'backups'];
            $sortedPermissionsByGroup = collect();
            
            foreach($orderedGroups as $group) {
                if(isset($permissionsByGroup[$group])) {
                    $sortedPermissionsByGroup[$group] = $permissionsByGroup[$group];
                }
            }
            
            // Add any remaining groups that weren't in our ordered list
            foreach($permissionsByGroup as $group => $permissions) {
                if(!isset($sortedPermissionsByGroup[$group])) {
                    $sortedPermissionsByGroup[$group] = $permissions;
                }
            }
          @endphp

          @foreach($sortedPermissionsByGroup as $group => $groupPermissions)
            @foreach($groupPermissions as $permission)
              <tr>
                <th class="text-start role-column" style="text-transform: uppercase; font-weight: normal;">
                  {{ strtoupper($permission->name) }}
                </th>
          @foreach($roles as $role)
                <td>
                    <div class="form-check d-flex justify-content-center">
                  <input type="checkbox"
                        class="perm-toggle form-check-input checkbox-red"
                    data-role-id="{{ $role->id }}"
                    data-permission-id="{{ $permission->id }}"
                        style="width: 1.2em; height: 1.2em;"
                        @if($role->hasPermissionTo($permission->slug)) checked @endif
                  >
                    </div>
                </td>
              @endforeach
            </tr>
            @endforeach
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