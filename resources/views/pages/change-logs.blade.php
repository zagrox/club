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
</style>
@endsection

@section('content')
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
    
    <div class="mt-4">
      <h6>Manual Change Log</h6>
      <ul class="timeline-with-icons">
        <li class="timeline-item mb-4">
          <span class="timeline-icon">
            <i class="bx bx-plus"></i>
          </span>
          <div class="timeline-event">
            <div class="timeline-header">
              <h6 class="mb-0">v1.0.0 - Initial Release</h6>
              <small class="text-muted">April 26, 2023</small>
            </div>
            <p class="mb-0">Initial release of the application with basic functionality.</p>
          </div>
        </li>
        <li class="timeline-item mb-4">
          <span class="timeline-icon">
            <i class="bx bx-customize"></i>
          </span>
          <div class="timeline-event">
            <div class="timeline-header">
              <h6 class="mb-0">v1.1.0 - Feature Update</h6>
              <small class="text-muted">July 15, 2023</small>
            </div>
            <p class="mb-0">Added user management and improved dashboard analytics.</p>
          </div>
        </li>
        <li class="timeline-item mb-4">
          <span class="timeline-icon">
            <i class="bx bx-bug"></i>
          </span>
          <div class="timeline-event">
            <div class="timeline-header">
              <h6 class="mb-0">v1.1.1 - Bug Fixes</h6>
              <small class="text-muted">August 3, 2023</small>
            </div>
            <p class="mb-0">Fixed authentication issues and improved performance.</p>
          </div>
        </li>
        <li class="timeline-item">
          <span class="timeline-icon">
            <i class="bx bx-rocket"></i>
          </span>
          <div class="timeline-event">
            <div class="timeline-header">
              <h6 class="mb-0">v1.2.0 - Major Update</h6>
              <small class="text-muted">October 12, 2023</small>
            </div>
            <p class="mb-0">Added theme customization and role-based access control.</p>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  // Any specific JavaScript for the Change Logs page can be added here
</script>
@endsection 