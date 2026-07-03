@extends('layouts.app')

@section('title', 'Detail Permintaan Kendaraan')

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
            padding: 10px;
            border: 1px solid #dee2e6;
            background-color: #ffffff;
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
                <h1 class="page-title">Detail Permintaan Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('permintaan-kendaraan.index') }}">Permintaan Kendaraan</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $data->id_permohonan }}</span>
                </nav>
            </div>

            {{-- INFO UTAMA --}}
            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">{{ $data->nama }}</h2>
                    <p class="show-info-subtitle">
                        {{ $data->divisi->nama_divisi ?? '-' }}
                    </p>
                </div>

                <div class="show-info-right text-end">
                    @php
                        $color = match ($data->status) {
                            'menunggu konfirmasi' => 'secondary',
                            'disetujui' => 'info',
                            'ditolak' => 'danger',
                            'dipakai' => 'warning',
                            'selesai' => 'success',
                            default => 'secondary',
                        };
                    @endphp

                    <span class="badge bg-{{ $color }}">
                        {{ ucfirst($data->status) }}
                    </span>
                </div>
            </div>

            {{-- INFORMASI --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Permintaan
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $data->id_permohonan }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $data->nama }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $data->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $data->divisi->nama_divisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $data->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- DETAIL PERJALANAN --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-route text-warning"></i> Detail Perjalanan
                </h5>

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Keperluan</th>
                            <th>Jemput</th>
                            <th>Tujuan</th>
                            <th>Catatan</th>
                            <th>Surat Tugas</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data->details as $d)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($d->tanggal_mulai)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($d->tanggal_selesai)->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $d->jam_mulai }} - {{ $d->jam_selesai }}
                                </td>

                                <td>{{ $d->keperluan }}</td>
                                <td>{{ $d->tempat_jemput }}</td>
                                <td>{{ $d->tempat_tujuan }}</td>
                                <td>{{ $d->catatan ?? '-' }}</td>

                                <td>
                                    @if ($d->surat_tugas)
                                        <a href="{{ asset('storage/' . $d->surat_tugas) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            Lihat
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Tidak ada detail</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- KENDARAAN --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-car text-info"></i> Kendaraan Digunakan
                </h5>

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Merk</th>
                            <th>Model</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data->details as $d)
                            @foreach ($d->items as $item)
                                <tr>
                                    <td>{{ $item->kendaraan->plat_nomor ?? '-' }}</td>
                                    <td>{{ $item->kendaraan->jenis_kendaraan ?? '-' }}</td>
                                    <td>{{ $item->kendaraan->merk ?? '-' }}</td>
                                    <td>{{ $item->kendaraan->model ?? '-' }}</td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $item->kendaraan->status_penggunaan ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5">Tidak ada kendaraan</td>
                            </tr>
                        @endforelse
                    </tbody>
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
                <a href="{{ route('permintaan-kendaraan.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </div>
    </main>
@endsection
