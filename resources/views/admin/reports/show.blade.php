@extends('layouts.admin')

@section('title', 'Response - ' . $campaign->title)

@section('content')
<h1 class="h3 mb-1">Response Detail</h1>
<p class="text-muted small mb-2">Campaign: <strong>{{ $campaign->title }}</strong> — {{ $response->participant_name }} ({{ $response->participant_email }})</p>
<p class="text-muted small mb-3">Full breakdown of this participant’s answers and the score for each question.</p>
<p>
    <a href="{{ route('admin.campaigns.reports.index', $campaign) }}" class="btn btn-secondary">Back to Reports</a>
</p>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <strong>Total Score:</strong> {{ number_format($response->total_score, 2) }}
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0">Questions &amp; Answers</h6>
        <p class="text-muted small mb-0">Each question with the participant’s answer and the points awarded.</p>
    </div>
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
                    <strong>{!! $q->question_text !!}</strong>
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
