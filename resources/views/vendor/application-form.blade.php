@extends('layouts.app')

@section('title', 'Vendor Application - EventHub')

@section('content')
<div class="container py-5">

    @if ($errors->any())
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <h4 class="card-title mb-0 text-center">
                        <i class="fas fa-file-alt me-2"></i>Vendor Application Form
                    </h4>
                </div>
                
                <div class="card-body p-5">
                    <form method="post" action="{{ route('vendor.apply.submit') }}" id="vendorApplicationForm" novalidate>
        @csrf

                        <!-- Business & Product Information Section -->
                        <div class="mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-store me-2"></i>Business & Product Information
                            </h5>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                               id="business_name" placeholder="Business Name" value="{{ old('business_name') }}" required>
                                        <label for="business_name">Business Name *</label>
                                        @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="business_type" class="form-select @error('business_type') is-invalid @enderror" id="business_type" required>
                                            <option value="">Select Business Type</option>
                                            <option value="retail" {{ old('business_type') == 'retail' ? 'selected' : '' }}>Retail</option>
                                            <option value="food_beverage" {{ old('business_type') == 'food_beverage' ? 'selected' : '' }}>Food & Beverage</option>
                                            <option value="technology" {{ old('business_type') == 'technology' ? 'selected' : '' }}>Technology</option>
                                            <option value="health_wellness" {{ old('business_type') == 'health_wellness' ? 'selected' : '' }}>Health & Wellness</option>
                                            <option value="fashion_beauty" {{ old('business_type') == 'fashion_beauty' ? 'selected' : '' }}>Fashion & Beauty</option>
                                            <option value="home_garden" {{ old('business_type') == 'home_garden' ? 'selected' : '' }}>Home & Garden</option>
                                            <option value="automotive" {{ old('business_type') == 'automotive' ? 'selected' : '' }}>Automotive</option>
                                            <option value="education" {{ old('business_type') == 'education' ? 'selected' : '' }}>Education</option>
                                            <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <label for="business_type">Business Type *</label>
                                        @error('business_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

            <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="product_category" class="form-select @error('product_category') is-invalid @enderror" id="product_category" required>
                                            <option value="">Select Main Product Category</option>
                                            <option value="food_beverage" {{ old('product_category') == 'food_beverage' ? 'selected' : '' }}>Food & Beverage</option>
                                            <option value="fashion_accessories" {{ old('product_category') == 'fashion_accessories' ? 'selected' : '' }}>Fashion & Accessories</option>
                                            <option value="home_garden" {{ old('product_category') == 'home_garden' ? 'selected' : '' }}>Home & Garden</option>
                                            <option value="beauty_wellness" {{ old('product_category') == 'beauty_wellness' ? 'selected' : '' }}>Beauty & Wellness</option>
                                            <option value="electronics" {{ old('product_category') == 'electronics' ? 'selected' : '' }}>Electronics & Gadgets</option>
                                            <option value="art_craft" {{ old('product_category') == 'art_craft' ? 'selected' : '' }}>Art & Craft</option>
                                            <option value="sports_outdoor" {{ old('product_category') == 'sports_outdoor' ? 'selected' : '' }}>Sports & Outdoor</option>
                                            <option value="books_media" {{ old('product_category') == 'books_media' ? 'selected' : '' }}>Books & Media</option>
                                            <option value="toys_games" {{ old('product_category') == 'toys_games' ? 'selected' : '' }}>Toys & Games</option>
                                            <option value="automotive" {{ old('product_category') == 'automotive' ? 'selected' : '' }}>Automotive</option>
                                            <option value="other" {{ old('product_category') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <label for="product_category">Main Product Category *</label>
                                        @error('product_category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
            </div>

            <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="target_audience" class="form-select @error('target_audience') is-invalid @enderror" id="target_audience" required>
                                            <option value="">Select Target Audience</option>
                                            <option value="children" {{ old('target_audience') == 'children' ? 'selected' : '' }}>Children (0-12 years)</option>
                                            <option value="teens" {{ old('target_audience') == 'teens' ? 'selected' : '' }}>Teens (13-19 years)</option>
                                            <option value="young_adults" {{ old('target_audience') == 'young_adults' ? 'selected' : '' }}>Young Adults (20-35 years)</option>
                                            <option value="middle_aged" {{ old('target_audience') == 'middle_aged' ? 'selected' : '' }}>Middle-aged (36-55 years)</option>
                                            <option value="seniors" {{ old('target_audience') == 'seniors' ? 'selected' : '' }}>Seniors (55+ years)</option>
                                            <option value="families" {{ old('target_audience') == 'families' ? 'selected' : '' }}>Families</option>
                                            <option value="professionals" {{ old('target_audience') == 'professionals' ? 'selected' : '' }}>Professionals</option>
                                            <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Students</option>
                                            <option value="all_ages" {{ old('target_audience') == 'all_ages' ? 'selected' : '' }}>All Ages</option>
                                        </select>
                                        <label for="target_audience">Target Audience *</label>
                                        @error('target_audience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
            </div>

            <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="business_description" class="form-control @error('business_description') is-invalid @enderror" 
                                                  id="business_description" placeholder="Describe your business" style="height: 100px" required>{{ old('business_description') }}</textarea>
                                        <label for="business_description">What do you sell? *</label>
                                        <div class="form-text">Briefly describe your products or services (max 500 characters)</div>
                                        @error('business_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
            </div>

                        <!-- Contact Information Section -->
                        <div class="mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-phone me-2"></i>Contact Information
                            </h5>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" name="business_phone" class="form-control @error('business_phone') is-invalid @enderror" 
                                               id="business_phone" placeholder="Phone Number" value="{{ old('business_phone') }}" required>
                                        <label for="business_phone">Phone Number *</label>
                                        <div class="form-text">Include country code if international</div>
                                        @error('business_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
            </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" name="business_email" class="form-control @error('business_email') is-invalid @enderror" 
                                               id="business_email" placeholder="Email Address" value="{{ old('business_email') }}" required>
                                        <label for="business_email">Email Address *</label>
                                        @error('business_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
            </div>
            </div>
            </div>

                        <!-- Business Experience Section -->
                        <div class="mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-chart-line me-2"></i>Business Experience
                            </h5>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="years_in_business" class="form-select @error('years_in_business') is-invalid @enderror" id="years_in_business" required>
                                            <option value="">Select Years in Business</option>
                                            <option value="0" {{ old('years_in_business') == '0' ? 'selected' : '' }}>Just Starting (0 years)</option>
                                            <option value="1" {{ old('years_in_business') == '1' ? 'selected' : '' }}>1 year</option>
                                            <option value="2" {{ old('years_in_business') == '2' ? 'selected' : '' }}>2 years</option>
                                            <option value="3" {{ old('years_in_business') == '3' ? 'selected' : '' }}>3 years</option>
                                            <option value="4" {{ old('years_in_business') == '4' ? 'selected' : '' }}>4 years</option>
                                            <option value="5" {{ old('years_in_business') == '5' ? 'selected' : '' }}>5 years</option>
                                            <option value="6-10" {{ old('years_in_business') == '6-10' ? 'selected' : '' }}>6-10 years</option>
                                            <option value="11-20" {{ old('years_in_business') == '11-20' ? 'selected' : '' }}>11-20 years</option>
                                            <option value="20+" {{ old('years_in_business') == '20+' ? 'selected' : '' }}>20+ years</option>
                                        </select>
                                        <label for="years_in_business">Years in Business *</label>
                                        @error('years_in_business')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
            </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="business_size" class="form-select @error('business_size') is-invalid @enderror" id="business_size" required>
                                            <option value="">Select Business Size</option>
                                            <option value="solo" {{ old('business_size') == 'solo' ? 'selected' : '' }}>Solo Entrepreneur</option>
                                            <option value="small" {{ old('business_size') == 'small' ? 'selected' : '' }}>Small (2-5 people)</option>
                                            <option value="medium" {{ old('business_size') == 'medium' ? 'selected' : '' }}>Medium (6-20 people)</option>
                                            <option value="large" {{ old('business_size') == 'large' ? 'selected' : '' }}>Large (20+ people)</option>
                                        </select>
                                        <label for="business_size">Business Size *</label>
                                        @error('business_size')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
            </div>
            </div>

            <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="annual_revenue" class="form-select @error('annual_revenue') is-invalid @enderror" id="annual_revenue" required>
                                            <option value="">Select Annual Revenue</option>
                                            <option value="under_10k" {{ old('annual_revenue') == 'under_10k' ? 'selected' : '' }}>Under RM 10,000</option>
                                            <option value="10k_25k" {{ old('annual_revenue') == '10k_25k' ? 'selected' : '' }}>RM 10,000 - RM 25,000</option>
                                            <option value="25k_50k" {{ old('annual_revenue') == '25k_50k' ? 'selected' : '' }}>RM 25,000 - RM 50,000</option>
                                            <option value="50k_100k" {{ old('annual_revenue') == '50k_100k' ? 'selected' : '' }}>RM 50,000 - RM 100,000</option>
                                            <option value="100k_250k" {{ old('annual_revenue') == '100k_250k' ? 'selected' : '' }}>RM 100,000 - RM 250,000</option>
                                            <option value="250k_500k" {{ old('annual_revenue') == '250k_500k' ? 'selected' : '' }}>RM 250,000 - RM 500,000</option>
                                            <option value="500k_1m" {{ old('annual_revenue') == '500k_1m' ? 'selected' : '' }}>RM 500,000 - RM 1,000,000</option>
                                            <option value="over_1m" {{ old('annual_revenue') == 'over_1m' ? 'selected' : '' }}>Over RM 1,000,000</option>
                                        </select>
                                        <label for="annual_revenue">Annual Revenue *</label>
                                        @error('annual_revenue')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
            </div>
            </div>

            <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="event_experience" class="form-select @error('event_experience') is-invalid @enderror" id="event_experience" required>
                                            <option value="">Select Event Experience</option>
                                            <option value="none" {{ old('event_experience') == 'none' ? 'selected' : '' }}>No previous event experience</option>
                                            <option value="1-2" {{ old('event_experience') == '1-2' ? 'selected' : '' }}>1-2 events</option>
                                            <option value="3-5" {{ old('event_experience') == '3-5' ? 'selected' : '' }}>3-5 events</option>
                                            <option value="6-10" {{ old('event_experience') == '6-10' ? 'selected' : '' }}>6-10 events</option>
                                            <option value="10+" {{ old('event_experience') == '10+' ? 'selected' : '' }}>10+ events</option>
                                        </select>
                                        <label for="event_experience">Previous Event Experience *</label>
                                        @error('event_experience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Strategy Section -->
                        <div class="mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-bullhorn me-2"></i>Marketing Strategy
                            </h5>
                            
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="marketing_strategy" class="form-control @error('marketing_strategy') is-invalid @enderror" 
                                                  id="marketing_strategy" placeholder="How will you attract customers?" style="height: 100px" required>{{ old('marketing_strategy') }}</textarea>
                                        <label for="marketing_strategy">How will you attract customers? *</label>
                                        <div class="form-text">Briefly describe your marketing approach or special offers (max 300 characters)</div>
                                        @error('marketing_strategy')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_agreement" required>
                                <label class="form-check-label" for="terms_agreement">
                                    I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a> *
                                </label>
            </div>
        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3 justify-content-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Submit Application
                            </button>
                            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
        </div>
    </form>
</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #007bff;
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}

.btn-outline-secondary {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.alert {
    border-radius: 10px;
    border: none;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

h5.text-primary {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .card-body {
        padding: 2rem !important;
    }
    
    .btn-lg {
        padding: 10px 20px !important;
        font-size: 1rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vendorApplicationForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
        
        if (form.checkValidity()) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;
        }
    });
    
    // Character counter for textareas
    const textareas = form.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        // Set maxlength based on field
        let maxLength = 500; // default
        if (textarea.id === 'marketing_strategy') {
            maxLength = 300;
        }
        
        textarea.setAttribute('maxlength', maxLength);
        
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.innerHTML = `<span class="char-count">0</span>/${maxLength} characters`;
        textarea.parentNode.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            const charCount = this.parentNode.querySelector('.char-count');
            charCount.textContent = currentLength;
            
            if (currentLength > maxLength * 0.9) {
                charCount.style.color = '#dc3545';
            } else if (currentLength > maxLength * 0.7) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#6c757d';
            }
        });
    });
    
    // Auto-resize textareas
    const autoResizeTextareas = form.querySelectorAll('textarea');
    autoResizeTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});
</script>
@endsection


