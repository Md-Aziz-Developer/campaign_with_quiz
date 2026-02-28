@extends('layouts.admin')

@section('title', 'Response - ' . $campaign->title)

@section('content')
<h1 class="mb-2">Response Detail</h1>
<p class="text-muted">Campaign: <strong>{{ $campaign->title }}</strong> — {{ $response->participant_name }} ({{ $response->participant_email }})</p>
<p>
    <a href="{{ route('admin.campaigns.reports.index', $campaign) }}" class="btn btn-secondary">Back to Reports</a>
</p>

<div class="card mb-3">
    <div class="card-body">
        <strong>Total Score:</strong> {{ number_format($response->total_score, 2) }}
    </div>
</div>

<div class="card">
    <div class="card-header">Question breakdown</div>
    <ul class="list-group list-group-flush">
        @foreach($response->responseAnswers as $answer)
        <li class="list-group-item d-flex justify-content-between">
            <span>{{ $answer->question->question_text }}</span>
            <span class="badge bg-primary">{{ number_format($answer->score, 2) }}</span>
        </li>
        @endforeach
    </ul>
</div>
@endsection
