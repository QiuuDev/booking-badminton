<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    // Member mengunggah bukti bayar
    public function uploadProof(Request $request) {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'image' => 'required|image|max:5120', // max 5MB
        ]);

        // store image in public disk under proofs/ and get path
        $path = $request->file('image')->store('proofs', 'public');

        $payment = Payment::create([
            'booking_id' => $data['booking_id'],
            'proof_image' => $path,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Bukti bayar berhasil diunggah', 'data' => $payment], 201);
    }

    // Admin memvalidasi pembayaran [cite: 1, 17, 29]
    public function validatePayment(Request $request, $id) {
        $payment = Payment::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:verified,rejected'
        ]);

        $payment->update(['status' => $data['status']]); // verified atau rejected

        if ($data['status'] === 'verified') {
            // mark booking as paid
            $booking = Booking::find($payment->booking_id);
            if ($booking) {
                $booking->update(['status' => 'Paid']);
            }
        }

        return response()->json(['message' => 'Status pembayaran diperbarui', 'data' => $payment]);
    }
}
