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
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Member bisa melihat data lapangan, alat, dan bookings
    Route::get('courts', [CourtController::class, 'index']);
    Route::get('equipments', [EquipmentController::class, 'index']);
    Route::get('bookings', [BookingController::class, 'index']);

    // Member melakukan booking
    Route::post('bookings', [BookingController::class, 'store']);

    // Admin melihat Log Aktivitas
    Route::middleware('role:admin')->get('logs', function() {
        return response()->json(\App\Models\ActivityLog::latest()->get());
    });

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::post('courts', [CourtController::class, 'store']);
        Route::put('courts/{id}', [CourtController::class, 'update']);
        Route::delete('courts/{id}', [CourtController::class, 'destroy']);

        Route::post('equipments', [EquipmentController::class, 'store']);
        Route::put('equipments/{id}', [EquipmentController::class, 'update']);
        Route::delete('equipments/{id}', [EquipmentController::class, 'destroy']);

        Route::put('payments/{id}/validate', [PaymentController::class, 'validatePayment']);
        Route::delete('bookings/{id}', [BookingController::class, 'destroy']);
    });
});
