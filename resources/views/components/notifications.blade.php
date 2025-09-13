@props(['notifications', 'unreadCount'])

<div class="notification-dropdown">
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle position-relative" type="button" 
                data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell"></i>
            @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadCount }}
                </span>
            @endif
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end notification-list" style="width: 350px; max-height: 400px; overflow-y: auto;">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <li class="notification-item {{ $notification->status === 'unread' ? 'unread' : '' }}">
                        <div class="d-flex align-items-start p-3 border-bottom">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1 notification-message">{{ $notification->message }}</h6>
                                    @if($notification->status === 'unread')
                                        <span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                </small>
                                @if($notification->inquiry)
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            <i class="fas fa-ticket-alt"></i> Inquiry #{{ $notification->inquiry_id }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
                
                <li class="dropdown-divider"></li>
                <li class="text-center p-2">
                    <a href="#" class="btn btn-sm btn-outline-primary mark-all-read">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </a>
                </li>
            @else
                <li class="text-center p-4 text-muted">
                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                    <p class="mb-0">No notifications yet</p>
                </li>
            @endif
        </ul>
    </div>
</div>

<style>
.notification-item.unread {
    background-color: #f8f9fa;
}

.notification-item:hover {
    background-color: #e9ecef;
}

.notification-list {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.notification-message {
    font-size: 0.9rem;
    line-height: 1.4;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark all as read functionality
    const markAllReadBtn = document.querySelector('.mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to update notifications
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>
