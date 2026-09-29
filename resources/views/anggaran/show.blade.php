@extends('layouts.app')

@section('title', 'Detail Pos Anggaran')

@section('content')
<style>
    .info-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .info-card h2 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #111;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }
    .info-item label {
        font-size: 0.75rem;
        color: #6b7280;
        display: block;
        margin-bottom: 2px;
    }
    .info-item span {
        font-weight: 600;
        color: #111;
        font-size: 0.95rem;
    }
    .stat-chips {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .stat-chip {
        flex: 1;
        min-width: 160px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .stat-chip .chip-label { font-size: 0.75rem; color: #6b7280; }
    .stat-chip .chip-value { font-size: 1.15rem; font-weight: 700; color: #111; }
    .stat-chip.danger  { border-left: 4px solid #ef4444; }
    .stat-chip.success { border-left: 4px solid #10b981; }
    .stat-chip.warning { border-left: 4px solid #f59e0b; }
    .stat-chip.primary { border-left: 4px solid #3b82f6; }

    /* Monthly table */
    .month-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .month-table th {
        background: #f9fafb;
        padding: 0.6rem 0.8rem;
        text-align: center;
        border-bottom: 2px solid #e5e7eb;
        white-space: nowrap;
        font-weight: 600;
        color: #374151;
    }
    .month-table td {
        padding: 0.55rem 0.8rem;
        text-align: right;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }
    .month-table td.col-bulan {
        text-align: left;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
    }
    .month-table tr.current-month td {
        background: #fffbeb;
    }
    .month-table tr.total-row td {
        background: #f0fdf4;
        font-weight: 700;
        border-top: 2px solid #10b981;
    }
    .sisa-plus  { color: #059669; font-weight: 600; }
    .sisa-minus { color: #dc2626; font-weight: 600; }
    .rencana-val { color: #6b7280; font-size: 0.8rem; }
    .selisih-plus  { color: #0284c7; font-size: 0.78rem; }
    .selisih-minus { color: #ef4444; font-size: 0.78rem; }

    /* Transaction list */
    .trx-section {
        margin-top: 1.5rem;
    }
    .trx-section h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 0.75rem;
    }
    .trx-month-group {
        margin-bottom: 1.25rem;
    }
    .trx-month-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 0.4rem;
        padding-left: 4px;
        border-left: 3px solid #ebca56;
        padding-left: 8px;
    }
    .trx-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.55rem 0.75rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 6px;
        gap: 1rem;
    }
    .trx-item .trx-desc {
        font-size: 0.825rem;
        color: #374151;
        flex: 1;
        line-height: 1.4;
    }
    .trx-item .trx-date {
        font-size: 0.75rem;
        color: #9ca3af;
        white-space: nowrap;
    }
    .trx-item .trx-amount {
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .trx-item .trx-amount.keluar { color: #dc2626; }
    .trx-item .trx-amount.masuk  { color: #059669; }
    .empty-month { color: #9ca3af; font-size: 0.8rem; padding: 0.4rem 0.75rem; }
</style>

<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Detail Pos Anggaran</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <a href="{{ route('anggaran-rkat.index') }}">Kelola Anggaran RKAT</a>
                <span class="separator">/</span>
                <span class="current">Detail</span>
            </nav>
        </div>

        {{-- INFO POS --}}
        <div class="info-card">
            <h2>Informasi Pos Anggaran</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Tahun</label>
                    <span>{{ $anggaran->tahun }}</span>
                </div>
                <div class="info-item">
                    <label>Kode Kegiatan</label>
                    <span>{{ $anggaran->kode_kegiatan }}</span>
                </div>
                <div class="info-item">
                    <label>Jenis Pengeluaran (COA)</label>
                    <span>{{ $anggaran->coa_pos }}</span>
                </div>
                <div class="info-item">
                    <label>Uraian Program Kerja</label>
                    <span>{{ $anggaran->nama_kegiatan }}</span>
                </div>
                <div class="info-item">
                    <label>Total Anggaran</label>
                    <span>Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- STAT CHIPS --}}
        <div class="stat-chips">
            <div class="stat-chip primary">
                <span class="chip-label">Total Anggaran</span>
                <span class="chip-value">Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}</span>
            </div>
            <div class="stat-chip danger">
                <span class="chip-label">Total Terpakai (Keluar)</span>
                <span class="chip-value">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
            </div>
            @if ($totalMasuk > 0)
            <div class="stat-chip success">
                <span class="chip-label">Total Masuk (PNBP)</span>
                <span class="chip-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="stat-chip {{ $sisa >= 0 ? 'success' : 'danger' }}">
                <span class="chip-label">Sisa Anggaran</span>
                <span class="chip-value {{ $sisa >= 0 ? 'sisa-plus' : 'sisa-minus' }}">
                    Rp {{ number_format(abs($sisa), 0, ',', '.') }}
                    {{ $sisa < 0 ? '(MELEBIHI)' : '' }}
                </span>
            </div>
            @php $persen = $anggaran->anggaran > 0 ? round($totalKeluar / $anggaran->anggaran * 100, 1) : 0; @endphp
            <div class="stat-chip warning">
                <span class="chip-label">Progress Realisasi</span>
                <span class="chip-value">{{ $persen }}%</span>
            </div>
        </div>

        {{-- TABEL BULANAN --}}
        <div class="info-card">
            <h2>Realisasi per Bulan</h2>

            <div style="overflow-x:auto">
                <table class="month-table">
                    <thead>
                        <tr>
                            <th style="text-align:left">Bulan</th>
                            <th>Rencana</th>
                            <th>Realisasi Keluar</th>
                            <th>Realisasi Masuk</th>
                            <th>Selisih vs Rencana</th>
                            <th>Kumulatif Keluar</th>
                            <th>Sisa Anggaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $bulanSekarang = now()->month; @endphp
                        @foreach ($byMonth as $m => $data)
                            @php
                                $rencanaKey = $rencanaKeys[$m];
                                $rencana    = $anggaran->$rencanaKey ?? 0;
                                $selisih    = $data['keluar'] - $rencana;
                                $isCurrent  = ($m == $bulanSekarang && $anggaran->tahun == now()->year);
                            @endphp
                            <tr {{ $isCurrent ? 'class=current-month' : '' }}>
                                <td class="col-bulan">
                                    {{ $data['label'] }}
                                    @if ($isCurrent)
                                        <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem">Ini</span>
                                    @endif
                                </td>
                                <td class="rencana-val">
                                    {{ $rencana > 0 ? 'Rp '.number_format($rencana, 0, ',', '.') : '—' }}
                                </td>
                                <td>
                                    {{ $data['keluar'] > 0 ? 'Rp '.number_format($data['keluar'], 0, ',', '.') : '—' }}
                                </td>
                                <td>
                                    @if ($data['masuk'] > 0)
                                        <span class="sisa-plus">Rp {{ number_format($data['masuk'], 0, ',', '.') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($rencana > 0 || $data['keluar'] > 0)
                                        <span class="{{ $selisih > 0 ? 'selisih-minus' : ($selisih < 0 ? 'selisih-plus' : '') }}">
                                            {{ $selisih > 0 ? '+' : '' }}{{ $selisih != 0 ? 'Rp '.number_format(abs($selisih), 0, ',', '.').($selisih > 0 ? ' lebih' : ' hemat') : '—' }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    {{ $data['kumulatif'] > 0 ? 'Rp '.number_format($data['kumulatif'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="{{ $data['sisa'] < 0 ? 'sisa-minus' : 'sisa-plus' }}">
                                    Rp {{ number_format(abs($data['sisa']), 0, ',', '.') }}
                                    {{ $data['sisa'] < 0 ? '⚠' : '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td class="col-bulan">TOTAL</td>
                            <td class="rencana-val">Rp {{ number_format(array_sum(array_map(fn($k) => $anggaran->$k ?? 0, $rencanaKeys)), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                            <td><span class="sisa-plus">{{ $totalMasuk > 0 ? 'Rp '.number_format($totalMasuk, 0, ',', '.') : '—' }}</span></td>
                            <td>—</td>
                            <td>Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                            <td class="{{ $sisa < 0 ? 'sisa-minus' : 'sisa-plus' }}">Rp {{ number_format(abs($sisa), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- RIWAYAT TRANSAKSI --}}
        <div class="info-card">
            <h2>Riwayat Transaksi Realisasi</h2>

            @php $adaTransaksi = collect($byMonth)->filter(fn($d) => $d['items']->isNotEmpty())->isNotEmpty(); @endphp

            @if (!$adaTransaksi)
                <p class="text-muted" style="font-size:0.875rem">Belum ada transaksi realisasi untuk pos anggaran ini.</p>
            @else
                @foreach ($byMonth as $m => $data)
                    @if ($data['items']->isNotEmpty())
                        <div class="trx-month-group">
                            <div class="trx-month-label">{{ $data['label'] }}</div>
                            @foreach ($data['items'] as $trx)
                                <div class="trx-item">
                                    <div>
                                        <div class="trx-desc">{{ $trx->deskripsi ?: '—' }}</div>
                                        <div class="trx-date">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</div>
                                    </div>
                                    <div class="trx-amount {{ $trx->jenis }}">
                                        {{ $trx->jenis === 'masuk' ? '+' : '-' }}Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('anggaran-rkat.index', ['tahun' => $anggaran->tahun]) }}"
               class="btn btn-outline-secondary">
                ← Kembali ke Daftar RKAT
            </a>
        </div>

    </div>
</main>
@endsection
