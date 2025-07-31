@extends('layout.app')

@section('title', 'Edit Barang')
@section('page-title', 'Edit Barang')
@section('page-description', 'Ubah informasi barang yang sudah ada')

@section('content')
<div class="row">
    <!-- Form Edit Barang -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Formulir Edit Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.update', $barang->id) }}" method="POST" id="editItemForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror"
                                   id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                                   placeholder="Masukkan nama barang">
                            @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="kategori" class="form-label">Kategori Barang</label>
                            <div class="input-group">
                                <select class="form-select @error('kategori') is-invalid @enderror"
                                        id="kategori" name="kategori">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->name }}"
                                            {{ old('kategori', $barang->kategori) == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary"
                                        data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Tambah Kategori Baru">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="form-text">Atau ketik kategori baru di bawah ini</div>
                            <input type="text" class="form-control mt-2" id="new_category"
                                   placeholder="Atau ketik kategori baru..."
                                   onchange="updateCategorySelect()">
                            @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stok" class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeStock(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror text-center"
                                       id="stok" name="stok" value="{{ old('stok', $barang->stok) }}"
                                       min="0" required placeholder="0">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeStock(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Stok sebelumnya: <strong>{{ $barang->getOriginal('stok') ?? $barang->stok }}</strong> unit
                                @if($borrowedCount > 0)
                                <br><span class="text-warning">Sedang dipinjam: {{ $borrowedCount }} unit</span>
                                @endif
                            </div>
                            @error('stok')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lokasi_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
                            <div class="input-group">
                                <select class="form-select @error('lokasi_penyimpanan') is-invalid @enderror"
                                        id="lokasi_select" onchange="updateLocationInput()">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location }}"
                                            {{ old('lokasi_penyimpanan', $barang->lokasi_penyimpanan) == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="toggleCustomLocation()" title="Lokasi Kustom">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2 @error('lokasi_penyimpanan') is-invalid @enderror"
                                   id="lokasi_penyimpanan" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan', $barang->lokasi_penyimpanan) }}"
                                   placeholder="Atau masukkan lokasi baru..." style="display: none;">
                            @error('lokasi_penyimpanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Barang</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Masukkan deskripsi detail tentang barang, kondisi, spesifikasi, dll...">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        <div class="form-text">
                            <span id="charCount">{{ strlen($barang->deskripsi ?? '') }}</span>/500 karakter
                        </div>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Perubahan yang Akan Disimpan -->
                    <div class="card bg-light mb-3" id="changesPreview" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title text-primary">
                                <i class="fas fa-info-circle me-2"></i>Perubahan yang Akan Disimpan
                            </h6>
                            <div id="changesList"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('barang.show', $barang->id) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </a>
                            <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning me-2" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel Informasi -->
    <div class="col-md-4">
        <!-- Informasi Barang Saat Ini -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Saat Ini</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Nama:</strong></td>
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
                        <td><strong>Stok:</strong></td>
                        <td>
                            <span class="badge bg-{{ $barang->stok > 5 ? 'success' : ($barang->stok > 0 ? 'warning' : 'danger') }}">
                                {{ $barang->stok }} unit
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Lokasi:</strong></td>
                        <td>{{ $barang->lokasi_penyimpanan ?? '-' }}</td>
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
        </div>

        <!-- Status Peminjaman -->
        @if($borrowedCount > 0)
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Sedang Dipinjam</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>{{ $borrowedCount }} unit</strong> dari barang ini sedang dipinjam.
                </div>

                @foreach($currentBorrowings as $peminjaman)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <div>
                        <strong>{{ $peminjaman->nama_peminjam }}</strong>
                        <br><small class="text-muted">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge bg-warning">{{ $peminjaman->jumlah }} unit</span>
                </div>
                @endforeach

                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Pastikan stok yang Anda masukkan sudah memperhatikan barang yang sedang dipinjam.
                </div>
            </div>
        </div>
        @endif

        <!-- Riwayat Perubahan -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>Dibuat:</strong><br>{{ $barang->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Terakhir Diubah:</strong><br>{{ $barang->updated_at->format('d/m/Y H:i') }}</p>
                    @if ($barang->updated_at !== $barang->created_at)
                    <p class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Diubah {{ $barang->updated_at->diffForHumans() }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-lightbulb me-1"></i>Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Pastikan nama barang jelas dan mudah dicari.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Gunakan kategori untuk memudahkan pengelompokan.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Update stok secara berkala.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Cantumkan lokasi penyimpanan yang spesifik.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="modalCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category_name" required
                               placeholder="Masukkan nama kategori baru">
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="category_description" rows="2"
                                  placeholder="Deskripsi kategori (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Reset -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Reset Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mereset semua perubahan?</p>
                <p class="text-warning"><small>Semua perubahan yang belum disimpan akan hilang.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="confirmReset()">
                    <i class="fas fa-undo me-2"></i>Ya, Reset Form
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Data original untuk perbandingan
const originalData = {
    nama_barang: "{{ $barang->nama_barang }}",
    kategori: "{{ $barang->kategori }}",
    stok: {{ $barang->stok }},
    lokasi_penyimpanan: "{{ $barang->lokasi_penyimpanan }}",
    deskripsi: "{{ $barang->deskripsi }}"
};

// Update kategori select ketika mengetik kategori baru
function updateCategorySelect() {
    const newCategory = document.getElementById('new_category').value;
    const categorySelect = document.getElementById('kategori');

    if (newCategory) {
        let exists = false;
        for (let option of categorySelect.options) {
            if (option.value.toLowerCase() === newCategory.toLowerCase()) {
                option.selected = true;
                exists = true;
                break;
            }
        }

        if (!exists) {
            const newOption = new Option(newCategory, newCategory, true, true);
            categorySelect.add(newOption);
        }

        document.getElementById('new_category').value = '';
        checkChanges();
    }
}

// Update input lokasi
function updateLocationInput() {
    const locationSelect = document.getElementById('lokasi_select');
    const locationInput = document.getElementById('lokasi_penyimpanan');

    if (locationSelect.value) {
        locationInput.value = locationSelect.value;
    }
    checkChanges();
}

// Toggle custom location input
function toggleCustomLocation() {
    const locationInput = document.getElementById('lokasi_penyimpanan');
    const locationSelect = document.getElementById('lokasi_select');

    if (locationInput.style.display === 'none') {
        locationInput.style.display = 'block';
        locationSelect.style.display = 'none';
        locationInput.focus();
    } else {
        locationInput.style.display = 'none';
        locationSelect.style.display = 'block';
    }
    checkChanges();
}

// Ubah stok dengan tombol +/-
function changeStock(change) {
    const stockInput = document.getElementById('stok');
    let currentValue = parseInt(stockInput.value) || 0;
    let newValue = currentValue + change;

    if (newValue < 0) newValue = 0;

    stockInput.value = newValue;
    checkChanges();
}

// Cek perubahan dan tampilkan preview
function checkChanges() {
    const currentData = {
        nama_barang: document.getElementById('nama_barang').value,
        kategori: document.getElementById('kategori').value,
        stok: parseInt(document.getElementById('stok').value) || 0,
        lokasi_penyimpanan: document.getElementById('lokasi_penyimpanan').value,
        deskripsi: document.getElementById('deskripsi').value
    };

    const changes = [];

    if (currentData.nama_barang !== originalData.nama_barang) {
        changes.push(`<strong>Nama:</strong> "${originalData.nama_barang}" → "${currentData.nama_barang}"`);
    }

    if (currentData.kategori !== originalData.kategori) {
        changes.push(`<strong>Kategori:</strong> "${originalData.kategori || 'Tanpa Kategori'}" → "${currentData.kategori || 'Tanpa Kategori'}"`);
    }

    if (currentData.stok !== originalData.stok) {
        const stockChange = currentData.stok - originalData.stok;
        const changeText = stockChange > 0 ? `+${stockChange}` : stockChange.toString();
        changes.push(`<strong>Stok:</strong> ${originalData.stok} → ${currentData.stok} (${changeText})`);
    }

    if (currentData.lokasi_penyimpanan !== originalData.lokasi_penyimpanan) {
        changes.push(`<strong>Lokasi:</strong> "${originalData.lokasi_penyimpanan || '-'}" → "${currentData.lokasi_penyimpanan || '-'}"`);
    }

    if (currentData.deskripsi !== originalData.deskripsi) {
        changes.push(`<strong>Deskripsi:</strong> Diubah`);
    }

    const changesPreview = document.getElementById('changesPreview');
    const changesList = document.getElementById('changesList');

    if (changes.length > 0) {
        changesList.innerHTML = changes.map(change => `<div class="mb-1">${change}</div>`).join('');
        changesPreview.style.display = 'block';
    } else {
        changesPreview.style.display = 'none';
    }
}

// Reset form
function resetForm() {
    new bootstrap.Modal(document.getElementById('resetModal')).show();
}

function confirmReset() {
    document.getElementById('nama_barang').value = originalData.nama_barang;
    document.getElementById('kategori').value = originalData.kategori;
    document.getElementById('stok').value = originalData.stok;
    document.getElementById('lokasi_penyimpanan').value = originalData.lokasi_penyimpanan;
    document.getElementById('deskripsi').value = originalData.deskripsi;

    document.getElementById('lokasi_penyimpanan').style.display = 'none';
    document.getElementById('lokasi_select').style.display = 'block';
    document.getElementById('lokasi_select').value = originalData.lokasi_penyimpanan;

    checkChanges();
    updateCharCount();

    bootstrap.Modal.getInstance(document.getElementById('resetModal')).hide();
}

// Update karakter count
function updateCharCount() {
    const description = document.getElementById('deskripsi').value;
    const charCount = document.getElementById('charCount');
    charCount.textContent = description.length;

    if (description.length > 500) {
        charCount.style.color = 'red';
    } else if (description.length > 400) {
        charCount.style.color = 'orange';
    } else {
        charCount.style.color = 'green';
    }
}

// Tambah kategori baru
document.getElementById('modalCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const categoryName = document.getElementById('category_name').value;
    const categoryDescription = document.getElementById('category_description').value;

    fetch('{{ route("categories.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: categoryName,
            description: categoryDescription
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const categorySelect = document.getElementById('kategori');
            const newOption = new Option(data.category.name, data.category.name, true, true);
            categorySelect.add(newOption);
            bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
            document.getElementById('modalCategoryForm').reset();
            checkChanges();
        } else {
            alert('Gagal menambah kategori: ' + (data.message || 'Kesalahan tidak diketahui'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menyimpan kategori. Silakan coba lagi.');
    });
});

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.getElementById('lokasi_penyimpanan');
    const locationSelect = document.getElementById('lokasi_select');

    if (locationSelect.value) {
        locationInput.style.display = 'none';
    } else {
        locationInput.style.display = 'block';
        locationSelect.style.display = 'none';
    }

    document.getElementById('nama_barang').addEventListener('input', checkChanges);
    document.getElementById('kategori').addEventListener('change', checkChanges);
    document.getElementById('stok').addEventListener('input', checkChanges);
    document.getElementById('lokasi_penyimpanan').addEventListener('input', checkChanges);
    document.getElementById('deskripsi').addEventListener('input', function() {
        checkChanges();
        updateCharCount();
    });

    updateCharCount();
});

// Validasi formulir sebelum submit
document.getElementById('editItemForm').addEventListener('submit', function(e) {
    const stock = parseInt(document.getElementById('stok').value);
    const borrowedCount = {{ $borrowedCount }};

    if (stock < borrowedCount) {
        e.preventDefault();
        alert(`Stok tidak boleh kurang dari ${borrowedCount} karena ada barang yang sedang dipinjam.`);
    }
});
</script>
@endpush
