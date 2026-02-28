<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Campaign;
use App\Models\NumberRule;
use App\Models\Option;
use App\Models\Question;
use App\Models\TextKeyword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Campaign $campaign): View
    {
        $this->authorize('update', $campaign);
        $campaign->load(['questions.options', 'questions.textKeyword', 'questions.numberRules']);
        return view('admin.questions.index', compact('campaign'));
    }

    public function store(StoreQuestionRequest $request, Campaign $campaign): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $campaign);
        $order = $campaign->questions()->max('order') + 1;
        $question = Question::create([
            'campaign_id' => $campaign->id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'is_mandatory' => $request->boolean('is_mandatory'),
            'order' => $request->input('order', $order),
        ]);

        if (in_array($question->type, [Question::TYPE_MCQ_SINGLE, Question::TYPE_MCQ_MULTI]) && $request->has('options')) {
            foreach ($request->options as $i => $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'score' => (float) ($opt['score'] ?? 0),
                    'is_correct' => ! empty($opt['is_correct']),
                    'order' => $i + 1,
                ]);
            }
        }

        if ($question->type === Question::TYPE_TEXT && $request->has('keyword_rules')) {
            TextKeyword::create([
                'question_id' => $question->id,
                'rules' => array_map(function ($r) {
                    return ['keyword' => $r['keyword'], 'score' => (float) $r['score']];
                }, $request->keyword_rules),
            ]);
        }

        if ($question->type === Question::TYPE_NUMBER && $request->has('number_rules')) {
            foreach ($request->number_rules as $nr) {
                NumberRule::create([
                    'question_id' => $question->id,
                    'exact_value' => isset($nr['exact_value']) && $nr['exact_value'] !== '' ? $nr['exact_value'] : null,
                    'min_value' => isset($nr['min_value']) && $nr['min_value'] !== '' ? $nr['min_value'] : null,
                    'max_value' => isset($nr['max_value']) && $nr['max_value'] !== '' ? $nr['max_value'] : null,
                    'score' => (float) $nr['score'],
                ]);
            }
        }

        if ($request->wantsJson()) {
            $question->load(['options', 'textKeyword', 'numberRules']);
            return response()->json([
                'success' => true,
                'question' => $question,
                'html' => view('admin.questions.partials.row', ['question' => $question])->render(),
            ]);
        }
        return redirect()->route('admin.campaigns.questions', $campaign)->with('success', 'Question added.');
    }

    public function destroy(Campaign $campaign, Question $question): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $campaign);
        if ($question->campaign_id !== $campaign->id) {
            abort(404);
        }
        $question->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.campaigns.questions', $campaign)->with('success', 'Question deleted.');
    }
}
