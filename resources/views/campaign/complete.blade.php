@extends('layouts.campaign')

@section('title', 'Results')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="card-title">Thank you!</h1>
        <p class="lead">Your total score: <strong>{{ number_format($response->total_score, 2) }}</strong></p>
        <hr>
        <h5>Your answers</h5>
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
                <strong>{{ $q->question_text }}</strong>
                <div class="text-muted mt-1">Your answer: {{ $answerDisplay }}</div>
                <span class="badge bg-primary mt-1">{{ number_format($answer->score, 2) }} pts</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
