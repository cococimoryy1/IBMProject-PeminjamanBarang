@extends('layout.app')

@section('title', 'Detail Barang')
@section('page-title', 'Detail Barang')
@section('page-description', 'Informasi lengkap tentang barang')

@section('content')
<div class="row">
    <!-- Informasi Utama Barang -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Informasi Barang</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nama Barang:</strong></td>
                                <td>{{ $barang->nama_barang }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori:</strong></td>
                                <td>
                                    @if($barang->kategori)
                                        <span class="badge bg-secondary">{{ $barang->kategori }}</span>
                                    @else
                                        <span class="text-muted">Tanpa Kategori</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah Stok:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $barang->stok > 5 ? 'success' : ($barang->stok > 0 ? 'warning' : 'danger') }} fs-6">
                                        {{ $barang->stok }} unit
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($barang->stok > 0)
                                        <span class="badge bg-success">Tersedia</span>
                                    @else
                                        <span class="badge bg-danger">Habis</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Lokasi Penyimpanan:</strong></td>
                                <td>{{ $barang->lokasi_penyimpanan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Ditambahkan:</strong></td>
                                <td>{{ $barang->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Terakhir Diupdate:</strong></td>
                                <td>{{ $barang->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($barang->deskripsi)
                <div class="mt-3">
                    <h6>Deskripsi Barang</h6>
                    <div class="bg-light p-3 rounded">
                        {{ $barang->deskripsi }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Panel Aksi -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Tindakan</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Barang
                    </a>

                    <button type="button" class="btn btn-danger"
                            onclick="confirmDelete({{ $barang->id }}, '{{ $barang->nama_barang }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Barang
                    </button>

                    <hr>

                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
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
    document.getElementById('deleteForm').action = '{{ route("barang.destroy", ":id") }}'.replace(':id', itemId);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
