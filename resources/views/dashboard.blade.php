@extends('layouts.app')

@section('title', 'Dashboard Aset')

@section('content')

<style>
/* ── year filter bar ──────────────────────────────────────────────────── */
.dash-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 20px 0 4px;
    flex-wrap: wrap;
}
.dash-filter-bar label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
}
.dash-year-select {
    height: 36px;
    padding: 0 32px 0 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: #fff;
    font-size: 14px;
    font-weight: 600;
    color: #111;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}
.dash-year-select:focus { outline: 2px solid #2a78d6; outline-offset: 1px; }

/* ── row helpers ──────────────────────────────────────────────────────── */
.dash-row {
    display: grid;
    gap: 16px;
    margin-top: 16px;
}
.dash-row.row-2 { grid-template-columns: 1fr 1fr; }
.dash-row.row-4 { grid-template-columns: repeat(4, 1fr); }

/* ── card base ────────────────────────────────────────────────────────── */
.dash-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    min-width: 0;
}
.dash-card-title {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 0 0 14px;
}

/* ── stat cards (row 1) ───────────────────────────────────────────────── */
.dash-stat-val {
    font-size: 36px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1;
    margin-bottom: 4px;
}
.dash-stat-label {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 500;
}
.dash-card.stat { border-top: 3px solid #2a78d6; }
.dash-card.stat.s2 { border-top-color: #1baf7a; }
.dash-card.stat.s3 { border-top-color: #eb6834; }
.dash-card.stat.s4 { border-top-color: #e34948; }

/* ── chart box ────────────────────────────────────────────────────────── */
.chart-wrap {
    position: relative;
    height: 260px;
}

/* ── summary panel (row 3) ────────────────────────────────────────────── */
.summary-big {
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: 4px;
}
.summary-sub {
    font-size: 13px;
    color: #64748b;
}

/* ── 4-cell grid inside a card (pengaduan) ────────────────────────────── */
.quad-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 4px;
}
.quad-cell {
    background: #f8fafc;
    border-radius: 8px;
    padding: 12px 14px;
}
.quad-cell-val {
    font-size: 26px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1;
}
.quad-cell-label {
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-top: 4px;
}
.quad-cell.hl { background: #eff6ff; }
.quad-cell.hl .quad-cell-val { color: #1d4ed8; }
.quad-cell.warn { background: #fff7ed; }
.quad-cell.warn .quad-cell-val { color: #c2410c; }
.quad-cell.ok { background: #f0fdf4; }
.quad-cell.ok .quad-cell-val { color: #15803d; }
.quad-cell.muted .quad-cell-val { color: #64748b; }

/* ── pending maintenance table ────────────────────────────────────────── */
.dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.dash-table thead th {
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 6px 10px;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
}
.dash-table tbody td {
    padding: 8px 10px;
    border-bottom: 1px solid #f8fafc;
    color: #334155;
    vertical-align: top;
}
.dash-table tbody tr:last-child td { border-bottom: none; }
.dash-table tbody tr:hover td { background: #f8fafc; }
.badge-perlu  { display:inline-block; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:600; background:#fff7ed; color:#c2410c; }
.badge-proses { display:inline-block; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:600; background:#eff6ff; color:#1d4ed8; }
.empty-note   { text-align:center; padding:24px; color:#94a3b8; font-size:13px; }

/* ── viz tokens ────────────────────────────────────────────────────────── */
.viz-root {
    --s1: #2a78d6;
    --s2: #eb6834;
    --s3: #1baf7a;
    --grid: rgba(0,0,0,.06);
    --axis: #c3c2b7;
    --muted: #898781;
}

/* ── responsive ─────────────────────────────────────────────────────────*/
@media (max-width: 900px) {
    .dash-row.row-2 { grid-template-columns: 1fr; }
    .dash-row.row-4 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 540px) {
    .dash-row.row-4 { grid-template-columns: 1fr 1fr; }
    .chart-wrap { height: 200px; }
    .dash-stat-val { font-size: 28px; }
}
</style>

<main class="main-content">
<div class="content-padding">

    <div class="page-header">
        <h1 class="page-title">Dashboard GA & Aset</h1>
        <nav class="breadcrumb"><span class="current">Dashboard</span></nav>
    </div>

    {{-- ── Year filter ──────────────────────────────────────────────── --}}
    <div class="dash-filter-bar">
        <label for="yearPicker">Periode:</label>
        <form method="GET" action="{{ route('dashboard') }}" id="yearForm">
            <select name="tahun" id="yearPicker" class="dash-year-select"
                    onchange="document.getElementById('yearForm').submit()">
                @for ($y = date('Y') + 1; $y >= 2020; $y--)
                    <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ROW 1 · 4 stat cards                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="dash-row row-4">

        <div class="dash-card stat">
            <div class="dash-stat-val">{{ number_format($totalAset) }}</div>
            <div class="dash-stat-label">Total Aset Aktif</div>
        </div>

        <div class="dash-card stat s2">
            <div class="dash-stat-val">{{ number_format($asetTersedia) }}</div>
            <div class="dash-stat-label">Tersedia</div>
        </div>

        <div class="dash-card stat s3">
            <div class="dash-stat-val">{{ number_format($asetMaintenance) }}</div>
            <div class="dash-stat-label">Dalam Maintenance</div>
        </div>

        <div class="dash-card stat s4">
            <div class="dash-stat-val">{{ number_format($sedangPemusnahan) }}</div>
            <div class="dash-stat-label">Proses Pemusnahan</div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ROW 2 · 2 grafik tren berdampingan                            --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="dash-row row-2 viz-root">

        <div class="dash-card">
            <p class="dash-card-title">Tren Biaya Maintenance · {{ $tahun }}</p>
            <div class="chart-wrap">
                <canvas id="chartMaintenance"></canvas>
            </div>
        </div>

        <div class="dash-card">
            <p class="dash-card-title">Tren Biaya Pengadaan Barang & Jasa · {{ $tahun }}</p>
            <div class="chart-wrap">
                <canvas id="chartPengadaan"></canvas>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ROW 3 · 2 panel ringkasan nilai uang                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="dash-row row-2">

        <div class="dash-card">
            <p class="dash-card-title">Total Biaya Maintenance · {{ $tahun }}</p>
            <div class="summary-big">Rp {{ number_format($totalBiayaMaintenance, 0, ',', '.') }}</div>
            <div class="summary-sub">{{ number_format($jumlahMaintenance) }} kali maintenance</div>
        </div>

        <div class="dash-card">
            <p class="dash-card-title">Total Nilai Pengadaan · {{ $tahun }}</p>
            <div class="summary-big">Rp {{ number_format($totalBiayaPengadaan, 0, ',', '.') }}</div>
            <div class="summary-sub">{{ number_format($jumlahPengadaan) }} kali pengadaan</div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ROW 4 · Pengaduan (kiri) + Maintenance pending (kanan)        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="dash-row row-2" style="margin-bottom:32px;">

        {{-- Kiri: 4 angka pengaduan --}}
        <div class="dash-card">
            <p class="dash-card-title">Pengaduan Kerusakan · {{ $tahun }}</p>
            <div class="quad-grid">
                <div class="quad-cell hl" style="grid-column:span 2">
                    <div class="quad-cell-val">{{ number_format($pengaduanTotal) }}</div>
                    <div class="quad-cell-label">Total Pengaduan</div>
                </div>
                <div class="quad-cell warn">
                    <div class="quad-cell-val">{{ number_format($pengaduanBelumApprove) }}</div>
                    <div class="quad-cell-label">Belum Di-approve</div>
                </div>
                <div class="quad-cell ok">
                    <div class="quad-cell-val">{{ number_format($pengaduanDisetujui) }}</div>
                    <div class="quad-cell-label">Disetujui / Proses</div>
                </div>
                <div class="quad-cell muted" style="grid-column:span 2">
                    <div class="quad-cell-val">{{ number_format($pengaduanDitolak) }}</div>
                    <div class="quad-cell-label">Ditolak</div>
                </div>
            </div>
        </div>

        {{-- Kanan: tabel maintenance pending --}}
        <div class="dash-card">
            <p class="dash-card-title">Maintenance Belum Di-approve</p>
            @if ($maintenancePending->isEmpty())
                <div class="empty-note">Tidak ada maintenance yang menunggu persetujuan.</div>
            @else
                <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Aset</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($maintenancePending as $m)
                            @php $firstDetail = $m->details->first(); @endphp
                            <tr>
                                <td style="white-space:nowrap;font-family:monospace;font-size:12px;">
                                    {{ $m->id_maintenance }}
                                </td>
                                <td style="white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($m->tanggal_laporan)->format('d M Y') }}
                                </td>
                                <td>
                                    @foreach ($m->details->take(2) as $d)
                                        {{ $d->aset?->nama_aset ?? '-' }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                    @if ($m->details->count() > 2)
                                        <span style="color:#94a3b8;font-size:11px;">+{{ $m->details->count() - 2 }} lainnya</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($firstDetail)
                                        @if ($firstDetail->status === 'Sedang Diperbaiki')
                                            <span class="badge-proses">Sedang Diperbaiki</span>
                                        @else
                                            <span class="badge-perlu">Perlu Perbaikan</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>

    </div>

</div>
</main>

{{-- ── Chart.js ──────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

    // palette tokens (match CSS vars)
    const S1   = '#2a78d6';
    const S2   = '#eb6834';
    const S3   = '#1baf7a';
    const GRID = 'rgba(0,0,0,.06)';
    const AXIS = '#c3c2b7';
    const MUTED = '#898781';

    // data from PHP — arrays indexed 1..12, json_encode gives 0..11
    const dMaintenance = @json(array_values($trendMaintenance));
    const dBarang      = @json(array_values($trendBarang));
    const dJasa        = @json(array_values($trendJasa));

    // shared axis / plugin defaults
    const rupiahTick = {
        callback: v => v === 0 ? '0' : 'Rp ' + Intl.NumberFormat('id-ID', {notation:'compact',maximumFractionDigits:1}).format(v)
    };
    const rupiahTooltip = {
        callbacks: {
            label: ctx => ' Rp ' + Intl.NumberFormat('id-ID').format(ctx.parsed.y)
        }
    };
    const sharedScales = {
        x: {
            grid: { color: GRID },
            ticks: { color: MUTED, font: { size: 11 } },
            border: { color: AXIS }
        },
        y: {
            grid: { color: GRID },
            ticks: { ...rupiahTick, color: MUTED, font: { size: 11 } },
            border: { color: AXIS },
            beginAtZero: true
        }
    };

    // ── Chart kiri: Tren Maintenance ──────────────────────────────────
    new Chart(document.getElementById('chartMaintenance'), {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                label: 'Biaya Maintenance',
                data: dMaintenance,
                borderColor: S1,
                backgroundColor: S1 + '18',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: S1,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: rupiahTooltip
            },
            scales: sharedScales
        }
    });

    // ── Chart kanan: Tren Pengadaan Barang & Jasa ──────────────────────
    new Chart(document.getElementById('chartPengadaan'), {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [
                {
                    label: 'Barang',
                    data: dBarang,
                    borderColor: S2,
                    backgroundColor: S2 + '18',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: S2,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Jasa',
                    data: dJasa,
                    borderColor: S3,
                    backgroundColor: S3 + '18',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: S3,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 16,
                        color: MUTED,
                        font: { size: 12 }
                    }
                },
                tooltip: rupiahTooltip
            },
            scales: sharedScales
        }
    });
})();
</script>

@endsection
