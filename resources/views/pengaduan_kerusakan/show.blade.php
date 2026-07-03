@extends('layouts.app')

@section('title', 'Detail Pengadaan Barang & Jasa')

@section('content')
    <style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .custom-table th {
            background-color: #9ea1a3;
            color: #000000;
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .custom-table td {
            vertical-align: middle;
            padding: 10px;
            border: 1px solid #dee2e6;
            background-color: #ffffff;
        }

        .custom-table img {
            display: block;
            max-height: 120px;
            width: auto;
            object-fit: contain;
        }

        .custom-table tbody tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .custom-table .total-row td {
            font-weight: bold;
            background-color: #e9ecef;
            font-size: 15px;
        }

        .show-badge-info {
            background-color: #0dcaf0;
            color: #fff;
        }

        .show-badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
    <main class="main-content">
        <div class="content-padding show-page">

            {{-- HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Detail Pengaduan Kerusakan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pengaduan-kerusakan.index') }}">Pengaduan</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $data->id_pengaduan }}</span>
                </nav>
            </div>

            {{-- INFO UTAMA --}}
            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">{{ $data->nama_pelapor }}</h2>
                    <p class="show-info-subtitle">
                        {{ $data->divisi->nama_divisi ?? '-' }}
                    </p>
                </div>

                <div class="show-info-right text-end">

                    @php
                        $color = match ($data->decision_status) {
                            'menunggu_persetujuan' => 'warning',
                            'disetujui' => 'success',
                            'ditolak' => 'danger',
                            default => 'secondary',
                        };
                    @endphp

                    <span class="badge bg-{{ $color }}">
                        {{ ucfirst(str_replace('_', ' ', $data->decision_status)) }}
                    </span>
                </div>
            </div>

            {{-- INFORMASI --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Pengaduan
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $data->id_pengaduan }}</td>
                    </tr>
                    <tr>
                        <th>Nama Pelapor</th>
                        <td>{{ $data->nama_pelapor }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $data->email_pelapor ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $data->divisi->nama_divisi ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- DETAIL --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-tools text-warning"></i> Detail Kerusakan
                </h5>

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Aset</th>
                            <th>Gedung</th>
                            <th>Ruangan</th>
                            <th>Kategori</th>
                            <th>Keluhan</th>
                            <th>Foto</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data->details as $d)
                            <tr>
                                <td>{{ $d->aset->nama_aset ?? '-' }}</td>

                                <td>
                                    {{ $d->aset->gedung->nama_gedung ?? '-' }}
                                </td>

                                <td>
                                    {{ $d->aset->ruangan->nama_ruangan ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($d->kategori_kerusakan) }}
                                    </span>
                                </td>

                                <td>{{ $d->keluhan }}</td>

                                <td>
                                    <img src="{{ asset('storage/' . $d->foto) }}" width="100">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Tidak ada detail</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- APPROVAL --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-check-circle text-success"></i> Approval
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $data->decision_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Diputuskan Oleh</th>
                        <td>{{ $data->approver->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>
                            {{ $data->decided_at ? \Carbon\Carbon::parse($data->decided_at)->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- METADATA --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $data->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate</th>
                        <td>{{ $data->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            {{-- ACTION --}}
            <div class="show-action">
                <a href="{{ route('pengaduan-kerusakan.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </main>
@endsection
