@extends('layouts.app')

@section('title', 'Detail Permintaan Barang Gudang')

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

            {{-- 🔹 Header --}}
            <div class="page-header">
                <h1 class="page-title">Detail Permintaan Barang Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('permintaan-barang.index') }}" class="breadcrumb-link">
                        Permintaan Barang Gudang
                    </a>
                    <span class="separator">/</span>
                    <span class="current">{{ $permintaan->id_permintaan }}</span>
                </nav>
            </div>

            {{-- 🔹 Info Ringkas --}}
            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">
                        {{ ucfirst($permintaan->jenis_permintaan) }}
                    </h2>
                    <p class="show-info-subtitle">
                        {{ $permintaan->divisi->nama_divisi ?? '-' }}
                    </p>
                </div>

                <div class="show-info-right text-end">
                    @php
                        $statusClass = match ($permintaan->status) {
                            'Belum Diproses' => 'show-badge-secondary',
                            'Sedang Diproses' => 'show-badge-warning',
                            'Tersedia' => 'show-badge-info',
                            'Selesai' => 'show-badge-success',
                            default => 'show-badge-secondary',
                        };
                    @endphp

                    <span class="show-badge {{ $statusClass }}">
                        {{ $permintaan->status }}
                    </span>

                    @php
                        $today = \Carbon\Carbon::today();
                        $tgl = \Carbon\Carbon::parse($permintaan->tanggal_kebutuhan);

                        $isOverdue = $permintaan->status !== 'Selesai' && $tgl->lt($today);
                        $isToday = $permintaan->status !== 'Selesai' && $tgl->isSameDay($today);
                    @endphp

                    @if ($isOverdue)
                        <div class="text-danger fw-bold mt-2">
                            Melewati tanggal kebutuhan
                        </div>
                    @elseif($isToday)
                        <div class="text-warning fw-bold mt-2">
                            Hari ini tanggal kebutuhan
                        </div>
                    @endif

                    <div class="fw-bold mt-2 show-info-cost">
                        Rp{{ number_format($permintaan->total_biaya ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Total Biaya</small>
                </div>
            </div>

            {{-- 🔹 Informasi Umum --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Permintaan
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID Permintaan</th>
                        <td>{{ $permintaan->id_permintaan }}</td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td>{{ ucfirst($permintaan->jenis_permintaan) }}</td>
                    </tr>
                    <tr>
                        <th>Nama Pengaju</th>
                        <td>{{ $permintaan->nama_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $permintaan->email_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $permintaan->divisi->nama_divisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Kebutuhan</th>
                        <td>{{ \Carbon\Carbon::parse($permintaan->tanggal_kebutuhan)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td>{{ $permintaan->alasan }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $permintaan->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- 🔹 Detail Item --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-box text-warning"></i> Detail Item
                </h5>

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Barang Gudang</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($permintaan->details as $detail)
                            <tr>
                                <td>
                                    <strong>
                                        {{ optional($detail->barang)->nama_barang ?? 'Barang Tidak Ditemukan' }}
                                    </strong>

                                    @if (!$detail->barang)
                                        <br>
                                        <small class="text-danger">
                                            Data barang sudah tidak tersedia di gudang
                                        </small>
                                    @endif
                                </td>

                                <td>{{ $detail->jumlah }}</td>

                                <td>-</td> {{-- belum ada harga di DB --}}

                                <td>-</td> {{-- belum ada subtotal --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">
                                    Tidak ada data detail
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3">TOTAL ITEM</td>
                            <td>
                                {{ $permintaan->details->sum('jumlah') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- 🔹 Approval Workflow --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-check-circle text-success"></i> Approval Workflow
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Status Approval</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $permintaan->decision_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Diputuskan Oleh</th>
                        <td>{{ $permintaan->decided_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $permintaan->decided_at ? \Carbon\Carbon::parse($permintaan->decided_at)->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- 🔹 Lampiran --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-paperclip text-secondary"></i> Lampiran
                </h5>

                @if ($permintaan->lampiran)
                    <a href="{{ $permintaan->lampiran }}" target="_blank" class="btn btn-outline-primary">
                        Buka Lampiran
                    </a>
                @else
                    <span class="text-muted">Tidak ada lampiran</span>
                @endif
            </div>

            {{-- 🔹 Metadata --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock text-dark"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($permintaan->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($permintaan->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            {{-- 🔹 Tombol --}}
            <div class="show-action">
                <a href="{{ route('permintaan-barang.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                @if ($permintaan->status === 'Belum Diproses')
                    <div class="d-flex gap-2">
                        <a href="{{ route('permintaan-barang.edit', $permintaan->id_permintaan) }}"
                            class="btn btn-primary">
                            Edit
                        </a>

                        <form action="{{ route('permintaan-barang.destroy', $permintaan->id_permintaan) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus permintaan ini?')">
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
