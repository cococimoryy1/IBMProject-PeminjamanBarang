@extends('layout.app')

@section('title', 'Detail Barang')
@section('page-title', 'Detail Barang')
@section('page-description', 'Lihat detail informasi barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Detail Barang</h5>
            </div>
            <div class="card-body">
                <h5>{{ $barang->nama_barang }}</h5>
                <p><strong>Kategori:</strong> {{ $barang->kategori ?? 'Tidak Ada Kategori' }}</p>
                <p><strong>Stok:</strong> {{ $barang->stok }} unit</p>
                <p><strong>Lokasi Penyimpanan:</strong> {{ $barang->lokasi_penyimpanan ?? '-' }}</p>
                <p><strong>Deskripsi:</strong> {{ $barang->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                <p><strong>Status Ketersediaan:</strong>
                    @if($barang->stok > 0)
                        <span class="badge bg-success">Tersedia</span>
                    @else
                        <span class="badge bg-danger">Habis</span>
                    @endif
                </p>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                    <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Ubah Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
