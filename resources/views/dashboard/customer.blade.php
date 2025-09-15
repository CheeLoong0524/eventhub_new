@extends('layouts.app')
/** Author: Tan Chim Yang 
 * RSW2S3G4
 * 23WMR14610 
 * **/
@section('title', 'Customer Dashboard - EventHub')

@section('styles')
<style>
    
    .dashboard-header {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        margin-left: 1rem;
        margin-right: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="customer-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23customer-pattern)"/></svg>');
        opacity: 0.3;
    }
    
    .dashboard-header > * {
        position: relative;
        z-index: 2;
    }
    
    .welcome-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
        color: white;
    }
    
    .stats-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .stats-card .card-body {
        padding: 1.5rem;
    }
    
    .stats-card .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
    }
    
    .stats-card .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-card .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 500;
    }
    
    .quick-actions-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .quick-actions-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1.5rem;
    }
    
    .action-btn {
        border-radius: 12px;
        padding: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        text-decoration: none;
    }
    
    .upcoming-events-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .upcoming-events-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 1.5rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem 1.5rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }
    
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    .timeline-item {
        position: relative;
    }
    
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background: #e9ecef;
    }
    
    .upcoming-event-card {
        transition: all 0.3s ease;
        border-radius: 16px;
        overflow: hidden;
    }
    
    .upcoming-event-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important;
    }
    
    .upcoming-event-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0;
    }
    
    .event-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(0,123,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    
    .event-details .event-icon:last-child {
        background: rgba(220,53,69,0.1);
    }
    
    .event-status .badge {
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem 1.5rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }
    
    /* Receipt Card Styles */
    .receipt-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        background: #fff;
    }
    
    .receipt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-color: #007bff;
    }
    
    .receipt-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }
    
    .receipt-title {
        color: #495057;
        font-size: 1.1rem;
    }
    
    .receipt-order {
        font-size: 0.85rem;
    }
    
    .receipt-item {
        padding: 0.5rem 0;
    }
    
    .amount-display {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #90caf9;
    }
    
    .amount {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .currency {
        font-size: 1rem;
        margin-right: 0.25rem;
    }
    
    /* Receipt Detail Modal Styles */
    .receipt-detail-container {
        font-family: 'Courier New', monospace;
        background: #fff;
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    
    .receipt-header h4 {
        color: #007bff;
        font-weight: 700;
    }
    
    .receipt-info h6 {
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    .event-details .card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .event-details .card-title {
        color: #007bff;
        font-weight: 600;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }
    
    .receipt-footer {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 4px;
        margin-top: 1rem;
    }
    
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .stats-card .stats-number {
            font-size: 2rem;
        }
        
        .action-btn {
            margin-bottom: 0.5rem;
        }
        
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        .upcoming-event-card .card-body {
            padding: 1.5rem;
        }
        
        .event-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="dashboard-header text-center">
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-user me-3"></i>Customer Dashboard
        </h1>
        <p class="lead mb-0 opacity-90">Discover amazing events and manage your bookings</p>
        <p class="small opacity-75 mt-2">
            <i class="fas fa-calendar me-1"></i>Today: {{ now()->format('M d, Y') }}
        </p>
    </div>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title fw-bold mb-2">
                            <i class="fas fa-heart me-2"></i>Welcome back, {{ $user->name }}!
                        </h5>
                        <p class="card-text mb-0 opacity-90">
                            Discover amazing events, book tickets, and enjoy unforgettable experiences with EventHub. 
                            Start exploring and create memories that last a lifetime!
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <i class="fas fa-user fa-4x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $bookedEvents }}</div>
                        <div class="stats-label">Booked Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-ticket-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $attendedEvents }}</div>
                        <div class="stats-label">Attended Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $upcomingEvents }}</div>
                        <div class="stats-label">Upcoming Events</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">RM {{ number_format($totalSpent, 0) }}</div>
                        <div class="stats-label">Total Spent</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card quick-actions-card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                </h5>
                <p class="text-muted mb-0 small">Access frequently used customer functions</p>
            </div>
            <div class="card-body">
                <div class="row g-3 justify-content-center">
                    <div class="col-lg-6 col-md-6">
                        <a href="{{ route('customer.events.index') }}" class="action-btn btn-primary">
                            <i class="fas fa-search me-2"></i>Browse Events
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <a href="#" class="action-btn btn-info" onclick="showReceipts()">
                            <i class="fas fa-receipt me-2"></i>My Receipts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Upcoming Events -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card upcoming-events-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-calendar me-2 text-primary"></i>My Upcoming Events
                    </h5>
                    <p class="text-muted mb-0 small">Your booked events coming up</p>
                </div>
            </div>
            <div class="card-body">
                @if($upcomingEventsList->count() > 0)
                    <div class="row g-4">
                        @foreach($upcomingEventsList as $order)
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-lg h-100 upcoming-event-card">
                                <div class="card-header bg-gradient-primary text-white border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0 fw-bold">{{ $order->event->name }}</h6>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-ticket-alt me-1"></i>{{ array_sum(array_column($order->ticket_details, 'quantity')) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="event-details mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="event-icon me-3">
                                                <i class="fas fa-calendar-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    @if($order->event->start_date)
                                                        {{ $order->event->start_date->format('M d, Y') }}
                                                    @elseif($order->event->start_time)
                                                        {{ $order->event->start_time->format('M d, Y') }}
                                                    @else
                                                        Date TBA
                                                    @endif
                                                </div>
                                                @if($order->event->start_time)
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $order->event->start_time->format('g:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="event-icon me-3">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    {{ $order->event->venue ? $order->event->venue->name : 'Venue TBA' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="event-status mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Confirmed
                                            </span>
                                            <span class="badge bg-info px-3 py-2">
                                                <i class="fas fa-ticket-alt me-1"></i>{{ array_sum(array_column($order->ticket_details, 'quantity')) }} tickets
                                            </span>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-primary fw-bold fs-5">
                                                RM {{ number_format($order->total_amount, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <a href="{{ route('customer.events.show', $order->event->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>View Event Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar"></i>
                        <h6 class="mb-2">No Upcoming Events</h6>
                        <p class="mb-2">Check back soon for exciting upcoming events!</p>
                        <a href="{{ route('customer.events.index') }}" class="btn btn-primary btn-sm" style="padding: 0.375rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-search me-1" style="font-size: 0.75rem;"></i>Browse Events
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card upcoming-events-card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-info"></i>Recent Activity
                </h5>
                <p class="text-muted mb-0 small">Track your latest bookings and activities</p>
            </div>
            <div class="card-body">
                @if($recentActivity->count() > 0)
                    <div class="timeline">
                        @foreach($recentActivity as $order)
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-marker me-3">
                                @php
                                    $event = $order->event;
                                    $eventDate = $event->start_date ? $event->start_date->toDateString() : 
                                                ($event->start_time ? $event->start_time->toDateString() : null);
                                    $isAttended = $eventDate && $eventDate <= now()->toDateString();
                                @endphp
                                <div class="timeline-icon {{ $isAttended ? 'bg-success' : 'bg-warning' }}">
                                    <i class="fas fa-{{ $isAttended ? 'check-circle' : 'calendar' }} text-white"></i>
                                </div>
                            </div>
                            <div class="timeline-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $order->event->name }}</h6>
                                        <p class="text-muted small mb-1">Order #{{ $order->order_number }} - {{ array_sum(array_column($order->ticket_details, 'quantity')) }} tickets</p>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                            <span class="badge bg-info me-2">
                                                <i class="fas fa-ticket-alt me-1"></i>{{ array_sum(array_column($order->ticket_details, 'quantity')) }} tickets
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                @if($order->event->start_date)
                                                    {{ $order->event->start_date->format('M d, Y') }}
                                                @elseif($order->event->start_time)
                                                    {{ $order->event->start_time->format('M d, Y') }}
                                                @else
                                                    TBA
                                                @endif
                                                @if($order->event->start_time)
                                                    <i class="fas fa-clock me-1 ms-2"></i>{{ $order->event->start_time->format('g:i A') }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">RM {{ number_format($order->total_amount, 2) }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h6 class="mb-2">No Recent Activity</h6>
                        <p class="mb-2">Start by browsing and booking your first event!</p>
                        <a href="{{ route('customer.events.index') }}" class="btn btn-primary btn-sm" style="padding: 0.375rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-search me-1" style="font-size: 0.75rem;"></i>Browse Events
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
</div>

@section('scripts')
<script>
function showReceipts() {
    // Use the recent activity data that's already loaded on the page
    const recentActivity = @json($recentActivity);
    
    if (recentActivity && recentActivity.length > 0) {
        // Transform the data to match the expected format
        const receipts = recentActivity.map(order => ({
            order_number: order.order_number,
            event: {
                name: order.event.name
            },
            payment: {
                amount: order.total_amount,
                status: 'completed'
            },
            ticket_details: order.ticket_details,
            created_at: order.created_at,
            order_date: order.created_at
        }));
        
        showReceiptsModal(receipts);
    } else {
        showAlert('info', 'No receipts found. Book some events to see your receipts here!');
    }
}

function showReceiptsModal(receipts) {
    let modalHtml = `
        <div class="modal fade" id="receiptsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-receipt me-2"></i>My Receipts
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(0.5);"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="row g-0">
    `;
    
    receipts.forEach((receipt, index) => {
        const ticketQuantity = receipt.ticket_details ? receipt.ticket_details.reduce((sum, ticket) => sum + ticket.quantity, 0) : 0;
        const totalAmount = parseFloat(receipt.payment ? receipt.payment.amount : receipt.total_amount || 0);
        const orderDate = new Date(receipt.order_date || receipt.created_at).toLocaleDateString('en-MY', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        modalHtml += `
            <div class="col-lg-6">
                <div class="receipt-card h-100 border-end border-bottom">
                    <div class="receipt-header p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="receipt-title mb-1 fw-bold">${receipt.event ? receipt.event.name : 'Event Name'}</h6>
                                <p class="receipt-order text-muted small mb-0">Order #${receipt.order_number}</p>
                            </div>
                            <div class="receipt-status">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Paid
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="receipt-body px-4 pb-4">
                        <div class="receipt-details mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="receipt-item">
                                        <small class="text-muted">Tickets</small>
                                        <div class="fw-semibold">
                                            <i class="fas fa-ticket-alt me-1 text-primary"></i>${ticketQuantity}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="receipt-item">
                                        <small class="text-muted">Date</small>
                                        <div class="fw-semibold">
                                            <i class="fas fa-calendar me-1 text-info"></i>${orderDate}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="receipt-amount text-center mb-3">
                            <div class="amount-display">
                                <span class="currency text-muted">RM</span>
                                <span class="amount text-primary fw-bold">${totalAmount.toFixed(2)}</span>
                            </div>
                        </div>
                        
                        <div class="receipt-actions">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewReceipt('${receipt.order_number}')">
                                    <i class="fas fa-eye me-2"></i>View Full Receipt
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="downloadReceipt('${receipt.order_number}')">
                                    <i class="fas fa-download me-2"></i>Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    modalHtml += `
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('receiptsModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('receiptsModal'));
    modal.show();
}

function viewReceipt(orderNumber) {
    // Fetch receipt data and show in modal
    fetch(`/api/v1/receipts/order/${orderNumber}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Handle both direct data and wrapped data responses
            const receiptData = data.data || data;
            if (receiptData) {
                showReceiptDetailModal(receiptData);
            } else {
                showAlert('error', 'Failed to load receipt details.');
            }
        })
        .catch(error => {
            console.error('Error fetching receipt:', error);
            showAlert('error', 'Failed to load receipt details.');
        });
}

function downloadReceipt(orderNumber) {
    // Open receipt in new tab for printing/downloading
    window.open(`/api/v1/receipts/order/${orderNumber}`, '_blank');
}

function showReceiptDetailModal(receiptData) {
    console.log('Receipt data received:', receiptData);
    
    // Handle different data structures
    let receipt;
    if (receiptData.data) {
        receipt = receiptData.data;
    } else if (receiptData.receipt) {
        receipt = receiptData.receipt;
    } else {
        receipt = receiptData;
    }
    
    console.log('Processed receipt:', receipt);
    console.log('Event data:', receipt.event);
    console.log('Ticket details:', receipt.ticket_details);
    console.log('Tickets data:', receipt.tickets);
    
    // Calculate ticket quantity from ticket_details array or tickets object
    let ticketQuantity = 0;
    if (receipt.tickets && receipt.tickets.quantity) {
        ticketQuantity = parseInt(receipt.tickets.quantity) || 0;
    } else if (receipt.ticket_details && Array.isArray(receipt.ticket_details)) {
        ticketQuantity = receipt.ticket_details.reduce((sum, ticket) => sum + (parseInt(ticket.quantity) || 0), 0);
    }
    
    console.log('Calculated ticket quantity:', ticketQuantity);
    
    // Calculate total amount
    let totalAmount = 0;
    if (receipt.payment && receipt.payment.amount) {
        totalAmount = parseFloat(receipt.payment.amount);
    } else if (receipt.total_amount) {
        totalAmount = parseFloat(receipt.total_amount);
    }
    
    // Format order date
    let orderDate = 'N/A';
    if (receipt.order_date) {
        orderDate = new Date(receipt.order_date).toLocaleDateString('en-MY', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Kuala_Lumpur'
        });
    } else if (receipt.created_at) {
        orderDate = new Date(receipt.created_at).toLocaleDateString('en-MY', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Kuala_Lumpur'
        });
    }
    
    console.log('Calculated order date:', orderDate);
    
    // Calculate unit price
    let unitPrice = 0;
    if (receipt.tickets && receipt.tickets.unit_price) {
        unitPrice = parseFloat(receipt.tickets.unit_price);
    } else if (ticketQuantity > 0) {
        unitPrice = totalAmount / ticketQuantity;
    }
    
    console.log('Calculated unit price:', unitPrice);
    
    // Get event details
    const eventName = receipt.event ? receipt.event.name : 'Event Name';
    
    // Handle date - check both date and time fields
    let eventDate = 'TBA';
    if (receipt.event) {
        console.log('Event date:', receipt.event.date);
        console.log('Event time:', receipt.event.time);
        
        if (receipt.event.date) {
            eventDate = new Date(receipt.event.date).toLocaleDateString('en-MY', { timeZone: 'Asia/Kuala_Lumpur' });
        } else if (receipt.event.time) {
            eventDate = new Date(receipt.event.time).toLocaleDateString('en-MY', { timeZone: 'Asia/Kuala_Lumpur' });
        }
    }
    
    // Handle time - check time field
    let eventTime = 'TBA';
    if (receipt.event && receipt.event.time) {
        eventTime = new Date(receipt.event.time).toLocaleTimeString('en-MY', {hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Kuala_Lumpur'});
    }
    
    console.log('Calculated event date:', eventDate);
    console.log('Calculated event time:', eventTime);
    
    // Handle venue - check if venue is loaded
    let eventVenue = 'TBA';
    if (receipt.event && receipt.event.venue) {
        // Venue can be a string or object
        if (typeof receipt.event.venue === 'string') {
            eventVenue = receipt.event.venue;
        } else {
            eventVenue = receipt.event.venue.name || receipt.event.venue;
        }
    } else if (receipt.event && receipt.event.venue_id) {
        // If venue is not loaded, show venue ID
        eventVenue = 'Venue ID: ' + receipt.event.venue_id;
    }
    
    console.log('Calculated event venue:', eventVenue);
    
    // Get customer details
    const customerName = receipt.customer_name || (receipt.customer ? receipt.customer.name : 'N/A');
    const customerEmail = receipt.customer_email || (receipt.customer ? receipt.customer.email : 'N/A');
    const customerPhone = receipt.customer_phone || (receipt.customer ? receipt.customer.phone : 'Not provided');
    
    // Get payment details
    const paymentMethod = receipt.payment ? 
        (receipt.payment.payment_method || receipt.payment.method || 'N/A') : 'N/A';
    const paymentStatus = receipt.payment ? 
        (receipt.payment.status || 'COMPLETED') : 'COMPLETED';
    
    let modalHtml = `
        <div class="modal fade" id="receiptDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-receipt me-2"></i>Receipt Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(0.5);"></button>
                    </div>
                    <div class="modal-body">
                        <div class="receipt-detail-container">
                            <!-- Receipt Header -->
                            <div class="receipt-header text-center mb-4">
                                <h4 class="text-primary fw-bold">EventHub</h4>
                                <p class="text-muted mb-0">Event Booking Receipt</p>
                                <hr class="my-3">
                            </div>
                            
                            <!-- Receipt Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="receipt-info">
                                        <h6 class="text-muted mb-2">Receipt Information</h6>
                                        <p class="mb-1"><strong>Receipt ID:</strong> ${receipt.order_number || 'N/A'}</p>
                                        <p class="mb-1"><strong>Order Number:</strong> ${receipt.order_number || 'N/A'}</p>
                                        <p class="mb-0"><strong>Order Date:</strong> ${orderDate}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="receipt-info">
                                        <h6 class="text-muted mb-2">Customer Information</h6>
                                        <p class="mb-1"><strong>Name:</strong> ${customerName}</p>
                                        <p class="mb-1"><strong>Email:</strong> ${customerEmail}</p>
                                        <p class="mb-0"><strong>Phone:</strong> ${customerPhone}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Event Details -->
                            <div class="event-details mb-4">
                                <h6 class="text-muted mb-3">Event Details</h6>
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">${eventName}</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><i class="fas fa-calendar me-2 text-info"></i><strong>Date:</strong> ${eventDate}</p>
                                                <p class="mb-1"><i class="fas fa-clock me-2 text-info"></i><strong>Time:</strong> ${eventTime}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong>Venue:</strong> ${eventVenue}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Details -->
                            <div class="payment-details mb-4">
                                <h6 class="text-muted mb-3">Payment Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Description</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-end">Unit Price</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Event Ticket</td>
                                                <td class="text-center">${ticketQuantity}</td>
                                                <td class="text-end">RM ${unitPrice.toFixed(2)}</td>
                                                <td class="text-end">RM ${totalAmount.toFixed(2)}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Total Amount:</th>
                                                <th class="text-end text-primary">RM ${totalAmount.toFixed(2)}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="payment-method mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Payment Method:</strong> ${paymentMethod.toUpperCase()}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Status:</strong> 
                                            <span class="badge bg-success">${paymentStatus.toUpperCase()}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="receipt-footer text-center">
                                <hr class="my-3">
                                <p class="text-muted small mb-0">Thank you for choosing EventHub!</p>
                                <p class="text-muted small">Generated on: ${new Date().toLocaleString('en-MY')}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="downloadReceipt('${receipt.order_number}')">
                            <i class="fas fa-download me-2"></i>Download Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('receiptDetailModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('receiptDetailModal'));
    modal.show();
}

function showAllUpcomingEvents() {
    // Redirect to events page with a filter for upcoming events
    window.location.href = '{{ route("customer.events.index") }}?filter=upcoming';
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-custom');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show alert-custom position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the end of body
    document.body.appendChild(alertDiv);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection 