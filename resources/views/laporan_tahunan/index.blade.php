@extends('layouts.app')

@section('title', 'Laporan Tahunan')

@section('content')
<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Laporan Tahunan</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Laporan Tahunan</span>
            </nav>
        </div>

        <div class="controls-section">
            <form action="{{ route('laporan_tahunan.generate') }}" method="POST" class="d-flex">
                @csrf
                <input type="number"
                       name="tahun"
                       class="form-control"
                       value="{{ date('Y') }}"
                       required
                       style="width: 140px; margin-right: 8px;"
                       placeholder="Tahun">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-plus"></i> Generate
                </button>
            </form>

            <div class="controls-right">
                <span class="search-label">Search:</span>
                <input type="text" id="searchInput" placeholder="Cari tahun..." class="search-input">
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell">No</th>
                        <th class="d-none d-lg-table-cell">ID Laporan</th>
                        <th>Tahun</th>
                        <th class="d-none d-md-table-cell">Jumlah Data</th>
                        <th class="d-none d-md-table-cell">Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporanTahunan as $index => $laporan)
                        <tr data-name="{{ strtolower($laporan->tahun) }}">
                            <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                            <td class="d-none d-lg-table-cell">{{ $laporan->id_laporan_tahunan }}</td>
                            <td>{{ $laporan->tahun }}</td>
                            <td class="d-none d-md-table-cell">
                                {{ optional($laporan->details)->count() ?? 0 }} data
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $laporan->created_at?->format('d M Y') }}
                            </td>
                            <td>
                                <a href="{{ route('laporan_tahunan.show', $laporan->id_laporan_tahunan) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>

                                @if (Route::has('laporan_tahunan.export'))
                                <a href="{{ route('laporan_tahunan.export', $laporan->id_laporan_tahunan) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Belum ada laporan tahunan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</main>

<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row => {
        const name = row.dataset.name || '';
        row.style.display = name.includes(q) ? '' : 'none';
    });
});
</script>
@endsection
