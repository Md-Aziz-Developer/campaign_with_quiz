@extends('layouts.admin')

@section('title', 'Reports - ' . $campaign->title)

@section('content')
<h1 class="mb-2">Reports</h1>
<p class="text-muted">Campaign: <strong>{{ $campaign->title }}</strong></p>
<p>
    <a href="{{ route('admin.campaigns.reports.export', $campaign) }}" class="btn btn-success">Export CSV</a>
    <a href="{{ route('admin.campaigns.questions', $campaign) }}" class="btn btn-outline-secondary">Questions</a>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">Back to Campaigns</a>
</p>

@if(isset($averageScore))
<div class="alert alert-info">Average score: <strong>{{ number_format($averageScore, 2) }}</strong></div>
@endif

<div class="card">
    <div class="card-header">Participants ({{ $responses->total() }})</div>
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
