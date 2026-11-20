<?php

use App\Http\Controllers\DashboardFacilityController;
use App\Http\Controllers\DashboardNewsController;
use App\Http\Controllers\DashboardOrderController;
use App\Http\Controllers\DashboardScheduleController;
use App\Http\Controllers\DashboardSuggestionController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SuggestionController;
use App\Http\Middleware\DemoOrAuthenticate;
use App\Http\Middleware\DisableRegistrationInDemo;
use App\Http\Middleware\GuestUnlessDemo;
use App\Services\DemoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::redirect('/home', '/');
Route::get('/tempat', [FacilityController::class, 'index'])->name('places');
Route::get('/jadwal', [ScheduleController::class, 'index'])->name('calendar');
Route::post('/search', [ScheduleController::class, 'search'])->middleware('throttle:30,1');
Route::get('/kritik', [SuggestionController::class, 'index']);
Route::get('/suggestion/create', [SuggestionController::class, 'create']);
Route::post('/suggestion/store', [SuggestionController::class, 'store'])->middleware('throttle:10,1');
Route::get('/tambah', [SuggestionController::class, 'create']);

// In demo mode, login stays visible and registration is unnecessary.
// Outside demo mode, the original guest-only login and registration apply.
Route::middleware(GuestUnlessDemo::class)->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->middleware('throttle:30,1');
});
Route::post('/demo/role/{role}', function (Request $request, string $role, DemoAccess $demo) {
    abort_unless(config('demo.enabled'), 404);
    Auth::logout();
    $demo->enter($request, $role);

    return redirect('/dashboard/utama');
})->whereIn('role', ['superadmin', 'organization', 'manager'])->middleware('throttle:30,1')->name('demo.role');
Route::middleware([GuestUnlessDemo::class, DisableRegistrationInDemo::class])->group(function () {
    Route::get('/register', [RegisterController::class, 'index']);
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:6,1');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Demo visitors may open deep dashboard links without first visiting /login.
// Demo visitors receive a demo session; normal users keep Laravel auth middleware.
Route::middleware(DemoOrAuthenticate::class)->group(function () {
    Route::redirect('/dashboard', '/dashboard/utama');
    Route::view('/dashboard/utama', 'utama')->name('dashboard');
    Route::get('/dashboard/order', [DashboardOrderController::class, 'index']);
    Route::get('/dashboard/kategori/{id}', [DashboardOrderController::class, 'kategori']);
    Route::get('/dashboard/schedule', [DashboardScheduleController::class, 'index']);
    Route::post('/dashboard/cari', [DashboardScheduleController::class, 'cari']);
    Route::resource('/order', DashboardOrderController::class)->except(['show']);
    // Older links are retained for existing templates and bookmarked forms.
    Route::get('/order/edit/{order}', [DashboardOrderController::class, 'edit']);
    Route::post('/order/store', [DashboardOrderController::class, 'store']);
    Route::post('/order/update/{order}', [DashboardOrderController::class, 'update']);
    Route::delete('/order/destroy/{order}', [DashboardOrderController::class, 'destroy']);
});

Route::middleware([DemoOrAuthenticate::class, 'admin'])->group(function () {
    Route::resource('/dashboard/facility', DashboardFacilityController::class)->names('facility');
    Route::resource('/dashboard/user', DashboardUserController::class)->names('user');
    Route::resource('/dashboard/suggestion', DashboardSuggestionController::class)->names('suggestion');
    Route::resource('/dashboard/news', DashboardNewsController::class)->names('news');
    Route::resource('/schedule', DashboardScheduleController::class)->except(['index'])->names('schedule');
});

Route::middleware([DemoOrAuthenticate::class, 'role:superadmin,manager'])->group(function () {
    Route::post('/dashboard/kirim_pesan_pemesanan', [DashboardOrderController::class, 'kirim_pesan_pemesanan']);
    Route::post('/dashboard/kirim_pesan_pemesanan_terima/{id}', [DashboardOrderController::class, 'kirim_pesan_pemesanan_terima']);
    Route::post('/dashboard/terima/{id}', [DashboardOrderController::class, 'terima']);
    Route::post('/dashboard/tolak/{id}', [DashboardOrderController::class, 'tolak']);
});
