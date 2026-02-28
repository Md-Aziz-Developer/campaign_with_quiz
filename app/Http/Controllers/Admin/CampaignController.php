<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Campaign::class, 'campaign');
    }

    public function index(): View
    {
        $campaigns = Campaign::where('created_by', auth()->id())->with('creator')->latest()->paginate(15);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('admin.campaigns.create');
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $slug = $this->generateUniqueSlug($request->title);
        $campaign = Campaign::create([
            'title' => $request->title,
            'description' => $request->description ?? '',
            'status' => $request->status ?? 'draft',
            'unique_slug' => $slug,
            'created_by' => $request->user()->id,
        ]);
        return redirect()->route('admin.campaigns.questions', $campaign)
            ->with('success', 'Campaign created. Add questions below.');
    }

    public function show(Campaign $campaign): RedirectResponse
    {
        return redirect()->route('admin.campaigns.questions', $campaign);
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update([
            'title' => $request->title,
            'description' => $request->description ?? $campaign->description,
            'status' => $request->status ?? $campaign->status,
        ]);
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted.');
    }

    public function publish(Request $request, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);
        if ($campaign->questions()->count() < 1) {
            return redirect()->back()->with('error', 'Campaign must have at least one question before publishing.');
        }
        $campaign->update(['status' => 'published']);
        return redirect()->back()->with('success', 'Campaign published.');
    }

    private function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (Campaign::where('unique_slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
