@extends('backoffice.layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
    <!-- Baris 1: Cards -->
    <div class="dashboard-grid-3">
        <!-- Total Saldo -->
        <div class="card">
            <div class="card-accent-border success"></div>
            <span class="card-title">Total Saldo</span>
            <div class="card-value">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</div>
            <span class="card-desc">Akumulasi Kas Aktif (Deposit & Pemasukan - Pengeluaran)</span>
        </div>

        <!-- Total Pengeluaran -->
        <div class="card">
            <div class="card-accent-border danger"></div>
            <span class="card-title">Total Pengeluaran</span>
            <div class="card-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            <span class="card-desc">Total Dana Terealisasi (Approved OUT)</span>
        </div>

        <!-- Total Pending -->
        <div class="card">
            <div class="card-accent-border warning"></div>
            <span class="card-title">Total Pending Transaksi</span>
            <div class="card-value">{{ $totalPendingTransaksi }}</div>
            <span class="card-desc">Transaksi & Deposit Menunggu Persetujuan</span>
        </div>
    </div>

    <!-- Baris 2: Tables -->
    <div class="dashboard-grid-2">
        <!-- Anggota Belum Iuran -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--danger);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <span>Anggota Belum Iuran Bulan Ini ({{ date('F Y') }})</span>
            </h3>
            <div class="table-container" style="max-height: 320px; overflow-y: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggotaBelumIuran as $anggota)
                            <tr>
                                <td style="font-weight: 500;">{{ $anggota['nama'] }}</td>
                                <td style="color: var(--text-secondary);">{{ $anggota['email'] }}</td>
                                <td>Rp {{ number_format($anggota['nominal'], 0, ',', '.') }}</td>
                                <td><span class="badge danger">{{ $anggota['status'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                                    Semua anggota sudah membayar iuran bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tagihan / Payment Plan -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                <span>Tagihan (Payment Priority)</span>
            </h3>
            <div class="table-container" style="max-height: 320px; overflow-y: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan as $plan)
                            <tr>
                                <td style="font-weight: 500;">{{ $plan->category->name ?? '-' }}</td>
                                <td style="color: var(--text-secondary);">{{ Str::limit($plan->description, 35) }}</td>
                                <td>Rp {{ number_format($plan->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if(strtolower($plan->status) == 'approved')
                                        <span class="badge success">{{ $plan->status }}</span>
                                    @elseif(strtolower($plan->status) == 'pending')
                                        <span class="badge warning">{{ $plan->status }}</span>
                                    @else
                                        <span class="badge info">{{ $plan->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                                    Belum ada tagihan/rencana pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Baris 3: Charts -->
    <div class="dashboard-grid-2" style="margin-bottom: 0;">
        <!-- Bar Chart -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.5rem;">Pemasukan & Pengeluaran (6 Bulan Terakhir)</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.5rem;">Pengeluaran Per Kategori (Approved OUT)</h3>
            <div style="height: 300px; position: relative; display: flex; justify-content: center;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Data injection from Laravel
    const barChartData = @json($barChartData);
    const pieChartData = @json($pieChartData);

    // Initialize Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barChartData.map(item => item.label),
            datasets: [
                {
                    label: 'Pemasukan (Rp)',
                    data: barChartData.map(item => item.pemasukan),
                    backgroundColor: 'rgba(16, 185, 129, 0.85)', // Emerald-500
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Pengeluaran (Rp)',
                    data: barChartData.map(item => item.pengeluaran),
                    backgroundColor: 'rgba(239, 68, 68, 0.85)', // Red-500
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: 'Inter', size: 12 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        font: { family: 'Inter', size: 10 }
                    }
                },
                x: {
                    ticks: {
                        font: { family: 'Inter', size: 10 }
                    }
                }
            }
        }
    });

    // Initialize Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    
    if (pieChartData.length === 0) {
        // Show empty placeholder text in canvas parent
        const canvas = document.getElementById('pieChart');
        const parent = canvas.parentElement;
        canvas.style.display = 'none';
        const placeholder = document.createElement('div');
        placeholder.style.display = 'flex';
        placeholder.style.alignItems = 'center';
        placeholder.style.justifyContent = 'center';
        placeholder.style.color = 'var(--text-secondary)';
        placeholder.style.fontSize = '0.9rem';
        placeholder.style.height = '100%';
        placeholder.innerText = 'Belum ada data pengeluaran.';
        parent.appendChild(placeholder);
    } else {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: pieChartData.map(item => item.name),
                datasets: [{
                    data: pieChartData.map(item => item.total),
                    backgroundColor: [
                        '#4f46e5', // indigo
                        '#0d9488', // teal
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // purple
                        '#ec4899', // pink
                        '#3b82f6', // blue
                        '#10b981'  // emerald
                    ],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Inter', size: 11 },
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                return label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
