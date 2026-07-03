@extends('layouts.app')

@section('title', 'Detail Ekspedisi')

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

            {{-- HEADER --}} <div class="page-header">
                <h1 class="page-title">Detail Ekspedisi</h1>
                <nav class="breadcrumb"> <a href="{{ url('/dashboard') }}">Dashboard</a> <span class="separator">/</span> <a
                        href="{{ route('ekspedisi.index') }}">Ekspedisi</a> <span class="separator">/</span> <span
                        class="current">{{ $data->id_ekspedisi }}</span> </nav>
            </div> {{-- INFO UTAMA --}} <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">{{ $data->nama_pengirim }}</h2>
                    <p class="show-info-subtitle"> {{ $data->divisi_pengirim->nama_divisi ?? '-' }} </p>
                </div>
                <div class="show-info-right text-end"> {{-- STATUS APPROVAL --}} @php
                    $acolor = match ($data->decision_status) {
                        'menunggu_persetujuan' => 'show-badge-warning',
                        'disetujui' => 'show-badge-success',
                        'ditolak' => 'show-badge-danger',
                        default => 'show-badge-secondary',
                    };
                @endphp <span
                        class="show-badge {{ $acolor }}"> {{ ucfirst(explode('_', $data->decision_status)[0]) }} </span>
                    {{-- STATUS PENGIRIMAN --}} @php
                        $statusKirim = optional($data->pengiriman)->status_pengiriman;
                        $pcolor = match ($statusKirim) {
                            'belum_dikirim' => 'show-badge-secondary',
                            'dikirim' => 'show-badge-info',
                            'diterima' => 'show-badge-warning',
                            'selesai' => 'show-badge-success',
                            default => 'show-badge-secondary',
                        };
                    @endphp <div class="mt-2"> <span
                            class="show-badge {{ $pcolor }}"> {{ $statusKirim ?? '-' }} </span> </div>
                </div>
            </div> {{-- INFORMASI --}} <div class="show-detail-card">
                <h5 class="fw-bold mb-3"> <i class="fas fa-info-circle text-primary"></i> Informasi Ekspedisi </h5>
                <table class="show-detail-table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $data->id_ekspedisi }}</td>
                    </tr>
                    <tr>
                        <th>Pengirim</th>
                        <td>{{ $data->nama_pengirim }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $data->email_pengirim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Penerima</th>
                        <td>{{ $data->nama_penerima }}</td>
                    </tr>
                    <tr>
                        <th>Instansi</th>
                        <td>{{ $data->instansi_penerima ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $data->alamat_penerima }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $data->keterangan ?? '-' }}</td>
                    </tr>
                </table>
            </div> {{-- DETAIL ISI --}} <div class="show-detail-card">
                <h5 class="fw-bold mb-3"> <i class="fas fa-box text-warning"></i> Detail Kiriman </h5>
                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DOKUMEN --}}
                        @foreach ($data->dokumen as $d)
                            <tr>
                                <td>{{ $d->nama_dokumen }}</td>
                                <td>Dokumen</td>
                                <td>{{ $d->jumlah }}</td>
                            </tr>
                        @endforeach
                        {{-- BARANG --}}
                        @foreach ($data->barang as $b)
                            <tr>
                                <td>{{ $b->nama_barang }}</td>
                                <td>Barang</td>
                                <td>{{ $b->jumlah }}</td>
                            </tr>
                        @endforeach
                        @if ($data->dokumen->isEmpty() && $data->barang->isEmpty())
                            <tr>
                                <td colspan="3">Tidak ada isi kiriman</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- PENGIRIMAN --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-truck text-info"></i>
                    Informasi Pengiriman
                </h5>

                <table class="show-detail-table">

                    <tr>
                        <th>Jenis Kurir</th>
                        <td>
                            {{ ucfirst($data->pengiriman?->jenis_kurir ?? '-') }}
                        </td>
                    </tr>

                    {{-- KURIR INTERNAL --}}
                    @if ($data->pengiriman?->jenis_kurir == 'internal')
                        <tr>
                            <th>Nama Kurir</th>
                            <td>
                                {{ $data->pengiriman?->kurirInternal?->name ?? '-' }}
                            </td>
                        </tr>
                    @endif

                    {{-- KURIR EKSTERNAL --}}
                    @if ($data->pengiriman?->jenis_kurir == 'eksternal')
                        <tr>
                            <th>Jasa Ekspedisi</th>
                            <td>
                                {{ $data->pengiriman?->nama_jasa_ekspedisi ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>No Resi</th>
                            <td>
                                {{ $data->pengiriman?->no_resi ?? '-' }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <th>Waktu Dikirim</th>
                        <td>
                            {{ $data->pengiriman?->waktu_dikirim
                                ? \Carbon\Carbon::parse($data->pengiriman->waktu_dikirim)->translatedFormat('d F Y H:i')
                                : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Waktu Diterima</th>
                        <td>
                            {{ $data->pengiriman?->waktu_diterima
                                ? \Carbon\Carbon::parse($data->pengiriman->waktu_diterima)->translatedFormat('d F Y H:i')
                                : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Nama Penerima</th>
                        <td>
                            {{ $data->pengiriman?->nama_penerima_ttd ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jabatan Penerima</th>
                        <td>
                            {{ $data->pengiriman?->jabatan_penerima ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Catatan</th>
                        <td>
                            {{ $data->pengiriman?->catatan_penerimaan ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Foto Bukti</th>
                        <td>
                            @if ($data->pengiriman?->foto_bukti)
                                <a href="{{ asset('storage/' . $data->pengiriman->foto_bukti) }}" target="_blank">

                                    <img src="{{ asset('storage/' . $data->pengiriman->foto_bukti) }}" alt="Foto Bukti"
                                        style="
                                        max-width: 250px;
                                        border-radius: 10px;
                                        border: 1px solid #ddd;
                                        padding: 4px;
                                    ">
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                </table>
            </div>
            {{-- APPROVAL --}} <div class="show-detail-card">
                <h5 class="fw-bold mb-3"> <i class="fas fa-check-circle text-success"></i> Approval </h5>
                <table class="show-detail-table">
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst(explode('_', $data->decision_status)[0]) }}</td>
                    </tr>
                    <tr>
                        <th>Disetujui Oleh</th>
                        <td>{{ $data->approved_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td> {{ $data->approved_at ? \Carbon\Carbon::parse($data->approved_at)->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div> {{-- METADATA --}} <div class="show-detail-card">
                <h5 class="fw-bold mb-3"> <i class="fas fa-clock text-dark"></i> Metadata </h5>
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
            <div class="show-action d-flex gap-2 flex-wrap">

                <a href="{{ route('ekspedisi.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                <a href="{{ route('ekspedisi.label', $data->id_ekspedisi) }}" target="_blank" class="btn btn-warning">
                    <i class="fas fa-tag"></i>
                    Print Label
                </a>

                <a href="{{ route('ekspedisi.tanda-terima', $data->id_ekspedisi) }}" target="_blank"
                    class="btn btn-success">
                    <i class="fas fa-file-signature"></i>
                    Tanda Terima
                </a>

            </div>

        </div>
    </main>
@endsection
