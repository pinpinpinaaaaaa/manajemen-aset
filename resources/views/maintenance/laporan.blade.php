@extends('layouts.app')

@section('title', 'Laporan Pengajuan Pemeliharaan Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Laporan Pengajuan Pemeliharaan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('maintenance.index') }}" class="breadcrumb-link">Pemeliharaan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Laporan Pengajuan Pemeliharaan Aset</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Total Biaya" :value="'Rp' . number_format($totalBiaya ?? 0, 0, ',', '.')" />
                <x-stat-card label="Selesai" :value="$totalSelesai ?? 0" />
                <x-stat-card label="Ditolak" :value="$totalDitolak ?? 0" />
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <form method="GET" action="{{ route('maintenance.laporan') }}" class="filter-form-inline">
                        <div>
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                class="form-control">
                        </div>
                        <div>
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                        <a href="{{ route('maintenance.laporan') }}" class="btn btn-outline">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                        <a href="{{ route('maintenance.exportExcel', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('maintenance.exportPdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </form>
                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari aset / gedung / ruangan..."
                        class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama Aset</th>
                            <th>Gedung</th>
                            <th>Ruangan</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Durasi (jam)</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($maintenance as $index => $m)
                            @php
                                $asetList = $m->details->pluck('aset.nama_aset')->filter()->implode(', ');

                                $gedungList = $m->details
                                    ->map(fn($d) => $d->aset?->ruangan?->gedung?->nama_gedung)
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');

                                $ruanganList = $m->details
                                    ->map(fn($d) => $d->aset?->ruangan?->nama_ruangan)
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');

                                $tanggalMulai = $m->details->pluck('tanggal_mulai')->filter()->sort()->first();

                                $tanggalSelesai = $m->details->pluck('tanggal_selesai')->filter()->sortDesc()->first();

                                $totalDurasi = $m->details->sum('durasi_jam');

                                $totalBiayaItem = $m->details->sum('biaya');

                                if ($m->decision_status == 'ditolak') {
                                    $status = 'Ditolak';
                                } elseif (
                                    $m->details->count() > 0 &&
                                    $m->details->every(fn($d) => $d->status == 'Selesai')
                                ) {
                                    $status = 'Selesai';
                                } elseif ($m->details->contains(fn($d) => $d->status == 'Sedang Diperbaiki')) {
                                    $status = 'Sedang Diperbaiki';
                                } else {
                                    $status = 'Perlu Perbaikan';
                                }
                            @endphp

                            <tr data-search="{{ strtolower($asetList . ' ' . $gedungList . ' ' . $ruanganList) }}">

                                <td>{{ $index + 1 }}</td>

                                <td>{{ $m->id_maintenance }}</td>

                                <td>{{ $asetList ?: '-' }}</td>

                                <td>{{ $gedungList ?: '-' }}</td>

                                <td>{{ $ruanganList ?: '-' }}</td>

                                <td>
                                    {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('d M Y H:i') : '-' }}
                                </td>

                                <td>
                                    {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y H:i') : '-' }}
                                </td>

                                <td>
                                    {{ $totalDurasi ?: '-' }}
                                </td>

                                <td>
                                    Rp{{ number_format($totalBiayaItem, 0, ',', '.') }}
                                </td>

                                <td>

                                    @php
                                        $badgeStatus = match ($status) {
                                            'Selesai' => 'badge badge-success',
                                            'Sedang Diperbaiki' => 'badge badge-warning',
                                            'Perlu Perbaikan' => 'badge badge-danger',
                                            'Ditolak' => 'badge badge-danger',
                                            default => 'badge badge-secondary',
                                        };
                                    @endphp

                                    <span class="{{ $badgeStatus }}">
                                        {{ $status }}
                                    </span>

                                </td>

                                <td>

                                    @php
                                        $ap = strtolower($m->decision_status);

                                        $badgeApproval = match ($ap) {
                                            'disetujui' => 'badge badge-success',
                                            'ditolak' => 'badge badge-danger',
                                            default => 'badge badge-secondary',
                                        };
                                    @endphp

                                    <span class="{{ $badgeApproval }}">
                                        {{ ucfirst(str_replace('_', ' ', $m->decision_status)) }}
                                    </span>

                                </td>

                                <td>
                                    {{ $m->catatan ?: '-' }}
                                </td>

                                <td>
                                    <a href="{{ route('maintenance.show', $m->id_maintenance) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted">
                                    Belum ada data riwayat maintenance.
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
            document.querySelectorAll('.data-table tbody tr').forEach(el => {
                const name = el.getAttribute('data-name');
                el.style.display = name && name.includes(q) ? '' : 'none';
            });
        });
    </script>
@endsection
