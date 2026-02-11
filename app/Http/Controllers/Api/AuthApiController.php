<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Cek user aktif
            if (!$user->is_active) {
                return response()->json(['message' => 'Akun tidak aktif'], 403);
            }

            // Ambil role dari relasi
            $role = $user->adminRole->role ?? null;
            if (!$role) {
                return response()->json(['message' => 'Role tidak ditemukan'], 403);
            }

            // Return JSON dengan token Sanctum
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
                'role' => $role,
                'isAdmin' => !!$role,  // Tambahkan flag isAdmin untuk Next.js
            ]);
        }

        return response()->json(['message' => 'Email atau password salah'], 401);
    }

    public function apiRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
            'isAdmin' => false,  // Default false untuk user baru
        ], 201);
    }

    // Tambahkan method logout
    public function apiLogout(Request $request)
    {
        // Revoke token saat ini (Sanctum)
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}