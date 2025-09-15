@extends('layouts.app')

@section('title', 'Frequently Asked Questions - EventHub')

@section('content')
<!-- Author: Yap Jia Wei -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-question-circle me-3"></i>Frequently Asked Questions
                </h1>
                <p class="lead text-muted">
                    @if($user)
                        Personalized FAQ for {{ $user->name }} ({{ ucfirst($user->role) }})
                    @else
                        Find answers to the most common questions about EventHub
                    @endif
                </p>
                @if($user)
                    <div class="badge bg-primary fs-6">
                        <i class="fas fa-user me-2"></i>{{ ucfirst($user->role) }} Questions
                    </div>
                @endif
            </div>

            <!-- Search Box -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="faqSearch" placeholder="Search FAQ...">
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('support.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Categories -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="btn-group w-100" role="group" aria-label="FAQ Categories">
                        <button type="button" class="btn btn-outline-primary active" data-category="all">All Questions</button>
                        <button type="button" class="btn btn-outline-primary" data-category="general">General</button>
                        <button type="button" class="btn btn-outline-primary" data-category="technical">Technical</button>
                        <button type="button" class="btn btn-outline-primary" data-category="billing">Billing</button>
                        <button type="button" class="btn btn-outline-primary" data-category="event">Events</button>
                        @if($user && $user->role === 'admin')
                            <button type="button" class="btn btn-outline-primary" data-category="admin">Admin</button>
                        @endif
                        @if($user && $user->role === 'vendor')
                            <button type="button" class="btn btn-outline-primary" data-category="vendor">Vendor</button>
                        @endif
                        @if($user && $user->role === 'customer')
                            <button type="button" class="btn btn-outline-primary" data-category="customer">Customer</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="accordion" id="faqAccordion">
                @foreach($faqs as $index => $faq)
                <div class="accordion-item border-0 shadow-sm mb-3 faq-item" data-category="{{ $faq['category'] ?? 'general' }}">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                            <div class="d-flex align-items-center w-100">
                                <i class="fas fa-question-circle me-3 text-primary"></i>
                                <div class="flex-grow-1">
                                    {{ $faq['question'] }}
                                </div>
                                @if(isset($faq['category_label']))
                                    <span class="badge bg-{{ $faq['category_badge_color'] }} me-2">
                                        {{ $faq['category_label'] }}
                                    </span>
                                @endif
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                         aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lightbulb me-3 text-warning mt-1"></i>
                                <div>
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            <!-- Still Need Help Section -->
            <div class="card border-0 shadow-sm mt-5">
                <div class="card-body text-center">
                    <h4 class="card-title mb-3">
                        <i class="fas fa-headset me-2 text-primary"></i>Still Need Help?
                    </h4>
                    <p class="card-text text-muted mb-4">
                        Can't find the answer you're looking for? Our support team is here to help!
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="fas fa-envelope me-2"></i>Contact Support
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal (same as in index.blade.php) -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">
                    <i class="fas fa-envelope me-2"></i>Contact Support
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('support.contact') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message *</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ Search functionality
        const searchInput = document.getElementById('faqSearch');
        const faqItems = document.querySelectorAll('.faq-item');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            faqItems.forEach(item => {
                const question = item.querySelector('.accordion-button').textContent.toLowerCase();
                const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Category filtering
        const categoryButtons = document.querySelectorAll('[data-category]');
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Update active button
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter FAQ items
                faqItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    
                    if (category === 'all' || itemCategory === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Clear search when filtering by category
                searchInput.value = '';
            });
        });
    });
</script>
@endsection
