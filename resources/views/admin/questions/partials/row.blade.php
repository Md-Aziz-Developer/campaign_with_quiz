<li class="list-group-item d-flex justify-content-between align-items-start" data-question-id="{{ $question->id }}">
    <div>
        <span class="badge bg-secondary me-2">#{{ $question->order }}</span>
        <strong>{!! $question->question_text !!}</strong>
        <span class="badge bg-info ms-2">{{ $question->type }}</span>
        @if($question->is_mandatory)<span class="badge bg-warning">Required</span>@endif
        <div class="small text-muted mt-1">
            @if($question->options->isNotEmpty())
                Options: {{ $question->options->pluck('option_text')->join(', ') }}
            @elseif($question->textKeyword)
                Keywords: {{ collect($question->textKeyword->rules)->pluck('keyword')->join(', ') }}
            @elseif($question->numberRules->isNotEmpty())
                Number rules: {{ $question->numberRules->count() }} rule(s)
            @endif
        </div>
    </div>
    <div>
        <button type="button" class="btn btn-sm btn-outline-primary edit-question-btn" data-id="{{ $question->id }}">Edit</button>
        <form action="{{ route('admin.campaigns.questions.destroy', [$question->campaign, $question]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this question?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </div>
</li>
