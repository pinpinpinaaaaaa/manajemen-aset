@extends('layouts.app')

@section('title', 'Detail Kendaraan ' . $kendaraan->plat_nomor)

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

        .badge {
            padding: 6px 10px;
            font-size: 13px;
            border-radius: 6px;
        }
    </style>

    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Detail Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('kendaraan.index') }}">Kendaraan</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $kendaraan->plat_nomor }}</span>
                </nav>
            </div>

            <div class="building-header-compact">
                <div class="compact-info-section">

                    <div class="compact-image">
                        <img
                            src="{{ $kendaraan->foto ? asset('storage/' . $kendaraan->foto) : asset('images/default-asset.jpg') }}">
                    </div>

                    <div class="compact-text-info">
                        <h2>{{ strtoupper($kendaraan->plat_nomor) }}</h2>

                        <p class="building-description">
                            {{ ucfirst($kendaraan->merk) }} {{ $kendaraan->model }}
                            • {{ ucfirst($kendaraan->tipe) }}
                        </p>

                        <div class="stats-grid">

                            <div class="stat-card">
                                <div class="stat-number">{{ $kendaraan->tahun_pembelian }}</div>
                                <div class="stat-label">Tahun Pembelian</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">{{ $kendaraan->umur_ekonomis }} Th</div>
                                <div class="stat-label">Umur Ekonomis</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ ucfirst($kendaraan->status_penggunaan) }}
                                </div>
                                <div class="stat-label">Status Penggunaan</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <h3>Informasi Kendaraan</h3>
                </div>

                <div class="card-body">

                    <div class="grid grid-2">

                        <p><b>Jenis Kendaraan:</b> {{ ucfirst($kendaraan->jenis_kendaraan) }}</p>

                        <p><b>Tipe:</b> {{ ucfirst($kendaraan->tipe) }}</p>

                        <p><b>Merk:</b> {{ $kendaraan->merk }}</p>

                        <p><b>Model:</b> {{ $kendaraan->model }}</p>

                        <p><b>No Rangka:</b> {{ $kendaraan->no_rangka ?? '-' }}</p>

                        <p><b>No Mesin:</b> {{ $kendaraan->no_mesin ?? '-' }}</p>

                        <p>
                            <b>Status Kondisi:</b>
                            <span
                                class="badge
{{ $kendaraan->status_kondisi == 'aktif'
    ? 'badge-active'
    : ($kendaraan->status_kondisi == 'perbaikan'
        ? 'badge-warning'
        : 'badge-danger') }}">
                                {{ ucfirst($kendaraan->status_kondisi) }}
                            </span>
                        </p>

                        <p>
                            <b>Status Penggunaan:</b>
                            <span class="badge badge-secondary">
                                {{ ucfirst($kendaraan->status_penggunaan) }}
                            </span>
                        </p>

                    </div>

                    @if ($kendaraan->spesifikasi)
                        <div class="mt-4">
                            <b>Spesifikasi:</b>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $kendaraan->spesifikasi }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>


            @if ($kendaraan->driver)
                <div class="card custom-card">
                    <div class="card-header">
                        <h3>Driver</h3>
                    </div>

                    <div class="card-body">
                        <p><b>Nama:</b> {{ $kendaraan->driver->nama }}</p>
                        <p><b>ID:</b> {{ $kendaraan->driver->id_user }}</p>
                    </div>
                </div>
            @endif


            <div class="mt-4">
                <a href="{{ route('kendaraan.edit', $kendaraan->id_kendaraan) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Kendaraan
                </a>
            </div>

        </div>
    </main>
@endsection
