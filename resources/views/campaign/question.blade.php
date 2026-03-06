@extends('layouts.campaign')

@section('title', 'Question ' . $order)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <p class="text-muted small mb-2">Question {{ $order }} of {{ $total }}</p>
        <p class="text-muted small mb-3">Select or enter your answer, then click <strong>Next</strong> to continue or <strong>Previous</strong> to go back.</p>
        <h4 class="mb-4">{!! $question->question_text !!} @if($question->is_mandatory)<span class="text-danger">*</span>@endif</h4>

        <form action="{{ route('campaign.answer', $campaign) }}" method="POST">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            @if($question->type === 'mcq_single')
                @foreach($question->options as $option)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="selected_option_ids[]" id="opt{{ $option->id }}" value="{{ $option->id }}" {{ in_array($option->id, old('selected_option_ids', $response->responseAnswers->firstWhere('question_id', $question->id)?->selected_option_ids ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="opt{{ $option->id }}">{{ $option->option_text }}</label>
                    </div>
                @endforeach
            @elseif($question->type === 'mcq_multi')
                @foreach($question->options as $option)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="selected_option_ids[]" id="opt{{ $option->id }}" value="{{ $option->id }}" {{ in_array($option->id, old('selected_option_ids', $response->responseAnswers->firstWhere('question_id', $question->id)?->selected_option_ids ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="opt{{ $option->id }}">{{ $option->option_text }}</label>
                    </div>
                @endforeach
            @elseif($question->type === 'text')
                <textarea class="form-control" name="answer_text" rows="3" @if($question->is_mandatory) required @endif>{{ old('answer_text', $response->responseAnswers->firstWhere('question_id', $question->id)?->answer_text) }}</textarea>
            @elseif($question->type === 'number')
                <input type="number" step="any" class="form-control" name="answer_number" value="{{ old('answer_number', $response->responseAnswers->firstWhere('question_id', $question->id)?->answer_number) }}" style="max-width:200px" @if($question->is_mandatory) required @endif>
            @endif

            <div class="mt-4 d-flex justify-content-between">
                @if($prevOrder !== null)
                    <a href="{{ route('campaign.question', [$campaign, $prevOrder]) }}" class="btn btn-outline-secondary">Previous</a>
                @else
                    <span></span>
                @endif
                <button type="submit" class="btn btn-primary" id="submit-btn">{{ $nextOrder !== null ? 'Next' : 'Finish' }}</button>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelector('form').addEventListener('submit', function() {
    var btn = document.getElementById('submit-btn');
    if (btn) btn.disabled = true;
});
</script>
@endsection
