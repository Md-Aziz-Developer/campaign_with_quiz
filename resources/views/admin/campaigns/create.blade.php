@extends('layouts.admin')

@section('title', 'Create Campaign')

@section('content')
<h1 class="h3 mb-1">Create Campaign</h1>
<p class="text-muted small mb-4">Define a new survey: add a title, optional description, and set whether it is draft or published. After saving, you will add questions on the next screen.</p>

<form action="{{ route('admin.campaigns.store') }}" method="POST" id="campaign-form">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="mb-0">Campaign details</h6>
            <p class="text-muted small mb-0">Title is required. Use the description to introduce the survey to participants (e.g. instructions or thank-you text).</p>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Description (rich text)</label>
                <div id="quill-editor" style="min-height: 200px;"></div>
                <input type="hidden" name="description" id="description-input" value="{{ old('description') }}">
                @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_multiple_responses" value="1" id="allow_multiple_responses" {{ old('allow_multiple_responses') ? 'checked' : '' }}>
                    <label class="form-check-label" for="allow_multiple_responses">Allow multiple responses from same participant (same email)</label>
                </div>
                <p class="text-muted small mb-0">If unchecked, each participant can submit only once. If checked, repeat submissions show a warning but are allowed.</p>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save & Add Questions</button>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Cancel</a>
</form>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var editor = new Quill('#quill-editor', { theme: 'snow' });
    var input = document.getElementById('description-input');
    if (input.value) editor.root.innerHTML = input.value;
    document.getElementById('campaign-form').addEventListener('submit', function() {
        input.value = editor.root.innerHTML;
    });
});
</script>
@endsection
