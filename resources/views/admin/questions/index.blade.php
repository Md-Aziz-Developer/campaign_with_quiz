@extends('layouts.admin')

@section('title', 'Questions - ' . $campaign->title)

@section('content')
<h1 class="mb-2">Questions</h1>
<p class="text-muted">Campaign: <strong>{{ $campaign->title }}</strong></p>
<p>
    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-outline-secondary">Edit Campaign</a>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Back to Campaigns</a>
</p>

<div class="card mb-4">
    <div class="card-header">Add Question</div>
    <div class="card-body">
        @include('admin.questions.partials.form', ['campaign' => $campaign])
    </div>
</div>

<div class="card">
    <div class="card-header">Questions ({{ $campaign->questions->count() }})</div>
    <div class="card-body">
        @if($campaign->questions->isEmpty())
            <p class="text-muted mb-0">No questions yet. Add one above.</p>
        @else
            <ul class="list-group list-group-flush" id="questions-list">
                @foreach($campaign->questions as $question)
                    @include('admin.questions.partials.row', ['question' => $question])
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
