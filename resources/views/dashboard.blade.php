@extends('layouts.app')

@section('title', 'Dashboard Aset')

@section('content')

<style>
/* ── row helpers ──────────────────────────────────────────────────────── */
.dash-row {
    display: grid;
    gap: 16px;
    margin-top: 16px;
}
.dash-row.row-2 { grid-template-columns: 1fr 1fr; }

/* ── card base ────────────────────────────────────────────────────────── */
.dash-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,.06);
    min-width: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 0 0 14px;
}

/* ── chart box ────────────────────────────────────────────────────────── */
.chart-wrap {
    position: relative;
    height: 260px;
}

/* ── summary panel (row 3) ────────────────────────────────────────────── */
.summary-big {
    font-size: 28px;
    font-weight: 700;
    color: #111;
    line-height: 1.1;
    margin-bottom: 4px;
}
.summary-sub {
    font-size: 13px;
    color: #6b7280;
}

/* ── 4-cell grid inside a card (pengaduan) ────────────────────────────── */
.quad-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 4px;
}
.quad-cell {
    background: #f3f4f6;
    border-radius: 8px;
    padding: 12px 14px;
}
.quad-cell-val {
    font-size: 26px;
    font-weight: 700;
    color: #111;
    line-height: 1;
}
.quad-cell-label {
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-top: 4px;
}
/* match existing badge system */
.quad-cell.hl   { background: #fef9e7; }
.quad-cell.hl   .quad-cell-val { color: #000; }
.quad-cell.warn { background: #fffbeb; }
.quad-cell.warn .quad-cell-val { color: #d97706; }
.quad-cell.ok   { background: #f0fdf4; }
.quad-cell.ok   .quad-cell-val { color: #16a34a; }
.quad-cell.err  { background: #fef2f2; }
.quad-cell.err  .quad-cell-val { color: #dc2626; }

/* ── pending maintenance table ────────────────────────────────────────── */
.dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.dash-table thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #fff;
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 6px 10px;
    text-align: left;
    border-bottom: 2px solid #f3f4f6;
    white-space: nowrap;
}
.dash-table tbody td {
    padding: 8px 10px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    vertical-align: top;
}
.dash-table tbody tr:last-child td { border-bottom: none; }
.dash-table tbody tr:hover td { background: #f9fafb; }
/* scroll wrapper — shows 5 rows then scrolls */
.table-scroll {
    overflow-y: auto;
    max-height: 260px;
}
/* reuse existing badge colours */
.badge-belum-approve { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; background:#fffbeb; color:#d97706; }
.badge-sedang-proses { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; background:#eff6ff; color:#2563eb; }
.empty-note          { text-align:center; padding:24px; color:#9ca3af; font-size:13px; }

/* ── clickable stat cards ────────────────────────────────────────────── */
.stat-card-link {
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}
.stat-card-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    color: inherit;
}
/* ── clickable dash-card ─────────────────────────────────────────────── */
.dash-card-link {
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
    text-decoration: none;
    display: block;
}
.dash-card-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    color: inherit;
}

/* ── responsive ─────────────────────────────────────────────────────────*/
@media (max-width: 900px) {
    .dash-row.row-2 { grid-template-columns: 1fr; }
    .chart-wrap { height: 220px; }
}
@media (max-width: 540px) {
    .chart-wrap { height: 190px; }
    .summary-big { font-size: 22px; }
}
</style>

<main class="main-content">
<div class="content-padding">

    <div class="page-header">
        <h1 class="page-title">Dashboard GA & Aset</h1>
        <nav class="breadcrumb"><span class="current">Dashboard</span></nav>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ROW 1 · 4 stat cards — pakai komponen existing               --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="stat-cards-grid">
        <x-stat-card label="Total Aset Aktif"  :value="number_format($totalAset)"       :href="route('aset.index')" />
        <x-stat-card label="Tersedia"          :value="number_format($asetTersedia)"    :href="route('aset.index')" />
        <x-stat-card label="Dalam Maintenance" :value="number_format($asetMaintenance)" :href="route('maintenance.index')" />
        <x-stat-card label="Proses Pemusnahan" :value="number_format($sedangPemusnahan)" :href="route('laporan_pemusnahan.index')" />
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ROW 2 · 2 grafik tren berdampingan                            --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="dash-row row-2">

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

        {{-- Kiri: 5 angka pengaduan (seluruh card bisa diklik) --}}
        <a href="{{ route('pengaduan-kerusakan.index') }}" class="dash-card dash-card-link">
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
                <div class="quad-cell" style="background:#eff6ff;">
                    <div class="quad-cell-val" style="color:#2563eb;">{{ number_format($pengaduanSedangDiproses) }}</div>
                    <div class="quad-cell-label">Sedang Diproses</div>
                </div>
                <div class="quad-cell ok">
                    <div class="quad-cell-val">{{ number_format($pengaduanSelesai) }}</div>
                    <div class="quad-cell-label">Sudah Selesai</div>
                </div>
                <div class="quad-cell err">
                    <div class="quad-cell-val">{{ number_format($pengaduanDitolak) }}</div>
                    <div class="quad-cell-label">Ditolak</div>
                </div>
            </div>
        </a>

        {{-- Kanan: tabel maintenance belum selesai --}}
        <div class="dash-card" style="display:flex;flex-direction:column;min-height:0;">
            <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:14px;">
                <p class="dash-card-title" style="margin:0;">Maintenance Belum Selesai · {{ $tahun }}</p>
                @if ($maintenanceBelumSelesai->isNotEmpty())
                    <a href="{{ route('maintenance.index') }}"
                       style="font-size:12px;color:#2563eb;text-decoration:none;white-space:nowrap;">
                        Lihat semua →
                    </a>
                @endif
            </div>
            @if ($maintenanceBelumSelesai->isEmpty())
                <div class="empty-note">Semua maintenance sudah selesai.</div>
            @else
                <div class="table-scroll">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Aset</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($maintenanceBelumSelesai as $m)
                            @php
                                $asets      = $m->details->map(fn($d) => $d->aset)->filter();
                                $firstAset  = $asets->first();
                                $extraCount = max(0, $asets->count() - 1);
                            @endphp
                            <tr>
                                {{-- Aset --}}
                                <td>
                                    @if ($firstAset)
                                        <span style="font-size:12px;color:#9ca3af;font-family:monospace;">
                                            {{ $firstAset->kode_aset }}
                                        </span><br>
                                        <span style="font-weight:500;">{{ $firstAset->nama_aset }}</span>
                                        @if ($extraCount > 0)
                                            <br><span style="color:#9ca3af;font-size:11px;">+{{ $extraCount }} aset lain</span>
                                        @endif
                                    @else
                                        <span style="color:#9ca3af;">—</span>
                                    @endif
                                </td>
                                {{-- Lokasi --}}
                                <td style="white-space:nowrap;">
                                    {{ $m->ruangan?->nama_ruangan ?? '—' }}
                                </td>
                                {{-- Status badge --}}
                                <td style="white-space:nowrap;">
                                    @if ($m->decision_status === 'menunggu_persetujuan')
                                        <span class="badge-belum-approve">Belum Approve</span>
                                    @else
                                        <span class="badge-sedang-proses">Sedang Proses</span>
                                    @endif
                                </td>
                                {{-- Tanggal --}}
                                <td style="white-space:nowrap;color:#6b7280;font-size:12px;">
                                    {{ \Carbon\Carbon::parse($m->tanggal_laporan)->format('d M Y') }}
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

    // warna dari sistem existing — sama dengan btn-primary, status badge, dll.
    const YELLOW = '#ebca56';  // brand primary (btn-primary, stat-card border)
    const BLUE   = '#2563eb';  // active/link colour
    const GREEN  = '#16a34a';  // success/ok colour
    const GRID   = '#e5e7eb';  // border colour used throughout styles.css
    const AXIS   = '#d1d5db';
    const MUTED  = '#9ca3af';

    const dMaintenance = @json(array_values($trendMaintenance));
    const dBarang      = @json(array_values($trendBarang));
    const dJasa        = @json(array_values($trendJasa));

    const rupiahTick = {
        callback: v => v === 0 ? '0' : 'Rp ' + Intl.NumberFormat('id-ID',{notation:'compact',maximumFractionDigits:1}).format(v)
    };
    const rupiahTooltip = {
        callbacks: { label: ctx => ' Rp ' + Intl.NumberFormat('id-ID').format(ctx.parsed.y) }
    };
    const sharedScales = {
        x: { grid:{color:GRID}, ticks:{color:MUTED,font:{size:11}}, border:{color:AXIS} },
        y: { grid:{color:GRID}, ticks:{...rupiahTick,color:MUTED,font:{size:11}}, border:{color:AXIS}, beginAtZero:true }
    };

    // ── Maintenance (satu seri, warna brand kuning) ───────────────────
    new Chart(document.getElementById('chartMaintenance'), {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                label: 'Biaya Maintenance',
                data: dMaintenance,
                borderColor: YELLOW,
                backgroundColor: 'rgba(235,202,86,.15)',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: YELLOW,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode:'index', intersect:false },
            plugins: { legend:{ display:false }, tooltip: rupiahTooltip },
            scales: sharedScales
        }
    });

    // ── Pengadaan: Barang (biru) dan Jasa (hijau) ─────────────────────
    new Chart(document.getElementById('chartPengadaan'), {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [
                {
                    label: 'Barang',
                    data: dBarang,
                    borderColor: BLUE,
                    backgroundColor: 'rgba(37,99,235,.10)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: BLUE,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Jasa',
                    data: dJasa,
                    borderColor: GREEN,
                    backgroundColor: 'rgba(22,163,74,.10)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: GREEN,
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
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle:true, pointStyle:'circle', boxWidth:8, padding:16, color:MUTED, font:{size:12} }
                },
                tooltip: rupiahTooltip
            },
            scales: sharedScales
        }
    });
})();
</script>

@endsection
