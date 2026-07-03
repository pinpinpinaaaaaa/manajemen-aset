@extends('layouts.app')

@section('title', 'Detail Laporan Pemusnahan')

@section('content')
    <main class="main-content">
        <div class="content-padding show-page">

            <div class="page-header">
                <h1 class="page-title">Detail Laporan Pemusnahan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('laporan_pemusnahan.index') }}" class="breadcrumb-link">Pemusnahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $laporan->id_pemusnahan }}</span>
                </nav>
            </div>

            @php
                $status = strtolower($laporan->status);
                $approval = $laporan->decision_status;

                $badge =
                    $approval === 'ditolak'
                        ? 'show-badge-danger'
                        : ($status === 'selesai'
                            ? 'show-badge-success'
                            : ($status === 'sedang dimusnahkan'
                                ? 'show-badge-warning'
                                : 'show-badge-secondary'));
            @endphp


            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">
                        {{ optional($laporan->aset)->nama_aset ?? 'Aset Tidak Diketahui' }}
                    </h2>

                    <p class="show-info-subtitle">
                        {{ optional(optional($laporan->aset)->gedung)->nama_gedung ?? '-' }}
                        -
                        {{ optional(optional($laporan->aset)->ruangan)->nama_ruangan ?? '-' }}
                    </p>
                </div>

                <div class="show-info-right text-end">
                    <span class="show-badge {{ $badge }}">
                        {{ $approval === 'ditolak' ? 'Ditolak' : ucfirst($laporan->status) }}
                    </span>

                    <div class="text-end mt-2">

                        <div class="fw-bold show-info-cost">
                            Rp{{ number_format($laporan->biaya_keluar ?? 0, 0, ',', '.') }}
                        </div>
                        <small class="text-muted d-block">Biaya Keluar</small>

                        <div class="fw-bold text-success mt-1">
                            Rp{{ number_format($laporan->nilai_masuk ?? 0, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">Nilai Masuk</small>

                    </div>

                </div>
            </div>


            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-fire text-danger"></i> Informasi Pemusnahan
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID Pemusnahan</th>
                        <td>{{ $laporan->id_pemusnahan }}</td>
                    </tr>
                    <tr>
                        <th>ID Aset</th>
                        <td>{{ $laporan->id_aset ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pemusnahan</th>
                        <td>{{ optional($laporan->tanggal_pemusnahan)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Metode</th>
                        <td>{{ $laporan->metode }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $laporan->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>


            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-check-circle text-success"></i> Approval Workflow
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Status Approval</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $laporan->decision_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Requested By</th>
                        <td>{{ optional($laporan->requester)->name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Decided By</th>
                        <td>{{ optional($laporan->decider)->name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Decided At</th>
                        <td>{{ optional($laporan->decided_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>


            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-user-cog text-info"></i> Pelaksana Pemusnahan
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Tipe Pelaksana</th>
                        <td>{{ ucfirst($laporan->pelaksana_type ?? '-') }}</td>
                    </tr>
                    <tr>
                        <th>Vendor</th>
                        <td>{{ optional($laporan->vendor)->nama_perusahaan ?? '-' }}</td>
                    </tr>
                </table>
            </div>


            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-paperclip text-secondary"></i> Lampiran
                </h5>

                @if ($laporan->lampiran)
                    <a href="{{ asset('storage/' . $laporan->lampiran) }}" target="_blank" class="btn btn-outline-primary">
                        Download Lampiran
                    </a>
                @else
                    <span class="text-muted">Tidak ada lampiran</span>
                @endif
            </div>


            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($laporan->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($laporan->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>


            <div class="show-action">
                <a href="{{ route('laporan_pemusnahan.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('laporan_pemusnahan.edit', $laporan->id_pemusnahan) }}" class="btn btn-primary">
                        Edit
                    </a>

                    <form action="{{ route('laporan_pemusnahan.destroy', $laporan->id_pemusnahan) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
@endsection
