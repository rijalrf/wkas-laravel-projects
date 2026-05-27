@extends('backoffice.layouts.app')

@section('title', 'Daftar Transaksi')
@section('header_title', 'Transaksi')

@section('styles')
<style>
    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
    .skeleton-loader {
        background: linear-gradient(90deg, var(--bg-secondary) 25%, var(--border) 50%, var(--bg-secondary) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    #detail_tx_photo {
        cursor: zoom-in;
        transition: opacity 0.2s ease-in-out;
    }
    #detail_tx_photo:hover {
        opacity: 0.85;
    }
</style>
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Transaksi Kas</h2>
    </div>
    
    <!-- Search and Filter Form -->
    <form action="{{ route('backoffice.transactions.index') }}" method="GET" style="width: 100%; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
            <!-- Keyword search -->
            <div style="position: relative; flex-grow: 1; min-width: 250px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari kata kunci (No Ref, Keterangan, Nama, Kategori)..." style="padding-left: 2.5rem; height: 42px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--text-secondary);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            
            <!-- Toggle Filter Button -->
            <button type="button" id="toggle-filter-btn" class="btn {{ request()->except(['page', 'search']) ? 'btn-primary' : 'btn-secondary' }}" style="padding: 0.625rem 1rem; height: 42px;" title="Tampilkan Filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>Filter</span>
                @if(request()->except(['page', 'search']))
                    <span style="background-color: var(--primary); color: #fff; font-size: 0.7rem; font-weight: bold; border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; margin-left: 0.25rem;">{{ count(request()->except(['page', 'search'])) }}</span>
                @endif
            </button>
            
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem; height: 42px;">Cari & Filter</button>
            
            @if(request()->anyFilled(['search', 'start_date', 'end_date', 'type', 'status', 'user_id', 'category_id']))
                <a href="{{ route('backoffice.transactions.index') }}" class="btn btn-danger" style="padding: 0.625rem 1.25rem; height: 42px; display: inline-flex; align-items: center; justify-content: center;">Reset</a>
            @endif
        </div>

        <!-- Collapsible Filter Group Panel -->
        <div id="filter-group-panel" style="display: {{ request()->except(['page', 'search']) ? 'block' : 'none' }}; background: var(--bg-secondary); border: 1px solid var(--border); padding: 1.25rem; border-radius: var(--radius-lg); margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <!-- Date Range -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>

                <!-- Type -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>MASUK (IN)</option>
                        <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>KELUAR (OUT)</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                        <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                        <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                    </select>
                </div>

                <!-- Oleh (User) -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Oleh (Pembuat)</label>
                    <select name="user_id" class="form-control">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; margin-top: 1rem; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 1rem;">Terapkan Filter</button>
            </div>
        </div>
    </form>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="border: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No Referensi</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <th>Oleh</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: right; padding-right: 1.5rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td style="font-weight: 600; font-family: monospace; font-size: 0.9rem;">
                                {{ $tx->reference_number }}
                            </td>
                            <td style="font-weight: 500;">
                                {{ $tx->category->name ?? '-' }}
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ Str::limit($tx->description, 45) }}
                            </td>
                            <td style="font-weight: 600; color: {{ $tx->type == 'IN' ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $tx->type == 'IN' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ $tx->created_at ? $tx->created_at->format('d M Y') : '-' }}
                            </td>
                            <td>{{ $tx->user->name ?? '-' }}</td>
                            <td>
                                @if($tx->status === 'APPROVED')
                                    <span class="badge success">{{ $tx->status }}</span>
                                @elseif($tx->status === 'PENDING')
                                    <span class="badge warning">{{ $tx->status }}</span>
                                @else
                                    <span class="badge danger">{{ $tx->status }}</span>
                                @endif
                            </td>
                            <td style="text-align: right; padding-right: 1.5rem; vertical-align: middle;">
                                <button class="btn btn-secondary btn-icon" onclick="viewTxDetail('{{ $tx->id }}')" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span style="font-size: 0.8rem; font-weight: 500;">View</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                                Tidak ada transaksi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        {{ $transactions->appends(request()->query())->links('partials.pagination') }}
    </div>

    <!-- View Transaction Modal -->
    <div class="modal-overlay" id="viewTxModal">
        <div class="modal-container" style="max-width: 550px;">
            <div class="modal-header">
                <h3 class="modal-title">Detail Transaksi</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body" style="font-size: 0.9rem; line-height: 1.6;">
                <div style="display: grid; grid-template-columns: 130px 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-secondary);">No Referensi:</div>
                    <div id="detail_tx_ref" style="font-weight: 600; font-family: monospace;">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Kategori:</div>
                    <div id="detail_tx_category" style="font-weight: 500;">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Tipe:</div>
                    <div><span id="detail_tx_type" class="badge">-</span></div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Oleh:</div>
                    <div id="detail_tx_user">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Tanggal:</div>
                    <div id="detail_tx_date">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Nominal:</div>
                    <div id="detail_tx_amount" style="font-weight: 700; font-size: 1.05rem;">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Status:</div>
                    <div><span id="detail_tx_status" class="badge">-</span></div>
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 0.75rem; margin-top: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem;">Keterangan Transaksi:</div>
                    <div id="detail_tx_desc" style="background-color: var(--bg-primary); padding: 0.75rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 0.75rem;">-</div>
                </div>

                <!-- Google Drive Photo Display -->
                <div style="border-top: 1px solid var(--border); padding-top: 0.75rem; margin-top: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem;">Bukti Transaksi (Google Drive):</div>
                    <div id="photo_display_container" style="width: 100%; height: 260px; background-color: var(--bg-primary); border: 1px dashed var(--border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- Skeleton Loader -->
                        <div id="detail_tx_skeleton" class="skeleton-loader" style="width: 100%; height: 100%; display: none; position: absolute; top: 0; left: 0;"></div>
                        
                        <img id="detail_tx_photo" src="" alt="Bukti Transaksi" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none; position: relative; z-index: 1;">
                        <span id="detail_tx_no_photo" style="color: var(--text-secondary); font-size: 0.85rem; position: relative; z-index: 1;">Tidak ada bukti foto.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Full Image Preview Modal -->
    <div id="fullImageModal" class="modal-overlay" style="display: none; background-color: rgba(0, 0, 0, 0.9); z-index: 9999; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
        <span style="position: absolute; top: 20px; right: 30px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer; user-select: none;" onclick="closeFullImage()">&times;</span>
        <img id="fullImageSrc" src="" alt="Preview Bukti" style="max-width: 90%; max-height: 90%; object-fit: contain; border-radius: var(--radius); box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
    </div>
@endsection

@section('scripts')
<script>
    function viewTxDetail(id) {
        fetch("{{ url('backoffice/transactions') }}/" + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('detail_tx_ref').innerText = data.reference_number;
                document.getElementById('detail_tx_category').innerText = data.category ? data.category.name : '-';
                document.getElementById('detail_tx_user').innerText = data.user ? data.user.name : '-';
                document.getElementById('detail_tx_desc').innerText = data.description || '-';
                
                // Set amount
                const formattedAmount = 'Rp ' + parseFloat(data.amount).toLocaleString('id-ID');
                document.getElementById('detail_tx_amount').innerText = formattedAmount;
                document.getElementById('detail_tx_amount').style.color = data.type === 'IN' ? 'var(--success)' : 'var(--danger)';

                // Set type badge
                const typeBadge = document.getElementById('detail_tx_type');
                typeBadge.innerText = data.type === 'IN' ? 'MASUK (IN)' : 'KELUAR (OUT)';
                typeBadge.className = 'badge ' + (data.type === 'IN' ? 'success' : 'danger');

                // Set status badge
                const statusBadge = document.getElementById('detail_tx_status');
                statusBadge.innerText = data.status;
                statusBadge.className = 'badge';
                if (data.status.toLowerCase() === 'approved') {
                    statusBadge.classList.add('success');
                } else if (data.status.toLowerCase() === 'pending') {
                    statusBadge.classList.add('warning');
                } else {
                    statusBadge.classList.add('danger');
                }

                // Format Date
                if (data.created_at) {
                    const date = new Date(data.created_at);
                    document.getElementById('detail_tx_date').innerText = date.toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    });
                } else {
                    document.getElementById('detail_tx_date').innerText = '-';
                }

                // Render Photo using Google Drive proxy ID
                const img = document.getElementById('detail_tx_photo');
                const noPhoto = document.getElementById('detail_tx_no_photo');
                const skeleton = document.getElementById('detail_tx_skeleton');
                
                if (data.photo) {
                    // Show skeleton and hide image / fallback text
                    skeleton.style.display = 'block';
                    img.style.display = 'none';
                    noPhoto.style.display = 'none';

                    // Set up event listeners first, then assign source to prevent race condition
                    img.onload = function() {
                        skeleton.style.display = 'none';
                        img.style.display = 'block';
                    };

                    img.onerror = function() {
                        skeleton.style.display = 'none';
                        img.style.display = 'none';
                        noPhoto.innerText = 'Gagal memuat bukti foto dari Google Drive.';
                        noPhoto.style.display = 'block';
                    };

                    // Using proxy URL for Google Drive file from gdrive-service
                    img.src = "{{ route('backoffice.gdrive.preview') }}?path=" + encodeURIComponent(data.photo);
                } else {
                    skeleton.style.display = 'none';
                    img.src = '';
                    img.style.display = 'none';
                    noPhoto.innerText = 'Tidak ada bukti foto.';
                    noPhoto.style.display = 'block';
                }

                openModal('viewTxModal');
            })
            .catch(err => {
                console.error("Gagal mengambil detail transaksi:", err);
                alert("Gagal mengambil data detail transaksi.");
            });
    }

    // Full image preview lightbox functions
    function openFullImage(src) {
        document.getElementById('fullImageSrc').src = src;
        document.getElementById('fullImageModal').style.display = 'flex';
    }

    function closeFullImage() {
        document.getElementById('fullImageModal').style.display = 'none';
    }

    // Attach click listener to detail image
    document.getElementById('detail_tx_photo').addEventListener('click', function() {
        openFullImage(this.src);
    });

    // Close full image overlay when clicking outside the image
    document.getElementById('fullImageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFullImage();
        }
    });

    // Toggle filter group panel
    document.getElementById('toggle-filter-btn').addEventListener('click', function() {
        const panel = document.getElementById('filter-group-panel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
        } else {
            panel.style.display = 'none';
            
            // Revert back to secondary if no active group filters are in URL
            const urlParams = new URLSearchParams(window.location.search);
            let hasFilters = false;
            for (const [key, value] of urlParams.entries()) {
                if (key !== 'page' && key !== 'search' && value !== '') {
                    hasFilters = true;
                    break;
                }
            }
            if (!hasFilters) {
                this.classList.remove('btn-primary');
                this.classList.add('btn-secondary');
            }
        }
    });
</script>
@endsection
