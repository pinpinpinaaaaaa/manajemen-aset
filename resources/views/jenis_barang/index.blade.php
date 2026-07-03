@extends('layouts.app')

@section('title', 'Jenis Barang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Jenis Barang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Management</span>
                    <span class="separator">/</span>
                    <span class="current">Jenis Barang</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('jenis_barang.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Jenis Barang
                    </a>
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
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
                            <th class="d-none d-md-table-cell" width="60">No</th>
                            <th class="d-none d-md-table-cell">Jenis</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th>Nama Barang</th>
                            <th class="d-none d-lg-table-cell">Prefix</th>
                            <th class="d-none d-lg-table-cell">Bisa Dipindah</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $index => $item)
                            <tr
                                data-search="
                        {{ strtolower($item->nama_barang) }}
                        {{ strtolower($item->jenis) }}
                        {{ strtolower($item->kategori) }}
                        {{ strtolower($item->prefix_kode) }}
                    ">
                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                                <td class="d-none d-md-table-cell">
                                    @if ($item->jenis == 'sarana')
                                        <span class="badge badge-success">
                                            Sarana
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            Prasarana
                                        </span>
                                    @endif
                                </td>

                                <td class="d-none d-md-table-cell">
                                    @if ($item->kategori == 'it')
                                        <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                            IT
                                        </span>
                                    @elseif($item->kategori == 'elektronik')
                                        <span class="badge" style="background:#dcfce7;color:#166534;">
                                            ELEKTRONIK
                                        </span>
                                    @else
                                        <span class="badge" style="background:#f3f4f6;color:#374151;">
                                            NON ELEKTRONIK
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $item->nama_barang }}</td>

                                <td class="d-none d-lg-table-cell">
                                    <code>{{ $item->prefix_kode }}</code>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @if ($item->bisa_dipindah)
                                        <span class="badge badge-success">
                                            Ya
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            Tidak
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('jenis_barang.edit', $item->id_jenis_barang) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('jenis_barang.destroy', $item->id_jenis_barang) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus jenis barang ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Belum ada data jenis barang
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

            const keyword = this.value.toLowerCase();

            document.querySelectorAll('tbody tr').forEach(row => {

                const searchText = row.dataset.search || '';

                row.style.display =
                    searchText.includes(keyword) ?
                    '' :
                    'none';
            });

        });
    </script>
@endsection
