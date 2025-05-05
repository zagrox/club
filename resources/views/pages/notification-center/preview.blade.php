<div class="notification-preview">
    <div class="preview-header mb-3">
        <div class="d-flex align-items-center mb-2">
            <span class="badge bg-label-{{ $notification->getPriorityBadgeClass() }} me-2">{{ ucfirst($notification->priority) }}</span>
            <span class="badge bg-label-{{ $notification->getCategoryBadgeClass() }}">{{ ucfirst($notification->category) }}</span>
            <span class="ms-auto text-muted small">Just now</span>
        </div>
        <h4 class="preview-title fw-bold">{{ $notification->title }}</h4>
    </div>
    
    <div class="preview-content mb-3">
        {!! $notification->message !!}
    </div>
    
    <div class="preview-footer d-flex justify-content-end">
        <button type="button" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bx bx-x me-1"></i> Dismiss
        </button>
        <button type="button" class="btn btn-sm btn-primary">
            <i class="bx bx-check me-1"></i> Mark as Read
 