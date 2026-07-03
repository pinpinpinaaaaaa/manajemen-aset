@extends('layouts.app')

@section('title', 'Dashboard Gudang')

@section('content')

    <style>
        /* ===============================
                                                                                        DASHBOARD GRID
                                                                                        ================================ */
        .dashboard-grid {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .dashboard-grid.wide {
            margin-top: 24px;
        }

        .dashboard-card {
            flex: 1;
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .dashboard-card.span-2 {
            flex: 2;
        }

        /* ===============================
                                                                                        CHART
                                                                                        ================================ */
        .chart-box {
            position: relative;
            height: 300px;
        }

        /* ===============================
                                                                                        CARD TITLE
                                                                                        ================================ */
        .card-title {
            font-weight: 600;
            margin-bottom: 12px;
        }

        .card-title-center {
            text-align: center;
            font-weight: 600;
            margin-bottom: 12px;
        }

        /* ===============================
                                                                                        CONTROLS
                                                                                        ================================ */
        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 28px 0 16px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 12px;
        }

        .controls-left {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .controls-right {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .search-input {
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }

        /* ===============================
                                                                                        IMAGE TABLE
                                                                                        ================================ */
        .table-container img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .rekap-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .rekap-month {
            height: 36px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }

        .badge-info {
            background-color: #0ea5e9 !important;
        }

        .badge-warning {
            background-color: #f59e0b !important;
        }

        .badge-danger {
            background-color: #ef4444 !important;
            color: #fff !important;
        }

        .badge-success {
            background-color: #22c55e !important;
            color: #fff !important;
        }

        .low-stock-scroll {
            max-height: 290px;
            overflow-x: auto;
            overflow-y: auto;
            border-radius: 10px;
            -webkit-overflow-scrolling: touch;
        }

        /* biar header tetap keliatan waktu scroll */
        .low-stock-scroll thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
        }

        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .summary-card {
            flex: 1 1 calc(50% - 10px);
            max-width: calc(50% - 10px);
            min-width: 0;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        }

        .summary-title {
            color: #64748b;
            font-size: 14px;
        }

        .summary-value {
            margin-top: 8px;
            font-size: 28px;
            font-weight: bold;
        }

        @media (max-width: 767px) {
            /* Stat cards: tetap 2 kolom di mobile */
            .summary-grid > .summary-card {
                padding: 14px;
            }

            .summary-value {
                font-size: 22px;
            }

            /* Charts + tabel: stack 1 kolom penuh */
            .dashboard-grid {
                flex-wrap: wrap;
            }

            .dashboard-grid > .dashboard-card {
                flex: 1 1 100%;
                min-width: 0;
            }

            .chart-box {
                height: 220px;
            }

            /* Controls bar: kolom di mobile */
            .controls-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .controls-right {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Dashboard Gudang</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Gudang</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Total Barang" :value="$totalBarang" />
                <x-stat-card label="Total Stok Tersedia" :value="number_format($totalStok)" />
                <x-stat-card label="Nilai Total Stok" :value="'Rp ' . number_format($totalNilaiStok, 0, ',', '.')" />
            </div>

            <div class="dashboard-grid">

                <div class="dashboard-card">
                    <h5 class="card-title-center">Top 10 Barang Paling Sering Keluar</h5>
                    <div class="chart-box">
                        <canvas id="chartKeluar"></canvas>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h5 class="card-title-center">Top 10 Barang Paling Sering Masuk</h5>
                    <div class="chart-box">
                        <canvas id="chartMasuk"></canvas>
                    </div>
                </div>

            </div>


            <div class="dashboard-grid wide">

                <div class="dashboard-card span-2">
                    <h4 class="card-title">Transaksi Terbaru</h4>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Jenis</th>
                                    <th>Harga (satuan)</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent as $t)
                                    <tr>
                                        <td>{{ $t->tanggal }}</td>
                                        <td>{{ $t->nama_barang }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $t->jenis_transaksi == 'masuk' ? 'badge-success' : 'badge-danger' }}">
                                                {{ strtoupper($t->jenis_transaksi) }}
                                            </span>
                                        </td>
                                        <td>
                                            Rp {{ number_format($t->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ $t->jumlah }} {{ $t->satuan_dasar }}
                                        </td>
                                        <td>
                                            Rp {{ number_format($t->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3">Tidak ada transaksi terbaru</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h4 class="card-title">Barang Hampir Habis</h4>

                    <div class="low-stock-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jenis</th>
                                    <th>Stok</th>
                                    <th>Limit</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($lowStock as $s)
                                    <tr>
                                        <td>{{ $s->nama_barang }}</td>
                                        <td>{{ strtoupper($s->jenis) }}</td>
                                        <td>
                                            {{ $s->stok_akhir }} {{ $s->satuan_dasar }}
                                            <span class="badge badge-danger ms-2">!</span>
                                        </td>
                                        <td>{{ $s->limit_stok }} {{ $s->satuan_dasar }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">Semua stok aman</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="controls-bar">
                <div class="controls-left">
                    <a href="{{ route('gudang.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </a>

                    <form action="{{ route('gudang.rekapBulanan') }}" method="POST" class="rekap-form">
                        @csrf

                        <input type="month" name="bulan" required class="rekap-month"
                            max="{{ now()->subMonth()->format('Y-m') }}">

                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm(
                                'Yakin ingin memproses rekap bulan ' + this.form.bulan.value + ' ?\n\nStok bulan tersebut akan ditutup!'
                            )">
                            <i class="fas fa-calendar-check"></i> Rekap
                        </button>
                    </form>

                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari nama barang..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">Foto</th>
                            <th>Nama Barang</th>
                            <th class="d-none d-md-table-cell">Jenis</th>
                            <th class="d-none d-lg-table-cell">Harga (satuan)</th>
                            <th class="d-none d-lg-table-cell">Satuan</th>
                            <th class="d-none d-lg-table-cell">Limit Stok</th>
                            <th class="d-none d-lg-table-cell">Stok Awal</th>
                            <th class="d-none d-md-table-cell">Masuk</th>
                            <th class="d-none d-md-table-cell">Keluar</th>
                            <th>Stok Akhir</th>
                            <th class="d-none d-lg-table-cell">Nilai Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableData">
                        @forelse ($barang as $index => $b)
                            <tr data-name="{{ strtolower($b->nama_barang) }} {{ strtolower($b->jenis) }}">

                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                                <td class="d-none d-lg-table-cell text-center">
                                    @if ($b->foto_produk)
                                        <img src="{{ asset('storage/' . $b->foto_produk) }}"
                                            alt="Foto {{ $b->nama_barang }}" width="60" height="60"
                                            style="object-fit:cover;border-radius:8px"
                                            onerror="this.src='{{ asset('images/no-image.png') }}'">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>{{ $b->nama_barang }}</td>

                                <td class="d-none d-md-table-cell">
                                    @php
                                        $jenis = strtolower(trim($b->jenis));
                                    @endphp

                                    <span class="badge {{ $jenis == 'atk' ? 'badge-info' : 'badge-warning' }}">
                                        {{ strtoupper($jenis) }}
                                    </span>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    Rp {{ number_format($b->harga_terakhir, 0, ',', '.') }}
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $b->satuan }} ({{ $b->konversi_satuan }} {{ $b->satuan_dasar }})
                                </td>

                                <td class="d-none d-lg-table-cell">{{ $b->limit_stok }}</td>
                                <td class="d-none d-lg-table-cell">{{ $b->stok_awal }}</td>
                                <td class="d-none d-md-table-cell">{{ $b->stok_masuk }}</td>
                                <td class="d-none d-md-table-cell">{{ $b->stok_keluar }}</td>

                                <td>
                                    {{ $b->stok_akhir }}
                                    @if ($b->stok_akhir <= $b->limit_stok)
                                        <span class="badge badge-danger">Menipis</span>
                                    @endif
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    Rp {{ number_format($b->nilai_stok, 0, ',', '.') }}
                                </td>

                                <td>
                                    <a href="{{ route('gudang.edit', $b->id_barang) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('gudang.destroy', $b->id_barang) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus barang ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center p-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                    Tidak ada data barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </main>
    <style>
        .row {
            margin-bottom: 20px;
        }

        .card {
            max-width: 49%;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ====================
        // TOP 10 KELUAR
        // ====================
        const chartKeluar = new Chart(document.getElementById('chartKeluar'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartKeluar_labels) !!},
                datasets: [{
                    label: "Jumlah Keluar",
                    data: {!! json_encode($chartKeluar_values) !!},
                    backgroundColor: "rgba(255, 99, 132, 0.7)",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // ====================
        // TOP 10 MASUK
        // ====================
        const chartMasuk = new Chart(document.getElementById('chartMasuk'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartMasuk_labels) !!},
                datasets: [{
                    label: "Jumlah Masuk",
                    data: {!! json_encode($chartMasuk_values) !!},
                    backgroundColor: "rgba(54, 162, 235, 0.7)",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('#tableData tr');

            searchInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase();

                rows.forEach(row => {
                    const text = row.dataset.name || '';

                    if (text.includes(keyword)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>

@endsection
