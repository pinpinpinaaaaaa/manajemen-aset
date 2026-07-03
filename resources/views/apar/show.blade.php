@extends('layouts.app')

@section('title', 'Detail APAR ' . $apar->id_apar)

@section('content')
    <style>
        .custom-card {
            margin-top: 32px;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            background: #fff;
        }

        .card-header {
            padding: 18px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-body {
            padding: 24px;
        }

        .grid.grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 40px;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 6px;
        }

        .log-scroll {
            max-height: 300px;
            overflow-y: auto;
        }

        .log-scroll thead th {
            position: sticky;
            top: 0;
            background: #f3f4f6;
            z-index: 2;
        }
    </style>

    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Detail APAR</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('apar.index') }}">APAR</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $apar->id_apar }}</span>
                </nav>
            </div>

            <div class="building-header-compact">
                <div class="compact-info-section">

                    <div class="compact-image">
                        <img src="{{ $apar->foto ? asset('storage/' . $apar->foto) : asset('images/default-asset.jpg') }}">
                    </div>

                    <div class="compact-text-info">
                        <h2>APAR {{ $apar->id_apar }}</h2>

                        <p class="building-description">
                            Lokasi:
                            <b>{{ $apar->ruangan->nama_ruangan ?? '-' }}</b>,
                            Gedung <b>{{ $apar->gedung->nama_gedung ?? '-' }}</b>

                            @if ($apar->lokasi)
                                <br>
                                Detail Lokasi: <b>{{ $apar->lokasi }}</b>
                            @endif
                        </p>

                        <div class="stats-grid">

                            <div class="stat-card">
                                <div class="stat-number">{{ strtoupper($apar->id_apar) }}</div>
                                <div class="stat-label">Kode APAR</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apar->last_checked_at ? \Carbon\Carbon::parse($apar->last_checked_at)->format('d M Y') : '-' }}
                                </div>
                                <div class="stat-label">Terakhir Dicek</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apar->last_used_at ? \Carbon\Carbon::parse($apar->last_used_at)->format('d M Y') : '-' }}
                                </div>
                                <div class="stat-label">Terakhir Dipakai</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <h3>Informasi APAR</h3>
                </div>

                <div class="card-body">
                    <div class="grid grid-2">

                        <p><b>Jenis:</b>
                            <span class="badge badge-info">{{ ucfirst($apar->jenis) }}</span>
                        </p>

                        <p><b>Ukuran:</b> {{ $apar->ukuran }}</p>

                        <p><b>Merk:</b> {{ $apar->merk ?? '-' }}</p>

                        <p><b>Media Isi:</b> {{ $apar->media_isi ?? '-' }}</p>

                        <p><b>Expired:</b> {{ $apar->expired_date ?? '-' }}</p>

                        <p><b>Tanggal Refill:</b> {{ $apar->tanggal_refill ?? '-' }}</p>

                        <p><b>Last Checked By:</b> {{ $apar->checker->name ?? '-' }}</p>

                        <p><b>Last Used By:</b> {{ $apar->userPemakai->name ?? '-' }}</p>

                    </div>

                    @if ($apar->keterangan)
                        <div class="mt-4">
                            <b>Keterangan:</b>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $apar->keterangan }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <h3>Riwayat Aktivitas APAR</h3>
                </div>

                <div class="card-body">

                    @if ($apar->logs->count())
                        <div class="log-scroll">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tipe</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($apar->logs as $i => $log)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>

                                            <td>
                                                <span
                                                    class="badge 
                                                    {{ $log->tipe == 'check' ? 'badge-success' : ($log->tipe == 'dipakai' ? 'badge-warning' : 'badge-secondary') }}">
                                                    {{ ucfirst($log->tipe) }}
                                                </span>
                                            </td>

                                            <td>{{ $log->keterangan ?? '-' }} - {{ $log->user->name }}</td>

                                            <td>{{ \Carbon\Carbon::parse($log->tanggal_kejadian)->format('d M Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada riwayat.</p>
                    @endif

                </div>
            </div>

            <div class="mt-4" style="margin-top: 30px;">
                <a href="{{ route('apar.edit', $apar->id_apar) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit APAR
                </a>
                <form action="{{ route('apar.check', $apar->id_apar) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0"
                        style="border: 1px solid #000; background: transparent;">
                        <i class="fas fa-check"></i> CEK FISIK APAR
                    </button>
                </form>
                <form action="{{ route('apar.use', $apar->id_apar) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0"
                        style="border: 1px solid #000; background: transparent;">
                        <i class="fas fa-fire-extinguisher"></i> PAKAI APAR
                    </button>
                </form>
            </div>

        </div>
    </main>
@endsection
