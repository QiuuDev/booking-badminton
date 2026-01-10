<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index() {
        return response()->json(Court::all()); // Mengambil semua data lapangan 
    }

    public function store(Request $request) {
    // 1. Simpan Lapangan
    $court = Court::create($request->all());

    // 2. CATAT LOG DI SINI (Pastikan baris ini ada)
    \App\Models\ActivityLog::create([
        'user_name' => auth()->user()->name,
        'activity' => 'Tambah Lapangan',
        'description' => "Admin menambahkan lapangan baru: " . $court->name
    ]);

    return response()->json(['message' => 'Lapangan berhasil ditambah', 'data' => $court], 201);
}

    public function destroy($id)
{
    // 1. Cari data lapangan berdasarkan ID
    $court = \App\Models\Court::find($id);

    if (!$court) {
        return response()->json(['message' => 'Lapangan tidak ditemukan'], 404);
    }

    // 2. Simpan nama lapangan untuk keperluan log sebelum dihapus
    $courtName = $court->name;

    // 3. Hapus data lapangan
    $court->delete();

    // 4. Catat aktivitas ke Log (Tugas Syaddad)
    // Pastikan model ActivityLog sudah memiliki $fillable agar tidak error
    \App\Models\ActivityLog::create([
        'user_name' => auth()->user()->name, 
        'activity' => 'Hapus Lapangan',
        'description' => "Admin menghapus lapangan: " . $courtName . " (ID: " . $id . ")"
    ]);

    return response()->json([
        'message' => 'Lapangan berhasil dihapus dari sistem'
    ], 200);
}
}

