@extends('layouts.app')

@section('title', 'Detail Pemindahan Aset')

@section('content')

    <main class="main-content">
        <div class="content-padding show-page">

            <div class="page-header">
                <h1 class="page-title">Detail Pemindahan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pemindahan_aset.index') }}" class="breadcrumb-link">Pemindahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $pemindahan->id_pemindahan }}</span>
                </nav>
            </div>

            @php
                $totalAset = $pemindahan->details->count();

                $sudahDipindah = $pemindahan->details->where('status', 'Sudah dipindahkan')->count();

                $belumDipindah = $totalAset - $sudahDipindah;

                $totalBiaya = $pemindahan->details->sum('biaya');

                $statusPemindahan = $belumDipindah == 0 ? 'Sudah dipindahkan' : 'Belum dipindahkan';
            @endphp

            <div class="show-info-card">

                <div class="show-info-left">
                    <h2 class="show-info-title">
                        {{ $pemindahan->id_pemindahan }}
                    </h2>

                    <p class="show-info-subtitle">
                        {{ $totalAset }} aset diajukan untuk dipindahkan
                    </p>
                </div>

                <div class="show-info-right text-end">

                    <span
                        class="show-badge
            {{ $pemindahan->decision_status == 'disetujui'
                ? 'show-badge-success'
                : ($pemindahan->decision_status == 'ditolak'
                    ? 'show-badge-danger'
                    : 'show-badge-warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $pemindahan->decision_status)) }}
                    </span>

                    <div class="mt-2">
                        <span
                            class="show-badge {{ $statusPemindahan == 'Sudah dipindahkan' ? 'show-badge-success' : 'show-badge-secondary' }}">
                            {{ $statusPemindahan }}
                        </span>
                    </div>

                    <div class="fw-bold mt-2 show-info-cost">
                        Rp{{ number_format($totalBiaya, 0, ',', '.') }}
                    </div>

                    <small class="text-muted">
                        Total Biaya Pemindahan
                    </small>

                </div>

            </div>

            <div class="show-detail-card">

                <h5 class="fw-bold mb-3">
                    <i class="fas fa-exchange-alt text-primary"></i>
                    Informasi Pemindahan
                </h5>

                <table class="show-detail-table">

                    <tr>
                        <th>ID Pemindahan</th>
                        <td>{{ $pemindahan->id_pemindahan }}</td>
                    </tr>

                    <tr>
                        <th>Alasan</th>
                        <td>{{ $pemindahan->alasan }}</td>
                    </tr>

                    <tr>
                        <th>Jumlah Aset</th>
                        <td>{{ $totalAset }} Aset</td>
                    </tr>

                    <tr>
                        <th>Total Biaya</th>
                        <td>
                            Rp{{ number_format($totalBiaya, 0, ',', '.') }}
                        </td>
                    </tr>

                </table>

            </div>

            <style>
                .custom-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .custom-table th {
                    background: #9ea1a3;
                    padding: 10px;
                    border: 1px solid #dee2e6;
                }

                .custom-table td {
                    padding: 10px;
                    border: 1px solid #dee2e6;
                }
            </style>

            <div class="show-detail-card">

                <h5 class="fw-bold mb-3">
                    <i class="fas fa-box text-warning"></i>
                    Detail Aset Dipindahkan
                </h5>

                <table class="custom-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Aset</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th>Pelaksana</th>
                            <th>Vendor</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($pemindahan->details as $i => $detail)
                            <tr>

                                <td>{{ $i + 1 }}</td>

                                <td>
                                    {{ $detail->aset->kode_aset ?? '-' }}
                                </td>

                                <td>
                                    {{ $detail->aset->nama_aset ?? '-' }}
                                </td>

                                <td>
                                    {{ $detail->gedungAsal->nama_gedung ?? '-' }}
                                    <br>
                                    <small>
                                        {{ $detail->ruanganAsal->nama_ruangan ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $detail->gedungTujuan->nama_gedung ?? '-' }}
                                    <br>
                                    <small>
                                        {{ $detail->ruanganTujuan->nama_ruangan ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ ucfirst($detail->pelaksana_type ?? '-') }}
                                </td>

                                <td>
                                    {{ $detail->vendor->nama_perusahaan ?? '-' }}
                                </td>

                                <td>
                                    Rp{{ number_format($detail->biaya ?? 0, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($detail->status == 'Sudah dipindahkan')
                                        <span class="badge bg-success">
                                            Sudah Dipindahkan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Belum Dipindahkan
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($pemindahan->decision_status == 'disetujui' && $detail->status == 'Belum dipindahkan')
                                        <form method="POST"
                                            action="{{ route('pemindahan_aset.pindahkan_detail', $detail->id) }}"
                                            onsubmit="return confirm('Pindahkan aset ini?')">

                                            @csrf

                                            <button class="btn btn-sm btn-info">
                                                Pindahkan
                                            </button>

                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-check-circle text-success"></i> Approval Workflow
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Status Approval</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $pemindahan->decision_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Requested By</th>
                        <td>{{ $pemindahan->requester->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Decided By</th>
                        <td>{{ $pemindahan->approver->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Decided At</th>
                        <td>{{ optional($pemindahan->decided_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock text-dark"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($pemindahan->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($pemindahan->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="show-action">
                <a href="{{ route('pemindahan_aset.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('pemindahan_aset.edit', $pemindahan->id_pemindahan) }}" class="btn btn-primary">
                        Edit
                    </a>

                    <form action="{{ route('pemindahan_aset.destroy', $pemindahan->id_pemindahan) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
@endsection
