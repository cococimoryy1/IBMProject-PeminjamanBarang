@extends('layout.app')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')
@section('page-description', 'Lihat semua riwayat transaksi peminjaman barang')

@section('content')
<!-- Bagian Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('peminjaman.riwayat') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status Peminjaman</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Cari Data</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="Nama barang atau peminjam..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Hapus Filter
                </a>
                <a href="{{ route('history.export.pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                   class="btn btn-success">
                    <i class="fas fa-file-pdf me-2"></i>Unduh PDF
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Kartu Statistik -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total'] ?? 0 }}</h4>
                        <p class="mb-0">Total Transaksi</p>
                    </div>
                    <i class="fas fa-list fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['borrowed'] ?? 0 }}</h4>
                        <p class="mb-0">Sedang Dipinjam</p>
                    </div>
                    <i class="fas fa-hand-holding fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['returned'] ?? 0 }}</h4>
                        <p class="mb-0">Sudah Dikembalikan</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['overdue'] ?? 0 }}</h4>
                        <p class="mb-0">Terlambat Kembali</p>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Transaksi Peminjaman</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="historyTable">
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
                            <br><small class="text-muted">{{ $borrowing->barang->kategori ?? 'Tanpa Kategori' }}</small>
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
                                $endDate = $borrowing->tanggal_kembali ?? now()->timezone('Asia/Jakarta');
                                $duration = $borrowing->tanggal_pinjam ? (int) $borrowing->tanggal_pinjam->diffInDays($endDate) : 0;
                                \Log::info('Duration debug', [
                                    'borrowing_id' => $borrowing->id,
                                    'tanggal_pinjam' => $borrowing->tanggal_pinjam,
                                    'endDate' => $endDate,
                                    'duration' => $duration
                                ]);
                            @endphp
                            {{ $duration }} hari
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info"
                                    onclick="showDetail({{ $borrowing->id }})" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-history fa-3x mb-3 d-block"></i>
                            Belum ada riwayat peminjaman yang tercatat
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Peminjaman Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Konten akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showDetail(borrowingId) {
    // Tampilkan loading
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Memuat detail peminjaman...</p>
        </div>
    `;

    // Tampilkan modal
    new bootstrap.Modal(document.getElementById('detailModal')).show();

    // Muat detail via AJAX
    fetch(`/peminjaman/${borrowingId}/detail`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detailContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Barang</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Nama Barang:</strong></td><td>${data.barang.nama_barang}</td></tr>
                            <tr><td><strong>Kategori:</strong></td><td>${data.barang.kategori || 'Tanpa Kategori'}</td></tr>
                            <tr><td><strong>Lokasi Penyimpanan:</strong></td><td>${data.barang.lokasi_penyimpanan || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Peminjaman</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Nama Peminjam:</strong></td><td>${data.nama_peminjam}</td></tr>
                            <tr><td><strong>Tanggal Pinjam:</strong></td><td>${data.tanggal_pinjam}</td></tr>
                            <tr><td><strong>Tanggal Kembali:</strong></td><td>${data.tanggal_kembali || 'Belum Dikembalikan'}</td></tr>
                            <tr><td><strong>Jumlah:</strong></td><td>${data.jumlah} unit</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge bg-${data.status == 'dipinjam' ? 'warning' : 'success'}">${data.status == 'dipinjam' ? 'Sedang Dipinjam' : 'Sudah Dikembalikan'}</span></td></tr>
                        </table>
                    </div>
                </div>
                ${data.catatan ? `
                <div class="mt-3">
                    <h6>Catatan Peminjaman</h6>
                    <p class="bg-light p-3 rounded">${data.catatan}</p>
                </div>
                ` : ''}
                ${data.return_notes ? `
                <div class="mt-3">
                    <h6>Catatan Pengembalian</h6>
                    <p class="bg-light p-3 rounded">${data.return_notes}</p>
                </div>
                ` : ''}
            `;
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat detail peminjaman. Silakan coba lagi.
                </div>
            `;
        });
}

$(document).ready(function() {
    $('#historyTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "ordering": true,
        "language": {
            "emptyTable": "Belum ada riwayat peminjaman yang tercatat",
            "zeroRecords": "Tidak ada data yang sesuai dengan pencarian"
        }
    });
});
</script>
@endpush
