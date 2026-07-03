@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')
    <style>
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #bfdbfe;
        }

        .data-table td {
            vertical-align: middle;
            text-align: center;
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Manajemen Role</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Roles</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('roles.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Role
                    </a>

                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari nama role..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID Role</th>
                            <th>Nama Role</th>
                            <th class="d-none d-md-table-cell">Menu Access</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableData">
                        @forelse ($roles as $index => $r)
                            <tr data-name="{{ strtolower($r->nama_role) }}">

                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $r->id_role }}</td>
                                <td>{{ $r->nama_role }}</td>

                                <td class="d-none d-md-table-cell" style="text-align:center;">
                                    @php
                                        if (is_null($r->menu)) {
                                            $menus = null; // FULL ACCESS
                                        } else {
                                            $menus = \App\Models\Menu::whereIn('id', $r->menu)->get();
                                        }
                                    @endphp

                                    @if (is_null($menus))
                                        <span style="color:#16a34a;font-weight:600;">
                                            Semua Menu
                                        </span>
                                    @elseif ($menus->count())
                                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                            @foreach ($menus as $menu)
                                                <span class="badge" title="{{ $menu->route_name }}">
                                                    {{ $menu->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color:#9ca3af;font-size:13px;">
                                            Tidak ada menu
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('roles.edit', $r->id_role) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('roles.destroy', $r->id_role) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus role ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">
                                    <i class="fas fa-user-lock fa-2x mb-2"></i><br>
                                    Tidak ada data role.
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
