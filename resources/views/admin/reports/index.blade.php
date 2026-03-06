@extends('layouts.admin')

@section('title', 'Reports - ' . $campaign->title)

@section('content')
<h1 class="h3 mb-1">Reports</h1>
<p class="text-muted small mb-2">Campaign: <strong>{{ $campaign->title }}</strong></p>
<p class="text-muted small mb-3">View completed responses, average score, and export data. Click <strong>View</strong> on a participant to see their answers and per-question scores.</p>
<p>
    <a href="{{ route('admin.campaigns.reports.export', $campaign) }}" class="btn btn-success">Export CSV</a>
    <a href="{{ route('admin.campaigns.questions', $campaign) }}" class="btn btn-outline-secondary">Questions</a>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Back to Campaigns</a>
</p>

@if(isset($averageScore))
<div class="alert alert-info border-0 shadow-sm">Average score: <strong>{{ number_format($averageScore, 2) }}</strong></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0">Participants ({{ $responses->total() }})</h6>
        <p class="text-muted small mb-0">Completed responses for this campaign. Export CSV to download all data for analysis.</p>
    </div>
    <div class="card-body">
        @if($responses->isEmpty())
            <p class="text-muted mb-0">No completed responses yet.</p>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Score</th>
                            <th>Completed</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $r)
                        <tr>
                            <td>{{ $r->participant_name }}</td>
                            <td>{{ $r->participant_email }}</td>
                            <td>{{ number_format($r->total_score, 2) }}</td>
                            <td>{{ $r->completed_at?->format('M j, Y H:i') }}</td>
                            <td><a href="{{ route('admin.campaigns.reports.show', [$campaign, $r]) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $responses->links() }}
        @endif
    </div>
</div>
@endsection
