@extends('layouts.app')

@section('title', 'Detail Audit Log')

@section('content')
<main class="main-content">
    <div class="content-padding show-page">

        <div class="page-header">
            <h1 class="page-title">Detail Audit Log</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                <span class="separator">/</span>
                <a href="{{ route('audit.logs') }}" class="breadcrumb-link">Audit Logs</a>
                <span class="separator">/</span>
                <span class="current">#{{ $log->id }}</span>
            </nav>
        </div>

        <div class="show-info-card">
            <div class="show-info-left">
                <h2 class="show-info-title">
                    {{ strtoupper($log->action) }} - {{ $log->table_name }}
                </h2>
                <p class="show-info-subtitle">
                    Record ID: {{ $log->record_id ?? '-' }}
                </p>
            </div>

            <div class="show-info-right text-end">
                @php
                    $color = match($log->action) {
                        'create' => 'show-badge-success',
                        'update' => 'show-badge-warning',
                        'delete' => 'show-badge-danger',
                        'login'  => 'show-badge-info',
                        'approve'=> 'show-badge-primary',
                        default  => 'show-badge-secondary'
                    };
                @endphp

                <span class="show-badge {{ $color }}">
                    {{ strtoupper($log->action) }}
                </span>

                <div class="fw-bold mt-2">
                    {{ $log->created_at->format('d M Y H:i:s') }}
                </div>
                <small class="text-muted">Waktu Aksi</small>
            </div>
        </div>

        <div class="show-detail-card">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-info-circle text-primary"></i> Informasi Audit
            </h5>

            <table class="show-detail-table">
                <tr>
                    <th>User</th>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                </tr>
                <tr>
                    <th>Action</th>
                    <td>{{ $log->action }}</td>
                </tr>
                <tr>
                    <th>Table</th>
                    <td>{{ $log->table_name }}</td>
                </tr>
                <tr>
                    <th>Record ID</th>
                    <td>{{ $log->record_id ?? '-' }}</td>
                </tr>
                <tr>
                    <th>IP Address</th>
                    <td>{{ $log->ip_address ?? '-' }}</td>
                </tr>
                <tr>
                    <th>User Agent</th>
                    <td class="small">{{ $log->user_agent ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="show-detail-card">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-exchange-alt text-warning"></i> Perubahan Data
            </h5>

            @php
                $old = $log->old_data ?? [];
                $new = $log->new_data ?? [];
                $keys = collect(array_merge(array_keys($old), array_keys($new)))->unique();
            @endphp

            <table class="show-detail-table">
                <thead>
                    <tr>
                        <th style="width:25%">Field</th>
                        <th style="width:37%">Sebelum</th>
                        <th style="width:38%">Sesudah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keys as $field)
                        <tr>
                            <th>{{ $field }}</th>
                            <td class="text-muted">
                                {{ is_array($old[$field] ?? null)
                                    ? json_encode($old[$field], JSON_UNESCAPED_UNICODE)
                                    : ($old[$field] ?? '-') }}
                            </td>
                            <td class="{{ ($old[$field] ?? null) != ($new[$field] ?? null) ? 'fw-bold text-success' : '' }}">
                                {{ is_array($new[$field] ?? null)
                                    ? json_encode($new[$field], JSON_UNESCAPED_UNICODE)
                                    : ($new[$field] ?? '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Tidak ada perubahan data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="show-action">
            <a href="{{ route('audit.logs') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>
</main>
@endsection
