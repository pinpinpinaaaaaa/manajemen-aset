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
    .pagination-info {
        font-size: 13px;
        color: #6b7280;
        margin-top: 10px;
        text-align: right;
    }
    .btn-hapus-realisasi {
        background: none;
        border: 1px solid #fecaca;
        color: #dc2626;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-hapus-realisasi:hover { background: #fee2e2; }
    @media (max-width: 640px) {
        .anggaran-page-header { flex-direction: column; }
        .realisasi-summary-card { flex: 1 1 100%; }
        .header-actions { width: 100%; }
    }
</style>

<main class="main-content">
<div class="content-padding">

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
            <select id="tahunSelect" class="form-select" style="width:auto;height:38px;font-size:14px;">
                <option value="2026" selected>2026</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
            </select>
            <button class="btn btn-outline" onclick="alert('Fitur export akan tersedia setelah backend tersambung.')">
                <i class="fas fa-file-excel"></i> Export
            </button>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="realisasi-summary-wrap">
        <div class="realisasi-summary-card">
            <div class="realisasi-summary-label"><i class="fas fa-receipt me-1"></i> Total Realisasi Tahun <span id="labelTahunCard">2026</span></div>
            <div class="realisasi-summary-value" id="cardTotalRealisasi">Rp 0</div>
        </div>
        <div class="realisasi-summary-card blue">
            <div class="realisasi-summary-label"><i class="fas fa-list-ol me-1"></i> Jumlah Transaksi</div>
            <div class="realisasi-summary-value" id="cardJumlahTx">0</div>
        </div>
    </div>

    {{-- ── Search ── --}}
    <div class="search-bar-wrap">
        <input type="text" id="searchRealisasi"
               placeholder="Cari kode kegiatan, nama, atau deskripsi...">
    </div>

    {{-- ── Table ── --}}
    <span class="tabel-title">Daftar Realisasi <span id="labelTahun">2026</span></span>

    <div class="table-container">
        <table class="data-table" id="tabelRealisasi">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Kegiatan</th>
                    <th style="text-align:left">Nama Kegiatan</th>
                    <th style="text-align:left">Deskripsi</th>
                    <th class="text-right">Jumlah (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="realisasiBody">
                {{-- diisi JS --}}
            </tbody>
        </table>
    </div>
    <div class="pagination-info" id="paginationInfo"></div>

</div>
</main>

<script>
// ── Data dummy Riwayat Realisasi ──
let realisasiData = [
    {
        tanggal : '2026-02-14',
        kode    : 'RKAT-2026-001',
        nama    : 'Pengadaan Alat Tulis Kantor (ATK)',
        deskripsi: 'Pembelian ATK untuk kebutuhan kantor semester 1',
        jumlah  : 1200000,
    },
    {
        tanggal : '2026-02-28',
        kode    : 'RKAT-2026-001',
        nama    : 'Pengadaan Alat Tulis Kantor (ATK)',
        deskripsi: 'Pembelian kertas A4 dan tinta printer',
        jumlah  : 1300000,
    },
    {
        tanggal : '2026-03-05',
        kode    : 'RKAT-2026-002',
        nama    : 'Pemeliharaan & Perbaikan Peralatan Kantor',
        deskripsi: 'Servis AC ruangan rapat lantai 3',
        jumlah  : 3500000,
    },
    {
        tanggal : '2026-03-20',
        kode    : 'RKAT-2026-002',
        nama    : 'Pemeliharaan & Perbaikan Peralatan Kantor',
        deskripsi: 'Perbaikan printer HP LaserJet',
        jumlah  : 1200000,
    },
    {
        tanggal : '2026-04-01',
        kode    : 'RKAT-2026-002',
        nama    : 'Pemeliharaan & Perbaikan Peralatan Kantor',
        deskripsi: 'Penggantian cartridge scanner dan keyboard',
        jumlah  : 3300000,
    },
    {
        tanggal : '2026-04-15',
        kode    : 'RKAT-2026-004',
        nama    : 'Biaya Pemeliharaan Gedung dan Bangunan',
        deskripsi: 'Pengecatan ulang koridor gedung D lantai 1–2',
        jumlah  : 12000000,
    },
    {
        tanggal : '2026-05-10',
        kode    : 'RKAT-2026-004',
        nama    : 'Biaya Pemeliharaan Gedung dan Bangunan',
        deskripsi: 'Perbaikan atap dan saluran air gedung D',
        jumlah  : 8000000,
    },
    {
        tanggal : '2026-05-22',
        kode    : 'RKAT-2026-005',
        nama    : 'Pengadaan Kendaraan Operasional',
        deskripsi: 'DP pembelian 1 unit Toyota Kijang Innova',
        jumlah  : 60000000,
    },
    {
        tanggal : '2026-06-30',
        kode    : 'RKAT-2026-005',
        nama    : 'Pengadaan Kendaraan Operasional',
        deskripsi: 'Pelunasan cicilan dan biaya balik nama kendaraan',
        jumlah  : 50433320,
    },
];

const fmt = v => 'Rp ' + v.toLocaleString('id-ID');
const fmtTgl = s => {
    const d = new Date(s + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
};

function renderTable(data) {
    const tbody = document.getElementById('realisasiBody');
    tbody.innerHTML = '';
    let total = 0;
    data.forEach((d, i) => {
        total += d.jumlah;
        const tr = document.createElement('tr');
        tr.dataset.search = (d.kode + ' ' + d.nama + ' ' + d.deskripsi).toLowerCase();
        tr.innerHTML = `
            <td>${i + 1}</td>
            <td style="white-space:nowrap">${fmtTgl(d.tanggal)}</td>
            <td><span class="badge-kode">${d.kode}</span></td>
            <td style="text-align:left">${d.nama}</td>
            <td style="text-align:left;color:#6b7280;font-size:13px">${d.deskripsi}</td>
            <td class="text-right" style="font-weight:600">${fmt(d.jumlah)}</td>
            <td>
                <button class="btn-hapus-realisasi" onclick="hapusBaris(${i})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
    });

    document.getElementById('cardTotalRealisasi').textContent = fmt(total);
    document.getElementById('cardJumlahTx').textContent       = data.length + ' transaksi';
    document.getElementById('paginationInfo').textContent     =
        `Menampilkan 1–${data.length} dari ${data.length} data`;
}

function hapusBaris(idx) {
    if (!confirm('Hapus transaksi realisasi ini?')) return;
    realisasiData.splice(idx, 1);
    renderTable(realisasiData);
}

document.getElementById('searchRealisasi').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#realisasiBody tr').forEach(tr => {
        tr.style.display = (tr.dataset.search || '').includes(q) ? '' : 'none';
    });
});

document.getElementById('tahunSelect').addEventListener('change', function () {
    const t = this.value;
    document.getElementById('labelTahun').textContent     = t;
    document.getElementById('labelTahunCard').textContent = t;
});

// Init
renderTable(realisasiData);
</script>
@endsection
