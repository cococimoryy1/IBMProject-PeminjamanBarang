<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $filter = $request->input('filter');

        $barangs = Barang::query();

        if ($query) {
            $barangs = $barangs->where('nama_barang', 'like', "%$query%")
                               ->orWhere('kategori', 'like', "%$query%");
        }

        if ($filter == 'low_stock') {
            $barangs = $barangs->where('stok', '<', 5)->where('stok', '>', 0);
        } elseif ($filter == 'out_of_stock') {
            $barangs = $barangs->where('stok', '=', 0);
        }

        $barangs = $barangs->paginate(10)->withQueryString();

        return view('items.index', compact('barangs'));
    }

    public function create()
    {
        $categories = Category::all(); // Ambil semua kategori dari tabel categories
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        \Log::info('Request Data:', $request->all());

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255|exists:categories,name', // Validasi kategori dari tabel categories
            'stok' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required|string|max:255', // Diubah ke required sesuai form
            'deskripsi' => 'nullable|string',
        ]);

        \Log::info('Validated Data:', $validated);

        try {
            Barang::create($validated);
            return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error menyimpan barang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan barang: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $categories = Category::all();
        $locations = Barang::distinct()->pluck('lokasi_penyimpanan')->filter()->values();
        $currentBorrowings = $barang->peminjamans()->where('status', 'dipinjam')->get();
        $borrowedCount = $currentBorrowings->sum('jumlah');

        return view('items.edit', compact('barang', 'categories', 'locations', 'currentBorrowings', 'borrowedCount'));
    }

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nama_barang' => 'required|string|max:255',
        'kategori' => 'nullable|string|max:255|exists:categories,name',
        'stok' => [
            'required',
            'integer',
            'min:0',
            function ($attribute, $value, $fail) use ($id) {
                $barang = Barang::findOrFail($id);
                $borrowedCount = $barang->peminjamans()->where('status', 'dipinjam')->sum('jumlah');
                if ($value < $borrowedCount) {
                    $fail("Stok tidak boleh kurang dari $borrowedCount karena ada barang yang sedang dipinjam.");
                }
            }
        ],
        'lokasi_penyimpanan' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
    ]);

    $barang = Barang::findOrFail($id);
    $barang->update($validated); // Gunakan $validated yang dihasilkan dari $request->validate()
    return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
}

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return view('items.show', compact('barang'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $barangs = Barang::where('nama_barang', 'like', "%$query%")
                        ->orWhere('kategori', 'like', "%$query%")
                        ->paginate(10);
        return view('items.index', compact('barangs'));
    }

    public function filterLowStock()
    {
        $barangs = Barang::where('stok', '<', 5)->where('stok', '>', 0)->paginate(10);
        return view('items.index', compact('barangs'));
    }
}
