<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Services\ScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function show(Campaign $campaign): View|RedirectResponse
    {
        if ($campaign->status !== 'published') {
            abort(404);
        }
        if ($campaign->isExpired()) {
            abort(404, 'Campaign has expired.');
        }
        return view('campaign.show', compact('campaign'));
    }

    public function start(Request $request, Campaign $campaign): RedirectResponse
    {
        if ($campaign->status !== 'published' || $campaign->isExpired()) {
            abort(404);
        }
        $valid = Validator::make($request->all(), [
            'participant_name' => ['required', 'string', 'max:255'],
            'participant_email' => ['required', 'email'],
        ])->validate();

        $email = $valid['participant_email'];
        $alreadySubmitted = Response::where('campaign_id', $campaign->id)
            ->where('participant_email', $email)
            ->whereNotNull('completed_at')
            ->exists();

        if ($alreadySubmitted && ! $campaign->allow_multiple_responses) {
            return redirect()->route('campaign.show', $campaign)
                ->withInput($request->only('participant_name', 'participant_email'))
                ->with('error', 'You have already submitted a response for this campaign.');
        }

        if ($alreadySubmitted && $campaign->allow_multiple_responses) {
            session()->flash('warning', 'You have already submitted a response. This will add another response.');
        }

        $response = Response::create([
            'campaign_id' => $campaign->id,
            'participant_name' => $valid['participant_name'],
            'participant_email' => $valid['participant_email'],
        ]);
        session(['campaign_response_id' => $response->id]);
        $first = $campaign->questions()->orderBy('order')->first();
        if (! $first) {
            return redirect()->route('campaign.complete', $campaign);
        }
        return redirect()->route('campaign.question', [$campaign, $first->order]);
    }

    public function question(Campaign $campaign, int $order): View|RedirectResponse
    {
        if ($campaign->status !== 'published' || $campaign->isExpired()) {
            abort(404);
        }
        $responseId = session('campaign_response_id');
        if (! $responseId) {
            return redirect()->route('campaign.show', $campaign)->with('error', 'Please enter your details first.');
        }
        $response = Response::find($responseId);
        if (! $response || $response->campaign_id !== $campaign->id) {
            session()->forget('campaign_response_id');
            return redirect()->route('campaign.show', $campaign);
        }
        if ($response->completed_at) {
            return redirect()->route('campaign.complete', $campaign);
        }
        $question = $campaign->questions()->where('order', $order)->first();
        if (! $question) {
            return redirect()->route('campaign.show', $campaign);
        }
        $total = $campaign->questions()->count();
        $prevOrder = $campaign->questions()->where('order', '<', $order)->max('order');
        $nextOrder = $campaign->questions()->where('order', '>', $order)->min('order');
        return view('campaign.question', compact('campaign', 'question', 'response', 'total', 'order', 'prevOrder', 'nextOrder'));
    }

    public function answer(Request $request, Campaign $campaign): RedirectResponse
    {
        if ($campaign->status !== 'published' || $campaign->isExpired()) {
            abort(404);
        }
        $responseId = session('campaign_response_id');
        if (! $responseId) {
            return redirect()->route('campaign.show', $campaign);
        }
        $response = Response::find($responseId);
        if (! $response || $response->campaign_id !== $campaign->id || $response->completed_at) {
            if ($response && $response->completed_at) {
                return redirect()->route('campaign.complete', $campaign);
            }
            session()->forget('campaign_response_id');
            return redirect()->route('campaign.show', $campaign);
        }

        $questionId = $request->input('question_id');
        $question = Question::find($questionId);
        if (! $question || $question->campaign_id !== $campaign->id) {
            return redirect()->back()->with('error', 'Invalid question.');
        }

        $rules = ['question_id' => 'required|exists:questions,id'];
        if ($question->is_mandatory) {
            if (in_array($question->type, ['mcq_single', 'mcq_multi'])) {
                $rules['selected_option_ids'] = 'required|array';
                $rules['selected_option_ids.*'] = 'exists:options,id';
                if ($question->type === 'mcq_multi') {
                    $rules['selected_option_ids'] = 'required|array|min:1';
                }
            } elseif ($question->type === 'text') {
                $rules['answer_text'] = 'required|string';
            } elseif ($question->type === 'number') {
                $rules['answer_number'] = 'required|numeric';
            }
        } else {
            $rules['answer_text'] = 'nullable|string';
            $rules['answer_number'] = 'nullable|numeric';
            $rules['selected_option_ids'] = 'nullable|array';
            $rules['selected_option_ids.*'] = 'exists:options,id';
        }
        $valid = $request->validate($rules);

        $answer = ResponseAnswer::updateOrCreate(
            [
                'response_id' => $response->id,
                'question_id' => $question->id,
            ],
            [
                'answer_text' => $valid['answer_text'] ?? null,
                'answer_number' => isset($valid['answer_number']) ? (float) $valid['answer_number'] : null,
                'selected_option_ids' => $valid['selected_option_ids'] ?? null,
            ]
        );

        app(ScoringService::class)->scoreResponse($response->fresh(['responseAnswers']));

        $nextOrder = $campaign->questions()->where('order', '>', $question->order)->min('order');
        if ($nextOrder === null) {
            $response->update(['completed_at' => now()]);
            return redirect()->route('campaign.complete', $campaign);
        }
        return redirect()->route('campaign.question', [$campaign, $nextOrder]);
    }

    public function complete(Campaign $campaign): View|RedirectResponse
    {
        if ($campaign->status !== 'published' || $campaign->isExpired()) {
            abort(404);
        }
        $responseId = session('campaign_response_id');
        if (! $responseId) {
            return redirect()->route('campaign.show', $campaign);
        }
        $response = Response::with(['responseAnswers.question.options'])->find($responseId);
        if (! $response || $response->campaign_id !== $campaign->id) {
            return redirect()->route('campaign.show', $campaign);
        }
        $response->setRelation('responseAnswers', $response->responseAnswers->sortBy(fn ($a) => $a->question->order)->values());
        return view('campaign.complete', compact('campaign', 'response'));
    }
}
