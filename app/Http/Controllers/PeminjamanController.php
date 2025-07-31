<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\RiwayatPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Pastikan middleware auth diterapkan di konstruktor
    }

    public function index()
    {
        $pinjamBelumKembali = Peminjaman::where('status', 'dipinjam')->get();
        return view('peminjaman.return', compact('pinjamBelumKembali'));
    }

    public function create()
    {
        $barangs = Barang::all();
        if ($barangs->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barang yang tersedia.');
        }
        return view('peminjaman.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'nama_peminjam' => 'required|string|max:255',
            'tanggal_pinjam' => 'required|date',
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = Barang::find($request->barang_id);
        if (!$barang) {
            return redirect()->back()->withErrors(['barang_id' => 'Barang tidak ditemukan.']);
        }
        if ($barang->stok < $request->jumlah) {
            return redirect()->back()->withErrors(['jumlah' => 'Stok tidak cukup.']);
        }

        $peminjaman = Peminjaman::create([
            'barang_id' => $request->barang_id,
            'user_id' => Auth::id(),
            'nama_peminjam' => $request->nama_peminjam,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jumlah' => $request->jumlah,
            'status' => 'dipinjam',
        ]);

        $barang->stok -= $request->jumlah;
        $barang->save();

        RiwayatPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'nama_barang' => $barang->nama_barang,
            'nama_peminjam' => $request->nama_peminjam,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'status' => 'dipinjam',
        ]);

        return redirect()->route('peminjaman.riwayat')->with('success', 'Peminjaman berhasil.');
    }

    public function returnItem($id)
    {
        $peminjaman = Peminjaman::with('barang')->find($id);
        if (!$peminjaman) {
            return redirect()->back()->with('error', 'Peminjaman tidak ditemukan.');
        }
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

        $riwayat = RiwayatPeminjaman::where('peminjaman_id', $peminjaman->id)->first();
        if ($riwayat) {
            $riwayat->status = 'dikembalikan';
            $riwayat->tanggal_kembali = now();
            $riwayat->save();
        }

        return redirect()->route('peminjaman.riwayat')->with('success', 'Barang berhasil dikembalikan.');
    }

    public function riwayat(Request $request)
    {
        $query = Peminjaman::with('barang');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status == 'borrowed' ? 'dipinjam' : 'dikembalikan');
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('tanggal_pinjam', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('tanggal_pinjam', '<=', $request->date_to);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            })->orWhere('nama_peminjam', 'like', "%{$search}%");
        }

        $borrowings = $query->orderBy('tanggal_pinjam', 'desc')->paginate(10);

        // Statistik
        $stats = [
            'total' => Peminjaman::count(),
            'borrowed' => Peminjaman::where('status', 'dipinjam')->count(),
            'returned' => Peminjaman::where('status', 'dikembalikan')->count(),
            'overdue' => Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_pinjam', '<', now()->subDays(7))
                ->count(),
        ];

        return view('history.index', compact('borrowings', 'stats'));
    }
    public function show($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);
        $data = [
            'barang' => $peminjaman->barang,
            'nama_peminjam' => $peminjaman->nama_peminjam,
            'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('d/m/Y'),
            'tanggal_kembali' => $peminjaman->tanggal_kembali?->format('d/m/Y'),
            'jumlah' => $peminjaman->jumlah,
            'status' => $peminjaman->status,
            'catatan' => $peminjaman->catatan ?? null,
            'return_notes' => $peminjaman->return_notes ?? null,
        ];

        return response()->json($data);
    }

}
