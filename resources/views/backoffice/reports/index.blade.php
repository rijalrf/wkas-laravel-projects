@extends('backoffice.layouts.app')

@section('title', 'Laporan Keuangan')
@section('header_title', 'Laporan Keuangan')

@section('content')
    <!-- Filter Card -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem;">Filter Laporan</h3>
        <form action="{{ route('backoffice.reports.index') }}" method="GET">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                <!-- Start Date -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>

                <!-- End Date -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="end_date">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>

                <!-- Category -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="category_id">Kategori</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="type">Tipe Transaksi</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="IN" {{ $type == 'IN' ? 'selected' : '' }}>Pemasukan (IN)</option>
                        <option value="OUT" {{ $type == 'OUT' ? 'selected' : '' }}>Pengeluaran (OUT)</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="PENDING" {{ $status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                        <option value="APPROVED" {{ $status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                        <option value="REJECTED" {{ $status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Tampilkan Laporan</span>
                </button>

                @if($reportData->isNotEmpty())
                    <a href="{{ route('backoffice.reports.pdf', request()->all()) }}" class="btn btn-secondary" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Download PDF</span>
                    </a>
                    <a href="{{ route('backoffice.reports.xlsx', request()->all()) }}" class="btn btn-secondary" style="color: var(--accent); border-color: rgba(13, 148, 136, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Download XLSX (CSV)</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Display Card -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="border: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Referensi No</th>
                        <th>Tipe</th>
                        <th>Kategori</th>
                        <th>Oleh</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                        <tr>
                            <td style="color: var(--text-secondary);">{{ $row->date }}</td>
                            <td style="font-weight: 600; font-family: monospace;">{{ $row->reference_number }}</td>
                            <td>
                                <span class="badge {{ $row->type == 'IN' ? 'success' : 'danger' }}">
                                    {{ $row->type }}
                                </span>
                            </td>
                            <td style="font-weight: 500;">{{ $row->category_name }}</td>
                            <td>{{ $row->user_name }}</td>
                            <td style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $row->description }}
                            </td>
                            <td style="font-weight: 600; color: {{ $row->type == 'IN' ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $row->type == 'IN' ? '+' : '-' }} Rp {{ number_format($row->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($row->status === 'APPROVED')
                                    <span class="badge success">{{ $row->status }}</span>
                                @elseif($row->status === 'PENDING')
                                    <span class="badge warning">{{ $row->status }}</span>
                                @else
                                    <span class="badge danger">{{ $row->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 4rem;">
                                @if(request()->anyFilled(['start_date', 'end_date', 'category_id', 'status', 'type']))
                                    Tidak ada data laporan yang cocok dengan kriteria filter.
                                @else
                                    Silakan isi kriteria filter di atas dan klik "Tampilkan Laporan".
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
