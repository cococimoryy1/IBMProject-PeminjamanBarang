<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\RiwayatPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            \DB::connection()->getPdo();
            Log::info('Database Connection Successful');

            // Total Barang
            $totalItems = Barang::count();
            Log::info('Total Barang', ['count' => $totalItems, 'all_items' => Barang::all()->toArray()]);

            // Barang Sedang Dipinjam
            $borrowedItems = Peminjaman::where('status', 'dipinjam')->sum('jumlah');
            Log::info('Barang Sedang Dipinjam', ['count' => $borrowedItems, 'query' => Peminjaman::where('status', 'dipinjam')->get()->toArray()]);

            // Barang Tersedia
            $availableItems = Barang::leftJoin('peminjamans', function ($join) {
                $join->on('barangs.id', '=', 'peminjamans.barang_id')
                     ->where('peminjamans.status', 'dipinjam');
            })
            ->select('barangs.*', DB::raw('COALESCE(SUM(peminjamans.jumlah), 0) as total_borrowed'))
            ->groupBy('barangs.id', 'barangs.nama_barang', 'barangs.kategori', 'barangs.stok', 'barangs.lokasi_penyimpanan', 'barangs.deskripsi', 'barangs.created_at', 'barangs.updated_at')
            ->havingRaw('stok - total_borrowed > 0')
            ->count();
            Log::info('Barang Tersedia', ['count' => $availableItems]);

            // Stok Menipis
            $lowStockItemsList = Barang::where('stok', '<=', 5)
                ->get(['nama_barang as name', 'stok', 'lokasi_penyimpanan as location']);
            $lowStockItems = $lowStockItemsList->count();
            Log::info('Stok Menipis', ['count' => $lowStockItems, 'list' => $lowStockItemsList->toArray()]);

            // Peminjaman Terbaru
            $recentBorrowings = Peminjaman::with('barang')
                ->orderBy('tanggal_pinjam', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($borrowing) {
                    return [
                        'item' => $borrowing->barang,
                        'borrower_name' => $borrowing->nama_peminjam,
                        'borrow_date' => $borrowing->tanggal_pinjam,
                        'status' => $borrowing->status,
                    ];
                });
            Log::info('Peminjaman Terbaru', ['count' => $recentBorrowings->count(), 'data' => $recentBorrowings->toArray()]);

            return view('dashboard', compact(
                'totalItems',
                'availableItems',
                'borrowedItems',
                'lowStockItems',
                'lowStockItemsList',
                'recentBorrowings'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('login')->with('error', 'Gagal memuat dashboard: ' . $e->getMessage());
        }
    }
}
