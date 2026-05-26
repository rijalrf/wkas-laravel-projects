@extends('backoffice.layouts.app')

@section('title', 'Daftar Transaksi')
@section('header_title', 'Transaksi')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Transaksi Kas</h2>
        
        <!-- Filter Form -->
        <form action="{{ route('backoffice.transactions.index') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <select name="type" class="form-control" style="width: 140px; padding: 0.375rem 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>MASUK (IN)</option>
                <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>KELUAR (OUT)</option>
            </select>
            <select name="status" class="form-control" style="width: 150px; padding: 0.375rem 0.75rem;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
            </select>
        </form>
    </div>

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
                    <div id="photo_display_container" style="width: 100%; height: 260px; background-color: var(--bg-primary); border: 1px dashed var(--border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="detail_tx_photo" src="" alt="Bukti Transaksi" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none;">
                        <span id="detail_tx_no_photo" style="color: var(--text-secondary); font-size: 0.85rem;">Tidak ada bukti foto.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal">Tutup</button>
            </div>
        </div>
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
                
                if (data.photo) {
                    // Using direct proxy URL for Google Drive file
                    img.src = 'https://lh3.googleusercontent.com/d/' + data.photo;
                    img.style.display = 'block';
                    noPhoto.style.display = 'none';
                } else {
                    img.src = '';
                    img.style.display = 'none';
                    noPhoto.style.display = 'block';
                }

                openModal('viewTxModal');
            })
            .catch(err => {
                console.error("Gagal mengambil detail transaksi:", err);
                alert("Gagal mengambil data detail transaksi.");
            });
    }
</script>
@endsection
