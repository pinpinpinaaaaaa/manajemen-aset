@extends('layouts.app')

@section('title', 'Dashboard Aset')

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ===============================
                                                                                                DASHBOARD GRID
                                                                                                ================================ */
        .dashboard-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .dashboard-grid.wide {
            margin-top: 24px;
        }

        .dashboard-card {
            flex: 1 1 calc(50% - 10px);
            max-width: calc(50% - 10px);
            min-width: 0;
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .dashboard-card.span-2 {
            flex: 2;
            max-width: none;
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

        .dashboard-grid.bottom-table .dashboard-card {
            min-height: 350px;
        }

        @media (max-width: 767px) {

            /* Stat cards: tetap 2 kolom di mobile */
            .dashboard-grid>.dashboard-card {
                padding: 12px;
            }

            .dashboard-grid>.dashboard-card h2 {
                font-size: 1.4rem;
            }

            .dashboard-grid>.dashboard-card h5 {
                font-size: 0.8rem;
                margin-bottom: 6px;
            }

            /* Chart + tabel bawah: 1 kolom penuh per baris */
            .dashboard-grid.wide>.dashboard-card {
                flex: 1 1 100%;
                max-width: 100%;
            }

            .chart-box {
                height: 220px;
            }
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Dashboard GA & Aset</h1>

                <nav class="breadcrumb">
                    <span class="current">Dashboard</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Total Aset" :value="$totalAset" />
                <x-stat-card label="Total Kendaraan" :value="$totalKendaraan" />
                <x-stat-card label="Maintenance Aktif" :value="$maintenanceAktif" />
                <x-stat-card label="Pengaduan Aktif" :value="$pengaduanAktif" />
            </div>

            <div class="dashboard-grid wide">

                <div class="dashboard-card">
                    <h4 class="card-title-center">
                        Status Aset
                    </h4>

                    <div class="chart-box">
                        <canvas id="asetChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h4 class="card-title-center">
                        Biaya Maintenance vs Pengadaan
                    </h4>

                    <div class="chart-box">
                        <canvas id="biayaChart"></canvas>
                    </div>
                </div>

            </div>

            <div class="dashboard-grid wide">

                <div class="dashboard-card">

                    <h4 class="card-title">
                        Pengadaan
                    </h4>

                    <h2>
                        Rp {{ number_format($totalPengadaan, 0, ',', '.') }}
                    </h2>

                    <p>
                        {{ $jumlahPengadaan }} Pengadaan
                    </p>

                </div>

                <div class="dashboard-card">

                    <h4 class="card-title">
                        Peminjaman Aktif
                    </h4>

                    <h2>
                        {{ $peminjamanAktif }}
                    </h2>

                    <p>
                        Aset & Ruangan
                    </p>

                </div>

            </div>

            <div class="dashboard-grid wide bottom-table">

                <div class="dashboard-card">

                    <h4 class="card-title">
                        Maintenance Berjalan
                    </h4>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aset</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($maintenanceBerjalanList as $maintenance)

                                @foreach ($maintenanceBerjalanList as $m)
                                    <tr>
                                        <td>{{ $m->id_maintenance }}</td>

                                        <td>
                                            @foreach ($m->details as $detail)
                                                • {{ $detail->aset?->nama_aset ?? '-' }}<br>
                                            @endforeach
                                        </td>

                                        <td>
                                            @foreach ($m->details as $detail)
                                                • {{ $detail->status ?? '-' }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach

                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Tidak ada maintenance berjalan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

                <div class="dashboard-card">

                    <h4 class="card-title">
                        Pengaduan Terbaru
                    </h4>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($pengaduanTerbaru as $p)
                                <tr>
                                    <td>{{ $p->created_at->format('d M') }}</td>
                                    <td>{{ $p->nama_pelapor }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <script>
                        new Chart(document.getElementById('asetChart'), {
                            type: 'bar',
                            data: {
                                labels: [
                                    'Tersedia',
                                    'Dipinjam',
                                    'Maintenance',
                                    'Dimusnahkan'
                                ],
                                datasets: [{
                                    data: [
                                        {{ $asetTersedia }},
                                        {{ $asetDipinjam }},
                                        {{ $asetMaintenance }},
                                        {{ $asetDimusnahkan }}
                                    ],
                                    backgroundColor: [
                                        '#198754',
                                        '#0d6efd',
                                        '#ffc107',
                                        '#dc3545'
                                    ],
                                    borderRadius: 8
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });

                        new Chart(document.getElementById('biayaChart'), {
                            type: 'bar',
                            data: {
                                labels: [
                                    'Maintenance',
                                    'Pengadaan'
                                ],
                                datasets: [{
                                    data: [
                                        {{ $totalBiayaMaintenance }},
                                        {{ $totalPengadaan }}
                                    ],
                                    backgroundColor: [
                                        '#ebca56',
                                        '#05253a'
                                    ],
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: value =>
                                                'Rp ' + value.toLocaleString('id-ID')
                                        }
                                    }
                                }
                            }
                        });
                    </script>

                @endsection
