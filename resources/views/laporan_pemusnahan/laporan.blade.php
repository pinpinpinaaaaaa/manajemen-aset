@extends('layouts.app')

@section('title', 'Laporan Pemusnahan')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Laporan Pemusnahan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('laporan_pemusnahan.index') }}" class="breadcrumb-link">Pemusnahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Laporan Pemusnahan Aset</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Biaya Keluar" :value="'Rp' . number_format($totalKeluar ?? 0, 0, ',', '.')" />
                <x-stat-card label="Nilai Masuk" :value="'Rp' . number_format($totalMasuk ?? 0, 0, ',', '.')" />
                <x-stat-card label="Selesai" :value="$totalSelesai ?? 0" />
                <x-stat-card label="Ditolak" :value="$totalDitolak ?? 0" />
            </div>


            <div class="controls-section">
                <div class="controls-left">

                    <form method="GET" action="{{ route('laporan_pemusnahan.laporan') }}" class="filter-form-inline">

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

                        <a href="{{ route('laporan_pemusnahan.laporan') }}"class="btn btn-outline">
                            Reset Filter
                        </a>

                        <a href="{{ route('laporan_pemusnahan.exportExcel', request()->query()) }}"
                            class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('laporan_pemusnahan.exportPdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>

                    </form>
                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari aset..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Aset</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Biaya Keluar</th>
                            <th>Nilai Masuk</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($laporan as $i => $l)
                            <tr data-name="{{ strtolower($l->aset->nama_aset ?? '') }}">

                                <td>{{ $i + 1 }}</td>

                                <td>{{ $l->id_pemusnahan }}</td>

                                <td>{{ $l->aset->nama_aset ?? '-' }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($l->tanggal_pemusnahan)->format('d M Y') }}
                                </td>

                                <td>{{ ucfirst($l->metode) }}</td>

                                <td>
                                    Rp{{ number_format($l->biaya_keluar, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp{{ number_format($l->nilai_masuk, 0, ',', '.') }}
                                </td>

                                <td>
                                    @php
                                        $status = strtolower($l->status ?? '');
                                        $badge = match ($status) {
                                            'selesai' => 'badge badge-success',
                                            'sedang dimusnahkan' => 'badge badge-warning',
                                            default => 'badge badge-secondary',
                                        };
                                    @endphp
                                    <span class="{{ $badge }}">
                                        {{ ucfirst($l->status ?? '-') }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $ap = strtolower($l->decision_status ?? '');
                                        $b = match ($ap) {
                                            'disetujui' => 'badge badge-success',
                                            'ditolak' => 'badge badge-danger',
                                            default => 'badge badge-secondary',
                                        };
                                    @endphp
                                    <span class="{{ $b }}">
                                        {{ ucfirst($l->decision_status ?? '-') }}
                                    </span>
                                </td>

                                <td>{{ $l->catatan ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('laporan_pemusnahan.show', $l->id_pemusnahan) }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    Belum ada data laporan pemusnahan
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
                    row.style.display =
                        name && name.includes(q) ? '' : 'none';
                });
        });
    </script>
@endsection
