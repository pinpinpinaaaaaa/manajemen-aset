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
                <h1 class="page-title">Detail Pengadaan Barang & Jasa</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pengadaan-barang.index') }}">Pengadaan Barang & Jasa</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $pengadaan->id_pengadaan }}</span>
                </nav>
            </div>

            {{-- INFO UTAMA --}}
            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">{{ $pengadaan->nama_pengaju }}</h2>
                    <p class="show-info-subtitle">{{ $pengadaan->divisi->nama_divisi ?? '-' }}</p>
                </div>

                <div class="show-info-right text-end"">
                    {{-- STATUS --}}

                    @php
                        $statusClass = match ($pengadaan->status) {
                            'Belum Diproses' => 'show-badge-secondary',
                            'Sedang Diproses' => 'show-badge-warning',
                            'Tersedia' => 'show-badge-info',
                            'Selesai' => 'show-badge-success',
                            default => 'show-badge-secondary',
                        };
                    @endphp

                    <span class="show-badge {{ $statusClass }}">
                        {{ $pengadaan->status }}
                    </span>

                    <div class="fw-bold mt-2 show-info-cost">
                        Rp{{ number_format($pengadaan->total_biaya ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Total Biaya</small>
                </div>
            </div>

            {{-- INFORMASI --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Pengadaan
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $pengadaan->id_pengadaan }}</td>
                    </tr>
                    <tr>
                        <th>Nama Pengaju</th>
                        <td>{{ $pengadaan->nama_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $pengadaan->email_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $pengadaan->divisi->nama_divisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Kebutuhan</th>
                        <td>{{ \Carbon\Carbon::parse($pengadaan->tanggal_kebutuhan)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td>{{ $pengadaan->alasan }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $pengadaan->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- DETAIL ITEM --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3"><i class="fas fa-box text-warning"></i> Detail Barang & Jasa</h5>

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Jenis</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th>Lampiran</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengadaan->details as $detail)
                            <tr>

                                <td>
                                    {{ $detail->jenis == 'barang' ? $detail->nama_barang : $detail->kategori_jasa }}
                                </td>

                                <td>
                                    {{ ucfirst($detail->jenis) }}
                                </td>

                                <td>
                                    {{ $detail->jumlah }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>

                                <td>
                                    @forelse($detail->files as $file)
                                        <div class="mb-1">
                                            <a href="{{ asset('storage/' . $file->file_path) }}" download>
                                                {{ $file->file_name }}
                                            </a>
                                        </div>

                                    @empty
                                        <span class="text-muted">
                                            Tidak ada file
                                        </span>
                                    @endforelse
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    Tidak ada detail
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr class="total-row">
                            <td colspan="4">
                                TOTAL
                            </td>

                            <td>
                                Rp{{ number_format($pengadaan->details->sum('subtotal'), 0, ',', '.') }}
                            </td>

                            <td>-</td>

                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- APPROVAL --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-check-circle text-success"></i> Approval Workflow
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $pengadaan->decision_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Diputuskan Oleh</th>
                        <td>{{ $pengadaan->decided_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>
                            {{ $pengadaan->decided_at ? \Carbon\Carbon::parse($pengadaan->decided_at)->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- 🔹 Metadata --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock text-dark"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($pengadaan->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($pengadaan->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            {{-- ACTION --}}
            <div class="show-action">
                <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                @if ($pengadaan->status === 'Belum Diproses')
                    <div class="d-flex gap-2">
                        <a href="{{ route('pengadaan-barang.edit', $pengadaan->id_pengadaan) }}" class="btn btn-primary">
                            Edit
                        </a>

                        <form action="{{ route('pengadaan-barang.destroy', $pengadaan->id_pengadaan) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus pengadaan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </main>
@endsection
