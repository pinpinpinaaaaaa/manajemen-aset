@extends('layouts.app')

@section('title', 'Detail Stok Opname')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Detail Stok Opname</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gudang.stok_opname.index') }}">Opname</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $header->kode_opname }}</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('gudang.stok_opname.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    @if ($header->status == 'draft')
                        <form action="{{ route('gudang.stok_opname.selesai', $header->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success" onclick="return confirm('Yakin selesaikan opname?')">
                                <i class="fas fa-check"></i> Selesaikan Opname
                            </button>
                        </form>
                    @endif

                    @if ($header->status == 'selesai')
                        <a href="{{ route('gudang.stok_opname.export.pdf', $header->id) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>

                        <a href="{{ route('gudang.stok_opname.export.excel', $header->id) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    @endif

                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari nama barang..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Selisih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableData">
                        @forelse ($details as $i => $d)
                            <tr data-name="{{ strtolower($d->nama_barang) }}">

                                <td>{{ $i + 1 }}</td>

                                <td>{{ $d->nama_barang }}</td>

                                <td>{{ $d->stok_sistem }}</td>

                                <td>
                                    @if ($header->status == 'draft')
                                        <input type="number" class="form-control form-control-sm stok-input"
                                            data-id="{{ $d->id }}" value="{{ $d->stok_fisik }}" min="0">
                                    @else
                                        {{ $d->stok_fisik ?? '-' }}
                                    @endif
                                </td>

                                <td>
                                    <span id="selisih-{{ $d->id }}">
                                        {{ $d->selisih ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($header->status == 'draft')
                                        <button class="btn btn-sm btn-primary btn-save" data-id="{{ $d->id }}">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <script>
        // =======================
        // SEARCH
        // =======================
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableData tr').forEach(el => {
                const name = el.getAttribute('data-name');
                el.style.display = name && name.includes(q) ? '' : 'none';
            });
        });

        // =======================
        // AJAX SAVE
        // =======================
        document.querySelectorAll('.btn-save').forEach(btn => {
            btn.addEventListener('click', function() {

                let id = this.dataset.id;
                let input = document.querySelector('.stok-input[data-id="' + id + '"]');
                let stok = input.value;

                fetch(`{{ url('gudang/stok_opname/update') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: new URLSearchParams({
                        stok_fisik: stok
                    })
                })

            });
        });
    </script>

@endsection
