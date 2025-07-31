<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatPeminjamanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Peminjaman::with('barang');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from') && $request->date_from) {
            $query->where('tanggal_pinjam', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('tanggal_pinjam', '<=', $request->date_to);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            })->orWhere('nama_peminjam', 'like', "%{$search}%");
        }

        $borrowings = $query->orderBy('tanggal_pinjam', 'desc')->paginate(10);

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

    public function exportPdf(Request $request)
    {
        try {
            $query = Peminjaman::with('barang');

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('date_from') && $request->date_from) {
                $query->where('tanggal_pinjam', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->where('tanggal_pinjam', '<=', $request->date_to);
            }
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%");
                })->orWhere('nama_peminjam', 'like', "%{$search}%");
            }

            $borrowings = $query->orderBy('tanggal_pinjam', 'desc')->get();

            $stats = [
                'total' => $borrowings->count(),
                'borrowed' => $borrowings->where('status', 'dipinjam')->count(),
                'returned' => $borrowings->where('status', 'dikembalikan')->count(),
                'overdue' => $borrowings->where('status', 'dipinjam')
                    ->filter(function ($borrowing) {
                        return $borrowing->tanggal_pinjam->diffInDays(now()) > 7;
                    })->count(),
            ];

            $filters = [
                'Status' => $request->status ? ($request->status == 'dipinjam' ? 'Sedang Dipinjam' : 'Sudah Dikembalikan') : 'Semua Status',
                'Dari Tanggal' => $request->date_from ?: 'Tidak ditentukan',
                'Sampai Tanggal' => $request->date_to ?: 'Tidak ditentukan',
                'Pencarian' => $request->search ?: 'Tidak ada',
            ];

            Log::info('Generating PDF for borrowing history', ['filters' => $request->all(), 'total_records' => $borrowings->count()]);

            $pdf = Pdf::loadView('history.pdf', compact('borrowings', 'stats', 'filters'))
                ->setPaper('a4', 'landscape') // Pastikan orientasi lanskap
                ->setOptions(['dpi' => 150, 'defaultFont' => 'Arial']); // Tingkatkan kualitas dan font default
            return $pdf->download('laporan_riwayat_peminjaman_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF for borrowing history', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghasilkan laporan PDF: ' . $e->getMessage());
        }
    }
}
