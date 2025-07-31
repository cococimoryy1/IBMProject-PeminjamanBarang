@extends('layout.app')

@section('title', 'Pengembalian Barang')
@section('page-title', 'Pengembalian Barang')
@section('page-description', 'Kelola pengembalian barang yang sedang dipinjam')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-undo me-2"></i>Daftar Barang yang Sedang Dipinjam</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="returnsTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Nama Peminjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jumlah Dipinjam</th>
                        <th>Lama Peminjaman</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $index => $borrowing)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $borrowing->barang->nama_barang ?? 'Tidak diketahui' }}</strong>
                            <br><small class="text-muted">Lokasi: {{ $borrowing->barang->lokasi_penyimpanan ?? 'Tidak diketahui' }}</small>
                        </td>
                        <td>{{ $borrowing->nama_peminjam ?? 'Tidak diketahui' }}</td>
                        <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-info">{{ $borrowing->jumlah }} unit</span>
                        </td>
                        <td>
                            @php
                                $days = $borrowing->tanggal_pinjam ? (int) $borrowing->tanggal_pinjam->diffInDays(now()->timezone('Asia/Jakarta')) : 0;
                                \Log::info('Duration debug', [
                                    'borrowing_id' => $borrowing->id,
                                    'tanggal_pinjam' => $borrowing->tanggal_pinjam,
                                    'now' => now()->timezone('Asia/Jakarta'),
                                    'days' => $days
                                ]);
                            @endphp
                            <span class="badge bg-{{ $days > 7 ? 'danger' : ($days > 3 ? 'warning' : 'success') }}">
                                {{ $days }} hari
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm"
                                    onclick="confirmReturn({{ $borrowing->id }}, '{{ $borrowing->barang->nama_barang ?? 'Tidak diketahui' }}', '{{ $borrowing->nama_peminjam ?? 'Tidak diketahui' }}', {{ $borrowing->jumlah }})">
                                <i class="fas fa-check me-1"></i>Kembalikan
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                            Semua barang sudah dikembalikan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

                <form id="returnForm" method="POST">
                    @csrf
                    @method('PATCH')

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
@endsection

@push('scripts')
<script>
function confirmReturn(borrowingId, itemName, borrowerName, quantity) {
    document.getElementById('returnItemName').textContent = itemName;
    document.getElementById('returnBorrowerName').textContent = borrowerName;
    document.getElementById('returnQuantity').textContent = quantity + ' unit';
    document.getElementById('returnForm').action = '{{ route('pengembalian.returnItem', ['id' => ':id']) }}'.replace(':id', borrowingId);

    new bootstrap.Modal(document.getElementById('returnModal')).show();
}

function processReturn() {
    document.getElementById('returnForm').submit();
}

$(document).ready(function() {
    $('#returnsTable').DataTable({
        "language": {
            "emptyTable": "Semua barang sudah dikembalikan",
            "zeroRecords": "Tidak ada data yang sesuai dengan pencarian",
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>
@endpush
