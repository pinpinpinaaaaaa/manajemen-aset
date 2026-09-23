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
    .anggaran-summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }
    .anggaran-summary-card {
        flex: 1 1 calc(33.333% - 11px);
        min-width: 180px;
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
        border-left: 4px solid #2563eb;
    }
    .anggaran-summary-card.orange { border-left-color: #d97706; }
    .anggaran-summary-card.green  { border-left-color: #16a34a; }
    .anggaran-summary-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }
    .anggaran-summary-value {
        font-size: 22px;
        font-weight: 700;
        color: #111;
        line-height: 1.2;
    }
    .anggaran-summary-card.green  .anggaran-summary-value { color: #16a34a; }
    .anggaran-summary-card.orange .anggaran-summary-value { color: #d97706; }
    .tabel-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .tabel-title {
        font-size: 16px;
        font-weight: 600;
        color: #111;
    }
    .text-sisa  { color: #16a34a; font-weight: 600; }
    .text-right { text-align: right !important; }
    .tabel-total-row td {
        font-weight: 700;
        background: #f9fafb;
        border-top: 2px solid #e5e7eb;
    }
    .pagination-info {
        font-size: 13px;
        color: #6b7280;
        margin-top: 10px;
        text-align: right;
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
        .anggaran-summary-card { flex: 1 1 100%; }
        .tabel-title-bar { flex-direction: column; align-items: flex-start; gap: 8px; }
        .header-actions { width: 100%; }
    }
</style>

<main class="main-content">
<div class="content-padding">

    {{-- ── Page Header ── --}}
    <div class="anggaran-page-header">
        <div class="page-header">
            <h1 class="page-title">Kelola Anggaran RKAT</h1>
            <p class="page-subtitle">Manajemen Rencana Kegiatan dan Anggaran Tahunan</p>
            <nav class="breadcrumb" style="margin-top:6px">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Anggaran RKAT</span>
            </nav>
        </div>
        <div class="header-actions">
            <select id="tahunSelect" class="form-select" style="width:auto;height:38px;font-size:14px;">
                <option value="2026" selected>2026</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
            </select>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Anggaran
            </button>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="anggaran-summary-grid">
        <div class="anggaran-summary-card">
            <div class="anggaran-summary-label"><i class="fas fa-wallet me-1"></i> Total Anggaran</div>
            <div class="anggaran-summary-value" id="cardTotalAnggaran">Rp 295.000.000</div>
        </div>
        <div class="anggaran-summary-card orange">
            <div class="anggaran-summary-label"><i class="fas fa-receipt me-1"></i> Total Realisasi</div>
            <div class="anggaran-summary-value" id="cardTotalRealisasi">Rp 140.933.320</div>
        </div>
        <div class="anggaran-summary-card green">
            <div class="anggaran-summary-label"><i class="fas fa-piggy-bank me-1"></i> Sisa Anggaran</div>
            <div class="anggaran-summary-value" id="cardSisaAnggaran">Rp 154.066.680</div>
        </div>
    </div>

    {{-- ── Search ── --}}
    <div class="search-bar-wrap">
        <input type="text" id="searchRkat" placeholder="Cari berdasarkan kode kegiatan, COA, atau nama kegiatan...">
    </div>

    {{-- ── Table ── --}}
    <div class="tabel-title-bar">
        <span class="tabel-title">Daftar Anggaran RKAT <span id="labelTahun">2026</span></span>
    </div>

    <div class="table-container">
        <table class="data-table" id="tabelRkat">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Kegiatan</th>
                    <th>COA</th>
                    <th style="text-align:left">Nama Kegiatan</th>
                    <th class="text-right">Anggaran (Rp)</th>
                    <th class="text-right">Realisasi (Rp)</th>
                    <th class="text-right">Sisa Anggaran (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="rkatBody">
                {{-- Baris dummy diisi oleh JS --}}
            </tbody>
            <tfoot>
                <tr class="tabel-total-row" id="rkatTotalRow">
                    <td colspan="4" style="text-align:left;padding-left:16px;">TOTAL</td>
                    <td class="text-right" id="footAnggaran"></td>
                    <td class="text-right" id="footRealisasi"></td>
                    <td class="text-right text-sisa" id="footSisa"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="pagination-info" id="paginationInfo"></div>

</div>
</main>

{{-- ══════════════════ MODAL TAMBAH ANGGARAN ══════════════════ --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Anggaran RKAT
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambah">
                    <div class="mb-3">
                        <label class="form-label">Kode Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputKode" placeholder="mis. RKAT-2026-006" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">COA <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputCoa" placeholder="mis. 51110101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputNama" placeholder="Nama kegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anggaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="inputAnggaran" placeholder="0" min="0" required>
                        <div class="form-text" id="anggaranPreview" style="font-weight:600;color:#2563eb;"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpan">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ MODAL EDIT ══════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Anggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <input type="hidden" id="editIdx">
                    <div class="mb-3">
                        <label class="form-label">Kode Kegiatan</label>
                        <input type="text" class="form-control" id="editKode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">COA</label>
                        <input type="text" class="form-control" id="editCoa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="editNama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anggaran (Rp)</label>
                        <input type="number" class="form-control" id="editAnggaran" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanEdit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ── Data dummy RKAT ──
let rkatData = [
    { kode: 'RKAT-2026-001', coa: '51110101', nama: 'Pengadaan Alat Tulis Kantor (ATK)',          anggaran: 5000000,   realisasi: 2500000  },
    { kode: 'RKAT-2026-002', coa: '51110201', nama: 'Pemeliharaan & Perbaikan Peralatan Kantor',  anggaran: 15000000,  realisasi: 8000000  },
    { kode: 'RKAT-2026-003', coa: '51110301', nama: 'Pengadaan Mebel dan Furnitur',               anggaran: 25000000,  realisasi: 0        },
    { kode: 'RKAT-2026-004', coa: '51120101', nama: 'Biaya Pemeliharaan Gedung dan Bangunan',     anggaran: 50000000,  realisasi: 20000000 },
    { kode: 'RKAT-2026-005', coa: '51120201', nama: 'Pengadaan Kendaraan Operasional',            anggaran: 200000000, realisasi: 110433320},
];

const fmt = v => 'Rp ' + v.toLocaleString('id-ID');

function renderTable(data) {
    const tbody = document.getElementById('rkatBody');
    tbody.innerHTML = '';

    let totAnggaran = 0, totRealisasi = 0;
    data.forEach((d, i) => {
        const sisa = d.anggaran - d.realisasi;
        totAnggaran  += d.anggaran;
        totRealisasi += d.realisasi;
        const tr = document.createElement('tr');
        tr.dataset.search = (d.kode + ' ' + d.coa + ' ' + d.nama).toLowerCase();
        tr.innerHTML = `
            <td>${i + 1}</td>
            <td><code style="font-size:12px">${d.kode}</code></td>
            <td>${d.coa}</td>
            <td style="text-align:left">${d.nama}</td>
            <td class="text-right">${fmt(d.anggaran)}</td>
            <td class="text-right">${fmt(d.realisasi)}</td>
            <td class="text-right text-sisa">${fmt(sisa)}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline" data-bs-toggle="dropdown" aria-expanded="false"
                            style="padding:3px 10px;font-size:16px;line-height:1;">⋯</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="openEdit(${i});return false;">
                            <i class="fas fa-edit me-2 text-primary"></i>Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="hapusBaris(${i});return false;">
                            <i class="fas fa-trash me-2"></i>Hapus</a></li>
                    </ul>
                </div>
            </td>`;
        tbody.appendChild(tr);
    });

    const totSisa = totAnggaran - totRealisasi;
    document.getElementById('footAnggaran').textContent  = fmt(totAnggaran);
    document.getElementById('footRealisasi').textContent = fmt(totRealisasi);
    document.getElementById('footSisa').textContent      = fmt(totSisa);

    // Update summary cards
    document.getElementById('cardTotalAnggaran').textContent  = fmt(totAnggaran);
    document.getElementById('cardTotalRealisasi').textContent = fmt(totRealisasi);
    document.getElementById('cardSisaAnggaran').textContent   = fmt(totSisa);

    document.getElementById('paginationInfo').textContent =
        `Menampilkan 1–${data.length} dari ${data.length} data`;
}

function openEdit(idx) {
    const d = rkatData[idx];
    document.getElementById('editIdx').value    = idx;
    document.getElementById('editKode').value   = d.kode;
    document.getElementById('editCoa').value    = d.coa;
    document.getElementById('editNama').value   = d.nama;
    document.getElementById('editAnggaran').value = d.anggaran;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function hapusBaris(idx) {
    if (!confirm(`Hapus "${rkatData[idx].nama}"?`)) return;
    rkatData.splice(idx, 1);
    renderTable(rkatData);
}

// Simpan tambah
document.getElementById('btnSimpan').addEventListener('click', function () {
    const kode     = document.getElementById('inputKode').value.trim();
    const coa      = document.getElementById('inputCoa').value.trim();
    const nama     = document.getElementById('inputNama').value.trim();
    const anggaran = parseInt(document.getElementById('inputAnggaran').value) || 0;
    if (!kode || !coa || !nama || !anggaran) {
        alert('Lengkapi semua field terlebih dahulu.');
        return;
    }
    rkatData.push({ kode, coa, nama, anggaran, realisasi: 0 });
    renderTable(rkatData);
    document.getElementById('formTambah').reset();
    document.getElementById('anggaranPreview').textContent = '';
    bootstrap.Modal.getInstance(document.getElementById('modalTambah')).hide();
});

// Preview format rupiah saat input anggaran
document.getElementById('inputAnggaran').addEventListener('input', function () {
    const v = parseInt(this.value) || 0;
    document.getElementById('anggaranPreview').textContent = v > 0 ? fmt(v) : '';
});

// Simpan edit
document.getElementById('btnSimpanEdit').addEventListener('click', function () {
    const idx = parseInt(document.getElementById('editIdx').value);
    rkatData[idx].kode     = document.getElementById('editKode').value.trim();
    rkatData[idx].coa      = document.getElementById('editCoa').value.trim();
    rkatData[idx].nama     = document.getElementById('editNama').value.trim();
    rkatData[idx].anggaran = parseInt(document.getElementById('editAnggaran').value) || 0;
    renderTable(rkatData);
    bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
});

// Search client-side
document.getElementById('searchRkat').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#rkatBody tr').forEach(tr => {
        tr.style.display = (tr.dataset.search || '').includes(q) ? '' : 'none';
    });
});

// Init
renderTable(rkatData);
</script>
@endsection
