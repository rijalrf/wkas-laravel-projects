@extends('backoffice.layouts.app')

@section('title', 'User Management')
@section('header_title', 'User Management')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Pengguna</h2>
        
        <!-- Filter Form -->
        <form action="{{ route('backoffice.users.index') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <select name="role" class="form-control" style="width: 150px; padding: 0.375rem 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Peran</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>ADMIN</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>USER</option>
            </select>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="border: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">Avatar</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Peran (Role)</th>
                        <th>Didaftar Pada</th>
                        <th style="width: 120px; text-align: right; padding-right: 1.5rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">
                                <div class="user-avatar" style="margin: 0 auto; width: 36px; height: 36px;">
                                    @if($user->photo)
                                        <img src="{{ route('backoffice.gdrive.preview', ['path' => $user->photo]) }}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                            </td>
                            <td style="font-weight: 600; font-size: 0.95rem;">{{ $user->name }}</td>
                            <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge success">{{ strtoupper($user->role) }}</span>
                                @else
                                    <span class="badge info">{{ strtoupper($user->role) }}</span>
                                @endif
                            </td>
                            <td style="color: var(--text-secondary);">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                            <td style="text-align: right; padding-right: 1.5rem; vertical-align: middle;">
                                <div class="action-dropdown">
                                    <button class="btn btn-secondary btn-icon action-dropdown-btn" style="border: none; background: none; padding: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                    <div class="action-dropdown-menu">
                                        <button class="action-dropdown-item" onclick="editUser('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')">Edit Pengguna</button>
                                        
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('backoffice.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" style="display: block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-dropdown-item" style="color: var(--danger);">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        {{ $users->appends(request()->query())->links('partials.pagination') }}
    </div>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editUserModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Edit Data Pengguna</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_name">Nama Pengguna</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_email">Alamat Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_role">Peran (Role)</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="user">USER</option>
                            <option value="admin">ADMIN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_password">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="edit_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Minimal 8 karakter.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function editUser(id, name, email, role) {
        const form = document.getElementById('editUserForm');
        form.action = "{{ url('backoffice/users') }}/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_password').value = ''; // Reset password field
        openModal('editUserModal');
    }
</script>
@endsection
