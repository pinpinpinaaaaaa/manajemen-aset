@extends('layouts.app')

@section('title', 'Riwayat Permintaan Kendaraan')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Riwayat Permintaan Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('permintaan-kendaraan.index') }}">Permintaan Kendaraan</a>
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

                    <form method="GET" action="{{ route('permintaan-kendaraan.riwayat') }}" class="filter-form-inline">

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

                        <a href="{{ route('permintaan-kendaraan.riwayat') }}" class="btn btn-outline">
                            Reset
                        </a>

                        <a href="{{ route('permintaan-kendaraan.riwayat.excel', request()->query()) }}"
                            class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('permintaan-kendaraan.riwayat.pdf', request()->query()) }}"
                            class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>

                    </form>

                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari nama / divisi..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>
                            <th>Nama</th>
                            <th class="d-none d-md-table-cell">Divisi</th>
                            <th class="d-none d-lg-table-cell">Tanggal</th>
                            <th class="d-none d-lg-table-cell">Jam</th>
                            <th class="d-none d-lg-table-cell">Tujuan</th>
                            <th class="d-none d-md-table-cell">Jumlah Kendaraan</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $i => $p)
                            @php
                                $detail = $p->details->first();
                            @endphp

                            <tr data-name="{{ strtolower($p->nama . ' ' . ($p->divisi->nama_divisi ?? '')) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_permohonan }}</td>
                                <td>{{ $p->nama }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi ?? '-' }}</td>

                                <td class="d-none d-lg-table-cell">
                                    @if ($detail)
                                        {{ \Carbon\Carbon::parse($detail->tanggal_mulai)->format('d M Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($detail->tanggal_selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @if ($detail)
                                        {{ $detail->jam_mulai }} - {{ $detail->jam_selesai }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="d-none d-lg-table-cell">{{ $detail->tempat_tujuan ?? '-' }}</td>

                                <td class="d-none d-md-table-cell">
                                    {{ $p->details->sum(fn($d) => $d->items->count()) }}
                                </td>

                                <td>
                                    @php
                                        $color = match ($p->status) {
                                            'selesai' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $p->created_at->format('d M Y') }}
                                </td>

                                <td>
                                    <a href="{{ route('permintaan-kendaraan.show', $p->id_permohonan) }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    Belum ada riwayat permintaan kendaraan
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
