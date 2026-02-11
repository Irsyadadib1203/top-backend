<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 🔢 TOTAL PESANAN
        $totalPesanan = Transaction::count();

        // 🔢 PESANAN HARI INI
        $pesananHarian = Transaction::whereDate('created_at', $today)->count();

        // 💰 TOTAL PENJUALAN (PAID & SUCCESS)
        $totalPenjualan = Transaction::whereDate('created_at', $today)
            ->where('status', 'success')
            ->sum('total_amount');

        // 💸 ADMIN FEE HARIAN
        // asumsi kolom admin_fee ada di tabel transaksi
        $adminFeeHarian = Transaction::whereDate('created_at', $today)
            ->where('status', 'success')
            ->sum('admin_fee');

        // 🔄 TRANSAKSI REALTIME (10 TERBARU)
        $transaksiRealtime = Transaction::with('game')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalPesanan',
            'pesananHarian',
            'totalPenjualan',
            'adminFeeHarian',
            'transaksiRealtime'
        ));
    }
}
