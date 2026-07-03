@extends('layouts.app')

@section('title', 'Detail Pengajuan Pemeliharaan Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding show-page">

            <div class="page-header">
                <h1 class="page-title">Detail Pengajuan Pemeliharaan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('maintenance.index') }}" class="breadcrumb-link">Pemeliharaan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $maintenance->id_maintenance }}</span>
                </nav>
            </div>

            <div class="show-info-card">
                <div class="show-info-left">
                    @php
                        $totalAset = $maintenance->details->count();

                        $totalBiaya = $maintenance->details->sum('biaya');

                        $selesai = $maintenance->details->where('status', 'Selesai')->count();

                        $progress = $selesai == $totalAset ? 'Selesai' : 'Berjalan';
                    @endphp

                    <h2 class="show-info-title">
                        {{ $maintenance->id_maintenance }}
                    </h2>

                    <p class="show-info-subtitle">
                        {{ $totalAset }} aset diajukan maintenance
                    </p>
                </div>
                <div class="show-info-right text-end">
                    <span
                        class="show-badge 
                    {{ strtolower($maintenance->status) === 'selesai'
                        ? 'show-badge-success'
                        : (strtolower($maintenance->status) === 'sedang diperbaiki'
                            ? 'show-badge-warning'
                            : 'show-badge-danger') }}">
                        {{ ucfirst($maintenance->status) }}
                    </span>
                    <div class="fw-bold mt-2 show-info-cost">
                        Rp{{ number_format($maintenance->biaya ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Total Biaya</small>
                </div>
            </div>

            <div class="show-detail-card">

                <h5 class="fw-bold mb-3">
                    <i class="fas fa-tools text-warning"></i>
                    Detail Aset Maintenance
                </h5>

                <table class="custom-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Aset</th>
                            <th>Lokasi</th>
                            <th>Kerusakan</th>
                            <th>Dok. Before</th>
                            <th>Dok. After</th>
                            <th>Lampiran</th>
                            <th>Pelaksana</th>
                            <th>Vendor</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($maintenance->details as $i => $detail)
                            <tr>

                                <td>{{ $i + 1 }}</td>

                                <td>
                                    {{ $detail->aset->kode_aset ?? '-' }}
                                    <hr>
                                    {{ $detail->aset->nama_aset ?? '-' }}
                                </td>

                                <td>
                                    {{ $detail->aset->ruangan->gedung->nama_gedung ?? '-' }}
                                    <hr>
                                    {{ $detail->aset->ruangan->nama_ruangan ?? '-' }}
                                </td>

                                <td>
                                    {{ $detail->kerusakan }}
                                </td>

                                <td>
                                    @if ($detail->foto_before)
                                        <a href="{{ asset('storage/' . $detail->foto_before) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $detail->foto_before) }}" width="60">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($detail->foto_after)
                                        <a href="{{ asset('storage/' . $detail->foto_after) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $detail->foto_after) }}" width="60">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($detail->lampiran)
                                        <a href="{{ asset('storage/' . $detail->lampiran) }}" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            Lampiran
                                        </a>
                                    @else
                                        -
                                    @endif
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

                                    @php
                                        $color = match ($detail->status) {
                                            'Perlu Perbaikan' => 'danger',
                                            'Sedang Diperbaiki' => 'warning',
                                            'Selesai' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ $detail->status }}
                                    </span>

                                </td>
                                <td>

                                    {{-- MULAI --}}
                                    @if ($maintenance->decision_status == 'disetujui' && $detail->status == 'Perlu Perbaikan')
                                        <form action="{{ route('maintenance.mulaiDetail', $detail->id) }}" method="POST">

                                            @csrf

                                            <button class="btn btn-sm btn-info">
                                                Mulai
                                            </button>

                                        </form>

                                        {{-- SELESAI --}}
                                    @elseif($detail->status == 'Sedang Diperbaiki')
                                        <button type="button" class="btn btn-sm btn-success btnSelesaiDetail"
                                            data-id="{{ $detail->id }}">
                                            Selesai
                                        </button>
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

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
                <h5 class="fw-bold mb-3"><i class="fas fa-check-circle text-success"></i> Approval Workflow</h5>
                <table class="show-detail-table">
                    <tr>
                        <th>Status Approval</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $maintenance->decision_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Requested By</th>
                        <td>{{ $maintenance->requested_by }}</td>
                    </tr>
                    <tr>
                        <th>Decided By</th>
                        <td>{{ $maintenance->decided_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Decided At</th>
                        <td>{{ optional($maintenance->decided_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="show-detail-card">
                <h5 class="fw-bold mb-3"><i class="fas fa-clock text-dark"></i> Metadata</h5>
                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($maintenance->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($maintenance->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="show-action">
                <a href="{{ route('maintenance.laporan') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('maintenance.edit', $maintenance->id_maintenance) }}" class="btn btn-primary">
                        Edit
                    </a>

                    <form action="{{ route('maintenance.destroy', $maintenance->id_maintenance) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>

        </div>
        <div class="modal fade" id="selesaiModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST" id="selesaiForm" enctype="multipart/form-data">

                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Selesaikan Maintenance
                            </h5>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label>Biaya Maintenance</label>

                                <input type="text" id="biaya_display" class="form-control" placeholder="Rp 0">

                                <input type="hidden" id="biaya" name="biaya">
                            </div>

                            <div class="mb-3">
                                <label>Pelaksana</label>
                                <select name="pelaksana_type" id="pelaksana_type" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="internal">Internal</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>

                            <div class="mb-3" id="vendorArea" style="display:none">

                                <label>Vendor</label>

                                <select name="id_vendor" id="id_vendor" class="form-control">

                                    <option value="">
                                        -- Pilih Vendor --
                                    </option>

                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id_vendor }}">
                                            {{ $v->nama_perusahaan }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="mb-3">
                                <label>Foto Setelah Perbaikan</label>
                                <input type="file" name="foto_after" class="form-control" accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label>Catatan</label>
                                <textarea name="catatan" class="form-control" rows="3"></textarea>
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-success">
                                Selesaikan
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalEl = document.getElementById('selesaiModal');
            const modal = new bootstrap.Modal(modalEl);

            document.querySelectorAll('.btnSelesaiDetail').forEach(btn => {

                btn.addEventListener('click', function() {

                    const id = this.dataset.id;

                    document.getElementById('selesaiForm').action =
                        `/maintenance/detail/${id}/selesai`;

                    modal.show();
                });

            });

            // tampilkan vendor jika vendor dipilih
            const pelaksana = document.getElementById('pelaksana_type');
            const vendorArea = document.getElementById('vendorArea');

            pelaksana.addEventListener('change', function() {

                if (this.value === 'vendor') {
                    vendorArea.style.display = 'block';
                } else {
                    vendorArea.style.display = 'none';
                    document.getElementById('id_vendor').value = '';
                }

            });

            // format rupiah
            const biayaDisplay = document.getElementById('biaya_display');
            const biayaHidden = document.getElementById('biaya');

            biayaDisplay.addEventListener('input', function() {

                let angka = this.value.replace(/\D/g, '');

                biayaHidden.value = angka;

                this.value = angka ?
                    new Intl.NumberFormat('id-ID').format(angka) :
                    '';
            });

        });
    </script>
@endsection
