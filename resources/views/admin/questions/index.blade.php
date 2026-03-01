@extends('layouts.admin')

@section('title', 'Questions - ' . $campaign->title)

@section('content')
<h1 class="mb-2">Questions</h1>
<p class="text-muted">Campaign: <strong>{{ $campaign->title }}</strong></p>
<p>
    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-outline-secondary">Edit Campaign</a>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Back to Campaigns</a>
</p>

<div id="question-form-success" class="alert alert-success alert-dismissible fade show d-none" role="alert">
    <span id="question-form-success-text"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="card mb-4">
    <div class="card-header">Add Question</div>
    <div class="card-body">
        @include('admin.questions.partials.form', ['campaign' => $campaign])
    </div>
</div>

<div class="card">
    <div class="card-header">Questions ({{ $campaign->questions->count() }})</div>
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
