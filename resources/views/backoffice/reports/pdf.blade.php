<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan WKAS</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; color: #1a252f; }
        .header p { margin: 5px 0 0; color: #7f8c8d; font-size: 12px; }
        .meta-info { margin-bottom: 15px; width: 100%; border-collapse: collapse; }
        .meta-info td { padding: 4px 0; font-size: 11px; }
        .report-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .report-table th { background-color: #f2f4f4; border: 1px solid #bdc3c7; padding: 6px; text-align: left; font-weight: bold; }
        .report-table td { border: 1px solid #ecf0f1; padding: 6px; vertical-align: top; }
        .badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 9px; display: inline-block; }
        .badge-success { background-color: #d4efdf; color: #196f3d; }
        .badge-danger { background-color: #fadbd8; color: #78281f; }
        .badge-warning { background-color: #fcf3cf; color: #7e5109; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #eaeded; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KAS WKAS</h2>
        <p>Laporan Keuangan Mutasi Kas Masuk & Keluar</p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 50%;"><strong>Periode:</strong> {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Hari Ini' }}</td>
            <td style="width: 50%; text-align: right;"><strong>Tanggal Cetak:</strong> {{ date('d M Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Referensi</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Oleh</th>
                <th>Keterangan</th>
                <th class="text-right">Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalIn = 0;
                $totalOut = 0;
            @endphp
            @forelse($reportData as $row)
                @php
                    if ($row->status === 'APPROVED') {
                        if ($row->type === 'IN') $totalIn += $row->amount;
                        else $totalOut += $row->amount;
                    }
                @endphp
                <tr>
                    <td>{{ $row->date }}</td>
                    <td style="font-family: monospace;">{{ $row->reference_number }}</td>
                    <td>{{ $row->type }}</td>
                    <td>{{ $row->category_name }}</td>
                    <td>{{ $row->user_name }}</td>
                    <td>{{ $row->description }}</td>
                    <td class="text-right" style="color: {{ $row->type == 'IN' ? '#27ae60' : '#c0392b' }}">
                        {{ $row->type == 'IN' ? '+' : '-' }} Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($row->status === 'APPROVED')
                            <span class="badge badge-success">APPROVED</span>
                        @elseif($row->status === 'PENDING')
                            <span class="badge badge-warning">PENDING</span>
                        @else
                            <span class="badge badge-danger">REJECTED</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #7f8c8d;">
                        Tidak ada data laporan ditemukan.
                    </td>
                </tr>
            @endforelse

            @if($reportData->isNotEmpty())
                <tr class="total-row">
                    <td colspan="6" class="text-right">TOTAL PEMASUKAN APPROVED (IN):</td>
                    <td class="text-right" style="color: #27ae60;">Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="6" class="text-right">TOTAL PENGELUARAN APPROVED (OUT):</td>
                    <td class="text-right" style="color: #c0392b;">Rp {{ number_format($totalOut, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="6" class="text-right">SALDO AKHIR PERIODE APPROVED:</td>
                    <td class="text-right" style="color: #2980b9;">Rp {{ number_format($totalIn - $totalOut, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
