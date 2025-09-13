@extends('layouts.admin')

@section('title', 'FAQ Management - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-question-circle me-2 text-primary"></i>FAQ Management
            </h1>
            <p class="text-muted mb-0">Manage frequently asked questions</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFaqModal">
                <i class="fas fa-plus me-2"></i>Add New FAQ
            </button>
        </div>
    </div>

    <!-- FAQs by Category -->
    @foreach($faqs as $category => $categoryFaqs)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-folder me-2"></i>{{ ucfirst($category) }} FAQs
                @php
                    $color = match($category) {
                        'general' => 'secondary',
                        'technical' => 'warning',
                        'billing' => 'primary',
                        'event' => 'dark',
                        'customer' => 'info',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $color }} ms-2">{{ $categoryFaqs->count() }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if($categoryFaqs->count() > 0)
                <div class="accordion" id="faqAccordion{{ $category }}">
                    @foreach($categoryFaqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $category }}{{ $index }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $category }}{{ $index }}" aria-expanded="false" 
                                    aria-controls="collapse{{ $category }}{{ $index }}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <span>{{ $faq->question }}</span>
                                    <div class="d-flex gap-2">
                                        @if($faq->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $category }}{{ $index }}" class="accordion-collapse collapse" 
                             aria-labelledby="heading{{ $category }}{{ $index }}" data-bs-parent="#faqAccordion{{ $category }}">
                            <div class="accordion-body">
                                <p>{{ $faq->answer }}</p>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editFaq({{ $faq->id }}, {{ json_encode($faq->question) }}, {{ json_encode($faq->answer) }}, '{{ $faq->category }}', {{ $faq->is_active ? 'true' : 'false' }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.support.faqs.delete', $faq->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this FAQ?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-question-circle fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No FAQs found in this category</p>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Create FAQ Modal -->
<div class="modal fade" id="createFaqModal" tabindex="-1" aria-labelledby="createFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.support.faqs.create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createFaqModalLabel">
                        <i class="fas fa-plus me-2"></i>Add New FAQ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="question" class="form-label">Question</label>
                        <input type="text" class="form-control" id="question" name="question" required>
                    </div>
                    <div class="mb-3">
                        <label for="answer" class="form-label">Answer</label>
                        <textarea class="form-control" id="answer" name="answer" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="general">General</option>
                                    <option value="technical">Technical</option>
                                    <option value="billing">Billing</option>
                                    <option value="event">Event</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">
                            Active (visible to users)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit FAQ Modal -->
<div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editFaqModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit FAQ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_question" class="form-label">Question</label>
                        <input type="text" class="form-control" id="edit_question" name="question" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_answer" class="form-label">Answer</label>
                        <textarea class="form-control" id="edit_answer" name="answer" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <option value="general">General</option>
                                    <option value="technical">Technical</option>
                                    <option value="billing">Billing</option>
                                    <option value="event">Event</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">
                            Active (visible to users)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editFaq(id, question, answer, category, isActive) {
    console.log('editFaq called with:', {id, question, answer, category, isActive});
    
    document.getElementById('editFaqForm').action = '{{ route("admin.support.faqs.update", ":id") }}'.replace(':id', id);
    document.getElementById('edit_question').value = question;
    document.getElementById('edit_answer').value = answer;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_is_active').checked = isActive;
    
    console.log('Form action set to:', document.getElementById('editFaqForm').action);
    
    new bootstrap.Modal(document.getElementById('editFaqModal')).show();
}

// Add form submission debugging
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editFaqForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('FAQ form submitted!');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            console.log('Form data:', new FormData(this));
        });
    }
});
</script>
@endsection
