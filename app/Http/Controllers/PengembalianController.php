<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $borrowings = Peminjaman::with('barang')
            ->where('status', 'dipinjam')
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();

        return view('pengembalian.index', compact('borrowings'));
    }

    public function returnItem($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);

        if ($peminjaman->status === 'dikembalikan') {
            return redirect()->back()->with('error', 'Barang sudah dikembalikan.');
        }

        $peminjaman->status = 'dikembalikan';
        $peminjaman->tanggal_kembali = now();
        $peminjaman->save();

        if ($peminjaman->barang) {
            $peminjaman->barang->stok += $peminjaman->jumlah;
            $peminjaman->barang->save();
        }

        if (class_exists(Pengembalian::class)) {
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tanggal_kembali' => now(),
                'jumlah_dikembalikan' => $peminjaman->jumlah,
            ]);
        }

        // Redirect ke halaman pengembalian atau riwayat
        return redirect()->route('pengembalian.index')->with('success', 'Barang berhasil dikembalikan.');
    }
}
