@extends('backoffice.layouts.app')

@section('title', 'Daftar Deposit')
@section('header_title', 'Deposit (Iuran Anggota)')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Daftar Deposit</h2>
    </div>

    <!-- Search and Filter Form -->
    <form action="{{ route('backoffice.deposits.index') }}" method="GET" style="width: 100%; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
            <!-- Keyword search -->
            <div style="position: relative; flex-grow: 1; min-width: 250px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari kata kunci (No Ref, Keterangan, Nama Anggota)..." style="padding-left: 2.5rem; height: 42px;">
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
            
            @if(request()->anyFilled(['search', 'month', 'year', 'user_id', 'status']))
                <a href="{{ route('backoffice.deposits.index') }}" class="btn btn-danger" style="padding: 0.625rem 1.25rem; height: 42px; display: inline-flex; align-items: center; justify-content: center;">Reset</a>
            @endif
        </div>

        <!-- Collapsible Filter Group Panel -->
        <div id="filter-group-panel" style="display: {{ request()->except(['page', 'search']) ? 'block' : 'none' }}; background: var(--bg-secondary); border: 1px solid var(--border); padding: 1.25rem; border-radius: var(--radius-lg); margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <!-- Month -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Bulan Iuran</label>
                    <select name="month" class="form-control">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $monthName = Carbon\Carbon::create(null, $m, 1)->format('F');
                            @endphp
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Year -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tahun Iuran</label>
                    <select name="year" class="form-control">
                        <option value="">Semua Tahun</option>
                        @php
                            $currentYear = date('Y');
                        @endphp
                        @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Oleh (User) -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Anggota (Oleh)</label>
                    <select name="user_id" class="form-control">
                        <option value="">Semua Anggota</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
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
        <!-- Pagination Links -->
        {{ $deposits->appends(request()->query())->links('partials.pagination') }}
    </div>
@endsection

@section('scripts')
<script>
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
