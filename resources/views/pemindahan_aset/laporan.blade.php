@extends('layouts.app')

@section('title', 'Laporan Pemindahan Aset')

@section('content')

    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Laporan Pemindahan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pemindahan_aset.index') }}" class="breadcrumb-link">Pemindahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Laporan Pemindahan Aset</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Total Pemindahan" :value="$total" />
                <x-stat-card label="Disetujui" :value="$disetujui" />
                <x-stat-card label="Ditolak" :value="$ditolak" />
                <x-stat-card label="Belum Dipindahkan" :value="$belum" />
            </div>

            <div class="controls-section">

                <div class="controls-left">

                    <form method="GET" action="{{ route('pemindahan_aset.laporan') }}" class="filter-form-inline">

                        <div>
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="form-control">
                        </div>

                        <div>
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>

                        <a href="{{ route('pemindahan_aset.laporan') }}"class="btn btn-outline">
                            Reset Filter
                        </a>

                        <a href="{{ route('pemindahan_aset.export.excel', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <a href="{{ route('pemindahan_aset.export.pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>

                    </form>
                </div>


                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari aset / ruangan..." class="search-input">
                </div>

            </div>


            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Aset</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th>Tanggal</th>
                            <th>Approval</th>
                            <th>Status</th>
                            <th>Alasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pemindahan as $i => $p)

                            @php
                                $totalAset = $p->details->count();

                                $sudahDipindah = $p->details->where('status', 'Sudah dipindahkan')->count();

                                $statusPemindahan =
                                    $sudahDipindah == $totalAset ? 'Sudah dipindahkan' : 'Belum dipindahkan';

                                $searchText = strtolower(
                                    $p->id_pemindahan .
                                        ' ' .
                                        $p->alasan .
                                        ' ' .
                                        $p->details->pluck('aset.nama_aset')->implode(' ') .
                                        ' ' .
                                        $p->details->pluck('ruanganAsal.nama_ruangan')->implode(' ') .
                                        ' ' .
                                        $p->details->pluck('ruanganTujuan.nama_ruangan')->implode(' '),
                                );
                            @endphp

                            <tr data-search="{{ $searchText }}">

                                <td>{{ $i + 1 }}</td>

                                <td>
                                    {{ $p->id_pemindahan }}
                                </td>

                                <td>
                                    {{ $totalAset }} Aset
                                </td>

                                <td>
                                    @foreach ($p->details->take(3) as $detail)
                                        {{ $detail->gedungAsal->nama_gedung ?? '-' }}
                                        -
                                        {{ $detail->ruanganAsal->nama_ruangan ?? '-' }}
                                        <br>
                                    @endforeach

                                    @if ($totalAset > 3)
                                        <small class="text-muted">
                                            +{{ $totalAset - 3 }} lokasi lainnya
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    @foreach ($p->details->take(3) as $detail)
                                        {{ $detail->gedungTujuan->nama_gedung ?? '-' }}
                                        -
                                        {{ $detail->ruanganTujuan->nama_ruangan ?? '-' }}
                                        <br>
                                    @endforeach

                                    @if ($totalAset > 3)
                                        <small class="text-muted">
                                            +{{ $totalAset - 3 }} lokasi lainnya
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $p->created_at->format('d M Y') }}
                                </td>

                                <td>

                                    @php
                                        $badge = match ($p->decision_status) {
                                            'disetujui' => 'badge badge-success',
                                            'ditolak' => 'badge badge-danger',
                                            default => 'badge badge-warning',
                                        };
                                    @endphp

                                    <span class="{{ $badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                                    </span>

                                </td>

                                <td>

                                    <span
                                        class="badge {{ $statusPemindahan == 'Sudah dipindahkan' ? 'badge-success' : 'badge-secondary' }}">

                                        {{ $statusPemindahan }}

                                    </span>

                                </td>

                                <td>
                                    {{ Str::limit($p->alasan, 50) }}
                                </td>

                                <td>

                                    <a href="{{ route('pemindahan_aset.show', $p->id_pemindahan) }}"
                                        class="btn btn-sm btn-primary">

                                        Detail

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Belum ada data pemindahan.
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
                        const text = row.dataset.search || '';
                        row.style.display =
                            text.includes(q) ? '' : 'none';
                    });
            });
    </script>


@endsection
