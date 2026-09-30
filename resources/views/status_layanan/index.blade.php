<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Layanan | SIMASTER</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjB0ZWNofGVufDF8fHx8MTc1ODIwNDQxMHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 1.5rem 1rem 3rem;
            position: relative;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 820px;
        }

        /* Back button */
        .btn-back {
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 20;
            background: rgba(255,255,255,0.85);
            border-radius: 10px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(235,202,86,0.4);
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
            text-decoration: none;
            color: #1E2226;
            transition: .2s;
        }
        .btn-back:hover { background: #fff; }

        /* Header card */
        .header-card {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(235,202,86,0.35);
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            padding: 2rem 2.5rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .logo-img { max-width: 200px; margin-bottom: 1rem; }

        .header-card h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1E2226;
            margin-bottom: .375rem;
        }
        .header-card h1 span { color: #ebca56; }
        .header-card p {
            font-size: .9375rem;
            color: #444;
        }

        /* Search form */
        .search-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(235,202,86,0.3);
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            padding: 1.5rem 2rem;
            margin-bottom: 1.25rem;
        }

        .search-label {
            display: block;
            font-size: .875rem;
            font-weight: 600;
            color: #333;
            margin-bottom: .5rem;
        }

        .search-row {
            display: flex;
            gap: .75rem;
        }

        .search-input {
            flex: 1;
            padding: .625rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: .625rem;
            font-size: .9375rem;
            color: #1E2226;
            background: #fff;
            outline: none;
            transition: border-color .2s;
        }
        .search-input:focus { border-color: #ebca56; }

        .btn-cari {
            padding: .625rem 1.5rem;
            background: #ebca56;
            color: #1E2226;
            border: none;
            border-radius: .625rem;
            font-size: .9375rem;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            white-space: nowrap;
        }
        .btn-cari:hover { background: #d4b342; }

        .hint {
            font-size: .8125rem;
            color: #666;
            margin-top: .5rem;
        }

        /* Results */
        .results-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(235,202,86,0.3);
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .results-header {
            padding: 1rem 1.5rem;
            background: rgba(235,202,86,0.15);
            border-bottom: 1px solid rgba(235,202,86,0.3);
            font-size: .9375rem;
            font-weight: 600;
            color: #1E2226;
        }

        .empty-state {
            padding: 3rem 2rem;
            text-align: center;
            color: #666;
        }
        .empty-state svg { margin-bottom: .75rem; opacity: .4; }
        .empty-state p { font-size: .9375rem; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead tr { background: rgba(0,0,0,0.04); }
        th {
            padding: .75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #555;
            white-space: nowrap;
        }
        td {
            padding: .75rem 1rem;
            border-top: 1px solid #f0f0f0;
            color: #222;
            vertical-align: top;
        }
        tr:hover td { background: rgba(235,202,86,0.06); }

        .badge {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
        }

        /* approval badges */
        .badge-menunggu  { background: #fef3c7; color: #92400e; }
        .badge-disetujui { background: #d1fae5; color: #065f46; }
        .badge-ditolak   { background: #fee2e2; color: #991b1b; }

        /* progress badges */
        .badge-belum    { background: #e5e7eb; color: #374151; }
        .badge-proses   { background: #dbeafe; color: #1e40af; }
        .badge-selesai  { background: #d1fae5; color: #065f46; }

        .modul-chip {
            display: inline-block;
            padding: .15rem .5rem;
            background: rgba(235,202,86,0.2);
            border: 1px solid rgba(235,202,86,0.4);
            border-radius: .375rem;
            font-size: .75rem;
            font-weight: 600;
            color: #7a5f00;
            white-space: nowrap;
        }

        .id-code {
            font-family: 'Courier New', monospace;
            font-size: .8125rem;
            color: #374151;
        }

        @media (max-width: 600px) {
            .header-card { padding: 1.5rem 1.25rem; }
            .search-card { padding: 1.25rem; }
            .header-card h1 { font-size: 1.375rem; }
        }
    </style>
</head>

<body>

    <div class="overlay"></div>

    <a href="{{ route('menu.form') }}" class="btn-back" title="Kembali">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
    </a>

    <div class="container">

        {{-- Header --}}
        <div class="header-card">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-img">
            <h1>CEK STATUS <span>LAYANAN</span></h1>
            <p>Masukkan ID permohonan atau alamat email untuk melihat status pengajuan Anda</p>
        </div>

        {{-- Search form --}}
        <div class="search-card">
            <form method="GET" action="{{ route('cek-status') }}">
                <label class="search-label" for="cari">ID Permohonan atau Email</label>
                <div class="search-row">
                    <input
                        id="cari"
                        type="text"
                        name="cari"
                        class="search-input"
                        value="{{ $cari }}"
                        placeholder="Contoh: PMR-20260901-0001 atau nama@email.com"
                        autocomplete="off"
                        autofocus
                    >
                    <button type="submit" class="btn-cari">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Cari
                    </button>
                </div>
                <p class="hint">
                    Mencari di: Peminjaman Ruangan, Peminjaman Aset, Permintaan Kendaraan, Ekspedisi,
                    Pengaduan Kerusakan, Permintaan Barang Gudang, Pengadaan Barang &amp; Jasa
                </p>
            </form>
        </div>

        {{-- Results --}}
        @if ($cari !== '')
        <div class="results-card">
            <div class="results-header">
                @if (count($hasil) > 0)
                    Ditemukan {{ count($hasil) }} permohonan untuk <em>"{{ $cari }}"</em>
                @else
                    Tidak ada hasil untuk <em>"{{ $cari }}"</em>
                @endif
            </div>

            @if (count($hasil) === 0)
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        <line x1="8" y1="11" x2="14" y2="11" stroke-linecap="round"/>
                    </svg>
                    <p>Tidak ditemukan permohonan dengan ID atau email tersebut.</p>
                    <p style="margin-top:.5rem;font-size:.8125rem;color:#888">
                        Pastikan ID permohonan ditulis lengkap (contoh: <strong>PJM-20260901-0001</strong>)
                        atau gunakan email yang Anda daftarkan saat mengajukan permohonan.
                    </p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Modul</th>
                                <th>ID Permohonan</th>
                                <th>Nama</th>
                                <th>Status Persetujuan</th>
                                <th>Status Proses</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil as $row)
                            <tr>
                                <td><span class="modul-chip">{{ $row['modul'] }}</span></td>
                                <td><span class="id-code">{{ $row['id'] }}</span></td>
                                <td>{{ $row['nama'] }}</td>
                                <td>
                                    @php
                                        $ap = $row['approval'];
                                        $apClass = match($ap) {
                                            'disetujui'               => 'badge-disetujui',
                                            'ditolak'                 => 'badge-ditolak',
                                            'menunggu_persetujuan'    => 'badge-menunggu',
                                            default                   => 'badge-belum',
                                        };
                                        $apLabel = match($ap) {
                                            'disetujui'               => 'Disetujui',
                                            'ditolak'                 => 'Ditolak',
                                            'menunggu_persetujuan'    => 'Menunggu',
                                            null                      => '—',
                                            default                   => ucwords(str_replace('_', ' ', $ap)),
                                        };
                                    @endphp
                                    @if ($ap !== null)
                                        <span class="badge {{ $apClass }}">{{ $apLabel }}</span>
                                    @else
                                        <span style="color:#999;font-size:.8125rem">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $st = $row['status'];
                                        $stClass = match(true) {
                                            str_contains($st ?? '', 'Selesai')         => 'badge-selesai',
                                            str_contains($st ?? '', 'Sedang')          => 'badge-proses',
                                            str_contains($st ?? '', 'Diproses')        => 'badge-proses',
                                            default                                     => 'badge-belum',
                                        };
                                    @endphp
                                    @if ($st)
                                        <span class="badge {{ $stClass }}">{{ $st }}</span>
                                    @else
                                        <span style="color:#999;font-size:.8125rem">—</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;color:#555">
                                    {{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') : '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @endif

    </div>

</body>
</html>
