@extends('layouts.admin')

@section('title', 'Campaigns')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Campaigns</h1>
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">+ Create Campaign</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($campaigns->isEmpty())
            <p class="text-muted mb-3">No campaigns yet. Create one to add questions and collect responses.</p>
            <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">Create Campaign</a>
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
                        @foreach($campaigns as $campaign)
                        <tr>
                            <td><strong>{{ $campaign->title }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $campaign->status === 'published' ? 'success' : 'secondary' }}">{{ $campaign->status }}</span>
                            </td>
                            <td><code class="small">{{ $campaign->unique_slug }}</code></td>
                            <td class="text-muted small">{{ $campaign->created_at->format('M j, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.campaigns.questions', $campaign) }}" class="btn btn-sm btn-primary">Manage questions</a>
                                <a href="{{ route('admin.campaigns.reports.index', $campaign) }}" class="btn btn-sm btn-success">View reports</a>
                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @if($campaign->status === 'draft')
                                    <form action="{{ route('admin.campaigns.publish', $campaign) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success">Publish</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this campaign?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
