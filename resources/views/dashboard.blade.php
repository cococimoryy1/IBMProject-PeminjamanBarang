@extends('layout.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan sistem peminjaman barang')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-md-3 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">{{ $totalItems ?? 0 }}</h3>
                    <p class="mb-0">Total Barang</p>
                </div>
                <i class="fas fa-boxes fa-2x opacity-75"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">{{ $availableItems ?? 0 }}</h3>
                    <p class="mb-0">Barang Tersedia</p>
                </div>
                <i class="fas fa-check-circle fa-2x opacity-75"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">{{ $borrowedItems ?? 0 }}</h3>
                    <p class="mb-0">Sedang Dipinjam</p>
                </div>
                <i class="fas fa-hand-holding fa-2x opacity-75"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">{{ $lowStockItems ?? 0 }}</h3>
                    <p class="mb-0">Stok Menipis</p>
                </div>
                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Borrowings -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Peminjaman Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Peminjam</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBorrowings ?? [] as $borrowing)
                            <tr>
                                <td>{{ $borrowing['item']->nama_barang ?? 'N/A' }}</td>
                                <td>{{ $borrowing['borrower_name'] }}</td>
                                <td>{{ $borrowing['borrow_date']->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $borrowing['status'] == 'dipinjam' ? 'warning' : 'success' }}">
                                        {{ $borrowing['status'] == 'dipinjam' ? 'Dipinjam' : 'Dikembalikan' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data peminjaman</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Stok Menipis</h5>
            </div>
            <div class="card-body">
                @forelse($lowStockItemsList ?? [] as $item)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                    <div>
                        <strong>{{ $item->name }}</strong>
                        <br><small class="text-muted">{{ $item->location }}</small>
                    </div>
                    <span class="badge bg-danger">{{ $item->stok }}</span>
                </div>
                @empty
                <p class="text-muted text-center">Semua barang stoknya aman</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('barang.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Tambah Barang
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('peminjaman.create') }}" class="btn btn-success w-100">
                            <i class="fas fa-hand-holding me-2"></i>Pinjam Barang
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('barang.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-undo me-2"></i>Kelola Barang
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-info w-100">
                            <i class="fas fa-history me-2"></i>Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
