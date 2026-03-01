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
    <div class="card-header">Questions &amp; Answers</div>
    <ul class="list-group list-group-flush">
        @foreach($response->responseAnswers as $answer)
        @php
            $q = $answer->question;
            $answerDisplay = '';
            if (in_array($q->type, ['mcq_single', 'mcq_multi'])) {
                $ids = $answer->selected_option_ids ?? [];
                $selected = $q->options->whereIn('id', $ids)->pluck('option_text');
                $answerDisplay = $selected->isNotEmpty() ? $selected->join(', ') : '—';
            } elseif ($q->type === 'text') {
                $answerDisplay = $answer->answer_text !== null && $answer->answer_text !== '' ? e($answer->answer_text) : '—';
            } elseif ($q->type === 'number') {
                $answerDisplay = $answer->answer_number !== null && $answer->answer_number !== '' ? number_format((float) $answer->answer_number, 2) : '—';
            }
        @endphp
        <li class="list-group-item">
            <div class="d-flex justify-content-between align-items-start">
                <div class="me-2">
                    <strong>{{ $q->question_text }}</strong>
                    <div class="text-muted mt-1">
                        <strong>Answer:</strong> {{ $answerDisplay }}
                    </div>
                </div>
                <span class="badge bg-primary">{{ number_format($answer->score, 2) }}</span>
            </div>
        </li>
        @endforeach
    </ul>
</div>
@endsection
