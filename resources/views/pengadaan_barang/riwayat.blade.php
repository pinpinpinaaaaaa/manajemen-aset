@extends('layouts.app')

@section('title', 'Riwayat Pengadaan Barang & Jasa')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Riwayat Pengadaan Barang & Jasa</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pengadaan-barang.index') }}">Pengadaan Barang & Jasa</a>
                    <span class="separator">/</span>
                    <span class="current">Riwayat</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Selesai" :value="$totalSelesai ?? 0" />
                <x-stat-card label="Ditolak" :value="$totalDitolak ?? 0" />
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <form method="GET" action="{{ route('pengadaan-barang.riwayat') }}" class="filter-form-inline">

                        <div>
                            <label class="form-label">Dari</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="form-control">
                        </div>

                        <div>
                            <label class="form-label">Sampai</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>

                        <button class="btn btn-primary">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>

                        <a href="{{ route('pengadaan-barang.riwayat') }}" class="btn btn-outline">
                            Reset
                        </a>

                        <a href="{{ route('pengadaan-barang.riwayat.excel', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('pengadaan-barang.riwayat.pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>

                    </form>

                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari pengaju / divisi..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>
                            <th>Pengaju</th>
                            <th class="d-none d-md-table-cell">Divisi</th>
                            <th class="d-none d-md-table-cell">Detail</th>
                            <th class="d-none d-md-table-cell">Total Item</th>
                            <th class="d-none d-lg-table-cell">Total Biaya</th>
                            <th class="d-none d-lg-table-cell">Status</th>
                            <th>Approval</th>
                            <th class="d-none d-lg-table-cell">Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($laporan as $i => $p)
                            @php
                                $detail = $p->details
                                    ->map(function ($d) {
                                        return $d->nama_item . ($d->jumlah ? ' (' . $d->jumlah . ')' : '');
                                    })
                                    ->implode(', ');
                            @endphp

                            <tr data-name="{{ strtolower($p->nama_pengaju . ' ' . ($p->divisi->nama_divisi ?? '')) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_pengadaan }}</td>
                                <td>{{ $p->nama_pengaju }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi ?? '-' }}</td>
                                <td class="d-none d-md-table-cell">{{ $detail ?: '-' }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->details->count() }}</td>
                                <td class="d-none d-lg-table-cell">Rp{{ number_format($p->total_biaya, 0, ',', '.') }}</td>

                                <td class="d-none d-lg-table-cell">
                                    @php
                                        $color = match ($p->status) {
                                            'Belum Diproses' => 'secondary',
                                            'Sedang Diproses' => 'warning',
                                            'Tersedia' => 'info',
                                            'Selesai' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ $p->status }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $acolor = match ($p->decision_status) {
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $acolor }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                                    </span>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $p->created_at->format('d M Y') }}
                                </td>

                                <td>
                                    <a href="{{ route('pengadaan-barang.show', $p->id_pengadaan) }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    Belum ada riwayat pengadaan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();

            document.querySelectorAll('.data-table tbody tr')
                .forEach(row => {
                    const name = row.getAttribute('data-name');
                    row.style.display = name && name.includes(q) ? '' : 'none';
                });
        });
    </script>

@endsection
