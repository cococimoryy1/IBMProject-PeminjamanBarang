@extends('layout.app')

@section('title', 'Pinjam Barang')
@section('page-title', 'Formulir Peminjaman Barang')
@section('page-description', 'Buat transaksi peminjaman barang baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-hand-holding me-2"></i>Formulir Peminjaman</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('peminjaman.store') }}" method="POST" id="borrowingForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="barang_id" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select class="form-select @error('barang_id') is-invalid @enderror"
                                    id="barang_id" name="barang_id" required onchange="updateItemInfo()">
                                <option value="">-- Pilih Barang yang Akan Dipinjam --</option>
                                @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}"
                                        data-stock="{{ $barang->stok }}"
                                        data-location="{{ $barang->lokasi_penyimpanan }}"
                                        {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} (Stok Tersedia: {{ $barang->stok }})
                                </option>
                                @endforeach
                            </select>
                            @error('barang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_peminjam" class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                                   id="nama_peminjam" name="nama_peminjam" value="{{ old('nama_peminjam') }}" required
                                   placeholder="Masukkan nama lengkap peminjam">
                            @error('nama_peminjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_pinjam" class="form-label">Tanggal Peminjaman <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                   id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                            @error('tanggal_pinjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jumlah" class="form-label">Jumlah yang Dipinjam <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                   id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}"
                                   min="1" required onchange="validateQuantity()"
                                   placeholder="Masukkan jumlah barang">
                            @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="stockInfo"></div>
                        </div>
                    </div>

                    <!-- Informasi Barang -->
                    <div class="card bg-light mb-3" id="itemInfoCard" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Barang yang Dipilih</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Stok Tersedia:</strong> <span id="availableStock">-</span> unit
                                </div>
                                <div class="col-md-6">
                                    <strong>Lokasi Penyimpanan:</strong> <span id="itemLocation">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan Peminjaman</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror"
                                  id="catatan" name="catatan" rows="3"
                                  placeholder="Masukkan catatan tambahan untuk peminjaman ini (opsional)...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Proses Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateItemInfo() {
    const select = document.getElementById('barang_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoCard = document.getElementById('itemInfoCard');

    if (selectedOption.value) {
        const stock = selectedOption.dataset.stock;
        const location = selectedOption.dataset.location;

        document.getElementById('availableStock').textContent = stock;
        document.getElementById('itemLocation').textContent = location;
        document.getElementById('jumlah').max = stock;

        infoCard.style.display = 'block';
        validateQuantity();
    } else {
        infoCard.style.display = 'none';
    }
}

function validateQuantity() {
    const select = document.getElementById('barang_id');
    const quantityInput = document.getElementById('jumlah');
    const stockInfo = document.getElementById('stockInfo');
    const submitBtn = document.getElementById('submitBtn');

    if (select.value) {
        const selectedOption = select.options[select.selectedIndex];
        const availableStock = parseInt(selectedOption.dataset.stock);
        const requestedQuantity = parseInt(quantityInput.value);

        if (requestedQuantity > availableStock) {
            stockInfo.innerHTML = '<span class="text-danger">Jumlah yang diminta melebihi stok yang tersedia!</span>';
            submitBtn.disabled = true;
            quantityInput.classList.add('is-invalid');
        } else if (availableStock === 0) {
            stockInfo.innerHTML = '<span class="text-danger">Maaf, barang ini sedang habis!</span>';
            submitBtn.disabled = true;
            quantityInput.classList.add('is-invalid');
        } else {
            stockInfo.innerHTML = `<span class="text-success">Stok tersedia: ${availableStock} unit</span>`;
            submitBtn.disabled = false;
            quantityInput.classList.remove('is-invalid');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateItemInfo();
});
</script>
@endpush
