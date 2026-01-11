<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BookingController;

// Publik
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
// Terproteksi JWT
Route::middleware('auth:api')->group(function () {

    // Member: Upload bukti bayar
    Route::post('payments/upload', [PaymentController::class, 'uploadProof']);

        // Khusus Admin: Validasi pembayaran
        Route::middleware('role:admin')->group(function () {
            Route::put('payments/{id}/validate', [PaymentController::class, 'validatePayment']);
        });
        Route::middleware('auth:api')->group(function () {
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
    Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('courts', [CourtController::class, 'store']);
    Route::delete('courts/{id}', [CourtController::class, 'destroy']);
    Route::put('courts/{id}', [CourtController::class, 'update']);
    Route::post('equipments', [EquipmentController::class, 'store']);
    // Route::post('/equipments', [EquipmentController::class, 'store']);
});

    // Member bisa melihat data lapangan dan alat [cite: 9]
    Route::get('courts', [CourtController::class, 'index']);
    Route::get('equipments', [EquipmentController::class, 'index']);

    Route::middleware('auth:api')->group(function () {
    // Member melakukan booking
    Route::post('bookings', [BookingController::class, 'store']);

    // Admin melihat Log Aktivitas
    Route::middleware('role:admin')->get('logs', function() {
        return response()->json(\App\Models\ActivityLog::latest()->get());
    });
    Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::put('/courts/{id}', [CourtController::class, 'update']);
});

    // Logout
    Route::post('logout', [AuthController::class, 'logout']);
});
Route::get('/equipments', [EquipmentController::class, 'index']);

// ATAU jika hanya yang login yang bisa melihat:
Route::middleware('auth:api')->group(function () {
    Route::get('/equipments', [EquipmentController::class, 'index']);
});
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/equipments', [EquipmentController::class, 'store']);
    Route::put('/equipments/{id}', [EquipmentController::class, 'update']);
    Route::delete('/equipments/{id}', [EquipmentController::class, 'destroy']);
});
Route::middleware('auth:api')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
});
// Grup rute yang memerlukan login
Route::middleware('auth:api')->group(function () {

    // Member & Admin bisa membuat booking
    Route::post('/bookings', [BookingController::class, 'store']);

    // KHUSUS ADMIN: Hanya admin yang bisa menghapus booking
    Route::middleware('role:Admin')->group(function () {
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    });

});
});
