@extends('backoffice.layouts.app')

@section('title', 'Daftar Tagihan')
@section('header_title', 'Tagihan (Payment Plans)')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Rencana Pembayaran / Tagihan</h2>
    </div>

    <!-- Table Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="border: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Deskripsi Rencana</th>
                        <th>Nominal</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Diajukan Oleh</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: right; padding-right: 1.5rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentPlans as $plan)
                        <tr>
                            <td style="font-weight: 600;">{{ $plan->category->name ?? '-' }}</td>
                            <td style="color: var(--text-secondary);">{{ Str::limit($plan->description, 50) }}</td>
                            <td style="font-weight: 500;">Rp {{ number_format($plan->amount, 0, ',', '.') }}</td>
                            <td style="color: var(--text-secondary);">{{ $plan->created_at ? $plan->created_at->format('d M Y') : '-' }}</td>
                            <td>{{ $plan->user->name ?? '-' }}</td>
                            <td>
                                @if(strtolower($plan->status) == 'approved')
                                    <span class="badge success">{{ $plan->status }}</span>
                                @elseif(strtolower($plan->status) == 'pending')
                                    <span class="badge warning">{{ $plan->status }}</span>
                                @else
                                    <span class="badge info">{{ $plan->status }}</span>
                                @endif
                            </td>
                            <td style="text-align: right; padding-right: 1.5rem; vertical-align: middle;">
                                <button class="btn btn-secondary btn-icon" onclick="viewPlanDetail('{{ $plan->id }}')" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span style="font-size: 0.8rem; font-weight: 500;">View</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                                Tidak ada rencana pembayaran / tagihan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        {{ $paymentPlans->appends(request()->query())->links('partials.pagination') }}
    </div>

    <!-- View Plan Modal -->
    <div class="modal-overlay" id="viewPlanModal">
        <div class="modal-container" style="max-width: 550px;">
            <div class="modal-header">
                <h3 class="modal-title">Detail Rencana Pembayaran</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body" style="font-size: 0.9rem; line-height: 1.6;">
                <div style="display: grid; grid-template-columns: 130px 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-secondary);">Kategori:</div>
                    <div id="detail_category" style="font-weight: 500;">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Diajukan Oleh:</div>
                    <div id="detail_user">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Tanggal Diajukan:</div>
                    <div id="detail_date">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Estimasi Nominal:</div>
                    <div id="detail_amount" style="font-weight: 700; color: var(--primary);">-</div>

                    <div style="font-weight: 600; color: var(--text-secondary);">Status:</div>
                    <div><span id="detail_status" class="badge info">-</span></div>
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 0.75rem; margin-top: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem;">Deskripsi Rencana:</div>
                    <div id="detail_desc" style="background-color: var(--bg-primary); padding: 0.75rem; border-radius: var(--radius); border: 1px solid var(--border); white-space: pre-wrap;">-</div>
                </div>

                <!-- Realized Transaction details (optional) -->
                <div id="detail_realization_container" style="border-top: 1px solid var(--border); padding-top: 0.75rem; margin-top: 0.75rem; display: none;">
                    <div style="font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem;">Realisasi Transaksi:</div>
                    <div style="background-color: var(--success-light); color: var(--text-primary); padding: 0.75rem; border-radius: var(--radius); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-weight: 600;">No Referensi:</span> <span id="detail_tx_ref"></span>
                        </div>
                        <div>
                            <span class="badge success" id="detail_tx_status">APPROVED</span>
                        </div>
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
    function viewPlanDetail(id) {
        fetch("{{ url('backoffice/payment-plans') }}/" + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('detail_category').innerText = data.category ? data.category.name : '-';
                document.getElementById('detail_user').innerText = data.user ? data.user.name : '-';
                document.getElementById('detail_desc').innerText = data.description || '-';
                document.getElementById('detail_amount').innerText = 'Rp ' + parseFloat(data.amount).toLocaleString('id-ID');
                
                // Set status badge
                const statusBadge = document.getElementById('detail_status');
                statusBadge.innerText = data.status;
                statusBadge.className = 'badge';
                if (data.status.toLowerCase() === 'approved') {
                    statusBadge.classList.add('success');
                } else if (data.status.toLowerCase() === 'pending') {
                    statusBadge.classList.add('warning');
                } else {
                    statusBadge.classList.add('info');
                }

                // Format Date
                if (data.created_at) {
                    const date = new Date(data.created_at);
                    document.getElementById('detail_date').innerText = date.toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    });
                } else {
                    document.getElementById('detail_date').innerText = '-';
                }

                // Show realization transaction
                const txContainer = document.getElementById('detail_realization_container');
                if (data.transaction) {
                    document.getElementById('detail_tx_ref').innerText = data.transaction.reference_number;
                    document.getElementById('detail_tx_status').innerText = data.transaction.status;
                    txContainer.style.display = 'block';
                } else {
                    txContainer.style.display = 'none';
                }

                openModal('viewPlanModal');
            })
            .catch(err => {
                console.error("Gagal mengambil data detail:", err);
                alert("Gagal mengambil data detail tagihan.");
            });
    }
</script>
@endsection
