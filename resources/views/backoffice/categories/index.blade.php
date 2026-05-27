@extends('backoffice.layouts.app')

@section('title', 'Kategori Management')
@section('header_title', 'Kategori Management')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Kategori</h2>
        <button class="btn btn-primary" onclick="openModal('createCategoryModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="border: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Gambar</th>
                        <th>Nama Kategori</th>
                        <th>Dibuat Tanggal</th>
                        <th style="width: 100px; text-align: right; padding-right: 1.5rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="width: 42px; height: 42px; border-radius: var(--radius); overflow: hidden; background-color: var(--bg-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid var(--border);">
                                    @if($category->image_url)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    @endif
                                </div>
                            </td>
                            <td style="font-weight: 600; font-size: 0.95rem;">{{ $category->name }}</td>
                            <td style="color: var(--text-secondary);">{{ $category->created_at ? $category->created_at->format('d M Y, H:i') : '-' }}</td>
                            <td style="text-align: right; padding-right: 1.5rem; vertical-align: middle;">
                                <div class="action-dropdown">
                                    <button class="btn btn-secondary btn-icon action-dropdown-btn" style="border: none; background: none; padding: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                    <div class="action-dropdown-menu">
                                        <button class="action-dropdown-item" onclick="editCategory('{{ $category->id }}', '{{ addslashes($category->name) }}', '{{ addslashes($category->image_url) }}')">Edit</button>
                                        <form action="{{ route('backoffice.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" style="display: block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-dropdown-item" style="color: var(--danger);">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                                Belum ada kategori yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        {{ $categories->appends(request()->query())->links('partials.pagination') }}
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createCategoryModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Kategori</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form action="{{ route('backoffice.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Kategori</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Konsumsi" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="image_url">Image URL</label>
                        <input type="text" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/icon.png" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editCategoryModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Edit Kategori</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="editCategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_name">Nama Kategori</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_image_url">Image URL</label>
                        <input type="text" name="image_url" id="edit_image_url" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function editCategory(id, name, imageUrl) {
        const form = document.getElementById('editCategoryForm');
        form.action = "{{ url('backoffice/categories') }}/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_image_url').value = imageUrl;
        openModal('editCategoryModal');
    }
</script>
@endsection
