@extends('layouts.admin')

@section('title', 'Check Customer Existence')

@section('content')
<style>
.badge {
    font-size: 0.75em;
    font-weight: 600;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.badge-danger { background-color: #dc3545; color: white; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-success { background-color: #28a745; color: white; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-primary { background-color: #007bff; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }

.role-badge {
    font-size: 0.8em;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.api-info-badge {
    font-size: 0.7em;
    font-weight: 500;
}

.status-badge {
    font-size: 0.8em;
    font-weight: 600;
}
</style>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.support.index') }}">
                    <i class="fas fa-headset"></i> Support
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-user-check"></i> Check Customer
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Check Customer Existence</h3>
                    <p class="card-subtitle text-muted">Verify if customer exists before creating support ticket</p>
                </div>
                <div class="card-body">
                    <form id="customerCheckForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="customerEmail">Customer Email Address</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="customerEmail" 
                                           name="email" 
                                           placeholder="Enter customer email address"
                                           required>
                                    <small class="form-text text-muted">
                                        This will check if the customer exists in our user database
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Check Customer
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="clearForm()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- API Consumption Toggle -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="useApiToggle">
                                    <label class="form-check-label" for="useApiToggle">
                                        Use External API (simulate other module consumption)
                                    </label>
                                    <small class="form-text text-muted">
                                        When checked, this will consume the User Authentication Module API externally
                                    </small>
                                </div>
                                <div id="apiStatus" class="mt-2" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> External API is currently unavailable. Please uncheck the box above to use internal service.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Results Section -->
                    <div id="results" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Customer Check Results</h5>
                            </div>
                            <div class="card-body">
                                <div id="customerInfo"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('customerCheckForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('customerEmail').value;
    const useApi = document.getElementById('useApiToggle').checked;
    
    // Show loading
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    submitBtn.disabled = true;
    
    // Make API call
    fetch('{{ route("admin.support.check.customer") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email: email,
            use_api: useApi
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success === false && data.data_source === 'external_failed') {
            displayExternalFailure(data, useApi);
        } else {
            displayResults(data, useApi);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayError('Failed to check customer: ' + error.message);
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function displayResults(data, usedApi) {
    const resultsDiv = document.getElementById('results');
    const customerInfoDiv = document.getElementById('customerInfo');
    
    // Get actual data source from API response
    const actualDataSource = data.data_source || (usedApi ? 'external' : 'internal');
    const isFallback = actualDataSource === 'internal_fallback';
    
    if (data.success && data.data.exists) {
        const user = data.data.user;
        customerInfoDiv.innerHTML = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle"></i> Customer Found!</h6>
                <p class="mb-0">This customer exists in our system.</p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Customer Details:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Name:</strong> ${user.name}</li>
                        <li><strong>Email:</strong> ${user.email}</li>
                        <li><strong>Role:</strong> <span class="badge ${getRoleBadgeClass(user.role)} role-badge">${user.role.toUpperCase()}</span></li>
                        <li><strong>Status:</strong> <span class="badge ${user.is_active ? 'badge-success' : 'badge-danger'} status-badge">${user.is_active ? 'Active' : 'Inactive'}</span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>API Information:</h6>
                    <ul class="list-unstyled">
                        <li><strong>API Used:</strong> <span class="badge ${getDataSourceBadgeClass(actualDataSource)} api-info-badge">${getDataSourceLabel(actualDataSource)}</span></li>
                        <li><strong>Response Time:</strong> <span class="badge badge-success api-info-badge">Fast</span></li>
                        <li><strong>Module:</strong> <span class="badge badge-info api-info-badge">User Authentication</span></li>
                        ${isFallback ? '<li><strong>Note:</strong> <span class="badge badge-warning api-info-badge">Auto-fallback</span></li>' : ''}
                    </ul>
                </div>
            </div>
        `;
    } else {
        customerInfoDiv.innerHTML = `
            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle"></i> Customer Not Found</h6>
                <p class="mb-0">No customer found with this email address.</p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Next Steps:</h6>
                    <ul>
                        <li>Verify the email address is correct</li>
                        <li>Ask customer to register first</li>
                        <li>Create support ticket as guest</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>API Information:</h6>
                    <ul class="list-unstyled">
                        <li><strong>API Used:</strong> <span class="badge ${usedApi ? 'badge-warning' : 'badge-primary'} api-info-badge">${usedApi ? 'External API' : 'Internal Service'}</span></li>
                        <li><strong>Response:</strong> <span class="badge badge-secondary api-info-badge">User not found</span></li>
                    </ul>
                </div>
            </div>
        `;
    }
    
    resultsDiv.style.display = 'block';
}

function displayExternalFailure(data, usedApi) {
    const resultsDiv = document.getElementById('results');
    const customerInfoDiv = document.getElementById('customerInfo');
    
    // Show API status warning
    document.getElementById('apiStatus').style.display = 'block';
    
    customerInfoDiv.innerHTML = `
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle"></i> External API Failed</h6>
            <p class="mb-2">${data.message}</p>
            <p class="mb-0"><strong>Suggestion:</strong> ${data.suggestion}</p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6>What happened?</h6>
                <ul>
                    <li>External API server is unavailable</li>
                    <li>Network connection failed</li>
                    <li>Service temporarily down</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>How to fix:</h6>
                <ul>
                    <li><strong>Uncheck</strong> "Use External API" checkbox</li>
                    <li>Click "Check Customer" again</li>
                    <li>System will use internal service</li>
                </ul>
                <div class="mt-3">
                    <button class="btn btn-warning btn-sm" onclick="switchToInternal()">
                        <i class="fas fa-arrow-left"></i> Switch to Internal Service
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-3 pt-3 border-top">
            <h6>API Information:</h6>
            <ul class="list-unstyled">
                <li><strong>API Used:</strong> <span class="badge badge-danger api-info-badge">External API (Failed)</span></li>
                <li><strong>Error:</strong> <span class="badge badge-secondary api-info-badge">${data.error}</span></li>
                <li><strong>Status:</strong> <span class="badge badge-danger api-info-badge">Service Unavailable</span></li>
                ${data.debug_info ? `
                <li><strong>Debug Info:</strong> 
                    <small class="text-muted">
                        ${data.debug_info.status ? 'Status: ' + data.debug_info.status + '<br>' : ''}
                        ${data.debug_info.exception_type ? 'Exception: ' + data.debug_info.exception_type + '<br>' : ''}
                        ${data.debug_info.error_message ? 'Message: ' + data.debug_info.error_message : ''}
                    </small>
                </li>
                ` : ''}
            </ul>
        </div>
    `;
    
    resultsDiv.style.display = 'block';
}

function switchToInternal() {
    // Uncheck the external API checkbox
    document.getElementById('useApiToggle').checked = false;
    
    // Hide API status warning
    document.getElementById('apiStatus').style.display = 'none';
    
    // Trigger form submission
    document.getElementById('customerCheckForm').dispatchEvent(new Event('submit'));
}

function displayError(message) {
    const resultsDiv = document.getElementById('results');
    const customerInfoDiv = document.getElementById('customerInfo');
    
    customerInfoDiv.innerHTML = `
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-circle"></i> Error</h6>
            <p class="mb-0">${message}</p>
        </div>
    `;
    
    resultsDiv.style.display = 'block';
}

function clearForm() {
    document.getElementById('customerCheckForm').reset();
    document.getElementById('results').style.display = 'none';
}

function getRoleBadgeClass(role) {
    switch(role.toLowerCase()) {
        case 'admin':
            return 'badge-danger';
        case 'vendor':
            return 'badge-warning';
        case 'customer':
            return 'badge-success';
        default:
            return 'badge-secondary';
    }
}

function getDataSourceLabel(dataSource) {
    switch(dataSource) {
        case 'external':
            return 'External API';
        case 'internal':
            return 'Internal Service';
        case 'internal_fallback':
            return 'Internal Service (Fallback)';
        case 'external_failed':
            return 'External API (Failed)';
        default:
            return 'Unknown';
    }
}

function getDataSourceBadgeClass(dataSource) {
    switch(dataSource) {
        case 'external':
            return 'badge-warning';
        case 'internal':
            return 'badge-primary';
        case 'internal_fallback':
            return 'badge-info';
        case 'external_failed':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}
</script>
@endsection
