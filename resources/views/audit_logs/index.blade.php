@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Audit Logs</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Audit Logs</span>
            </nav>
        </div>

        <div class="controls-section">
            <div class="controls-left">
                <button class="btn btn-outline" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Reload
                </button>
            </div>

            <div class="controls-right">
                <span class="search-label">Search:</span>
                <input type="text" id="searchInput" placeholder="Cari user / aksi / tabel..." class="search-input">
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell">No</th>
                        <th class="d-none d-lg-table-cell">Tanggal</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th class="d-none d-md-table-cell">Tabel</th>
                        <th class="d-none d-lg-table-cell">Record ID</th>
                        <th class="d-none d-lg-table-cell">IP</th>
                        <th>Detail</th>
                    </tr>
                </thead>

                <tbody id="tableData">
                    @forelse ($logs as $index => $log)
                        <tr
                            data-search="
                                {{ strtolower($log->user->name ?? '') }}
                                {{ strtolower($log->action) }}
                                {{ strtolower($log->table_name) }}
                                {{ strtolower($log->record_id ?? '') }}
                            "
                        >
                            <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                            <td class="d-none d-lg-table-cell">{{ $log->created_at->format('d-m-Y H:i') }}</td>

                            <td>
                                <span class="badge badge-info">
                                    {{ $log->user->name ?? 'System' }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $color = match($log->action) {
                                        'create' => 'success',
                                        'update' => 'warning',
                                        'delete' => 'danger',
                                        'login'  => 'info',
                                        'approve'=> 'primary',
                                        default  => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $color }}">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>

                            <td class="d-none d-md-table-cell">{{ $log->table_name }}</td>

                            <td class="d-none d-lg-table-cell">{{ $log->record_id ?? '-' }}</td>

                            <td class="d-none d-lg-table-cell">{{ $log->ip_address ?? '-' }}</td>

                            <td>
                                <a href="{{ route('audit.logs.show', $log->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                                Tidak ada audit log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>

    </div>
</main>

<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tableData tr').forEach(el => {
        const data = el.getAttribute('data-search');
        el.style.display = data && data.includes(q) ? '' : 'none';
    });
});
</script>
@endsection
