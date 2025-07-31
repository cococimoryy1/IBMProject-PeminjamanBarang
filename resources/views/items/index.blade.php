@extends('layout.app')

@section('title', 'Kelola Barang')
@section('page-title', 'Kelola Data Barang')
@section('page-description', 'Kelola semua data barang yang tersedia untuk dipinjam')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <a href="{{ route('barang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Barang Baru
        </a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="{{ route('barang.index') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control search-box"
                       placeholder="Cari barang berdasarkan nama atau kategori..."
                       value="{{ request('search') }}">
                <select name="filter" class="form-select" style="max-width: 200px;">
                    <option value="">Semua Barang</option>
                    <option value="low_stock" {{ request('filter') == 'low_stock' ? 'selected' : '' }}>Stok Kurang dari 5</option>
                    <option value="out_of_stock" {{ request('filter') == 'out_of_stock' ? 'selected' : '' }}>Stok Habis</option>
                </select>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('filter'))
                <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Daftar Barang</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="itemsTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah Stok</th>
                        <th>Lokasi Penyimpanan</th>
                        <th>Status Ketersediaan</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
@forelse($barangs as $index => $item)
<tr>
    <td>{{ $barangs->firstItem() + $index }}</td>
    <td>
        <strong>{{ $item->nama_barang }}</strong>
        @if($item->deskripsi)
        <br><small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
        @endif
    </td>
    <td>
        @if($item->kategori)
        <span class="badge bg-secondary">{{ $item->kategori }}</span>
        @else
        <span class="text-muted">Tidak Ada Kategori</span>
        @endif
    </td>
    <td>
        <span class="badge bg-{{ $item->stok > 5 ? 'success' : ($item->stok > 0 ? 'warning' : 'danger') }}">
            {{ $item->stok }} unit
        </span>
    </td>
    <td>{{ $item->lokasi_penyimpanan }}</td>
    <td>
        @if($item->stok > 0)
        <span class="badge bg-success">Tersedia</span>
        @else
        <span class="badge bg-danger">Habis</span>
        @endif
    </td>
    <td>
        <div class="btn-group" role="group">
            <a href="{{ route('barang.show', $item->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('barang.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Ubah Data">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-sm btn-danger" title="Hapus Barang"
                    onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_barang }}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted py-4">
        <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
        Belum ada data barang yang tersimpan
    </td>
</tr>
@endforelse

                </tbody>
            </table>
        </div>

        @if($barangs->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $barangs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus barang <strong id="itemName"></strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait barang ini.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Barang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(itemId, itemName) {
    document.getElementById('itemName').textContent = itemName;
    document.getElementById('deleteForm').action = `/items/${itemId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

$(document).ready(function() {
    $('#itemsTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "ordering": true,
        "language": {
            "emptyTable": "Belum ada data barang yang tersimpan",
            "zeroRecords": "Tidak ada data yang sesuai dengan pencarian"
        }
    });
});
</script>
@endpush
