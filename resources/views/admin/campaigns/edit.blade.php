@extends('layouts.admin')

@section('title', 'Edit Campaign')

@section('content')
<h1 class="h3 mb-1">Edit Campaign</h1>
<p class="text-muted small mb-4">Update the campaign title, description, status, or response settings. The shareable link (slug) is shown below and does not change when you edit.</p>

<form action="{{ route('admin.campaigns.update', $campaign) }}" method="POST" id="campaign-form">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="mb-0">Campaign details</h6>
            <p class="text-muted small mb-0">Changes here apply to the campaign. Use <strong>Manage Questions</strong> to add or edit questions.</p>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $campaign->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Description (rich text)</label>
                <div id="quill-editor" style="min-height: 200px;"></div>
                <input type="hidden" name="description" id="description-input" value="{{ old('description', $campaign->description) }}">
                @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft" {{ old('status', $campaign->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $campaign->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_multiple_responses" value="1" id="allow_multiple_responses" {{ old('allow_multiple_responses', $campaign->allow_multiple_responses) ? 'checked' : '' }}>
                    <label class="form-check-label" for="allow_multiple_responses">Allow multiple responses from same participant (same email)</label>
                </div>
                <p class="text-muted small mb-0">If unchecked, each participant can submit only once. If checked, repeat submissions show a warning but are allowed.</p>
            </div>
            <p class="text-muted small">Slug: <code>{{ $campaign->unique_slug }}</code></p>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('admin.campaigns.questions', $campaign) }}" class="btn btn-outline-secondary">Manage Questions</a>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Back to List</a>
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
