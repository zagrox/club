@extends('layouts.app')

@section('title', 'صندوق پیام‌ها')

@section('page-css')
<style>
    .notification-item {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .notification-item:hover {
        background-color: rgba(93, 135, 255, 0.05);
    }
    
    .notification-item.unread {
        border-left: 3px solid #5d87ff;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .notification-time {
        font-size: 0.75rem;
        color: #a1acb8;
    }
    
    .notification-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .notification-content {
        color: #697a8d;
    }
    
    .mark-read {
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .notification-item:hover .mark-read {
        visibility: visible;
        opacity: 1;
    }
    
    .notification-tabs .nav-link {
        color: #697a8d;
        font-weight: 500;
    }
    
    .notification-tabs .nav-link.active {
        color: #5d87ff;
        background-color: rgba(93, 135, 255, 0.1);
    }
    
    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(50%, -50%);
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">حساب کاربری /</span> صندوق پیام‌ها
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">پیام‌ها و اعلان‌ها</h5>
                    
                    <div>
                        <a href="{{ route('inbox.markAllAsRead') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-check-double me-1"></i>خواندن همه
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <!-- Notification Tabs -->
                    <ul class="nav nav-tabs notification-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" id="standard-tab" data-bs-toggle="tab" href="#standard-notifications">
                                اعلان‌های استاندارد
                                @if($dbNotifications->count() > 0)
                                    <span class="badge bg-primary ms-1">{{ $dbNotifications->count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="wallet-tab" data-bs-toggle="tab" href="#wallet-notifications">
                                اعلان‌های کیف پول
                                @if($walletNotifications->count() > 0)
                                    <span class="badge bg-primary ms-1">{{ $walletNotifications->count() }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Standard Notifications -->
                        <div class="tab-pane fade show active" id="standard-notifications">
                            @if($dbNotifications->count() > 0)
                                <div class="notifications-list">
                                    @foreach($dbNotifications as $notification)
                                        @php
                                            $notificationData = $notification->data;
                                            $isRead = $notification->read_at !== null;
                                            $icon = $notificationData['icon'] ?? 'bx-bell';
                                            $color = $notificationData['color'] ?? 'primary';
                                        @endphp
                                        
                                        <div class="notification-item p-3 {{ $isRead ? '' : 'unread' }}" data-id="{{ $notification->id }}" data-type="standard">
                                            <div class="d-flex align-items-start">
                                                <div class="notification-icon bg-label-{{ $color }} me-3">
                                                    <i class="bx {{ $icon }}"></i>
                                                </div>
                                                
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="notification-title">{{ $notificationData['title'] ?? 'اعلان جدید' }}</h6>
                                                        <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    
                                                    <p class="notification-content mb-1">{{ $notificationData['message'] ?? '' }}</p>
                                                    
                                                    @if(isset($notificationData['description']) && !empty($notificationData['description']))
                                                        <small class="text-muted">{{ $notificationData['description'] }}</small>
                                                    @endif
                                                    
                                                    <div class="mt-2">
                                                        <a href="{{ route('inbox.readAndRedirect', ['id' => $notification->id, 'type' => 'standard']) }}" class="btn btn-sm btn-outline-{{ $color }}">
                                                            <i class="bx bx-link-external me-1"></i>مشاهده جزئیات
                                                        </a>
                                                        
                                                        @if(!$isRead)
                                                            <button type="button" class="btn btn-sm btn-outline-secondary mark-read ms-2" data-id="{{ $notification->id }}" data-type="standard">
                                                                <i class="bx bx-check me-1"></i>خوانده شد
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $dbNotifications->links() }}
                                </div>
                            @else
                                <div class="text-center p-5">
                                    <i class="bx bx-envelope-open bx-lg text-primary mb-3"></i>
                                    <h5>اعلان استانداردی وجود ندارد</h5>
                                    <p class="text-muted">در حال حاضر هیچ اعلانی در این بخش ندارید.</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Wallet Notifications -->
                        <div class="tab-pane fade" id="wallet-notifications">
                            @if($walletNotifications->count() > 0)
                                <div class="notifications-list">
                                    @foreach($walletNotifications as $notification)
                                        @php
                                            $notificationData = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                            $isRead = $notification->read_at !== null;
                                            $icon = $notificationData['icon'] ?? 'bx-wallet';
                                            $color = $notificationData['color'] ?? 'primary';
                                        @endphp
                                        
                                        <div class="notification-item p-3 {{ $isRead ? '' : 'unread' }}" data-id="{{ $notification->id }}" data-type="wallet">
                                            <div class="d-flex align-items-start">
                                                <div class="notification-icon bg-label-{{ $color }} me-3">
                                                    <i class="bx {{ $icon }}"></i>
                                                </div>
                                                
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="notification-title">{{ $notificationData['title'] ?? 'اعلان کیف پول' }}</h6>
                                                        <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    
                                                    <p class="notification-content mb-1">{{ $notificationData['message'] ?? '' }}</p>
                                                    
                                                    @if(isset($notificationData['description']) && !empty($notificationData['description']))
                                                        <small class="text-muted">{{ $notificationData['description'] }}</small>
                                                    @endif
                                                    
                                                    <div class="mt-2">
                                                        <a href="{{ route('inbox.readAndRedirect', ['id' => $notification->id, 'type' => 'wallet']) }}" class="btn btn-sm btn-outline-{{ $color }}">
                                                            <i class="bx bx-link-external me-1"></i>مشاهده جزئیات
                                                        </a>
                                                        
                                                        @if(!$isRead)
                                                            <button type="button" class="btn btn-sm btn-outline-secondary mark-read ms-2" data-id="{{ $notification->id }}" data-type="wallet">
                                                                <i class="bx bx-check me-1"></i>خوانده شد
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $walletNotifications->links() }}
                                </div>
                            @else
                                <div class="text-center p-5">
                                    <i class="bx bx-wallet bx-lg text-primary mb-3"></i>
                                    <h5>اعلان کیف پولی وجود ندارد</h5>
                                    <p class="text-muted">در حال حاضر هیچ اعلان کیف پولی ندارید.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle mark as read buttons
        document.querySelectorAll('.mark-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                const notificationType = this.getAttribute('data-type') || 'standard';
                
                // Send AJAX request to mark notification as read
                fetch('{{ route("inbox.markAsRead") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: notificationId, type: notificationType })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove unread class and button
                        const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"][data-type="${notificationType}"]`);
                        notificationItem.classList.remove('unread');
                        this.remove();
                        
                        // Update unread count in navbar
                        updateNotificationCount();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            });
        });
        
        // Handle mark all as read link
        const markAllAsReadLink = document.querySelector('a[href="{{ route("inbox.markAllAsRead") }}"]');
        if (markAllAsReadLink) {
            markAllAsReadLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Send AJAX request to mark all as read
                fetch('{{ route("inbox.markAllAsRead") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove unread class from all items
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                        });
                        
                        // Remove all mark read buttons
                        document.querySelectorAll('.mark-read').forEach(button => {
                            button.remove();
                        });
                        
                        // Update unread count in navbar
                        updateNotificationCount(0);
                        
                        // Show success message
                        const successElement = document.createElement('div');
                        successElement.className = 'alert alert-success mb-3';
                        successElement.textContent = data.message;
                        document.querySelector('.card-body').prepend(successElement);
                        
                        // Remove success message after 3 seconds
                        setTimeout(() => {
                            successElement.remove();
                        }, 3000);
                    }
                })
                .catch(error => console.error('Error marking all notifications as read:', error));
            });
        }
        
        // Helper function to update notification count in navbar
        function updateNotificationCount(count = null) {
            const unreadBadge = document.querySelector('.navbar .badge-notifications');
            if (unreadBadge) {
                if (count !== null) {
                    unreadBadge.textContent = count > 0 ? count : '';
                    if (count <= 0) {
                        unreadBadge.style.display = 'none';
                    }
                } else {
                    // If count is not provided, decrement the current count
                    const currentCount = parseInt(unreadBadge.textContent) - 1;
                    unreadBadge.textContent = currentCount > 0 ? currentCount : '';
                    if (currentCount <= 0) {
                        unreadBadge.style.display = 'none';
                    }
                }
            }
        }
    });
</script>
@endsection 