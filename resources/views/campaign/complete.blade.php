@extends('layouts.campaign')

@section('title', 'Results')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="card-title">Thank you!</h1>
        <p class="lead">Your total score: <strong>{{ number_format($response->total_score, 2) }}</strong></p>
        <hr>
        <h5>Breakdown by question</h5>
        <ul class="list-group list-group-flush">
            @foreach($response->responseAnswers as $answer)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ Str::limit($answer->question->question_text, 60) }}</span>
                    <span class="badge bg-primary">{{ number_format($answer->score, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
