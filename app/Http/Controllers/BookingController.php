<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\ActivityLog;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class BookingController extends Controller
{
    public function store(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'court_id'     => 'required|exists:courts,id',
        'booking_date' => 'required|date|after_or_equal:today',
        'start_time'   => 'required|date_format:H:i',
        'end_time'     => 'required|date_format:H:i|after:start_time',
    ]);

    // 2. Ambil Data Lapangan untuk mendapatkan harga
    $court = \App\Models\Court::find($request->court_id);

    // 3. Logika Anti-Bentrok (Gunakan kode overlap yang sebelumnya)
    $isBooked = \App\Models\Booking::where('court_id', $request->court_id)
        ->where('booking_date', $request->booking_date)
        ->where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('start_time', '<=', $request->start_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->orWhere(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>=', $request->end_time);
            });
        })->exists();

    if ($isBooked) {
        return response()->json(['message' => 'Jadwal bentrok!'], 422);
    }

    // 4. HITUNG TOTAL HARGA OTOMATIS
    $startTime = \Carbon\Carbon::parse($request->start_time);
    $endTime = \Carbon\Carbon::parse($request->end_time);

    // Menghitung durasi dalam jam (misal 14:00 ke 16:00 = 2 jam)
    $durationInHours = $startTime->diffInHours($endTime);

    // Total = Durasi x Harga Lapangan
    $totalPrice = $durationInHours * $court->price_per_hour;

    // 5. Simpan ke Database
    $booking = \App\Models\Booking::create([
        'user_id'      => auth('api')->id(),
        'court_id'     => $request->court_id,
        'booking_date' => $request->booking_date,
        'start_time'   => $request->start_time,
        'end_time'     => $request->end_time,
        'total_price'  => $totalPrice, // Hasil hitungan otomatis
        'status'       => 'pending'
    ]);

    // 6. Catat Log (Tugas Syaddad)
    \App\Models\ActivityLog::create([
        'user_name'   => auth('api')->user()->name,
        'activity'    => 'Membuat Booking',
        'description' => "Booking Lapangan " . $court->name . " senilai Rp " . number_format($totalPrice)
    ]);

    return response()->json([
        'message' => 'Booking berhasil dibuat',
        'total_bayar' => $totalPrice,
        'data' => $booking
    ], 201);
}
public function destroy($id)
{
    // 1. Cari data booking berdasarkan ID
    $booking = \App\Models\Booking::find($id);

    // 2. Jika tidak ditemukan, kirim error 404
    if (!$booking) {
        return response()->json(['message' => 'Data booking tidak ditemukan'], 404);
    }

    // 3. Simpan ID booking untuk deskripsi log sebelum dihapus
    $bookingId = $booking->id;
    $userName = $booking->user_id; // Bisa dikembangkan untuk mengambil nama user

    // 4. Hapus data booking
    $booking->delete();

    // 5. Catat ke Log Aktivitas (Tugas Syaddad)
    \App\Models\ActivityLog::create([
        'user_name' => auth('api')->user()->name,
        'activity' => 'Hapus Booking',
        'description' => "Admin " . auth('api')->user()->name . " menghapus data booking ID: " . $bookingId
    ]);

    return response()->json([
        'message' => 'Booking berhasil dihapus oleh Admin'
    ], 200);
}
}