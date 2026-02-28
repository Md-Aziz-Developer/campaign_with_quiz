@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">+ New Campaign</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">Total Campaigns</h6>
                <p class="mb-0 display-6 fw-bold">{{ $totalCampaigns }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">Published</h6>
                <p class="mb-0 display-6 fw-bold">{{ $publishedCampaigns }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">Total Responses</h6>
                <p class="mb-0 display-6 fw-bold">{{ $totalResponses }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">My Campaigns</h5>
        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="card-body">
        @if($recentCampaigns->isEmpty())
            <p class="text-muted mb-3">You don’t have any campaigns yet.</p>
            <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">Create your first campaign</a>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Slug</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCampaigns as $c)
                        <tr>
                            <td><strong>{{ $c->title }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $c->status === 'published' ? 'success' : 'secondary' }}">{{ $c->status }}</span>
                            </td>
                            <td><code class="small">{{ $c->unique_slug }}</code></td>
                            <td class="text-muted small">{{ $c->created_at->format('M j, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.campaigns.questions', $c) }}" class="btn btn-sm btn-outline-primary">Manage questions</a>
                                <a href="{{ route('admin.campaigns.reports.index', $c) }}" class="btn btn-sm btn-outline-success">Reports</a>
                                <a href="{{ route('admin.campaigns.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @if($c->status === 'draft')
                                    <form action="{{ route('admin.campaigns.publish', $c) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Publish</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
