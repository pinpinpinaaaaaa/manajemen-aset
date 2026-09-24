@extends('layouts.app')

@section('title', 'Kelola Anggaran RKAT')

@section('content')
<style>
    .anggaran-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .anggaran-page-header .page-header { margin-bottom: 0; }
    .anggaran-page-header .page-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin-top: 4px;
    }
    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .anggaran-stat-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }
    .anggaran-stat-card {
        flex: 1 1 200px;
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
    }
    .anggaran-stat-card.yellow { border-left: 4px solid #ebca56; }
    .anggaran-stat-card.green  { border-left: 4px solid #16a34a; }
    .anggaran-stat-card.blue   { border-left: 4px solid #2563eb; }
    .anggaran-stat-card.orange { border-left: 4px solid #d97706; }
    .anggaran-stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }
    .anggaran-stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }
    .anggaran-stat-card.yellow .anggaran-stat-value { color: #b45309; }
    .anggaran-stat-card.green  .anggaran-stat-value { color: #16a34a; }
    .anggaran-stat-card.blue   .anggaran-stat-value { color: #2563eb; }
    .anggaran-stat-card.orange .anggaran-stat-value { color: #d97706; }
    .persen-badge {
        display: inline-block;
        margin-top: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
    }
    .persen-badge.aman    { background: #dcfce7; color: #16a34a; }
    .persen-badge.sedang  { background: #fef9c3; color: #854d0e; }
    .persen-badge.kritis  { background: #fee2e2; color: #dc2626; }
    .tabel-title { font-size: 16px; font-weight: 600; color: #111; margin-bottom: 12px; display: block; }
    .text-right { text-align: right !important; }
    .coa-badge {
        display: inline-block;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .progress-wrap { min-width: 80px; }
    .progress-bar-bg {
        background: #e5e7eb;
        border-radius: 99px;
        height: 6px;
        margin-top: 4px;
    }
    .progress-bar-fill {
        background: #2563eb;
        border-radius: 99px;
        height: 6px;
    }
    .progress-bar-fill.kritis { background: #dc2626; }
    .progress-bar-fill.sedang { background: #d97706; }
    .progress-pct { font-size: 11px; color: #6b7280; white-space: nowrap; }
    .search-bar-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .search-bar-wrap input {
        flex: 1;
        height: 38px;
        padding: 0 14px 0 38px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.099zm-5.242 1.406a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11'/%3E%3C/svg%3E") no-repeat 12px center;
        outline: none;
        transition: border-color .15s;
        max-width: 460px;
    }
    .search-bar-wrap input:focus { border-color: #2563eb; }
    .total-row td { font-weight: 700; background: #f8fafc; border-top: 2px solid #e2e8f0; }
    @media (max-width: 640px) {
        .anggaran-page-header { flex-direction: column; }
        .anggaran-stat-card { flex: 1 1 100%; }
        .header-actions { width: 100%; flex-wrap: wrap; }
    }
</style>

<main class="main-content">
<div class="content-padding">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Page Header ── --}}
    <div class="anggaran-page-header">
        <div class="page-header">
            <h1 class="page-title">Kelola Anggaran RKAT</h1>
            <p class="page-subtitle">Rencana Kerja dan Anggaran Tahunan — pengelolaan pos anggaran & realisasi</p>
            <nav class="breadcrumb" style="margin-top:6px">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Anggaran RKAT</span>
            </nav>
        </div>
        <div class="header-actions">
            <form method="GET" action="{{ route('anggaran-rkat.index') }}" class="d-flex gap-2">
                <select name="tahun" class="form-select" style="width:auto;height:38px;font-size:14px;" onchange="this.form.submit()">
                    @foreach ($tahunList as $t)
                        <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
                    @endforeach
                    @unless ($tahunList->contains(now()->year))
                        <option value="{{ now()->year }}" @selected(now()->year == $tahun)>{{ now()->year }}</option>
                    @endunless
                </select>
            </form>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAnggaran">
                <i class="fas fa-plus me-1"></i> Tambah Pos Anggaran
            </button>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    @php
        $pct      = $totalAnggaran > 0 ? ($totalRealisasi / $totalAnggaran * 100) : 0;
        $pctClass = $pct >= 90 ? 'kritis' : ($pct >= 60 ? 'sedang' : 'aman');
    @endphp
    <div class="anggaran-stat-wrap">
        <div class="anggaran-stat-card yellow">
            <div class="anggaran-stat-label"><i class="fas fa-file-invoice-dollar me-1"></i> Total Anggaran {{ $tahun }}</div>
            <div class="anggaran-stat-value">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</div>
        </div>
        <div class="anggaran-stat-card green">
            <div class="anggaran-stat-label"><i class="fas fa-check-double me-1"></i> Total Realisasi</div>
            <div class="anggaran-stat-value">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
            <span class="persen-badge {{ $pctClass }}">{{ number_format($pct, 1) }}%</span>
        </div>
        <div class="anggaran-stat-card blue">
            <div class="anggaran-stat-label"><i class="fas fa-wallet me-1"></i> Sisa Anggaran</div>
            <div class="anggaran-stat-value">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
        </div>
        <div class="anggaran-stat-card orange">
            <div class="anggaran-stat-label"><i class="fas fa-list-ul me-1"></i> Jumlah Pos</div>
            <div class="anggaran-stat-value">{{ $data->count() }} pos</div>
        </div>
    </div>

    {{-- ── Search ── --}}
    <div class="search-bar-wrap">
        <input type="text" id="searchRkat" placeholder="Cari kode, nama kegiatan, atau COA...">
    </div>

    {{-- ── Table ── --}}
    <span class="tabel-title">Daftar Pos Anggaran {{ $tahun }}</span>

    <div class="table-container">
        <table class="data-table" id="tabelRkat">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th style="text-align:left">Nama Kegiatan</th>
                    <th style="text-align:left">COA</th>
                    <th class="text-right">Anggaran (Rp)</th>
                    <th class="text-right">Realisasi (Rp)</th>
                    <th>Progres</th>
                    <th class="text-right">Sisa (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    @php
                        $r      = (float) ($row->realisasis_sum_jumlah ?? 0);
                        $a      = (float) $row->anggaran;
                        $s      = $a - $r;
                        $p      = $a > 0 ? ($r / $a * 100) : 0;
                        $pClass = $p >= 90 ? 'kritis' : ($p >= 60 ? 'sedang' : '');
                    @endphp
                    <tr data-search="{{ strtolower($row->kode_kegiatan . ' ' . $row->nama_kegiatan . ' ' . $row->coa_pos . ' ' . $row->coa_sub) }}">
                        <td>{{ $i + 1 }}</td>
                        <td style="white-space:nowrap;font-family:monospace;font-size:13px">{{ $row->kode_kegiatan }}</td>
                        <td style="text-align:left">{{ $row->nama_kegiatan }}</td>
                        <td style="text-align:left">
                            <span class="coa-badge">{{ $row->coa_sub }}</span>
                            <div style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $row->coa_pos }}</div>
                        </td>
                        <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($a, 0, ',', '.') }}</td>
                        <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($r, 0, ',', '.') }}</td>
                        <td>
                            <div class="progress-wrap">
                                <span class="progress-pct">{{ number_format($p, 1) }}%</span>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill {{ $pClass }}" style="width:{{ min($p, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-right" style="font-variant-numeric:tabular-nums{{ $s < 0 ? ';color:#dc2626;font-weight:600' : '' }}">
                            {{ number_format($s, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-outline-primary" title="Edit"
                                    onclick="bukaModalEdit({{ $row->id }}, '{{ $row->tahun }}', '{{ addslashes($row->kode_kegiatan) }}', '{{ addslashes($row->coa_pos) }}', '{{ addslashes($row->coa_sub) }}', '{{ addslashes($row->nama_kegiatan) }}', {{ $row->anggaran }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('anggaran-rkat.destroy', $row->id) }}"
                                      onsubmit="return confirm('Hapus pos anggaran ini? Semua realisasinya juga akan dihapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada pos anggaran untuk tahun {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($data->isNotEmpty())
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right" style="text-align:right">TOTAL</td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                    <td>
                        <span class="progress-pct">{{ number_format($pct, 1) }}%</span>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill {{ $pctClass }}" style="width:{{ min($pct, 100) }}%"></div>
                        </div>
                    </td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>
</main>

{{-- ──────────── MODAL TAMBAH ──────────── --}}
<div class="modal fade" id="modalTambahAnggaran" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('anggaran-rkat.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Pos Anggaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $tahun) }}" min="2020" max="2100" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Kode Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kegiatan" class="form-control" value="{{ old('kode_kegiatan') }}" placeholder="Contoh: RKAT-2026-001" maxlength="50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">COA — Pos Utama <span class="text-danger">*</span></label>
                            <select name="coa_pos" id="coaPosAdd" class="form-select" required onchange="isiSubPos('coaPosAdd','coaSubAdd')">
                                <option value="">-- Pilih Pos --</option>
                                @foreach (array_keys($coaList) as $pos)
                                    <option value="{{ $pos }}" @selected(old('coa_pos') == $pos)>{{ $pos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">COA — Sub-Pos <span class="text-danger">*</span></label>
                            <select name="coa_sub" id="coaSubAdd" class="form-select" required>
                                <option value="">-- Pilih pos utama dulu --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan') }}" placeholder="Deskripsi kegiatan yang dianggarkan" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="anggaran" class="form-control" value="{{ old('anggaran') }}" placeholder="0" min="0" step="1000" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ──────────── MODAL EDIT ──────────── --}}
<div class="modal fade" id="modalEditAnggaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formEditAnggaran">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2 text-warning"></i>Edit Pos Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" id="editTahun" class="form-control" min="2020" max="2100" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Kode Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kegiatan" id="editKode" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">COA — Pos Utama <span class="text-danger">*</span></label>
                            <select name="coa_pos" id="coaPosEdit" class="form-select" required onchange="isiSubPos('coaPosEdit','coaSubEdit')">
                                <option value="">-- Pilih Pos --</option>
                                @foreach (array_keys($coaList) as $pos)
                                    <option value="{{ $pos }}">{{ $pos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">COA — Sub-Pos <span class="text-danger">*</span></label>
                            <select name="coa_sub" id="coaSubEdit" class="form-select" required>
                                <option value="">-- Pilih pos utama dulu --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" id="editNama" class="form-control" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="anggaran" id="editAnggaran" class="form-control" min="0" step="1000" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i> Perbarui</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const COA_LIST = @json($coaList);

function isiSubPos(posElId, subElId, currentSub) {
    const pos   = document.getElementById(posElId).value;
    const subEl = document.getElementById(subElId);
    const subs  = COA_LIST[pos] || [];

    subEl.innerHTML = '<option value="">-- Pilih Sub-Pos --</option>';
    subEl.disabled  = subs.length === 0;

    subs.forEach(s => {
        const opt       = document.createElement('option');
        opt.value       = s;
        opt.textContent = s;
        if (s === currentSub) opt.selected = true;
        subEl.appendChild(opt);
    });
}

function bukaModalEdit(id, tahun, kode, pos, sub, nama, anggaran) {
    document.getElementById('formEditAnggaran').action = '/anggaran-rkat/' + id;
    document.getElementById('editTahun').value    = tahun;
    document.getElementById('editKode').value     = kode;
    document.getElementById('editNama').value     = nama;
    document.getElementById('editAnggaran').value = anggaran;

    const posEl = document.getElementById('coaPosEdit');
    posEl.value = pos;
    isiSubPos('coaPosEdit', 'coaSubEdit', sub);

    new bootstrap.Modal(document.getElementById('modalEditAnggaran')).show();
}

document.getElementById('searchRkat').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabelRkat tbody tr[data-search]').forEach(tr => {
        tr.style.display = (tr.dataset.search || '').includes(q) ? '' : 'none';
    });
});

// Restore modal jika ada error validasi
@if ($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalTambahAnggaran')).show();
        @if (old('coa_pos'))
            document.getElementById('coaPosAdd').value = '{{ old('coa_pos') }}';
            isiSubPos('coaPosAdd', 'coaSubAdd', '{{ old('coa_sub') }}');
        @endif
    });
@endif
</script>
@endsection
