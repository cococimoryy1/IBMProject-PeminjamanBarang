@extends('layout.app')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Daftar Peminjaman Barang')
@section('page-description', 'Kelola semua transaksi peminjaman barang')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Peminjaman Baru
        </a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="{{ route('peminjaman.riwayat') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control search-box"
                       placeholder="Cari berdasarkan nama barang atau peminjam..."
                       value="{{ request('search') }}">
                <select name="status" class="form-select" style="max-width: 200px;">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                </select>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-hand-holding me-2"></i>Daftar Peminjaman</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="borrowingsTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Nama Peminjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Lama Pinjam</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $index => $borrowing)
                    <tr>
                        <td>{{ $borrowings->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $borrowing->barang->nama_barang }}</strong>
                            <br><small class="text-muted">{{ $borrowing->barang->lokasi_penyimpanan }}</small>
                        </td>
                        <td>{{ $borrowing->nama_peminjam }}</td>
                        <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>
                            @if($borrowing->tanggal_kembali)
                                {{ $borrowing->tanggal_kembali->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Belum Dikembalikan</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $borrowing->jumlah }} unit</span>
                        </td>
                        <td>
                            @if($borrowing->status == 'dipinjam')
                                @php
                                    $days = $borrowing->tanggal_pinjam->diffInDays(now());
                                    $isOverdue = $days > 7;
                                @endphp
                                <span class="badge bg-{{ $isOverdue ? 'danger' : 'warning' }}">
                                    {{ $isOverdue ? 'Terlambat' : 'Sedang Dipinjam' }}
                                </span>
                            @else
                                <span class="badge bg-success">Sudah Dikembalikan</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $endDate = $borrowing->tanggal_kembali ?? now();
                                $duration = $borrowing->tanggal_pinjam->diffInDays($endDate);
                            @endphp
                            {{ $duration }} hari
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('peminjaman.show', $borrowing->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($borrowing->status == 'dipinjam')
                                <button type="button" class="btn btn-sm btn-success" title="Kembalikan Barang"
                                        onclick="confirmReturn({{ $borrowing->id }}, '{{ $borrowing->barang->nama_barang }}', '{{ $borrowing->nama_peminjam }}', {{ $borrowing->jumlah }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus Peminjaman"
                                        onclick="confirmDelete({{ $borrowing->id }}, '{{ $borrowing->barang->nama_barang }}', '{{ $borrowing->nama_peminjam }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-hand-holding fa-3x mb-3 d-block"></i>
                            Belum ada data peminjaman yang tersimpan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($borrowings->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $borrowings->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Pengembalian -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengembalian Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Konfirmasi pengembalian barang berikut:
                </div>

                <table class="table table-borderless">
                    <tr>
                        <td><strong>Nama Barang:</strong></td>
                        <td id="returnItemName">-</td>
                    </tr>
                    <tr>
                        <td><strong>Nama Peminjam:</strong></td>
                        <td id="returnBorrowerName">-</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Dikembalikan:</strong></td>
                        <td id="returnQuantity">-</td>
                    </tr>
                </table>

                <form id="returnForm" method="POST" action="{{ route('peminjaman.returnItem', ':id') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" id="return_id" name="id">

                    <div class="mb-3">
                        <label for="return_notes" class="form-label">Catatan Pengembalian</label>
                        <textarea class="form-control" id="return_notes" name="return_notes" rows="3"
                                  placeholder="Kondisi barang saat dikembalikan, catatan tambahan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="processReturn()">
                    <i class="fas fa-check me-1"></i>Konfirmasi Pengembalian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data peminjaman ini?</p>
                <div class="bg-light p-3 rounded">
                    <strong>Barang:</strong> <span id="deleteItemName">-</span><br>
                    <strong>Peminjam:</strong> <span id="deleteBorrowerName">-</span>
                </div>
                <p class="text-danger mt-2"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="{{ route('peminjaman.destroy', ':id') }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_id" name="id">
                    <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmReturn(peminjamanId, itemName, borrowerName, quantity) {
    document.getElementById('returnItemName').textContent = itemName;
    document.getElementById('returnBorrowerName').textContent = borrowerName;
    document.getElementById('returnQuantity').textContent = quantity + ' unit';
    document.getElementById('return_id').value = peminjamanId;
    document.getElementById('returnForm').action = '{{ route('peminjaman.returnItem', ':id') }}'.replace(':id', peminjamanId);

    new bootstrap.Modal(document.getElementById('returnModal')).show();
}

function processReturn() {
    document.getElementById('returnForm').submit();
}

function confirmDelete(peminjamanId, itemName, borrowerName) {
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteBorrowerName').textContent = borrowerName;
    document.getElementById('delete_id').value = peminjamanId;
    document.getElementById('deleteForm').action = '{{ route('peminjaman.destroy', ':id') }}'.replace(':id', peminjamanId);

    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

$(document).ready(function() {
    $('#borrowingsTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "ordering": true,
        "language": {
            "emptyTable": "Belum ada data peminjaman yang tersimpan",
            "zeroRecords": "Tidak ada data yang sesuai dengan pencarian"
        }
    });
});
</script>
@endpush
