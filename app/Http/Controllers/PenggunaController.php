<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    // ✅ Menampilkan semua pengguna
    public function index()
    {
        $pengguna = User::with(['adminRole', 'adminProfile'])->get()
            ->map(function ($user) {
                return (object) [
                    'id_pengguna' => $user->id,
                    'nm_pengguna' => $user->adminProfile->full_name ?? $user->name,
                    'username'    => $user->email,
                    'role'        => $user->adminRole->role ?? '-',
                    'status'      => $user->is_active ? 'aktif' : 'nonaktif',
                ];
            });

        return view('superadmin.pengguna.pengguna', compact('pengguna'));
    }

    // ✅ Simpan data pengguna baru
    public function store(Request $request)
    {
        $request->validate([
            'nm_pengguna' => 'required|string|max:100',
            'username'    => 'required|email|unique:users,email',
            'password'    => 'required|min:6',
            'role'        => 'required|in:admin,operator,superadmin',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->nm_pengguna,
                'email'     => $request->username,
                'password'  => Hash::make($request->password),
                'is_active' => $request->status === 'aktif',
            ]);

            AdminProfile::create([
                'user_id'   => $user->id,
                'full_name' => $request->nm_pengguna,
            ]);

            AdminRole::create([
                'user_id' => $user->id,
                'role'    => $request->role,
            ]);
        });

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // ✅ Update data pengguna
    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_pengguna' => 'required|string|max:100',
            'username'    => 'required|email|unique:users,email,' . $id,
            'role'        => 'required|in:admin,operator,viewer',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($request, $id) {
            $user = User::findOrFail($id);

            $user->update([
                'name'      => $request->nm_pengguna,
                'email'     => $request->username,
                'is_active' => $request->status === 'aktif',
            ]);

            $user->adminProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $request->nm_pengguna]
            );

            $user->adminRole()->updateOrCreate(
                ['user_id' => $user->id],
                ['role' => $request->role]
            );
        });

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // ✅ Hapus pengguna
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);
            $user->adminRole()->delete();
            $user->adminProfile()->delete();
            $user->delete();
        });

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
