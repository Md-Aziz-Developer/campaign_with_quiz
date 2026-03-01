@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-3">{{ __('You are logged in!') }}</p>
                    <p class="text-muted small mb-0">Campaign management is available in the admin area. <a href="{{ route('admin.login') }}">Log in as admin</a> or try the <a href="{{ url('/campaign/demo-customer-satisfaction') }}">demo survey</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
