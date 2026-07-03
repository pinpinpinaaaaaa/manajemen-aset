@extends('layouts.app')

@section('title', 'Manajemen User')

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
                <h1 class="page-title">Manajemen User</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Users</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('users.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah User
                    </a>

                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari nama user..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID User</th>
                            <th>Nama</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableData">
                        @forelse ($users as $index => $u)
                            <tr
                                data-name="{{ strtolower($u->name) }} {{ strtolower($u->email) }} {{ strtolower($u->role->nama_role ?? '') }}">

                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                                <td class="d-none d-lg-table-cell">{{ $u->id_user }}</td>

                                <td>{{ $u->name }}</td>

                                <td class="d-none d-md-table-cell">{{ $u->email }}</td>

                                <td>
                                    <span class="badge badge-info">
                                        {{ $u->role->nama_role ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('users.edit', $u->id_user) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('users.destroy', $u->id_user) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus user ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    <i class="fas fa-user-slash fa-2x mb-2"></i><br>
                                    Tidak ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $users->withQueryString()->links() }}
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
