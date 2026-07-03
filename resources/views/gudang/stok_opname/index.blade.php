@extends('layouts.app')

@section('title', 'Stok Opname Gudang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Stok Opname Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Opname</span>
                </nav>
            </div>

            <div class="controls-section">

                <div class="controls-left">
                    <form action="{{ route('gudang.stok_opname.mulai') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">
                            <i class="fas fa-play"></i> Mulai Opname
                        </button>
                    </form>

                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari kode opname..." class="search-input">
                </div>
            </div>

            <div class="table-container mt-3">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">Kode</th>
                            <th class="d-none d-md-table-cell">Mulai</th>
                            <th class="d-none d-lg-table-cell">Selesai</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableData">
                        @forelse ($data as $i => $d)
                            <tr data-name="{{ strtolower($d->kode_opname) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>

                                <td class="d-none d-lg-table-cell"><strong>{{ $d->kode_opname }}</strong></td>

                                <td class="d-none d-md-table-cell">
                                    {{ $d->started_at ? \Carbon\Carbon::parse($d->started_at)->format('d M Y H:i') : '-' }}
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $d->finished_at ? \Carbon\Carbon::parse($d->finished_at)->format('d M Y H:i') : '-' }}
                                </td>

                                <td>
                                    @if ($d->status == 'draft')
                                        <span class="badge badge-warning">Draft</span>
                                    @else
                                        <span class="badge badge-success">Selesai</span>
                                    @endif
                                </td>

                                <td>{{ $d->id_user ?? '-' }}</td>

                                <td class="d-flex gap-1">

                                    <a href="{{ route('gudang.stok_opname.detail', $d->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if ($d->status == 'draft')
                                        <form action="{{ route('gudang.stok_opname.selesai', $d->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Selesaikan opname ini?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">
                                    Belum ada data opname.
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
