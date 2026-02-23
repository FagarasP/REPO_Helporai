<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('register', [App\Http\Controllers\RegisterController::class, 'register'])->name('register');
Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index']);

Route::get('/change-password', [PasswordChangeController::class, 'show'])->name('password.change');
Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.change.update');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin/panel', [App\Http\Controllers\AdminController::class, 'panel'])->name('admin.panel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/reset-password', [UserController::class, 'sendResetLink'])
        ->name('users.reset-password');
    Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])
        ->name('users.updatePermissions');
    Route::resource('users', UserController::class)->only([
        'index', 'store', 'update', 'destroy',
    ]);

    Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::put('/tickets/{ticket}', [App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::get('/communication', [App\Http\Controllers\CommunicationController::class, 'index'])
        ->name('communication.index');
    Route::post('/communication/{room}/messages', [App\Http\Controllers\CommunicationController::class, 'storeMessage'])
        ->name('communication.messages.store');

    Route::get('/messages', [App\Http\Controllers\ConversationController::class, 'index'])->name('messages.index');
    Route::get('/messages/contacts', [App\Http\Controllers\ConversationController::class, 'contacts'])->name('messages.contacts');
    Route::post('/messages', [App\Http\Controllers\ConversationController::class, 'createConversation'])->name('messages.createConversation');
    Route::get('/messages/{conversation}', [App\Http\Controllers\ConversationController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}/send', [App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('messages.sendMessage');
    Route::get('/messages/{conversation}/list', [App\Http\Controllers\ConversationController::class, 'listMessages'])->name('messages.listMessages');
    Route::post('/messages/{conversation}/mark-read', [App\Http\Controllers\ConversationController::class, 'markRead'])->name('messages.markRead');
    Route::post('/messages/{conversation}/typing', [App\Http\Controllers\ConversationController::class, 'typing'])->name('messages.typing');

    Route::post('/calls/{conversation}/offer', [App\Http\Controllers\CallSignalController::class, 'offer'])->name('calls.offer');
    Route::post('/calls/{conversation}/answer', [App\Http\Controllers\CallSignalController::class, 'answer'])->name('calls.answer');
    Route::post('/calls/{conversation}/ice', [App\Http\Controllers\CallSignalController::class, 'ice'])->name('calls.ice');
    Route::post('/calls/{conversation}/hangup', [App\Http\Controllers\CallSignalController::class, 'hangup'])->name('calls.hangup');

    Route::post('/presence/heartbeat', [App\Http\Controllers\PresenceController::class, 'heartbeat'])->name('presence.heartbeat');
    Route::get('/presence/bulk', [App\Http\Controllers\PresenceController::class, 'bulk'])->name('presence.bulk');

});

Route::get('image/{path}', function ($path) {
    $path = storage_path('app/public/'.$path);

    if (! File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header('Content-Type', $type);

    return $response;
})->where('path', '.*');

require __DIR__.'/auth.php';

// Company
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/company/details/{company}/remove-logo', [App\Http\Controllers\CompanyDetailsController::class, 'removeLogo'])->name('company.details.removeLogo');
    Route::post('/company/details', [App\Http\Controllers\CompanyDetailsController::class, 'store'])->name('company.details.store');
    Route::post('/company/details/{company}', [App\Http\Controllers\CompanyDetailsController::class, 'update'])->name('company.details.update');
    Route::get('/company/dashboard', [App\Http\Controllers\CompanyController::class, 'dashboard'])->name('company.dashboard');
    Route::resource('company/projects', App\Http\Controllers\ProjectController::class)->except([
        'show',
    ])->names([
        'index' => 'company.projects.index',
        'create' => 'company.projects.create',
        'store' => 'company.projects.store',
        'edit' => 'company.projects.edit',
        'update' => 'company.projects.update',
        'destroy' => 'company.projects.destroy',
    ]);
    Route::post('/company/projects/applications/{application}/reject', [App\Http\Controllers\ProjectController::class, 'rejectApplication'])->name('company.projects.applications.reject');
    Route::get('/company/projects/{project}/applicants', [App\Http\Controllers\ProjectController::class, 'showApplicants'])->name('company.projects.applicants');
    Route::resource('company/teams', App\Http\Controllers\TeamController::class)->except([
        'show',
    ])->names([
        'index' => 'company.teams.index',
        'create' => 'company.teams.create',
        'store' => 'company.teams.store',
        'edit' => 'company.teams.edit',
        'update' => 'company.teams.update',
        'destroy' => 'company.teams.destroy',
    ]);
    Route::get('/company/project-management', [App\Http\Controllers\CompanyController::class, 'projectManagement'])->name('company.project-management');
    Route::get('/company/shift-management', [App\Http\Controllers\CompanyController::class, 'shiftManagement'])->name('company.shift-management');
    Route::get('/company/workforce/scounting', [App\Http\Controllers\ScoutingController::class, 'index'])->name('company.workforce.scounting');
    Route::post('/company/workforce/scounting/search', [App\Http\Controllers\ScoutingController::class, 'search'])->name('company.workforce.scounting.search');
    Route::get('/company/workforce/recruitment', [App\Http\Controllers\CompanyController::class, 'workforceRecruitment'])->name('company.workforce.recruitment');
    Route::get('/company/workforce/watchlist', [App\Http\Controllers\CompanyController::class, 'workforceWatchlist'])->name('company.workforce.watchlist');
    Route::get('/company/finance/payment-entry', [App\Http\Controllers\CompanyController::class, 'financePaymentEntry'])->name('company.finance.payment-entry');
    Route::get('/company/finance/wallet-page', [App\Http\Controllers\CompanyController::class, 'financeWalletPage'])->name('company.finance.wallet-page');
    Route::get('/company/finance/invoice-page', [App\Http\Controllers\CompanyController::class, 'financeInvoicePage'])->name('company.finance.invoice-page');
    Route::post('/freelancer/profile', [App\Http\Controllers\FreelancerProfileController::class, 'store'])->name('freelancer.profile.store');
    Route::post('/freelancer/profile/{user}', [App\Http\Controllers\FreelancerProfileController::class, 'update'])->name('freelancer.profile.update');
});

// Freelancer
Route::get('/freelancer/public/{user}', [App\Http\Controllers\FreelancerController::class, 'publicProfile'])->name('freelancer.public.profile');
Route::get('/freelancer/dashboard', [App\Http\Controllers\FreelancerController::class, 'dashboard'])->name('freelancer.dashboard');
Route::get('/freelancer/job-offers', [App\Http\Controllers\ProjectController::class, 'jobOffers'])->name('freelancer.job-offers');
Route::get('/freelancer/job-offers/{project}', [App\Http\Controllers\ProjectController::class, 'showJobOffer'])->name('freelancer.job-offers.show');
Route::post('/freelancer/job-offers/{project}/apply', [App\Http\Controllers\ProjectController::class, 'apply'])->name('freelancer.job-offers.apply');
Route::get('/freelancer/my-projects', [App\Http\Controllers\FreelancerController::class, 'myProjects'])->name('freelancer.my-projects');
Route::get('/freelancer/shifts', [App\Http\Controllers\FreelancerController::class, 'shifts'])->name('freelancer.shifts');
Route::get('/freelancer/finance', [App\Http\Controllers\FreelancerController::class, 'finance'])->name('freelancer.finance');
Route::get('/freelancer/profile/{user}', [App\Http\Controllers\FreelancerController::class, 'profile'])->name('freelancer.profile');

// Other
Route::get('/other/dashboard', [App\Http\Controllers\OtherController::class, 'dashboard'])->name('other.dashboard');
Route::get('/other/project-management', [App\Http\Controllers\OtherController::class, 'projectManagement'])->name('other.project-management');
Route::resource('other/projects', App\Http\Controllers\ProjectController::class)->except([
    'show',
])->names([
    'index' => 'other.projects.index',
    'create' => 'other.projects.create',
    'store' => 'other.projects.store',
    'edit' => 'other.projects.edit',
    'update' => 'other.projects.update',
    'destroy' => 'other.projects.destroy',
]);
Route::get('/other/projects/{project}/applicants', [App\Http\Controllers\ProjectController::class, 'showApplicants'])->name('other.projects.applicants');
Route::resource('other/teams', App\Http\Controllers\TeamController::class)->except([
    'show',
])->names([
    'index' => 'other.teams.index',
    'create' => 'other.teams.create',
    'store' => 'other.teams.store',
    'edit' => 'other.teams.edit',
    'update' => 'other.teams.update',
    'destroy' => 'other.teams.destroy',
]);
Route::get('/other/shift-management', [App\Http\Controllers\OtherController::class, 'shiftManagement'])->name('other.shift-management');
Route::get('/other/workforce/scounting', [App\Http\Controllers\ScoutingController::class, 'index'])->name('other.workforce.scounting');
Route::post('/other/workforce/scounting/search', [App\Http\Controllers\ScoutingController::class, 'search'])->name('other.workforce.scounting.search');
Route::get('/other/workforce/recruitment', [App\Http\Controllers\OtherController::class, 'workforceRecruitment'])->name('other.workforce.recruitment');
Route::get('/other/workforce/watchlist', [App\Http\Controllers\OtherController::class, 'workforceWatchlist'])->name('other.workforce.watchlist');
Route::get('/other/finance/payment-entry', [App\Http\Controllers\OtherController::class, 'financePaymentEntry'])->name('other.finance.payment-entry');
Route::get('/other/finance/wallet-page', [App\Http\Controllers\OtherController::class, 'financeWalletPage'])->name('other.finance.wallet-page');
Route::get('/other/finance/invoice-page', [App\Http\Controllers\OtherController::class, 'financeInvoicePage'])->name('other.finance.invoice-page');

// Settings
Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::get('/settings/user-management', [App\Http\Controllers\UserController::class, 'index'])->name('settings.user-management');
Route::get('/settings/permissions', [App\Http\Controllers\SettingsController::class, 'permissions'])->name('settings.permissions');
Route::resource('settings/project-settings', App\Http\Controllers\ProjectSettingController::class)->except([
    'show',
])->names([
    'index' => 'settings.project-settings.index',
    'create' => 'settings.project-settings.create',
    'store' => 'settings.project-settings.store',
    'edit' => 'settings.project-settings.edit',
    'update' => 'settings.project-settings.update',
    'destroy' => 'settings.project-settings.destroy',
]);
