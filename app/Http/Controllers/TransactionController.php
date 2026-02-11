<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Game;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * =========================
     * LIST & FILTER TRANSAKSI
     * =========================
     */
    public function index(Request $request)
    {
        $query = Transaction::with('game');

        // 🔍 Search invoice / ID
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
            });
        }

        // 🎮 Filter game
        if ($request->filled('game')) {
            $query->where('game_id', $request->game);
        }

        // 📦 Status pesanan
        if ($request->filled('status_pesanan')) {
            $query->where('status_pesanan', $request->status_pesanan);
        }

        // 💳 Status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // 📅 Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $transaksis = $query
            ->latest()
            ->paginate(10);

        $games = Game::orderBy('name')->get();

        return view('admin.transaction.transaction', compact('transaksis', 'games'));
    }

    /**
     * =========================
     * UPDATE TRANSAKSI
     * (status + serial number)
     * =========================
     */
    public function update(Request $request, $id)
    {
        $transaksi = Transaction::findOrFail($id);

        $request->validate([
            'status_pesanan'    => 'required|in:pending,success,failed',
            'status_pembayaran' => 'required|in:paid,unpaid,failed',
            'serial_number'     => 'nullable|string|max:255',
        ]);

        $transaksi->update([
            'status_pesanan'    => $request->status_pesanan,
            'status_pembayaran' => $request->status_pembayaran,
            'serial_number'     => $request->serial_number,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * =========================
     * HAPUS TRANSAKSI
     * =========================
     */
    public function destroy($id)
    {
        $transaksi = Transaction::findOrFail($id);

        // 🔒 Optional safety rule
        // if ($transaksi->status_pesanan === 'success') {
        //     return back()->withErrors('Transaksi sukses tidak boleh dihapus.');
        // }

        $transaksi->delete();

        return redirect()
            ->back()
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
