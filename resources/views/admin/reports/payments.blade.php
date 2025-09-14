@extends('layouts.admin')

{{-- Author  : Choong Yoong Sheng (Vendor module) --}}

@section('title', 'Payment Reports - EventHub')
@section('page-title', 'Payment Reports')
@section('page-description', 'View detailed vendor payment history and transactions')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Payments</h6>
                            <h3 class="stats-number mb-0">{{ $totalPayments }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-credit-card fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-check-circle me-1"></i>
                            Successful transactions
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Amount</h6>
                            <h3 class="stats-number mb-0">RM {{ number_format($totalPaymentAmount, 2) }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-chart-line me-1"></i>
                            Revenue collected
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
                            <h6 class="card-title text-white-50 mb-2">Average Payment</h6>
                            <h3 class="stats-number mb-0">RM {{ $totalPayments > 0 ? number_format($totalPaymentAmount / $totalPayments, 2) : '0.00' }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-percentage me-1"></i>
                            Per transaction
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
                            <h6 class="card-title text-white-50 mb-2">This Month</h6>
                            <h3 class="stats-number mb-0">{{ $payments->where('paid_at', '>=', now()->startOfMonth())->count() }}</h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-75">
                            <i class="fas fa-clock me-1"></i>
                            Recent payments
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Payment History
                    </h5>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Vendor</th>
                                        <th>Event</th>
                                        <th>Amount</th>
                                        <th>Paid Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <code class="text-muted">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</code>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($payment->vendor->business_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $payment->vendor->business_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $payment->vendor->contact_person }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $payment->event->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $payment->event->venue->name ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">RM {{ number_format($payment->event->booth_price, 2) }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $payment->paid_at->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $payment->paid_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Paid
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.vendor.applications.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.reports.event', $payment->event->id) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No payments found</h5>
                            <p class="text-muted">No vendor payments have been processed yet.</p>
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
}

code {
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}
</style>
@endsection
