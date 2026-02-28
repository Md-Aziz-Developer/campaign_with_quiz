<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $totalCampaigns = Campaign::where('created_by', $userId)->count();
        $publishedCampaigns = Campaign::where('created_by', $userId)->published()->count();
        $totalResponses = Response::whereHas('campaign', fn ($q) => $q->where('created_by', $userId))->whereNotNull('completed_at')->count();
        $recentCampaigns = Campaign::where('created_by', $userId)->with('creator')->latest()->take(10)->get();

        return view('admin.dashboard.index', compact('totalCampaigns', 'publishedCampaigns', 'totalResponses', 'recentCampaigns'));
    }
}
