<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CultivationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SettingController;
use App\Http\Middleware\ActiveUser;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::view('/tentang', 'public.about');
Route::view('/kontak', 'public.contact');
Route::get('/kwt', [PublicController::class, 'kwts']);
Route::get('/kwt/{kwt}', [PublicController::class, 'kwt']);
Route::get('/komoditas', [PublicController::class, 'commoditiesPage']);
Route::get('/komoditas/{commodity}', [PublicController::class, 'commodity']);
foreach (['sedang-ditanam' => 'Sedang Ditanam', 'segera-panen' => 'Segera Panen', 'ready' => 'Ready'] as $path => $status) {
    Route::get('/' . $path, fn() => redirect('/komoditas?' . http_build_query(['status' => $status])));
}
Route::get('/jadwal-panen', [PublicController::class, 'schedule']);
Route::get('/produk-olahan', [PublicController::class, 'products']);
Route::get('/produk-olahan/{product}', [PublicController::class, 'product']);
// Route::get('/kegiatan', [PublicController::class, 'posts']);
// Route::get('/kegiatan/{post:slug}', [PublicController::class, 'post']);
Route::get('/whatsapp/{kind}/{id}', [PublicController::class, 'whatsapp'])->middleware('throttle:30,1');
Route::get('/foto/{kind}/{id}', [CultivationController::class, 'photo'])->name('photo');
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::prefix('admin')->middleware(['auth', ActiveUser::class])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::match(['get', 'post'], '/account', [AuthController::class, 'account']);
    Route::match(['get', 'post'], '/settings', SettingController::class);
    Route::get('/reports', ReportController::class);
    Route::get('/harvests', [CultivationController::class, 'harvests']);
    Route::get('/monitorings', [CultivationController::class, 'monitorings']);
    Route::get('/cycles/{cycle}', [CultivationController::class, 'show']);
    Route::post('/cycles/{cycle}/monitorings', [CultivationController::class, 'monitor']);
    Route::post('/cycles/{cycle}/harvests', [CultivationController::class, 'harvest']);
    Route::delete('/harvests/{harvest}', [CultivationController::class, 'deleteHarvest']);
    Route::post('/commodities/{commodity}/availability', [CultivationController::class, 'availability']);
    Route::delete('/photos/{kind}/{id}', [CultivationController::class, 'deletePhoto']);
    Route::get('/manage/{resource}', [ResourceController::class, 'index']);
    Route::get('/manage/{resource}/create', [ResourceController::class, 'create']);
    Route::post('/manage/{resource}', [ResourceController::class, 'store']);
    Route::get('/manage/{resource}/{id}/edit', [ResourceController::class, 'edit']);
    Route::put('/manage/{resource}/{id}', [ResourceController::class, 'update']);
    Route::delete('/manage/{resource}/{id}', [ResourceController::class, 'destroy']);
});
