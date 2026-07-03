@extends('layouts.app')

@section('title', 'Transaksi Barang Gudang')

@section('content')
    <style>
        @media (max-width: 768px) {
            .gudang-filter-form {
                flex-wrap: wrap !important;
                gap: 0.5rem !important;
            }
            .gudang-filter-form input[type="date"] {
                flex: 1 1 calc(50% - 0.25rem);
                max-width: none !important;
            }
            .gudang-filter-form > button {
                flex: 1 1 100%;
            }
            .gudang-filter-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                width: 100%;
            }
            .gudang-filter-actions .btn {
                flex: 1 1 calc(50% - 0.25rem);
                justify-content: center;
            }
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Transaksi Barang Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Transaksi Barang Gudang</span>
                </nav>
            </div>

            <div class="controls-section mb-3">

                <div class="controls-left">
                    <form action="{{ route('gudang.transaksi.index') }}" method="GET" class="d-flex gap-2 gudang-filter-form">
                        <input type="date" name="dari" class="form-control" value="{{ request('dari') }}"
                            style="max-width:160px">

                        <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}"
                            style="max-width:160px">

                        <button class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>

                    <div class="gudang-filter-actions">
                        <a href="{{ route('gudang.transaksi.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Tambah Transaksi
                        </a>

                        <button class="btn btn-outline" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Reload
                        </button>
                    </div>
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
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">Tanggal</th>
                            <th class="d-none d-lg-table-cell">ID Transaksi</th>
                            <th>Jenis Transaksi</th>
                            <th class="d-none d-lg-table-cell">ID Barang</th>
                            <th>Nama Barang</th>
                            <th class="d-none d-md-table-cell">Jumlah</th>
                            <th class="d-none d-lg-table-cell">Total Biaya</th>
                            <th>Status</th>
                            <th class="d-none d-md-table-cell">Keterangan</th>
                            <th>Aksi</th>
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
                                        <td class="d-none d-md-table-cell" rowspan="{{ $detailCount }}">{{ $loop->parent->iteration }}</td>
                                        <td class="d-none d-lg-table-cell" rowspan="{{ $detailCount }}">{{ $trx->tanggal }}</td>
                                        <td class="d-none d-lg-table-cell" rowspan="{{ $detailCount }}">{{ $trx->id_transaksi }}</td>
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
                                    <td class="d-none d-lg-table-cell">{{ $detail->id_barang }}</td>
                                    <td>{{ $detail->barang->nama_barang }}</td>

                                    {{-- JUMLAH --}}
                                    <td class="d-none d-md-table-cell">{{ $detail->jumlah }}</td>

                                    @if ($i === 0)
                                        <td class="d-none d-lg-table-cell" rowspan="{{ $detailCount }}">
                                            Rp {{ number_format($trx->total_biaya, 0, ',', '.') }}
                                        </td>

                                        <td rowspan="{{ $detailCount }}">
                                            @if ($trx->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif ($trx->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                    @endif

                                    @if ($i === 0)
                                        <td rowspan="{{ $detailCount }}" style="white-space:nowrap;">

                                            {{-- DETAIL --}}
                                            <a href="{{ route('gudang.transaksi.detail', $trx->id_transaksi) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- EDIT hanya kalau belum approved --}}
                                            @if ($trx->status == 'pending')
                                                <a href="{{ route('gudang.transaksi.edit', $trx->id_transaksi) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            {{-- ================= WORKFLOW ================= --}}

                                            @if ($trx->status == 'pending')
                                                {{-- APPROVE --}}
                                                <form action="{{ route('gudang.transaksi.approve', $trx->id_transaksi) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="return confirm('Approve transaksi ini?')">
                                                        Approve
                                                    </button>
                                                </form>

                                                {{-- REJECT --}}
                                                <form action="{{ route('gudang.transaksi.reject', $trx->id_transaksi) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Tolak transaksi ini?')">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif

                                        </td>
                                    @endif

                                    {{-- KETERANGAN --}}
                                    @if ($i === 0)
                                        <td class="d-none d-md-table-cell" rowspan="{{ $detailCount }}">{{ $trx->referensi ?? '-' }}</td>
                                    @endif

                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="10" class="text-center p-4">
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
