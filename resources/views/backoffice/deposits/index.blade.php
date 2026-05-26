@extends('backoffice.layouts.app')

@section('title', 'Daftar Deposit')
@section('header_title', 'Deposit (Iuran Anggota)')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Deposit</h2>
        
        <!-- Filter Form -->
        <form action="{{ route('backoffice.deposits.index') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <select name="status" class="form-control" style="width: 160px; padding: 0.375rem 0.75rem;" onchange="this.form.submit()">
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
                        <th>Nama Anggota</th>
                        <th>Bulan Iuran</th>
                        <th>Nominal</th>
                        <th>Tanggal Kirim</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                        <tr>
                            <td style="font-weight: 600; font-family: monospace; font-size: 0.9rem;">
                                {{ $deposit->reference_number }}
                            </td>
                            <td style="font-weight: 500;">{{ $deposit->user->name ?? '-' }}</td>
                            <td style="color: var(--text-secondary);">
                                {{ Carbon\Carbon::parse($deposit->month . '-01')->format('F Y') }}
                            </td>
                            <td style="font-weight: 600; color: var(--accent);">
                                Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ $deposit->created_at ? $deposit->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td>
                                @if($deposit->status === 'APPROVED')
                                    <span class="badge success">{{ $deposit->status }}</span>
                                @elseif($deposit->status === 'PENDING')
                                    <span class="badge warning">{{ $deposit->status }}</span>
                                @else
                                    <span class="badge danger">{{ $deposit->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                                Tidak ada data deposit ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
