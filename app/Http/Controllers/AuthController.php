<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function register(Request $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'member',
        ]);
        ActivityLog::create([
            'user_name' => $user->name,
            'activity' => 'User Register',
            'description' => "User baru dengan nama " . $user->name . " telah mendaftar sebagai " . $user->role
        ]);
        return response()->json(['message' => 'User berhasil didaftarkan', 'user' => $user]);
    }


    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Tambahkan 'api' di dalam kurung auth agar mendapatkan string token JWT
    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json(['error' => 'Email atau Password Salah'], 401);
    }

    $user = auth('api')->user();

    // Mencatat log aktivitas
    \App\Models\ActivityLog::create([
        'user_name' => $user->name,
        'activity' => 'User Login',
        'description' => "User " . $user->name . " berhasil masuk ke sistem"
    ]);

    return response()->json([
        'message' => 'Login Berhasil',
        'user' => $user,
        'token' => $token // Sekarang ini akan berisi string panjang
    ]);
}
// public function refresh()
// {
//     try {
//         // 1. Mencoba memperbarui token yang dikirim di Header Postman
//         $newToken = auth('api')->refresh();

//         return response()->json([
//             'message' => 'Token berhasil diperbarui',
//             'token' => $newToken
//         ]);
//     } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
//         return response()->json(['error' => 'Token sudah terlalu lama, silakan login ulang'], 401);
//     } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
//         return response()->json(['error' => 'Gagal memperbarui token: ' . $e->getMessage()], 500);
//     }
// }


    public function logout() {
        $user = auth()->user();
        ActivityLog::create([
            'user_name' => $user->name,
            'activity' => 'User Logout',
            'description' => "User " . $user->name . " telah keluar dari sistem"
        ]);
        auth()->logout();
        return response()->json(['message' => 'Logout berhasil']);
    }

}
