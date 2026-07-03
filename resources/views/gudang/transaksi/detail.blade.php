@extends('layouts.app')

@section('title', 'Detail Transaksi Barang Gudang')

@section('content')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background-color: #f9fafb;
            color: #000000;
            line-height: 1.5;
        }

        /* Header Styles */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-box {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            background-color: #ebca56;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #000000;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #ebca56;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: #000000;
        }

        .nav-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            padding: 0.5rem;
        }

        .nav-button.home {
            color: #2563eb;
        }

        .nav-button svg {
            width: 16px;
            height: 16px;
        }

        /* Layout */
        .container {
            display: flex;
            min-height: calc(100vh - 73px);
        }

        /* Sidebar Styles */
        .sidebar {
            width: 256px;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 1rem;
        }

        .search-box {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .search-box:focus {
            outline: none;
            border-color: #ebca56;
        }

        .sidebar-label {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background-color: #ebca56;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            color: #000000;
        }

        .sidebar-item svg {
            width: 16px;
            height: 16px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.5rem;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            margin-bottom: 1rem;
            color: #000000;
            transition: color 0.2s;
        }

        .back-button:hover {
            color: #ebca56;
        }

        .back-button svg {
            width: 16px;
            height: 16px;
        }

        .page-title {
            font-size: 1.5rem;
            color: #000000;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        /* Receipt Container */
        .receipt-container {
            max-width: 960px;
            margin: 0 auto;
        }

        .receipt-header {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            border-radius: 0.5rem 0.5rem 0 0;
            padding: 2rem;
        }

        .receipt-title-section {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #ebca56;
        }

        .receipt-title {
            font-size: 1.875rem;
            color: #000000;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .receipt-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .receipt-id-section p:first-child {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .receipt-id-section p:last-child {
            color: #000000;
            font-weight: 500;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: #000000;
            font-weight: 500;
        }

        .status-diproses {
            background-color: #ebca56;
        }

        .status-disetujui {
            background-color: #10b981;
        }

        .status-ditolak {
            background-color: #ef4444;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }

        .info-section h3 {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .info-item {
            margin-bottom: 0.5rem;
        }

        .info-item p:first-child {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-bottom: 0.125rem;
        }

        .info-item p:last-child {
            color: #000000;
        }

        .info-item .email {
            color: #2563eb;
        }

        /* Items Table */
        .receipt-body {
            background-color: #ffffff;
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            padding: 2rem;
            padding-top: 0;
        }

        .section-title {
            color: #000000;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .items-table thead {
            background-color: #ebca56;
        }

        .items-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
            color: #000000;
            font-weight: 600;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table tbody tr:last-child {
            border-bottom: none;
        }

        .items-table td {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #000000;
        }

        .items-table td.keterangan {
            color: #6b7280;
        }

        /* Timeline */
        .receipt-footer {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            padding: 2rem;
            padding-top: 0;
        }

        .timeline {
            margin-top: 0;
        }

        .timeline-item {
            display: flex;
            gap: 1rem;
            position: relative;
        }

        .timeline-dot-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ebca56;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px #ebca56;
            z-index: 1;
        }

        .timeline-line {
            width: 2px;
            flex: 1;
            background-color: rgba(235, 202, 86, 0.3);
            margin-top: 0.25rem;
            min-height: 40px;
        }

        .timeline-content {
            flex: 1;
            padding-bottom: 1rem;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .timeline-status {
            color: #000000;
            font-weight: 500;
        }

        .timeline-description {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .timeline-date {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Action Buttons */
        .action-buttons {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid;
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-print {
            background-color: #ffffff;
            border-color: #d1d5db;
            color: #000000;
        }

        .btn-print:hover {
            border-color: #ebca56;
            background-color: #fffbeb;
        }

        .btn-reject {
            background-color: #ffffff;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .btn-reject:hover {
            background-color: #fef2f2;
        }

        .btn-approve {
            background-color: #ebca56;
            border-color: #ebca56;
            color: #000000;
        }

        .btn-approve:hover {
            background-color: #d4b84d;
        }

        /* Print Styles */
        @media print {

            .header,
            .sidebar,
            .back-button,
            .breadcrumb,
            .action-buttons {
                display: none !important;
            }

            .container {
                display: block;
            }

            .main-content {
                padding: 0;
            }

            .receipt-container {
                max-width: 100%;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <main class="main-content">
        <button class="back-button" onclick="window.history.back()">
            ← Kembali ke Daftar
        </button>

        <h1 class="page-title">Detail Transaksi Barang Gudang</h1>

        <div class="breadcrumb">
            <a href="{{ route('gudang.index') }}">Dashboard</a>
            <span>/</span>
            <a href="{{ route('gudang.transaksi.index') }}">Transaksi Gudang</a>
            <span>/</span>
            <span>Detail</span>
        </div>

        <div class="receipt-container">

            {{-- HEADER --}}
            <div class="receipt-header">
                <div class="receipt-title-section">
                    <h2 class="receipt-title">TRANSAKSI GUDANG</h2>
                    <p class="receipt-subtitle">Sistem Informasi Gudang</p>
                </div>

                <div class="receipt-info-row">
                    <div class="receipt-id-section">
                        <p>ID Transaksi</p>
                        <p>{{ $transaksi->id_transaksi }}</p>
                    </div>

                    <div>
                        @if ($transaksi->jenis_transaksi === 'masuk')
                            <span class="status-badge status-disetujui">MASUK</span>
                        @elseif ($transaksi->jenis_transaksi === 'keluar')
                            <span class="status-badge status-ditolak">KELUAR</span>
                        @else
                            <span class="status-badge status-diproses">PENYESUAIAN</span>
                        @endif
                    </div>
                </div>

                {{-- INFO --}}
                <div class="info-grid">
                    <div class="info-section">
                        <h3>Informasi Transaksi</h3>
                        <div class="info-item">
                            <p>Tanggal</p>
                            <p>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</p>
                        </div>
                        <div class="info-item">
                            <p>Referensi</p>
                            <p>{{ $transaksi->referensi ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Ringkasan</h3>
                        <div class="info-item">
                            <p>Total Item</p>
                            <p>{{ $transaksi->details->count() }} barang</p>
                        </div>
                        <div class="info-item">
                            <p>Total Biaya</p>
                            <p>
                                Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL BARANG --}}
            <div class="receipt-body">
                <h3 class="section-title">Detail Barang</h3>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksi->details as $i => $detail)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                <td>
                                    {{ $detail->id_barang }}
                                </td>

                                <td>
                                    {{ $detail->barang->nama_barang }}
                                </td>

                                <td>
                                    {{ $detail->jumlah_input }}
                                    {{ $detail->satuan }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right">Total Biaya</th>
                            <th colspan="2">
                                Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>


            </div>

            {{-- FOOTER --}}
            <div class="receipt-footer">
                <h3 class="section-title">Riwayat Transaksi</h3>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot-container">
                            <div class="timeline-dot"></div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <div>
                                    <p class="timeline-status">Transaksi Dibuat</p>
                                    <p class="timeline-description">
                                        Transaksi {{ $transaksi->jenis_transaksi }} dicatat ke sistem
                                    </p>
                                </div>
                                <p class="timeline-date">
                                    {{ $transaksi->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="action-buttons">
                <button class="btn btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>

        </div>
    </main>
@endsection
