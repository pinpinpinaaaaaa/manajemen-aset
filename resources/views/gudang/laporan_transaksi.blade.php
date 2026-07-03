@extends('layouts.app')

@section('title', 'Laporan Transaksi Gudang')

@section('content')
<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Laporan Transaksi Gudang</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Laporan Transaksi</span>
            </nav>
        </div>

        <div class="controls-section mb-3">

            <div class="controls-left">
                <form action="{{ route('gudang.laporan_transaksi') }}" method="GET" class="d-flex gap-2">
                    <a href="{{ route('gudang.transaksi_form') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Transaksi
                    </a>

                    <input type="date" name="dari" class="form-control"
                        value="{{ request('dari') }}" style="max-width:160px">

                    <input type="date" name="sampai" class="form-control"
                        value="{{ request('sampai') }}" style="max-width:160px">

                    <button class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>

                <button class="btn btn-outline ms-3" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Reload
                </button>
            </div>

            <div class="controls-right">
                <span class="search-label">Search:</span>
                <input type="text" id="searchInput" placeholder="Cari nama barang..."
                    class="search-input">
            </div>

        </div>

        <div class="table-container mt-3">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>ID Transaksi</th>
                        <th>Jenis Transaksi</th>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody id="tableData">
                    @forelse ($transaksi as $trx)
                        @php
                            $detailCount = $trx->details->count();
                        @endphp

                        @foreach ($trx->details as $i => $detail)
                            <tr data-name="{{ strtolower($detail->barang->nama_barang) }}">

                                {{-- NO --}}
                                @if ($i === 0)
                                    <td rowspan="{{ $detailCount }}">{{ $loop->parent->iteration }}</td>
                                    <td rowspan="{{ $detailCount }}">{{ $trx->tanggal }}</td>
                                    <td rowspan="{{ $detailCount }}">{{ $trx->id_transaksi }}</td>
                                @endif

                                {{-- JENIS --}}
                                @if ($i === 0)
                                    <td rowspan="{{ $detailCount }}">
                                        @if ($trx->jenis_transaksi == 'masuk')
                                            <span class="badge badge-success">Masuk</span>
                                        @elseif ($trx->jenis_transaksi == 'keluar')
                                            <span class="badge badge-danger">Keluar</span>
                                        @else
                                            <span class="badge badge-warning">Penyesuaian</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- DETAIL BARANG --}}
                                <td>{{ $detail->id_barang }}</td>
                                <td>{{ $detail->barang->nama_barang }}</td>

                                {{-- JUMLAH --}}
                                <td>{{ $detail->jumlah }}</td>

                                {{-- KETERANGAN --}}
                                @if ($i === 0)
                                    <td rowspan="{{ $detailCount }}">{{ $trx->referensi ?? '-' }}</td>
                                @endif

                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                Tidak ada transaksi ditemukan.
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
