@extends('layouts.app')

@section('title', 'Laporan Opname Gudang')

@section('content')
<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Laporan Opname (Rekap Bulanan)</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Opname</span>
            </nav>
        </div>

        <div class="controls-section">
            <div class="controls-left">
                <form method="GET" action="{{ route('gudang.laporan_opname') }}" class="filter-form">

                    <select name="bulan" class="form-select" style="width:auto; display:inline-block;">
                        <option value="">Semua Bulan</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="tahun" class="form-select" style="width:auto; display:inline-block;">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                </form>

                <button class="btn btn-outline" onclick="window.location='{{ route('gudang.laporan_opname') }}'">
                    <i class="fas fa-undo"></i> Reset
                </button>

                <button class="btn btn-outline" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Reload
                </button>
            </div>

            <div class="controls-right">
                <span class="search-label">Search:</span>
                <input type="text" id="searchInput" placeholder="Cari nama barang..." class="search-input">
            </div>
        </div>

        <div class="table-container mt-3">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th>Stok Awal</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Stok Akhir</th>
                    </tr>
                </thead>

                <tbody id="tableData">
                    @forelse ($rekap as $i => $r)
                        <tr data-name="{{ strtolower($r->barang->nama_barang) }}">

                            <td>{{ $i + 1 }}</td>

                            <td>
                                {{ DateTime::createFromFormat('!m', $r->bulan)->format('F') }}
                                {{ $r->tahun }}
                            </td>

                            <td>{{ $r->barang->nama_barang }}</td>

                            <td>
                                <span class="badge {{ $r->barang->jenis == 'atk' ? 'badge-info' : 'badge-warning' }}">
                                    {{ strtoupper($r->barang->jenis) }}
                                </span>
                            </td>

                            <td>{{ $r->stok_awal }}</td>
                            <td>{{ $r->stok_masuk }}</td>
                            <td>{{ $r->stok_keluar }}</td>

                            <td>
                                {{ $r->stok_akhir }}
                                @if ($r->stok_akhir <= $r->barang->limit_stok)
                                    <span class="badge badge-danger">Menipis</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                Belum ada data opname bulanan.
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
    document.querySelectorAll('#tableData tr').forEach(el => {
        const name = el.getAttribute('data-name');
        el.style.display = name && name.includes(q) ? '' : 'none';
    });
});
</script>

@endsection
