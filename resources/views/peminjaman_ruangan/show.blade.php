@extends('layouts.app')

@section('title', 'Detail Peminjaman Ruangan')

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
            vertical-align: middle;
        }

        .custom-table tbody tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .row-terlambat td {
            background-color: #ffe5e5 !important;
        }

        .badge-rusak-ringan {
            background: #ffc107;
            color: black;
        }

        .badge-rusak-berat {
            background: #dc3545;
            color: white;
        }

        .badge-baik {
            background: #198754;
            color: white;
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
                <h1 class="page-title">Detail Peminjaman Ruangan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('peminjaman-ruangan.index') }}">Peminjaman Ruangan</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $data->id_peminjaman }}</span>
                </nav>
            </div>

            {{-- INFO RINGKAS --}}
            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">{{ $data->nama_pengaju }}</h2>
                    <p class="show-info-subtitle">{{ $data->divisi->nama_divisi ?? '-' }}</p>
                </div>

                <div class="show-info-right text-end">

                    {{-- STATUS --}}
                    @php
                        $statusClass = match ($data->status) {
                            'Belum Diproses' => 'show-badge-secondary',
                            'Sedang Diproses' => 'show-badge-warning',
                            'Sudah Tersedia' => 'show-badge-info',
                            'Selesai' => 'show-badge-success',
                            default => 'show-badge-secondary',
                        };
                    @endphp

                    <span class="show-badge {{ $statusClass }}">
                        {{ $data->status }}
                    </span>

                    <div class="fw-bold mt-2">
                        {{ $data->details->count() }} Sesi
                    </div>

                    <small class="text-muted">
                        {{ ucfirst(str_replace('_', ' ', $data->decision_status)) }}
                    </small>
                </div>
            </div>

            {{-- INFORMASI PEMINJAMAN --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Peminjaman
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $data->id_peminjaman }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $data->nama_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $data->email_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $data->divisi->nama_divisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kegiatan</th>
                        <td>{{ ucfirst($data->jenis_kegiatan) }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <td>{{ $data->nama_kegiatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Peserta Rapat</th>
                        <td>{{ $data->peserta_rapat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $data->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- DETAIL RUANGAN --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-building text-warning"></i> Detail Ruangan
                </h5>

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Gedung</th>
                            <th>Ruangan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->details as $d)
                            <tr>
                                <td>{{ $d->gedung->nama_gedung ?? $d->id_gedung }}</td>
                                <td>{{ $d->ruangan->nama_ruangan ?? $d->id_ruangan }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($d->tanggal_mulai)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($d->tanggal_selesai)->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $d->jam_mulai }} - {{ $d->jam_selesai }}
                                </td>

                                <td>{{ $d->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ASET --}}
            @php
                $allAset = $data->details->flatMap(function ($d) {
                    return $d->aset;
                });
            @endphp

            @if ($allAset->count())
                <div class="show-detail-card">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-box text-success"></i> Aset Digunakan
                    </h5>

                    <table class="show-detail-table">
                        <thead>
                            <tr>
                                <th>Nama Aset</th>
                                <th>Jumlah</th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->details as $d)
                                @foreach ($d->aset as $a)
                                    <tr>
                                        <td>{{ $a->aset->nama_aset ?? '-' }}</td>
                                        <td>{{ $a->jumlah }}</td>
                                        <td>{{ ucfirst($a->sumber_barang) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- KONSUMSI --}}
            @if ($data->konsumsi->count())
                <div class="show-detail-card">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-utensils text-danger"></i> Konsumsi
                    </h5>

                    <table class="show-detail-table">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->konsumsi as $k)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $k->jenis_konsumsi)) }}</td>
                                    <td>{{ $k->jumlah }}</td>
                                    <td>{{ $k->catatan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- METADATA --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock text-dark"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($data->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($data->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            {{-- ACTION --}}
            <div class="show-action">
                <a href="{{ route('peminjaman-ruangan.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                @if ($data->status === 'Belum Diproses')
                    <a href="{{ route('peminjaman-ruangan.edit', $data->id_peminjaman) }}" class="btn btn-primary">
                        Edit
                    </a>
                @endif
            </div>

        </div>
    </main>

@endsection
