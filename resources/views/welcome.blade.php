<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} – Campaign Questionnaire Platform</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero { background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%); color: #fff; border-radius: 0.75rem; }
        .btn-admin { background: #1e3a5f; color: #fff; }
        .btn-admin:hover { background: #2d4a6f; color: #fff; }
        .btn-demo { background: #0d9488; color: #fff; }
        .btn-demo:hover { background: #0f766e; color: #fff; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">{{ config('app.name') }}</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('admin.login') }}">Admin Login</a>
            </div>
        </div>
    </nav>
    <main class="container py-5">
        <div class="hero p-5 mb-5 text-center">
            <h1 class="display-5 fw-bold mb-3">Campaign Questionnaire Platform</h1>
            <p class="lead mb-4 opacity-90">Create surveys, collect responses, and view reports in one place.</p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('admin.login') }}" class="btn btn-admin btn-lg px-4">Admin Login</a>
                <a href="{{ url('/campaign/demo-customer-satisfaction') }}" class="btn btn-demo btn-lg px-4">Try Demo Survey</a>
            </div>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title">Create Campaigns</h5>
                        <p class="card-text text-muted small">Build surveys with multiple question types: MCQ, text, and number.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title">Collect Responses</h5>
                        <p class="card-text text-muted small">Share a link; participants answer one question at a time.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title">View Reports</h5>
                        <p class="card-text text-muted small">See scores, breakdowns, and export to CSV.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="container py-3 text-center text-muted small">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>
</html>
