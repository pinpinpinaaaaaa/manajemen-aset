@extends('layouts.app')

@section('title', 'Riwayat Ekspedisi')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Riwayat Ekspedisi</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('ekspedisi.index') }}">Ekspedisi</a>
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

                    <form method="GET" action="{{ route('ekspedisi.riwayat') }}" class="filter-form-inline">

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

                        <a href="{{ route('ekspedisi.riwayat') }}" class="btn btn-outline">
                            Reset
                        </a>

                        <a href="{{ route('ekspedisi.riwayat.excel', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('ekspedisi.riwayat.pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>

                    </form>

                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari pengirim / penerima..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>
                            <th>Asal</th>
                            <th class="d-none d-lg-table-cell">Tujuan</th>
                            <th class="d-none d-md-table-cell">Detail</th>
                            <th>Status Approval</th>
                            <th>Status Kirim</th>
                            <th class="d-none d-md-table-cell">Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $i => $e)
                            @php
                                $detail = collect([]);

                                foreach ($e->dokumen as $d) {
                                    $detail->push($d->nama_dokumen);
                                }

                                foreach ($e->barang as $b) {
                                    $detail->push($b->nama_barang . ' (' . $b->jumlah . ')');
                                }
                            @endphp

                            <tr data-name="{{ strtolower($e->nama_pengirim . ' ' . $e->nama_penerima) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>

                                <td class="d-none d-lg-table-cell">{{ $e->id_ekspedisi }}</td>

                                <td>
                                    {{ $e->divisi_pengirim->nama_divisi ?? '-' }} <br>
                                    <small>{{ $e->nama_pengirim }}</small>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $e->instansi_penerima ?? '-' }} <br>
                                    <small>{{ $e->nama_penerima }}</small>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    {{ $detail->implode(', ') ?: '-' }}
                                </td>

                                <td>
                                    @php
                                        $acolor = match ($e->decision_status) {
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $acolor }}">
                                        {{ ucfirst(explode('_', $e->decision_status)[0]) }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $statusKirim = optional($e->pengiriman)->status_pengiriman;
                                        $pcolor = match ($statusKirim) {
                                            'belum_dikirim' => 'secondary',
                                            'dikirim' => 'info',
                                            'diterima' => 'warning',
                                            'selesai' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $pcolor }}">
                                        {{ $statusKirim ?? '-' }}
                                    </span>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    {{ $e->created_at->format('d M Y') }}
                                </td>

                                <td>
                                    <a href="{{ route('ekspedisi.show', $e->id_ekspedisi) }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada riwayat ekspedisi
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
