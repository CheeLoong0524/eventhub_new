@extends('layouts.admin')

{{-- Author  : Choong Yoong Sheng (Vendor module) --}}

@section('title', 'Event Financial Report - EventHub')
@section('page-title', 'Event Financial Report')
@section('page-description', 'Detailed financial analysis for "' . $event->name . '"')

@section('content')
<div class="container-fluid">
    <!-- Event Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>{{ $event->name }}
                            </h4>
                            <p class="mb-0 text-white-75">{{ $event->venue->name ?? 'N/A' }} • {{ $event->start_time->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-muted mb-2">{{ $event->description }}</p>
                            <div class="d-flex flex-wrap gap-3">
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i>Organizer: {{ $event->organizer }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Duration: {{ $event->start_time->diffForHumans($event->end_time, true) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>View Event
                            </a>
                            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit me-2"></i>Edit Event
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Revenue</h6>
                            <h3 class="stats-number mb-0">RM {{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-store me-1"></i>
                            RM {{ number_format($boothRevenue, 2) }} from booths
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Costs</h6>
                            <h3 class="stats-number mb-0">RM {{ number_format($totalCosts, 2) }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-chart-line me-1"></i>
                            All event expenses
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stats-card {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Net Profit</h6>
                            <h3 class="stats-number mb-0">RM {{ number_format($netProfit, 2) }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-percentage me-1"></i>
                            Margin: {{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 1) : '0.0' }}%
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Booth Occupancy</h6>
                            <h3 class="stats-number mb-0">{{ number_format($occupancyRate, 1) }}%</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-store fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-hashtag me-1"></i>
                            {{ $event->booth_sold }} booths sold
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Revenue Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-success">RM {{ number_format($boothRevenue, 2) }}</h4>
                            <p class="text-muted mb-0">Booth Revenue</p>
                            <small class="text-muted">{{ $boothSalesBreakdown['total_booths'] }} booths sold</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">RM {{ number_format($ticketRevenue, 2) }}</h4>
                            <p class="text-muted mb-0">Ticket Revenue</p>
                            <small class="text-muted">{{ $event->ticket_sold }} tickets × RM {{ number_format($event->ticket_price ?? 0, 2) }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h5 class="text-primary">RM {{ number_format($totalRevenue, 2) }}</h5>
                        <p class="text-muted mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Cost Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="cost-item d-flex justify-content-between mb-2">
                        <span>Venue Cost:</span>
                        <span class="fw-bold">RM {{ number_format($event->venue_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="cost-item d-flex justify-content-between mb-2">
                        <span>Staff Cost:</span>
                        <span class="fw-bold">RM {{ number_format($event->staff_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="cost-item d-flex justify-content-between mb-2">
                        <span>Equipment Cost:</span>
                        <span class="fw-bold">RM {{ number_format($event->equipment_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="cost-item d-flex justify-content-between mb-2">
                        <span>Marketing Cost:</span>
                        <span class="fw-bold">RM {{ number_format($event->marketing_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="cost-item d-flex justify-content-between mb-2">
                        <span>Other Costs:</span>
                        <span class="fw-bold">RM {{ number_format($event->other_costs ?? 0, 2) }}</span>
                    </div>
                    <hr>
                    <div class="cost-item d-flex justify-content-between">
                        <span class="fw-bold">Total Costs:</span>
                        <span class="fw-bold text-warning">RM {{ number_format($totalCosts, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Booth Sales Breakdown -->
    @if(count($boothSalesBreakdown['breakdown']) > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Booth Sales Breakdown by Size
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($boothSalesBreakdown['breakdown'] as $size => $data)
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-primary">{{ $data['size'] }}</h6>
                                    <div class="mb-2">
                                        <span class="h4 text-success">RM {{ number_format($data['revenue'], 2) }}</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small class="text-muted">Quantity</small>
                                            <div class="fw-bold">{{ $data['quantity'] }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Avg Price</small>
                                            <div class="fw-bold">RM {{ number_format($data['average_price'], 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5 class="text-primary">RM {{ number_format($boothSalesBreakdown['total_revenue'], 2) }}</h5>
                            <p class="text-muted mb-0">Total Booth Revenue</p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-info">{{ $boothSalesBreakdown['total_booths'] }}</h5>
                            <p class="text-muted mb-0">Total Booths Sold</p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-success">RM {{ number_format($boothSalesBreakdown['total_booths'] > 0 ? $boothSalesBreakdown['total_revenue'] / $boothSalesBreakdown['total_booths'] : 0, 2) }}</h5>
                            <p class="text-muted mb-0">Average per Booth</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Booth Applications Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Booth Applications ({{ $boothApplications->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($boothApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Business Name</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Paid Date</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boothApplications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($application->vendor->business_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->vendor->business_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $application->vendor->contact_person }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $application->vendor->business_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $application->status === 'paid' ? 'success' : ($application->status === 'approved' ? 'warning' : ($application->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($application->paid_at)
                                                    {{ $application->paid_at->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($application->status === 'paid')
                                                    <span class="text-success fw-bold">RM {{ number_format($event->booth_price, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No booth applications yet</h5>
                            <p class="text-muted">Vendors haven't applied for booths at this event.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>
.stats-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
}

.stats-icon {
    opacity: 0.3;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
    font-weight: bold;
}

.cost-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.cost-item:last-child {
    border-bottom: none;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection
