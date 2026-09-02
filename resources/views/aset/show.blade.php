@extends('layouts.app')

@section('title', 'Detail Aset ' . $aset->nama_aset)

@section('content')
    <style>
        /* ================= CARD IMPROVEMENT ================= */

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

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .card-body {
            padding: 24px;
        }

        .grid.grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 40px;
        }

        .bg-light {
            background: #f9fafb;
        }

        .data-table thead {
            background: #f3f4f6;
        }

        .data-table th {
            font-weight: 600;
            color: #374151;
        }

        .badge {
            padding: 6px 10px;
            font-size: 13px;
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

        .stats-grid .stat-number {
            overflow-wrap: anywhere;
        }

        /* Kode aset di h2 bisa panjang tanpa spasi — cegah overflow */
        .compact-text-info h2 {
            overflow-wrap: anywhere;
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Detail Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('aset.index') }}">Aset</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $aset->nama_aset }}</span>
                </nav>
            </div>

            <div class="building-header-compact">
                <div class="compact-info-section">

                    <div class="compact-image">
                        <img src="{{ $aset->foto ? asset('storage/' . $aset->foto) : asset('images/default-asset.jpg') }}"
                            alt="{{ $aset->nama_aset }}">
                    </div>

                    <div class="compact-text-info">
                        <h2>{{ $aset->kode_aset }}</h2>

                        <p class="building-description">
                            Lokasi:
                            <b>{{ $aset->ruangan->nama_ruangan ?? '-' }}</b>,
                            Gedung <b>{{ $aset->gedung->nama_gedung ?? '-' }}</b>
                        </p>

                        <div class="stats-grid">

                            <div class="stat-card">
                                <div class="stat-number">{{ $aset->nama_aset }}</div>
                                <div class="stat-label">Nama Aset</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">
                                    Rp {{ number_format($totalBiayaMaintenance ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="stat-label">Total Biaya Maintenance</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">{{ ucfirst($aset->status) }}</div>
                                <div class="stat-label">Status Aset</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <h3>Informasi Aset</h3>
                </div>

                <div class="card-body">


                    <div class="grid grid-2 mt-3">

                        <p>
                            <b>Jenis Barang:</b>
                            <span class="badge badge-secondary">
                                {{ $aset->jenisBarang->nama_barang ?? '-' }}
                            </span>
                        </p>

                        <p>
                            <b>Jenis:</b>
                            <span class="badge badge-info">
                                {{ ucfirst($aset->jenisBarang->jenis ?? '-') }}
                            </span>
                        </p>

                        <p>
                            <b>Kategori:</b>
                            <span class="badge badge-secondary">
                                {{ ucfirst($aset->jenisBarang->kategori ?? '-') }}
                            </span>
                        </p>

                        <p><b>Merk:</b> {{ $aset->merk ?? '-' }}</p>

                        <p><b>Tipe / Model:</b> {{ $aset->tipe_model ?? '-' }}</p>

                        <p><b>Tahun Perolehan:</b> {{ $aset->tahun_perolehan ?? '-' }}</p>

                        <p>
                            <b>Status:</b>
                            <span class="badge badge-secondary">
                                {{ ucfirst($aset->status ?? '-') }}
                            </span>
                        </p>

                        <p>
                            <b>Kelayakan:</b>
                            <span
                                class="badge
            {{ $aset->keterangan_kelayakan == 'Layak'
                ? 'badge-active'
                : ($aset->keterangan_kelayakan == 'Perlu pemantauan'
                    ? 'badge-info'
                    : ($aset->keterangan_kelayakan == 'Perlu perbaikan'
                        ? 'badge-warning'
                        : 'badge-danger')) }}">
                                {{ $aset->keterangan_kelayakan }}
                            </span>
                        </p>

                        <p><b>Nilai Aset:</b>
                            Rp {{ number_format($aset->nilai ?? 0, 0, ',', '.') }}
                        </p>

                    </div>

                    {{-- SPESIFIKASI FULL WIDTH --}}
                    @if ($aset->spesifikasi)
                        <div class="mt-4">
                            <b>Spesifikasi:</b>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $aset->spesifikasi }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>



            <div class="card custom-card">
                <div class="card-header">
                    <h3>Riwayat Aktivitas Aset</h3>
                </div>

                <div class="card-body">


                    @if ($aset->logs->count())
                        <div class="log-scroll">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Aktivitas</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($aset->logs as $i => $log)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>

                                            <td>
                                                @php
                                                    $badge = match ($log->tipe) {
                                                        'maintenance' => 'badge badge-warning',
                                                        'pemindahan' => 'badge badge-info',
                                                        'pemusnahan' => 'badge badge-danger',
                                                        'update' => 'badge badge-secondary',
                                                        'create' => 'badge badge-active',
                                                        default => 'badge badge-secondary',
                                                    };
                                                @endphp

                                                <span class="{{ $badge }}">
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
                        <p class="text-muted mt-2">Belum ada riwayat aktivitas aset.</p>
                    @endif
                </div>
            </div>
            <div class="mt-4" style="margin-top: 30px;">
                <a href="{{ route('aset.edit', $aset->id_aset) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Aset
                </a>
                <form action="{{ route('aset.check', $aset->id_aset) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0"
                        style="border: 1px solid #000; background: transparent;">
                        <i class="fas fa-check"></i> CEK FISIK ASET
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection
