@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Riwayat Peminjaman Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('peminjaman_aset.index') }}">Peminjaman Aset</a>
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

                    <form method="GET" action="{{ route('peminjaman_aset.riwayat') }}" class="filter-form-inline">

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

                        <a href="{{ route('peminjaman_aset.riwayat') }}" class="btn btn-outline">
                            Reset Filter
                        </a>

                        <a href="{{ route('peminjaman_aset.riwayat.excel', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('peminjaman_aset.riwayat.pdf', request()->query()) }}" class="btn btn-danger">
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
                            <th class="d-none d-md-table-cell">Total Item</th>
                            <th>Approval</th>
                            <th class="d-none d-lg-table-cell">Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $i => $p)
                            <tr data-name="{{ strtolower($p->nama_pengaju . ' ' . $p->divisi->nama_divisi) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_peminjaman }}</td>
                                <td>{{ $p->nama_pengaju }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->details->count() }}</td>

                                <td>
                                    <span class="badge badge-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                                    </span>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $p->created_at->format('d M Y') }}
                                </td>

                                <td>
                                    <a href="{{ route('peminjaman_aset.show', $p->id_peminjaman) }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada riwayat peminjaman
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </main>

    <script>
        document.getElementById('searchInput')
            .addEventListener('keyup', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.data-table tbody tr')
                    .forEach(row => {
                        const name = row.getAttribute('data-name');
                        row.style.display = name && name.includes(q) ? '' : 'none';
                    });
            });
    </script>

@endsection
