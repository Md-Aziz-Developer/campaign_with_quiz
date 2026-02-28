<?php

use App\Http\Controllers\CampaignController as PublicCampaignController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/campaign/{campaign:unique_slug}', [PublicCampaignController::class, 'show'])->name('campaign.show');
Route::post('/campaign/{campaign:unique_slug}/start', [PublicCampaignController::class, 'start'])->name('campaign.start');
Route::get('/campaign/{campaign:unique_slug}/question/{order}', [PublicCampaignController::class, 'question'])->name('campaign.question');
Route::post('/campaign/{campaign:unique_slug}/answer', [PublicCampaignController::class, 'answer'])->name('campaign.answer');
Route::get('/campaign/{campaign:unique_slug}/complete', [PublicCampaignController::class, 'complete'])->name('campaign.complete');

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout')->middleware('auth');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('campaigns/{campaign}/publish', [CampaignController::class, 'publish'])->name('campaigns.publish');
    Route::get('campaigns/{campaign}/questions', [QuestionController::class, 'index'])->name('campaigns.questions');
    Route::post('campaigns/{campaign}/questions', [QuestionController::class, 'store'])->name('campaigns.questions.store');
    Route::delete('campaigns/{campaign}/questions/{question}', [QuestionController::class, 'destroy'])->name('campaigns.questions.destroy');
    Route::resource('campaigns', CampaignController::class);
    Route::get('campaigns/{campaign}/reports', [ReportController::class, 'index'])->name('campaigns.reports.index');
    Route::get('campaigns/{campaign}/reports/export', [ReportController::class, 'export'])->name('campaigns.reports.export');
    Route::get('campaigns/{campaign}/reports/{response}', [ReportController::class, 'show'])->name('campaigns.reports.show');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
