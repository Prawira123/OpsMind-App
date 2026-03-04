<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\AccountController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Tenant\ChartOfAccountController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use OpenAI\Enums\Moderations\Category;

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

Route::middleware(['auth', 'otpVerified', 'tenantExists', 'setCurrentTenant'])->group(function(){
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    //Account Route
    Route::resource('accounts', AccountController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    //Category Route
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    //Chart of accounts Route
    Route::resource('chart-of-accounts', ChartOfAccountController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
}) ;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
