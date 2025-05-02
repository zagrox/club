@extends('layouts.app')

@section('title', 'Backup Management')

@section('page-css')
<style>
  .backup-card {
    transition: transform 0.3s;
  }
  .backup-card:hover {
    transform: translateY(-5px);
  }
  .backup-size {
    font-size: 0.875rem;
    color: #697a8d;
  }
  .backup-date {
    font-size: 0.75rem;
    color: #a1acb8;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Backup Management</h5>
        <div>
          <form action="{{ route('backup.cleanup') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Are you sure you want to run the backup cleanup? This will delete old backups according to your retention policy.');">
              <i class="bx bx-trash me-1"></i>Run Cleanup
            </button>
          </form>
          <form action="{{ route('backup.start') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-plus me-1"></i>Create New Backup
            </button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="alert alert-primary">
          <div class="d-flex">
            <i class="bx bx-info-circle bx-sm me-2"></i>
            <div>
              <h6 class="alert-heading mb-1">About Backups</h6>
              <p class="mb-0">
                Manage your application backups. You can create new backups, download existing ones, or delete old backups.
                Backups contain essential data and configurations for system recovery.
              </p>
            </div>
          </div>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <div class="row mb-3">
          <div class="col-12">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="mb-2">Backup Information</h6>
                <ul class="mb-0">
                  <li>Backups are stored in <code>storage/app/private/mailzila</code></li>
                  <li>Backups are automatically named with date and time</li>
                  <li>We recommend keeping at least 3 recent backups</li>
                  <li>Previous backup files are automatically excluded to prevent recursive backups</li>
                  <li>Backup cleanup retains: all backups for 7 days, daily backups for 16 days, weekly backups for 8 weeks, monthly backups for 4 months</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        
        @if(empty($backups))
          <div class="text-center p-5">
            <i class="bx bx-archive bx-lg text-muted mb-3"></i>
            <h5>No Backups Found</h5>
            <p class="text-muted">No backups are currently available. Create your first backup!</p>
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Filename</th>
                  <th>Size</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($backups as $backup)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bx bx-archive me-2 text-primary"></i>
                        <span>{{ $backup['file_name'] }}</span>
                      </div>
                    </td>
                    <td>{{ $backup['file_size'] }}</td>
                    <td>{{ $backup['last_modified'] }}</td>
                    <td>
                      <div class="d-flex">
                        <a href="{{ route('backup.download', ['fileName' => $backup['file_name']]) }}" class="btn btn-outline-primary btn-sm me-2">
                          <i class="bx bx-download me-1"></i>Download
                        </a>
                        <form action="{{ route('backup.delete') }}" method="POST">
                          @csrf
                          <input type="hidden" name="file_path" value="{{ $backup['file_path'] }}">
                          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?');">
                            <i class="bx bx-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <div class="mt-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
              @foreach($backups as $backup)
                <div class="col">
                  <div class="card h-100 backup-card">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between">
                        <div>
                          <h5 class="card-title text-truncate mb-0" title="{{ $backup['file_name'] }}">
                            <i class="bx bx-archive me-2 text-primary"></i>{{ $backup['file_name'] }}
                          </h5>
                          <p class="backup-size mb-1">Size: {{ $backup['file_size'] }}</p>
                          <p class="backup-date mb-3">Created: {{ $backup['last_modified'] }}</p>
                        </div>
                      </div>
                      <div class="d-flex mt-3">
                        <a href="{{ route('backup.download', ['fileName' => $backup['file_name']]) }}" class="btn btn-outline-primary btn-sm me-2">
                          <i class="bx bx-download me-1"></i>Download
                        </a>
                        <form action="{{ route('backup.delete') }}" method="POST">
                          @csrf
                          <input type="hidden" name="file_path" value="{{ $backup['file_path'] }}">
                          <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?');">
                            <i class="bx bx-trash me-1"></i>Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Any JavaScript specific to the backup management page
  });
</script>
@endsection 