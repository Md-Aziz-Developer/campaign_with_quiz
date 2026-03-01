<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Campaign $campaign): View
    {
        $this->authorize('view', $campaign);
        $responses = Response::where('campaign_id', $campaign->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->paginate(15);
        $averageScore = Response::where('campaign_id', $campaign->id)
            ->whereNotNull('completed_at')
            ->avg('total_score');
        return view('admin.reports.index', compact('campaign', 'responses', 'averageScore'));
    }

    public function show(Campaign $campaign, Response $response): View
    {
        $this->authorize('view', $campaign);
        if ($response->campaign_id !== $campaign->id) {
            abort(404);
        }
        $response->load(['responseAnswers.question.options']);
        $response->setRelation('responseAnswers', $response->responseAnswers->sortBy(fn ($a) => $a->question->order)->values());
        return view('admin.reports.show', compact('campaign', 'response'));
    }

    public function export(Campaign $campaign): StreamedResponse
    {
        $this->authorize('view', $campaign);
        $questions = $campaign->questions()->orderBy('order')->get();
        $responses = Response::where('campaign_id', $campaign->id)->whereNotNull('completed_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="campaign-' . $campaign->unique_slug . '-reports.csv"',
        ];

        return response()->stream(function () use ($questions, $responses) {
            $out = fopen('php://output', 'w');
            $headerRow = ['Name', 'Email', 'Total Score'];
            foreach ($questions as $q) {
                $headerRow[] = 'Q' . $q->order;
            }
            fputcsv($out, $headerRow);
            foreach ($responses as $r) {
                $row = [$r->participant_name, $r->participant_email, $r->total_score];
                $answersByQuestion = $r->responseAnswers->keyBy('question_id');
                foreach ($questions as $q) {
                    $row[] = $answersByQuestion->get($q->id)?->score ?? '';
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, 200, $headers);
    }
}
