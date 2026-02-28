@extends('layouts.campaign')

@section('title', $campaign->title)

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="card-title">{{ $campaign->title }}</h1>
        @if($campaign->description)
            <div class="mb-4">{!! $campaign->description !!}</div>
        @endif
        <hr>
        <h5>Enter your details to start</h5>
        <form action="{{ route('campaign.start', $campaign) }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label for="participant_name" class="form-label">Name *</label>
                <input type="text" class="form-control @error('participant_name') is-invalid @enderror" id="participant_name" name="participant_name" value="{{ old('participant_name') }}" required>
                @error('participant_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="participant_email" class="form-label">Email *</label>
                <input type="email" class="form-control @error('participant_email') is-invalid @enderror" id="participant_email" name="participant_email" value="{{ old('participant_email') }}" required>
                @error('participant_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Start Questionnaire</button>
            </div>
        </form>
    </div>
</div>
@endsection
