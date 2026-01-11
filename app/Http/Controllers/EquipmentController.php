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

}
