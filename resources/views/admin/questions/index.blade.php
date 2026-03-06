@extends('layouts.admin')

@section('title', 'Questions - ' . $campaign->title)

@section('content')
<h1 class="h3 mb-1">Questions</h1>
<p class="text-muted small mb-2">Campaign: <strong>{{ $campaign->title }}</strong></p>
<p class="text-muted small mb-3">Add and manage questions for this campaign. Use the form below to add a question (rich text, type, mandatory flag, and type-specific options). Edit or delete from the list. Duplicate question text is not allowed within the same campaign.</p>
<p>
    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-outline-secondary">Edit Campaign</a>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Back to Campaigns</a>
</p>

<div id="question-form-success" class="alert alert-success alert-dismissible fade show d-none" role="alert">
    <span id="question-form-success-text"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0">Add Question</h6>
        <p class="text-muted small mb-0">Choose type: <strong>MCQ Single</strong> (one answer), <strong>MCQ Multi</strong> (multiple answers), <strong>Text</strong> (keyword scoring), or <strong>Number</strong> (exact or range + score). Mark as mandatory if the participant must answer.</p>
    </div>
    <div class="card-body">
        @include('admin.questions.partials.form', ['campaign' => $campaign])
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0">Questions ({{ $campaign->questions->count() }})</h6>
        <p class="text-muted small mb-0">List of questions in this campaign. Use <strong>Edit</strong> to change text or options, <strong>Delete</strong> to remove. Order is used when participants take the survey.</p>
    </div>
    <div class="card-body">
        <p class="text-muted mb-0" id="questions-empty-msg" @if(!$campaign->questions->isEmpty()) style="display:none" @endif>No questions yet. Add one above.</p>
        <ul class="list-group list-group-flush" id="questions-list">
            @foreach($campaign->questions as $question)
                @include('admin.questions.partials.row', ['question' => $question])
            @endforeach
        </ul>
    </div>
</div>
@endsection
