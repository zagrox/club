@extends('layouts.app')

@section('title', 'Change Logs')

@section('page-css')
<style>
  .release-date {
    font-size: 0.875rem;
    color: #a1acb8;
  }
  .release-tag {
    font-weight: 600;
    color: #5d87ff;
  }
  .release-body {
    margin-top: 1rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eaeaec;
  }
  .github-link {
    text-decoration: none;
  }
  .github-link:hover {
    text-decoration: underline;
  }
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
<!-- Backup Management Section -->
@if($showBackupSection)
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Backup Management</h5>
    <form action="{{ route('backup.start') }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-primary">
        <i class="bx bx-plus me-1"></i>Create New Backup
      </button>
    </form>
  </div>
  <div class="card-body">
    <div class="alert alert-info">
      <div class="d-flex">
        <i class="bx bx-info-circle bx-sm me-2"></i>
        <div>
          <h6 class="alert-heading mb-1">Backups</h6>
          <p class="mb-0">
            Manage your application backups. You can create new backups, download existing ones, or delete old backups.
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
    
    @if(empty($backups))
      <div class="text-center p-5">
        <i class="bx bx-backup bx-lg text-muted mb-3"></i>
        <h5>No Backups Found</h5>
        <p class="text-muted">No backups are currently available. Create your first backup!</p>
      </div>
    @else
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($backups as $backup)
          <div class="col">
            <div class="card h-100 backup-card">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                  <div>
                    <h5 class="card-title text-truncate mb-0" title="{{ $backup['file_name'] }}">
                      <i class="bx bx-archive me-2"></i>{{ $backup['file_name'] }}
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
    @endif
  </div>
</div>
@endif

<!-- Change Logs Section -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Change Logs</h5>
    <a href="https://github.com/zagrox/club" target="_blank" class="btn btn-primary">
      <i class="bx bxl-github me-1"></i>View on GitHub
    </a>
  </div>
  <div class="card-body">
    <div class="alert alert-primary">
      <div class="d-flex">
        <i class="bx bx-info-circle bx-sm me-2"></i>
        <div>
          <h6 class="alert-heading mb-1">Project Information</h6>
          <p class="mb-0">
            Track all the changes and updates to our application. This page shows the latest tags and versions
            from our GitHub repository.
          </p>
        </div>
      </div>
    </div>
    
    @if(empty($releases))
      <div class="text-center p-5">
        <i class="bx bx-error-circle bx-lg text-muted mb-3"></i>
        <h5>No Tags Found</h5>
        <p class="text-muted">Unable to fetch tag information at this time.</p>
      </div>
    @else
      <div class="timeline">
        @foreach($releases as $release)
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="release-tag">{{ $release['name'] ?? $release['tag_name'] ?? 'Unknown' }}</h5>
              <span class="release-date">{{ isset($release['published_at']) ? \Carbon\Carbon::parse($release['published_at'])->format('F d, Y') : 'Unknown date' }}</span>
            </div>
            <div class="release-body">
              {!! isset($release['body']) ? nl2br(e($release['body'])) : 'No description available' !!}
            </div>
            @if(isset($release['html_url']))
              <div class="mt-2">
                <a href="{{ $release['html_url'] }}" target="_blank" class="github-link">
                  <i class="bx bx-link-external me-1"></i>View tag details
                </a>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection

@section('page-js')
<script>
  // Any specific JavaScript for the Change Logs page can be added here
</script>
@endsection 