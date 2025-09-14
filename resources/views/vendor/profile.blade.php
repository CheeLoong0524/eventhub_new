@extends('layouts.vendor')

@section('title', 'Vendor Profile')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile-info" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="fas fa-user me-2"></i>Profile Information
                    </a>
                    <a href="#company-info" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-building me-2"></i>Company Details
                    </a>
                    <a href="#service-info" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-cogs me-2"></i>Service Information
                    </a>
                    <a href="#documents" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-file-alt me-2"></i>Documents
                    </a>
                    <a href="#social-media" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-share-alt me-2"></i>Social Media
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Vendor Profile</h4>
                    <div>
                        <span class="badge bg-{{ $vendor->status_badge_color }} me-2">{{ ucfirst($vendor->status) }}</span>
                        @if($vendor->is_verified)
                            <span class="badge bg-success">Verified</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Profile Information Tab -->
                        <div class="tab-pane fade show active" id="profile-info">
                            <form method="POST" action="{{ route('vendor.profile.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                                                   value="{{ old('contact_person', $vendor->contact_person) }}" required>
                                            @error('contact_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Phone</label>
                                            <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                                                   value="{{ old('contact_phone', $vendor->contact_phone) }}" required>
                                            @error('contact_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           value="{{ old('contact_email', $vendor->contact_email) }}" required>
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Business Address</label>
                                    <textarea name="business_address" class="form-control @error('business_address') is-invalid @enderror" 
                                              rows="3" required>{{ old('business_address', $vendor->business_address) }}</textarea>
                                    @error('business_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                                           value="{{ old('website', $vendor->website) }}" placeholder="https://example.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                        
                        <!-- Company Information Tab -->
                        <div class="tab-pane fade" id="company-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" value="{{ $vendor->company_name }}" readonly>
                                        <small class="text-muted">Company name cannot be changed after approval</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Business Registration Number</label>
                                        <input type="text" class="form-control" value="{{ $vendor->business_registration_number }}" readonly>
                                        <small class="text-muted">Registration number cannot be changed</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Company Description</label>
                                <textarea class="form-control" rows="4" readonly>{{ $vendor->company_description }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" class="form-control" value="{{ ucfirst($vendor->status) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Approved Date</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $vendor->approved_at ? $vendor->approved_at->format('Y-m-d H:i') : 'N/A' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Service Information Tab -->
                        <div class="tab-pane fade" id="service-info">
                            <form method="POST" action="{{ route('vendor.profile.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="form-label">Service Type</label>
                                    <input type="text" class="form-control" value="{{ $vendor->service_type_label }}" readonly>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Service Description</label>
                                    <textarea name="service_description" class="form-control @error('service_description') is-invalid @enderror" 
                                              rows="4" required>{{ old('service_description', $vendor->service_description) }}</textarea>
                                    @error('service_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Service Categories</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if($vendor->service_categories)
                                            @foreach($vendor->service_categories as $category)
                                                <span class="badge bg-primary">{{ $category }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No categories specified</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Rating</label>
                                            <div class="d-flex align-items-center">
                                                <div class="rating me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $vendor->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="ms-2">{{ number_format($vendor->rating, 1) }}/5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Total Events</label>
                                            <input type="text" class="form-control" value="{{ $vendor->total_events }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Service Info</button>
                            </form>
                        </div>
                        
                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents">
                            @if($vendor->documents && count($vendor->documents) > 0)
                                <div class="row">
                                    @foreach($vendor->documents as $document)
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="card-title">{{ $document['name'] }}</h6>
                                                            <small class="text-muted">{{ number_format($document['size'] / 1024, 1) }} KB</small>
                                                        </div>
                                                        <a href="{{ Storage::url($document['path']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No documents uploaded</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Social Media Tab -->
                        <div class="tab-pane fade" id="social-media">
                            <form method="POST" action="{{ route('vendor.profile.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="url" name="facebook" class="form-control @error('facebook') is-invalid @enderror" 
                                                   value="{{ old('facebook', $vendor->social_media['facebook'] ?? '') }}" 
                                                   placeholder="https://facebook.com/yourpage">
                                            @error('facebook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Instagram</label>
                                            <input type="url" name="instagram" class="form-control @error('instagram') is-invalid @enderror" 
                                                   value="{{ old('instagram', $vendor->social_media['instagram'] ?? '') }}" 
                                                   placeholder="https://instagram.com/yourpage">
                                            @error('instagram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="url" name="twitter" class="form-control @error('twitter') is-invalid @enderror" 
                                                   value="{{ old('twitter', $vendor->social_media['twitter'] ?? '') }}" 
                                                   placeholder="https://twitter.com/yourpage">
                                            @error('twitter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="url" name="linkedin" class="form-control @error('linkedin') is-invalid @enderror" 
                                                   value="{{ old('linkedin', $vendor->social_media['linkedin'] ?? '') }}" 
                                                   placeholder="https://linkedin.com/company/yourcompany">
                                            @error('linkedin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Social Media</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.rating {
    font-size: 1.2rem;
}

.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.tab-content {
    min-height: 400px;
}
</style>
@endsection
