@extends('layouts.admin')

@section('title', 'Financial Reports - EventHub')
@section('page-title', 'Financial Reports')
@section('page-description', 'View financial performance and profit/loss analysis')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Events</h6>
                            <h3 class="stats-number mb-0">{{ $totalEvents }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $activeEvents }} Active
                        </small>
                    </div>
                </div>
            </div>
        </div>

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
                            Average: RM {{ $totalEvents > 0 ? number_format($totalCosts / $totalEvents, 2) : '0.00' }} per event
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
    </div>

    <!-- Booth Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store me-2"></i>Booth Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary">{{ $soldBooths }}</h3>
                            <p class="text-muted mb-0">Booths Sold</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $totalBooths - $soldBooths }}</h3>
                            <p class="text-muted mb-0">Available</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $totalBooths > 0 ? ($soldBooths / $totalBooths) * 100 : 0 }}%">
                                {{ $totalBooths > 0 ? number_format(($soldBooths / $totalBooths) * 100, 1) : '0' }}%
                            </div>
                        </div>
                        <small class="text-muted">Occupancy Rate</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>Top Performing Events
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($topEvents as $event)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0">{{ $event->name }}</h6>
                                <small class="text-muted">{{ $event->venue->name ?? 'N/A' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">RM {{ number_format($event->total_revenue, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No events found</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Recent Events Financial Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Venue</th>
                                    <th>Start Date</th>
                                    <th>Booths</th>
                                    <th>Revenue</th>
                                    <th>Costs</th>
                                    <th>Profit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEvents as $event)
                                    <tr>
                                        <td>
                                            <strong>{{ $event->name }}</strong>
                                        </td>
                                        <td>{{ $event->venue->name ?? 'N/A' }}</td>
                                        <td>{{ $event->start_time->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $event->booth_sold }}/{{ $event->booth_quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">RM {{ number_format($event->total_revenue, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-warning fw-bold">RM {{ number_format($event->total_costs, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $event->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                RM {{ number_format($event->net_profit, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $event->status === 'active' ? 'success' : ($event->status === 'completed' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($event->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.reports.event', $event->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No events found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
