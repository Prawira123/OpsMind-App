<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Tenant\AccountController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Tenant\ChartOfAccountController;
use App\Http\Controllers\Tenant\ClientController;
use App\Http\Controllers\Tenant\NotificationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//isolation test
// Route::get('/test-isolation', function () {
//     $user = Auth::user();

//     return response()->json([
//         'logged_in_as'    => $user->name,
//         'tenant_id'       => $user->tenant_id,
//         'categories'      => \App\Models\Category::all(),
//         'transactions'    => \App\Models\Transaction::all(),
//     ]);
// })->middleware(['auth', 'setCurrentTenant']);

route::get('/SubscriptionPlan', [SubscriptionController::class, 'index'])->name('subs.index')->middleware('auth');
route::post('/SubscriptionPlan', [SubscriptionController::class, 'select'])->name('subs.select')->middleware('auth');

Route::middleware(['auth', 'otpVerified', 'tenantExists', 'setCurrentTenant', 'SubscriptionActive'])->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Account Route
    Route::delete('accounts/bulk-destroy', [AccountController::class, 'bulkDestroy'])
     ->name('accounts.bulk-destroy')->middleware(['throttle:60,1']);
    Route::resource('accounts', AccountController::class)->only(['index', 'create' ,'store', 'edit', 'update', 'destroy']);

    //Category Route
    Route::delete('categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->middleware(['throttle:60,1'])
    ->name('categories.bulk-destroy');
    Route::resource('categories', CategoryController::class)->only(['index', 'create' ,'store', 'edit', 'update', 'destroy']);

    //Chart of accounts Route
    Route::delete('CoA/bulk-destroy', [ChartOfAccountController::class, 'bulkDestroy'])->middleware(['throttle:60,1'])
    ->name('CoA.bulk-destroy');
    Route::resource('chart-of-accounts', ChartOfAccountController::class)->only(['index', 'create' ,'store', 'edit', 'update', 'destroy']);

    //Client Route
    Route::resource('clients', ClientController::class)->only(['index', 'create' ,'store', 'edit', 'update', 'destroy']);

    //Profile route
    Route::resource('profiles', ProfileController::class)->only(['index']);
    Route::post('profile/update_tenant', [ProfileController::class, 'updateTenant'])->name('profile.update_tenant');
    Route::put('profile/update_user/{id}', [ProfileController::class, 'updateUser'])->name('profile.update_user');
    Route::put('profile/update_2fa', [ProfileController::class, 'update2FA'])->name('profile.update_2fa');
    Route::put('profile/update_password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');

    //Notification Route
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])
     ->name('notifications.destroy');
    Route::get('notifications/{id}', [NotificationController::class, 'show'])
     ->name('notifications.show');
});

require __DIR__.'/auth.php';
