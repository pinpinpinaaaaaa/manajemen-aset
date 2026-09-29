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
    .anggaran-stat-card.teal   { border-left: 4px solid #0891b2; }
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
    .anggaran-stat-card.teal   .anggaran-stat-value { color: #0891b2; }
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
            <a href="{{ route('anggaran-rkat.export', ['tahun' => $tahun]) }}"
               class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAnggaran">
                <i class="fas fa-plus me-1"></i> Tambah Pos Anggaran
            </button>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    @php
        $pct      = $totalAnggaran > 0 ? ($totalKeluar / $totalAnggaran * 100) : 0;
        $pctClass = $pct >= 90 ? 'kritis' : ($pct >= 60 ? 'sedang' : 'aman');
    @endphp
    <div class="anggaran-stat-wrap">
        <div class="anggaran-stat-card yellow">
            <div class="anggaran-stat-label"><i class="fas fa-file-invoice-dollar me-1"></i> Total Anggaran {{ $tahun }}</div>
            <div class="anggaran-stat-value">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</div>
        </div>
        <div class="anggaran-stat-card green">
            <div class="anggaran-stat-label"><i class="fas fa-arrow-up me-1"></i> Realisasi Keluar</div>
            <div class="anggaran-stat-value">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
            <span class="persen-badge {{ $pctClass }}">{{ number_format($pct, 1) }}%</span>
        </div>
        <div class="anggaran-stat-card blue">
            <div class="anggaran-stat-label"><i class="fas fa-wallet me-1"></i> Sisa Anggaran</div>
            <div class="anggaran-stat-value" style="{{ $totalSisa < 0 ? 'color:#dc2626' : '' }}">
                Rp {{ number_format($totalSisa, 0, ',', '.') }}
            </div>
        </div>
        <div class="anggaran-stat-card teal">
            <div class="anggaran-stat-label"><i class="fas fa-hand-holding-usd me-1"></i> PNBP / Hasil Jual Aset</div>
            <div class="anggaran-stat-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
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
                    <th style="text-align:center">No</th>
                    <th style="text-align:center">COA</th>
                    <th style="text-align:center">Jenis Pengeluaran</th>
                    <th style="text-align:center">LK</th>
                    <th style="text-align:center">Kode Anggaran</th>
                    <th style="text-align:center">Uraian Program Kerja</th>
                    <th style="text-align:center">Anggaran (Rp)</th>
                    <th style="text-align:center">Real. Keluar (Rp)</th>
                    <th style="text-align:center">PNBP / Masuk (Rp)</th>
                    <th style="text-align:center">Sisa (Rp)</th>
                    <th style="text-align:center">Progres</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    @php
                        $keluar = (float) ($row->total_keluar ?? 0);
                        $masuk  = (float) ($row->total_masuk  ?? 0);
                        $a      = (float) $row->anggaran;
                        $s      = $a - $keluar;
                        $p      = $a > 0 ? ($keluar / $a * 100) : 0;
                        $pClass = $p >= 90 ? 'kritis' : ($p >= 60 ? 'sedang' : '');
                    @endphp
                    <tr data-search="{{ strtolower(($row->kode_coa ?? '') . ' ' . $row->kode_kegiatan . ' ' . $row->nama_kegiatan . ' ' . $row->coa_pos) }}">
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td style="text-align:center;white-space:nowrap;font-family:monospace;font-size:13px;font-weight:600;color:#0e7490">
                            {{ $row->kode_coa ?? '—' }}
                        </td>
                        <td style="text-align:center">{{ $row->coa_pos }}</td>
                        <td style="text-align:center">
                            @if($row->laporan_keuangan)
                                <span class="badge" style="{{ $row->laporan_keuangan === 'IS' ? 'background:#dbeafe;color:#1d4ed8' : 'background:#fef9c3;color:#854d0e' }}">
                                    {{ $row->laporan_keuangan }}
                                </span>
                            @else
                                <span style="color:#d1d5db">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;white-space:nowrap;font-family:monospace;font-size:13px">{{ $row->kode_kegiatan }}</td>
                        <td style="text-align:center">{{ $row->nama_kegiatan }}</td>
                        <td style="text-align:center;font-variant-numeric:tabular-nums">{{ number_format($a, 0, ',', '.') }}</td>
                        <td style="text-align:center;font-variant-numeric:tabular-nums">{{ number_format($keluar, 0, ',', '.') }}</td>
                        <td style="text-align:center;font-variant-numeric:tabular-nums;color:{{ $masuk > 0 ? '#0891b2' : '#9ca3af' }}">
                            {{ $masuk > 0 ? number_format($masuk, 0, ',', '.') : '—' }}
                        </td>
                        <td style="text-align:center;font-variant-numeric:tabular-nums">
                            @if ($s < 0)
                                <span style="color:#dc2626;font-weight:600">{{ number_format($s, 0, ',', '.') }}</span>
                                <span class="badge bg-danger ms-1" style="font-size:10px;vertical-align:middle">OVER</span>
                            @else
                                {{ number_format($s, 0, ',', '.') }}
                            @endif
                        </td>
                        <td style="text-align:center">
                            <div class="progress-wrap">
                                <span class="progress-pct">{{ number_format($p, 1) }}%</span>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill {{ $pClass }}" style="width:{{ min($p, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('anggaran-rkat.show', $row->id) }}"
                                   class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <button class="btn btn-sm btn-warning" title="Edit"
                                    data-id="{{ $row->id }}"
                                    data-tahun="{{ $row->tahun }}"
                                    data-kode-coa="{{ $row->kode_coa ?? '' }}"
                                    data-laporan="{{ $row->laporan_keuangan ?? '' }}"
                                    data-kode="{{ $row->kode_kegiatan }}"
                                    data-pos="{{ $row->coa_pos }}"
                                    data-nama="{{ $row->nama_kegiatan }}"
                                    data-anggaran="{{ $row->anggaran }}"
                                    data-jan="{{ $row->rencana_jan ?? 0 }}"
                                    data-feb="{{ $row->rencana_feb ?? 0 }}"
                                    data-mar="{{ $row->rencana_mar ?? 0 }}"
                                    data-apr="{{ $row->rencana_apr ?? 0 }}"
                                    data-mei="{{ $row->rencana_mei ?? 0 }}"
                                    data-jun="{{ $row->rencana_jun ?? 0 }}"
                                    data-jul="{{ $row->rencana_jul ?? 0 }}"
                                    data-agu="{{ $row->rencana_agu ?? 0 }}"
                                    data-sep="{{ $row->rencana_sep ?? 0 }}"
                                    data-okt="{{ $row->rencana_okt ?? 0 }}"
                                    data-nov="{{ $row->rencana_nov ?? 0 }}"
                                    data-des="{{ $row->rencana_des ?? 0 }}"
                                    onclick="bukaModalEdit(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('anggaran-rkat.destroy', $row->id) }}"
                                      onsubmit="return confirm('Hapus pos anggaran ini? Semua realisasinya juga akan dihapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada pos anggaran untuk tahun {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($data->isNotEmpty())
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right" style="text-align:right">TOTAL</td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums;color:{{ $totalMasuk > 0 ? '#0891b2' : '#9ca3af' }}">
                        {{ $totalMasuk > 0 ? number_format($totalMasuk, 0, ',', '.') : '—' }}
                    </td>
                    <td class="text-right" style="font-variant-numeric:tabular-nums">
                        @if ($totalSisa < 0)
                            <span style="color:#dc2626;font-weight:700">{{ number_format($totalSisa, 0, ',', '.') }}</span>
                            <span class="badge bg-danger ms-1" style="font-size:10px;vertical-align:middle">OVER</span>
                        @else
                            {{ number_format($totalSisa, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>
                        <span class="progress-pct">{{ number_format($pct, 1) }}%</span>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill {{ $pctClass }}" style="width:{{ min($pct, 100) }}%"></div>
                        </div>
                    </td>
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
                            <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $tahun) }}" min="{{ now()->year }}" max="{{ now()->year + 5 }}" required>
                            <div class="form-text">Tahun berjalan atau tahun depan.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kegiatan" class="form-control" value="{{ old('kode_kegiatan') }}" placeholder="Contoh: GA-001" maxlength="50" required>
                            <div class="form-text">Format bebas, contoh: <code>GA-001</code>, <code>IT-2026-03</code></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Klasifikasi Lap. Keuangan</label>
                            <select name="laporan_keuangan" id="addLaporanKeuangan" class="form-select">
                                <option value="">— Pilih (opsional) —</option>
                                <option value="IS" {{ old('laporan_keuangan') === 'IS' ? 'selected' : '' }}>I/S — Income Statement</option>
                                <option value="BS" {{ old('laporan_keuangan') === 'BS' ? 'selected' : '' }}>B/S — Balance Sheet</option>
                            </select>
                            <div class="form-text">
                                <strong>I/S</strong> = Laporan Laba Rugi (beban operasional, pendapatan) &nbsp;·&nbsp;
                                <strong>B/S</strong> = Neraca (aset, kewajiban, ekuitas)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode COA</label>
                            <input type="text" name="kode_coa" id="addKodeCoa" class="form-control" value="{{ old('kode_coa') }}"
                                   placeholder="Mis. 5.1.02" maxlength="30" oninput="autoFillCoa('add')">
                            <div class="form-text">Ketik kode → nama jenis terisi otomatis.</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Jenis Pengeluaran (COA) <span class="text-danger">*</span></label>
                            <input type="text" name="coa_pos" id="addCoaPos" class="form-control" value="{{ old('coa_pos') }}"
                                   placeholder="Mis. Beban Pemeliharaan, Beban Pengadaan ATK" maxlength="255" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" id="addNamaKegiatan" class="form-control" value="{{ old('nama_kegiatan') }}"
                                   placeholder="Mis. Maintenance AC Gedung A Lantai 3, Pengadaan ATK 2026" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="anggaran" id="addAnggaran" class="form-control rupiah-input"
                                   value="{{ old('anggaran') ? number_format((int)old('anggaran'), 0, ',', '.') : '' }}"
                                   placeholder="0" inputmode="numeric" required
                                   oninput="formatRupiah(this); hitungSubtotalAdd()">
                        </div>

                        {{-- Rencana Bulanan --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <div class="fw-semibold mb-2" style="font-size:13px;color:#374151">
                                <i class="fas fa-calendar-alt me-1 text-muted"></i> Rencana Penyerapan per Bulan
                                <small class="text-muted fw-normal">(opsional, boleh sebagian)</small>
                            </div>
                            @if ($errors->has('rencana'))
                                <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:13px">{{ $errors->first('rencana') }}</div>
                            @endif
                            <div class="row g-2">
                                @foreach ([
                                    'jan'=>'Jan','feb'=>'Feb','mar'=>'Mar','apr'=>'Apr',
                                    'mei'=>'Mei','jun'=>'Jun','jul'=>'Jul','agu'=>'Agu',
                                    'sep'=>'Sep','okt'=>'Okt','nov'=>'Nov','des'=>'Des',
                                ] as $key => $label)
                                    @php $oldVal = (int) old('rencana_'.$key, 0); @endphp
                                    <div class="col-md-3 col-6">
                                        <label class="form-label mb-1" style="font-size:12px;color:#6b7280">{{ $label }}</label>
                                        <input type="text" name="rencana_{{ $key }}" class="form-control form-control-sm rencana-add rupiah-input"
                                               value="{{ $oldVal > 0 ? number_format($oldVal, 0, ',', '.') : '' }}"
                                               placeholder="0" inputmode="numeric"
                                               oninput="formatRupiah(this); hitungSubtotalAdd()">
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2" style="font-size:13px" id="subtotalAddWrap">
                                Sub-total rencana: <strong id="subtotalAddVal">Rp 0</strong>
                            </div>
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
                            <input type="number" name="tahun" id="editTahun" class="form-control" min="{{ now()->year }}" max="{{ now()->year + 5 }}" required>
                            <div class="form-text">Tahun berjalan atau tahun depan.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kegiatan" id="editKode" class="form-control" placeholder="Contoh: GA-001" maxlength="50" required>
                            <div class="form-text">Format bebas, contoh: <code>GA-001</code>, <code>IT-2026-03</code></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Klasifikasi Lap. Keuangan</label>
                            <select name="laporan_keuangan" id="editLaporanKeuangan" class="form-select">
                                <option value="">— Pilih (opsional) —</option>
                                <option value="IS">I/S — Income Statement</option>
                                <option value="BS">B/S — Balance Sheet</option>
                            </select>
                            <div class="form-text">
                                <strong>I/S</strong> = Laporan Laba Rugi (beban operasional, pendapatan) &nbsp;·&nbsp;
                                <strong>B/S</strong> = Neraca (aset, kewajiban, ekuitas)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode COA</label>
                            <input type="text" name="kode_coa" id="editKodeCoa" class="form-control"
                                   placeholder="Mis. 5.1.02" maxlength="30" oninput="autoFillCoa('edit')">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Jenis Pengeluaran (COA) <span class="text-danger">*</span></label>
                            <input type="text" name="coa_pos" id="editCoaPos" class="form-control"
                                   placeholder="Mis. Beban Pemeliharaan, Beban Pengadaan ATK" maxlength="255" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" id="editNamaKegiatan" class="form-control"
                                   placeholder="Mis. Maintenance AC Gedung A Lantai 3, Pengadaan ATK 2026" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Anggaran (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="anggaran" id="editAnggaran" class="form-control rupiah-input"
                                   placeholder="0" inputmode="numeric" required
                                   oninput="formatRupiah(this); hitungSubtotalEdit()">
                        </div>

                        {{-- Rencana Bulanan --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <div class="fw-semibold mb-2" style="font-size:13px;color:#374151">
                                <i class="fas fa-calendar-alt me-1 text-muted"></i> Rencana Penyerapan per Bulan
                                <small class="text-muted fw-normal">(opsional, boleh sebagian)</small>
                            </div>
                            <div class="row g-2">
                                @foreach ([
                                    'jan'=>'Jan','feb'=>'Feb','mar'=>'Mar','apr'=>'Apr',
                                    'mei'=>'Mei','jun'=>'Jun','jul'=>'Jul','agu'=>'Agu',
                                    'sep'=>'Sep','okt'=>'Okt','nov'=>'Nov','des'=>'Des',
                                ] as $key => $label)
                                    <div class="col-md-3 col-6">
                                        <label class="form-label mb-1" style="font-size:12px;color:#6b7280">{{ $label }}</label>
                                        <input type="text" name="rencana_{{ $key }}" id="editRencana{{ ucfirst($key) }}" class="form-control form-control-sm rencana-edit rupiah-input"
                                               placeholder="0" inputmode="numeric"
                                               oninput="formatRupiah(this); hitungSubtotalEdit()">
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2" style="font-size:13px" id="subtotalEditWrap">
                                Sub-total rencana: <strong id="subtotalEditVal">Rp 0</strong>
                            </div>
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
const bulanKeysEdit = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];
const COA_MAP = @json($coaKodeMap);

// ── Format ribuan ──────────────────────────────────────────────────────────
function formatRupiah(input) {
    const pos   = input.selectionStart;
    const before = input.value.length;
    const raw   = input.value.replace(/\D/g, '');
    input.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
    // Jaga posisi kursor setelah penambahan titik
    const diff = input.value.length - before;
    try { input.setSelectionRange(pos + diff, pos + diff); } catch(_) {}
}

function getRaw(el) {
    return parseInt((el.value || '0').replace(/\./g, ''), 10) || 0;
}

// Strip titik ribuan sebelum kirim form agar server terima angka bersih
function stripRupiahForm(form) {
    form.querySelectorAll('.rupiah-input').forEach(el => {
        el.value = el.value.replace(/\./g, '');
    });
}

document.getElementById('formEditAnggaran')
    ?.closest('form, [id=formEditAnggaran]');

document.addEventListener('DOMContentLoaded', () => {
    // Strip saat submit
    document.querySelectorAll('form').forEach(f => {
        f.addEventListener('submit', () => stripRupiahForm(f));
    });
});

// ── COA auto-fill ──────────────────────────────────────────────────────────
function autoFillCoa(prefix) {
    const kodeEl = document.getElementById(prefix + 'KodeCoa');
    const posEl  = document.getElementById(prefix + 'CoaPos');
    const kode   = kodeEl ? kodeEl.value.trim() : '';
    if (kode && COA_MAP[kode] && posEl) posEl.value = COA_MAP[kode];
}

// ── Buka modal edit ────────────────────────────────────────────────────────
function bukaModalEdit(btn) {
    const d = btn.dataset;
    document.getElementById('formEditAnggaran').action = '/anggaran-rkat/' + d.id;
    document.getElementById('editTahun').value   = d.tahun;
    document.getElementById('editKodeCoa').value = d.kodeCoa || '';
    document.getElementById('editKode').value    = d.kode;
    const lkEl = document.getElementById('editLaporanKeuangan');
    if (lkEl) lkEl.value = d.laporan || '';
    document.getElementById('editCoaPos').value        = d.pos;
    document.getElementById('editNamaKegiatan').value  = d.nama || '';

    // Format anggaran dengan titik ribuan
    const ang = parseInt(d.anggaran || 0);
    document.getElementById('editAnggaran').value = ang ? ang.toLocaleString('id-ID') : '';

    bulanKeysEdit.forEach(k => {
        const el  = document.getElementById('editRencana' + k.charAt(0).toUpperCase() + k.slice(1));
        const val = parseInt(d[k] || 0);
        if (el) el.value = val > 0 ? val.toLocaleString('id-ID') : '';
    });

    hitungSubtotalEdit();
    new bootstrap.Modal(document.getElementById('modalEditAnggaran')).show();
}

// ── Subtotal rencana ───────────────────────────────────────────────────────
function fmt(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function hitungSubtotalAdd() {
    const ang   = getRaw(document.getElementById('addAnggaran'));
    const total = Array.from(document.querySelectorAll('.rencana-add'))
        .reduce((s, el) => s + getRaw(el), 0);
    const el = document.getElementById('subtotalAddVal');
    el.textContent = fmt(total);
    el.style.color = total > ang ? '#dc2626' : '#16a34a';
}

function hitungSubtotalEdit() {
    const ang   = getRaw(document.getElementById('editAnggaran'));
    const total = Array.from(document.querySelectorAll('.rencana-edit'))
        .reduce((s, el) => s + getRaw(el), 0);
    const el = document.getElementById('subtotalEditVal');
    el.textContent = fmt(total);
    el.style.color = total > ang ? '#dc2626' : '#16a34a';
}

// ── Search ─────────────────────────────────────────────────────────────────
document.getElementById('searchRkat').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabelRkat tbody tr[data-search]').forEach(tr => {
        tr.style.display = (tr.dataset.search || '').includes(q) ? '' : 'none';
    });
});

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalTambahAnggaran')).show();
    });
@endif
</script>
@endsection
