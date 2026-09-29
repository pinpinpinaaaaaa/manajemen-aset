@extends('layouts.app')

@section('title', 'Riwayat Realisasi')

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
    .realisasi-summary-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }
    .realisasi-summary-card {
        flex: 1 1 220px;
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
        border-left: 4px solid #d97706;
    }
    .realisasi-summary-card.blue { border-left-color: #2563eb; }
    .realisasi-summary-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }
    .realisasi-summary-value {
        font-size: 22px;
        font-weight: 700;
        color: #d97706;
        line-height: 1.2;
    }
    .realisasi-summary-card.blue .realisasi-summary-value { color: #2563eb; }
    .tabel-title { font-size: 16px; font-weight: 600; color: #111; margin-bottom: 12px; display: block; }
    .text-right { text-align: right !important; }
    .badge-kode {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
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
    @media (max-width: 640px) {
        .anggaran-page-header { flex-direction: column; }
        .realisasi-summary-card { flex: 1 1 100%; }
        .header-actions { width: 100%; }
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
            <h1 class="page-title">Riwayat Realisasi</h1>
            <p class="page-subtitle">Daftar semua transaksi realisasi anggaran RKAT</p>
            <nav class="breadcrumb" style="margin-top:6px">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Riwayat Realisasi</span>
            </nav>
        </div>
        <div class="header-actions">
            <form method="GET" action="{{ route('riwayat-realisasi.index') }}" class="d-flex gap-2">
                <select name="tahun" class="form-select" style="width:auto;height:38px;font-size:14px;" onchange="this.form.submit()">
                    @foreach ($tahunList as $t)
                        <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
                    @endforeach
                    @unless ($tahunList->contains(now()->year))
                        <option value="{{ now()->year }}" @selected(now()->year == $tahun)>{{ now()->year }}</option>
                    @endunless
                </select>
            </form>
            @if ($anggaranList->isNotEmpty())
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRealisasi">
                    <i class="fas fa-plus me-1"></i> Catat Realisasi
                </button>
            @endif
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="realisasi-summary-wrap">
        <div class="realisasi-summary-card">
            <div class="realisasi-summary-label"><i class="fas fa-receipt me-1"></i> Total Realisasi Tahun {{ $tahun }}</div>
            <div class="realisasi-summary-value">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
        </div>
        <div class="realisasi-summary-card blue">
            <div class="realisasi-summary-label"><i class="fas fa-list-ol me-1"></i> Jumlah Transaksi</div>
            <div class="realisasi-summary-value">{{ $jumlahTransaksi }} transaksi</div>
        </div>
    </div>

    {{-- ── Search ── --}}
    <div class="search-bar-wrap">
        <input type="text" id="searchRealisasi" placeholder="Cari kode kegiatan, nama, atau deskripsi...">
    </div>

    {{-- ── Table ── --}}
    <span class="tabel-title">Daftar Realisasi {{ $tahun }}</span>

    <div class="table-container">
        <table class="data-table" id="tabelRealisasi">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Kegiatan</th>
                    <th style="text-align:left">Nama Kegiatan</th>
                    <th style="text-align:left">Deskripsi</th>
                    <th>Jenis</th>
                    <th class="text-right">Jumlah (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr data-search="{{ strtolower(($row->anggaran->kode_kegiatan ?? '') . ' ' . ($row->anggaran->nama_kegiatan ?? '') . ' ' . $row->deskripsi) }}">
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td style="white-space:nowrap">{{ $row->tanggal->format('d M Y') }}</td>
                        <td><span class="badge-kode">{{ $row->anggaran->kode_kegiatan ?? '-' }}</span></td>
                        <td style="text-align:left">{{ $row->anggaran->nama_kegiatan ?? '-' }}</td>
                        <td style="text-align:left;color:#6b7280;font-size:13px">{{ $row->deskripsi ?? '-' }}</td>
                        <td>
                            @if ($row->jenis === 'masuk')
                                <span class="badge bg-info text-dark" style="font-size:11px">Masuk</span>
                            @else
                                <span class="badge bg-danger" style="font-size:11px">Keluar</span>
                            @endif
                        </td>
                        <td class="text-right" style="font-weight:600;font-variant-numeric:tabular-nums">
                            {{ number_format($row->jumlah, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if (!$row->sumber_type)
                                    {{-- Hanya entri manual (bukan auto-linked) yang bisa diedit --}}
                                    <button class="btn btn-sm btn-warning" title="Edit"
                                        data-id="{{ $row->id }}"
                                        data-anggaran-id="{{ $row->rkat_anggaran_id }}"
                                        data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                                        data-jumlah="{{ $row->jumlah }}"
                                        data-jenis="{{ $row->jenis }}"
                                        data-deskripsi="{{ $row->deskripsi }}"
                                        onclick="bukaEditRealisasi(this)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('riwayat-realisasi.destroy', $row->id) }}"
                                      onsubmit="return confirm('Hapus transaksi realisasi ini?')">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            Belum ada realisasi untuk tahun {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div style="font-size:13px;color:#6b7280">
            @if ($data->total() > 0)
                Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }} dari {{ $data->total() }} data
            @endif
        </div>
        {{ $data->appends(['tahun' => $tahun])->links() }}
    </div>

</div>
</main>

{{-- ──────────── MODAL TAMBAH REALISASI ──────────── --}}
@if ($anggaranList->isNotEmpty())
<div class="modal fade" id="modalTambahRealisasi" tabindex="-1" aria-labelledby="modalRealisasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('riwayat-realisasi.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRealisasiLabel">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Catat Realisasi Anggaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pos Anggaran <span class="text-danger">*</span></label>
                            <select name="rkat_anggaran_id" class="form-select" required>
                                <option value="">-- Pilih Pos Anggaran --</option>
                                @foreach ($anggaranList as $ag)
                                    <option value="{{ $ag->id }}" @selected(old('rkat_anggaran_id') == $ag->id)>
                                        {{ $ag->kode_kegiatan }} — {{ $ag->coa_pos }} ({{ $ag->nama_kegiatan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="keluar" @selected(old('jenis') === 'keluar')>Keluar (Pengeluaran)</option>
                                <option value="masuk" @selected(old('jenis') === 'masuk')>Masuk (Penerimaan/PNBP)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah" class="form-control rupiah-input"
                                   value="{{ old('jumlah') ? number_format((int)old('jumlah'), 0, ',', '.') : '' }}"
                                   placeholder="0" inputmode="numeric" required
                                   oninput="formatRupiah(this)">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2" maxlength="500" placeholder="Keterangan singkat realisasi...">{{ old('deskripsi') }}</textarea>
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
@endif

{{-- ──────────── MODAL EDIT REALISASI ──────────── --}}
@if ($anggaranList->isNotEmpty())
<div class="modal fade" id="modalEditRealisasi" tabindex="-1" aria-labelledby="modalEditRealisasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formEditRealisasi" action="">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditRealisasiLabel">
                        <i class="fas fa-edit me-2 text-warning"></i>Edit Realisasi Anggaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pos Anggaran <span class="text-danger">*</span></label>
                            <select name="rkat_anggaran_id" id="editRealisasiAnggaranId" class="form-select" required>
                                <option value="">-- Pilih Pos Anggaran --</option>
                                @foreach ($anggaranList as $ag)
                                    <option value="{{ $ag->id }}">
                                        {{ $ag->kode_kegiatan }} — {{ $ag->coa_pos }} ({{ $ag->nama_kegiatan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="editRealisasiTanggal" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" id="editRealisasiJenis" class="form-select" required>
                                <option value="keluar">Keluar (Pengeluaran)</option>
                                <option value="masuk">Masuk (Penerimaan/PNBP)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah" id="editRealisasiJumlah" class="form-control rupiah-input"
                                   placeholder="0" inputmode="numeric" required
                                   oninput="formatRupiah(this)">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" id="editRealisasiDeskripsi" class="form-control" rows="2" maxlength="500" placeholder="Keterangan singkat realisasi..."></textarea>
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
@endif

<script>
// ── Format ribuan ──────────────────────────────────────────────────────────
function formatRupiah(input) {
    const pos    = input.selectionStart;
    const before = input.value.length;
    const raw    = input.value.replace(/\D/g, '');
    input.value  = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
    const diff   = input.value.length - before;
    try { input.setSelectionRange(pos + diff, pos + diff); } catch(_) {}
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(f => {
        f.addEventListener('submit', () => {
            f.querySelectorAll('.rupiah-input').forEach(el => {
                el.value = el.value.replace(/\./g, '');
            });
        });
    });
});

document.getElementById('searchRealisasi').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabelRealisasi tbody tr[data-search]').forEach(tr => {
        tr.style.display = (tr.dataset.search || '').includes(q) ? '' : 'none';
    });
});

function bukaEditRealisasi(btn) {
    const d = btn.dataset;
    document.getElementById('formEditRealisasi').action = '/riwayat-realisasi/' + d.id;
    document.getElementById('editRealisasiAnggaranId').value = d.anggaranId;
    document.getElementById('editRealisasiTanggal').value = d.tanggal;
    document.getElementById('editRealisasiJenis').value = d.jenis;
    const jumlahRaw = parseInt(d.jumlah || 0);
    document.getElementById('editRealisasiJumlah').value = jumlahRaw ? jumlahRaw.toLocaleString('id-ID') : '';
    document.getElementById('editRealisasiDeskripsi').value = d.deskripsi || '';
    new bootstrap.Modal(document.getElementById('modalEditRealisasi')).show();
}

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modalTambahRealisasi');
        if (modal) new bootstrap.Modal(modal).show();
    });
@endif
</script>
@endsection
