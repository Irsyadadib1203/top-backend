<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ❌ user nonaktif
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun tidak aktif.'
                ]);
            }

            // ambil role dari relasi
            $role = $user->adminRole->role ?? null;

            if (!$role) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Role tidak ditemukan.'
                ]);
            }

            // arahkan berdasarkan role
            return match ($role) {
                'superadmin' => redirect()->route('admin.dashboard'),
                'admin'      => redirect()->route('admin.dashboard'),
                'operator'   => redirect()->route('admin.dashboard'),
                default      => $this->logoutWithError(),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    private function logoutWithError()
    {
        Auth::logout();
        return redirect('/')->withErrors([
            'email' => 'Role tidak dikenali.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function register()
    {
        // Cegah register kalau user sudah ada
        if (User::count() > 0) {
            abort(403, 'Register sudah ditutup');
        }

        return view('admin.auth.register');
    }

    public function store(Request $request)
    {
        // Cegah register ulang
        if (User::count() > 0) {
            abort(403, 'Register sudah ditutup');
        }

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([  
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'superadmin', // user pertama
        ]);

        return redirect()->route('login')->with('success', 'User pertama berhasil dibuat');
    }
}
