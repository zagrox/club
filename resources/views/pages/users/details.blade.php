<div class="modal-header">
  <h5 class="modal-title" id="userDetailsModalLabel">User Details: {{ $user->name }}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-lg-4 col-md-4 col-12 mb-md-0 mb-4 text-center">
      <div class="card-body">
        <div class="user-avatar-section mb-3">
          <div class="d-flex flex-column align-items-center">
            <div class="avatar avatar-xl mb-3">
              <span class="avatar-initial rounded-circle bg-label-{{ strtolower(substr($user->name, 0, 1)) == 'a' ? 'primary' : (strtolower(substr($user->name, 0, 1)) == 'b' ? 'success' : (strtolower(substr($user->name, 0, 1)) == 'c' ? 'warning' : (strtolower(substr($user->name, 0, 1)) == 'd' ? 'info' : 'secondary'))) }}">
                {{ strtoupper(substr($user->name, 0, 2)) }}
              </span>
            </div>
            <div class="user-info text-center">
              <h4 class="mb-1">{{ $user->name }}</h4>
              <span class="badge bg-label-{{ $user->role === 'Admin' ? 'primary' : ($user->role === 'Editor' ? 'success' : 'secondary') }} rounded-pill">{{ $user->role }}</span>
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-center mb-2">
          <span class="badge bg-label-{{ $user->status === 'Active' ? 'success' : ($user->status === 'Inactive' ? 'warning' : 'secondary') }} rounded-pill">{{ $user->status ?? 'Active' }}</span>
        </div>

        <div class="d-flex justify-content-center pt-3">
          <a href="{{ route('settings.account', ['manage_user_id' => $user->id]) }}" class="btn btn-primary me-2">
            <i class="bx bx-edit-alt me-1"></i> Edit
          </a>
          <button class="btn btn-outline-danger suspend-user">
            <i class="bx bx-trash me-1"></i> Delete
          </button>
        </div>
      </div>
    </div>
    <div class="col-lg-8 col-md-8 col-12">
      <div class="card mb-3">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">User Information</h5>
        </div>
        <div class="card-body">
          <table class="table">
            <tbody>
              <tr>
                <td class="fw-semibold">Email</td>
                <td>{{ $user->email }}</td>
              </tr>
              <tr>
                <td class="fw-semibold">Account created</td>
                <td>{{ $user->created_at->format('F d, Y') }}</td>
              </tr>
              <tr>
                <td class="fw-semibold">Last login</td>
                <td>{{ $user->last_login_at ?? 'Never' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Assigned Roles</h5>
        </div>
        <div class="card-body">
          @if($user->roles->count() > 0)
            <div class="d-flex flex-wrap gap-2">
              @foreach($user->roles as $role)
                <span class="badge bg-label-primary">{{ $role->name }}</span>
              @endforeach
            </div>
          @else
            <p class="text-muted mb-0">No roles assigned</p>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Permissions (via roles)</h5>
        </div>
        <div class="card-body">
          @php
            try {
              $permissions = $user->getPermissionsViaRoles();
            } catch (\Exception $e) {
              $permissions = collect();
            }
          @endphp
          
          @if(count($permissions) > 0)
            <div class="d-flex flex-wrap gap-2">
              @foreach($permissions as $permission)
                <span class="badge bg-label-info">{{ $permission->name }}</span>
              @endforeach
            </div>
          @else
            <p class="text-muted mb-0">No permissions assigned</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
</div> 