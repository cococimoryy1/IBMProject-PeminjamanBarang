@extends('layout.app')

@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori Barang')
@section('page-description', 'Kelola semua kategori barang yang tersedia')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus me-2"></i>Tambah Kategori Baru
        </button>
    </div>
    <div class="col-md-6">
        <form method="GET" action="{{ route('categories.index') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control search-box"
                       placeholder="Cari kategori..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Daftar Kategori</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="categoriesTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Dibuat</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                    <tr>
                        <td>{{ $categories->firstItem() + $index }}</td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->description ? Str::limit($category->description, 50) : '<span class="text-muted">Tidak ada deskripsi</span>' }}</td>
                        <td>{{ $category->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info" title="Lihat Detail"
                                        onclick="showCategoryDetail({{ $category->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" title="Edit Kategori"
                                        onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus Kategori"
                                        onclick="confirmDeleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                            Belum ada kategori yang tersimpan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="Masukkan nama kategori">
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Kategori</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Masukkan deskripsi kategori (opsional)"></textarea>
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

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback" id="edit_name-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Deskripsi Kategori</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Kategori -->
<div class="modal fade" id="categoryDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="categoryDetailContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat detail kategori...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="categoryName"></strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteCategoryForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Kategori</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editCategory(id, name, description) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description || '';
    const form = document.getElementById('editCategoryForm');
    form.action = `/categories/${id}`;
    document.getElementById('edit_name-error').innerText = '';

    form.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
                window.location.reload(); // Muat ulang halaman setelah update
            } else {
                const errors = data.errors || { message: 'Kesalahan tidak diketahui' };
                if (errors.name) {
                    document.getElementById('edit_name').classList.add('is-invalid');
                    document.getElementById('edit_name-error').innerText = errors.name[0];
                } else {
                    alert('Gagal memperbarui kategori: ' + (data.message || 'Kesalahan tidak diketahui'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memperbarui kategori. Silakan coba lagi.');
        });
    };

    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function confirmDeleteCategory(id, name) {
    document.getElementById('categoryName').textContent = name;
    const form = document.getElementById('deleteCategoryForm');
    form.action = `/categories/${id}`;

    form.onsubmit = function(e) {
        e.preventDefault();
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal')).hide();
                window.location.reload(); // Muat ulang halaman setelah hapus
            } else {
                alert('Gagal menghapus kategori: ' + (data.message || 'Kesalahan tidak diketahui'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menghapus kategori. Silakan coba lagi.');
        });
    };

    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}

function showCategoryDetail(categoryId) {
    document.getElementById('categoryDetailContent').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Memuat detail kategori...</p>
        </div>
    `;

    new bootstrap.Modal(document.getElementById('categoryDetailModal')).show();

    fetch(`/categories/${categoryId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('categoryDetailContent').innerHTML = `
            <div class="row">
                <div class="col-12">
                    <h6>Informasi Kategori</h6>
                    <table class="table table-borderless">
                        <tr><td><strong>Nama Kategori:</strong></td><td>${data.name}</td></tr>
                        <tr><td><strong>Deskripsi:</strong></td><td>${data.description || 'Tidak ada deskripsi'}</td></tr>
                        <tr><td><strong>Dibuat Tanggal:</strong></td><td>${data.created_at}</td></tr>
                    </table>
                </div>
            </div>
        `;
    })
    .catch(error => {
        document.getElementById('categoryDetailContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Gagal memuat detail kategori. Silakan coba lagi.
            </div>
        `;
    });
}

$(document).ready(function() {
    $('#categoriesTable').DataTable({
        paging: false, // Nonaktifkan paginasi DataTables, gunakan paginasi Laravel
        searching: true,
        info: false,
        ordering: true,
        language: {
            emptyTable: "Belum ada kategori yang tersimpan",
            zeroRecords: "Tidak ada data yang sesuai dengan pencarian"
        }
    });

    document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('name-error');
        nameInput.classList.remove('is-invalid');
        nameError.innerText = '';

        const formData = new FormData(form);
        fetch('{{ route("categories.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('createCategoryModal')).hide();
                form.reset();
                window.location.reload(); // Muat ulang halaman setelah tambah
            } else {
                const errors = data.errors || { message: 'Kesalahan tidak diketahui' };
                if (errors.name) {
                    nameInput.classList.add('is-invalid');
                    nameError.innerText = errors.name[0];
                } else {
                    alert('Gagal menyimpan kategori: ' + (data.message || 'Kesalahan tidak diketahui'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menyimpan kategori. Silakan coba lagi.');
        });
    });
});
</script>
@endpush
@endsection
