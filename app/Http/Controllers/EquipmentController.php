<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment; 
use App\Models\ActivityLog;

class EquipmentController extends Controller
{
    // 1. GET: Melihat semua alat
    public function index()
    {
        $equipments = Equipment::all();
        return response()->json(['data' => $equipments], 200);
    }
    // 2. POST: Menambah alat baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
    $equipment = Equipment::create($request->all());

        ActivityLog::create([
            'user_name' => auth()->user()->name, 
            'activity' => 'Tambah Alat',
            'description' => "Admin menambahkan alat: " . $equipment->name
        ]);

        return response()->json(['message' => 'Alat berhasil ditambahkan', 'data' => $equipment], 201);
    }
    // 3. PUT: Mengupdate data alat (TAMBAHKAN KODE INI)
    public function update(Request $request, $id)
    {
        // Cari alat berdasarkan ID
        $equipment = Equipment::find($id);

        if (!$equipment) {
            return response()->json(['message' => 'Alat tidak ditemukan'], 404);
        }

        // Simpan nama lama untuk keperluan deskripsi log
        $oldName = $equipment->name;

        // Update data
        $equipment->update($request->all());

        // Catat Log Aktivitas
        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'activity' => 'Update Alat',
            'description' => "Admin mengubah data alat '$oldName' menjadi '" . $equipment->name . "'"
        ]);

        return response()->json([
            'message' => 'Data alat berhasil diperbarui',
            'data' => $equipment
        ], 200);
    }
    public function destroy($id)
{
    // 1. Cari alat berdasarkan ID
    $equipment = Equipment::find($id);

    // 2. Jika alat tidak ditemukan, kirim pesan error 404
    if (!$equipment) {
        return response()->json([
            'message' => 'Alat tidak ditemukan atau sudah dihapus'
        ], 404);
    }

    // 3. Simpan nama alat untuk catatan log
    $equipmentName = $equipment->name;

    // 4. Hapus data dari database
    $equipment->delete();

    // 5. Catat ke Log Aktivitas
    // Pastikan Anda sudah login untuk mendapatkan auth()->user()
    ActivityLog::create([
        'user_name' => auth()->user()->name,
        'activity' => 'Hapus Alat',
        'description' => "Admin menghapus alat '$equipmentName' (ID: $id) dari stok"
    ]);

    return response()->json([
        'message' => 'Alat berhasil dihapus dari sistem'
    ], 200);
}
}
