@extends('layouts.app')

@section('title', 'Detail Laporan Tahunan')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Detail Laporan Tahun {{ $laporan->tahun }}</h1>
                <nav class="breadcrumb">
                    <a href="{{ route('laporan_tahunan.index') }}">Laporan Tahunan</a>
                    <span class="separator">/</span>
                    <span class="current">Detail</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="ID Laporan" :value="$laporan->id_laporan_tahunan" />
                <x-stat-card label="Tahun" :value="$laporan->tahun" />
                <x-stat-card label="Jumlah Data" :value="$laporan->details->count()" />
            </div>

            <div class="controls-section">
                <div class="controls-left d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-primary filter-btn" data-view="pengadaan">Pengadaan</button>
                    <button class="btn btn-sm btn-primary filter-btn" data-view="gudang">Gudang</button>

                    <button class="btn btn-sm btn-primary jenis-btn" data-jenis="maintenance">Maintenance</button>
                    <button class="btn btn-sm btn-primary jenis-btn" data-jenis="pemusnahan">
                        Pemusnahan
                    </button>
                </div>

            </div>

            <div id="view-pengadaan" style="display:none;">

                <div class="stat-cards-grid mb-4">
                    <x-stat-card label="Total Pengadaan" :value="$totalPengadaan" />
                    <x-stat-card label="Total Biaya" :value="'Rp ' . number_format($totalBiayaPengadaan, 0, ',', '.')" />
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Pengadaan</th>
                                <th>Pengaju</th>
                                <th>Barang / Jasa</th>
                                <th>Jumlah Item</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($pengadaanList as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        {{ $item->id_pengadaan }}
                                    </td>

                                    <td>
                                        {{ $item->nama_pengaju }}
                                    </td>

                                    <td>
                                        @foreach ($item->details as $detail)
                                            @if ($detail->jenis == 'barang')
                                                • {{ $detail->nama_barang }}
                                                @if ($detail->merk)
                                                    ({{ $detail->merk }})
                                                @endif
                                            @else
                                                • Jasa {{ $detail->kategori_jasa }}
                                            @endif
                                            <br>
                                        @endforeach
                                    </td>

                                    <td>
                                        {{ $item->details->count() }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $item->status }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        Tidak ada data pengadaan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <div id="view-gudang" style="display:none;">

                <div class="stat-cards-grid mb-4">
                    <x-stat-card label="Jenis Barang" :value="$jumlahJenisBarang" />
                    <x-stat-card label="Total Transaksi" :value="number_format($laporan->summary->total_transaksi_gudang)" />
                    <x-stat-card label="Nilai Transaksi" :value="'Rp ' . number_format($totalNilaiTransaksi, 0, ',', '.')" />
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Stok Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Stok Akhir</th>
                                <th>Nilai Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gudangList as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td>{{ $item['stok_awal'] }}</td>
                                    <td>{{ $item['stok_masuk'] }}</td>
                                    <td>{{ $item['stok_keluar'] }}</td>
                                    <td>{{ $item['stok_akhir'] }}</td>
                                    <td>
                                        Rp {{ number_format($item['nilai_transaksi'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Tidak ada data gudang
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="view-maintenance" style="display:none;">

                <div class="stat-cards-grid mb-4">
                    <x-stat-card label="Total Maintenance" :value="$totalMaintenance" />
                    <x-stat-card label="Total Biaya" :value="'Rp ' . number_format($totalBiayaMaintenance, 0, ',', '.')" />
                    <x-stat-card label="Aset Termaintenance" :value="$asetTermaintenance" />
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Aset</th>
                                <th>Lokasi</th>
                                <th>Kerusakan</th>
                                <th>Biaya</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp

                            @forelse ($maintenanceList as $maintenance)

                                @foreach ($maintenance->details as $detail)
                                    <tr>
                                        <td>{{ $no++ }}</td>

                                        <td>
                                            {{ $maintenance->tanggal_laporan?->format('d M Y') }}
                                        </td>

                                        <td>
                                            {{ $detail->aset->nama_aset ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $maintenance->gedung->nama_gedung ?? '-' }}<br>
                                            <small class="text-muted">
                                                {{ $maintenance->ruangan->nama_ruangan ?? '-' }}
                                            </small>
                                        </td>

                                        <td>
                                            {{ $detail->kerusakan ?? '-' }}
                                        </td>

                                        <td>
                                            Rp {{ number_format($detail->biaya ?? 0, 0, ',', '.') }}
                                        </td>

                                        <td>
                                            {{ $detail->catatan ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Tidak ada data maintenance
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="view-pemusnahan" style="display:none;">

                <div class="stat-cards-grid mb-4">
                    <x-stat-card label="Total Pemusnahan" :value="$totalPemusnahan" />
                    <x-stat-card label="Biaya Keluar" :value="'Rp ' . number_format($totalBiayaKeluarPemusnahan, 0, ',', '.')" />
                    <x-stat-card label="Nilai Masuk" :value="'Rp ' . number_format($totalNilaiMasukPemusnahan, 0, ',', '.')" />
                </div>


                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Aset</th>
                                <th>Lokasi</th>
                                <th>Metode</th>
                                <th>Biaya Keluar</th>
                                <th>Nilai Masuk</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pemusnahanList as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pemusnahan)->format('d M Y') }}</td>
                                    <td>{{ $item->aset->nama_aset ?? '-' }}</td>
                                    <td>
                                        {{ $item->gedung->nama_gedung ?? '-' }}<br>
                                        <small class="text-muted">
                                            {{ $item->ruangan->nama_ruangan ?? '-' }}
                                        </small>
                                    </td>
                                    <td>{{ $item->metode ?? '-' }}</td>
                                    <td>
                                        Rp {{ number_format($item->biaya_keluar, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($item->nilai_masuk, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $item->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Tidak ada data pemusnahan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <a href="{{ route('laporan_tahunan.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>
                <a href="#" class="btn btn-primary" id="btnExportPdf">
                    Export PDF
                </a>
            </div>

        </div>
    </main>
    <script>
        let activeSection = 'pengadaan';
        document.addEventListener('DOMContentLoaded', () => {

            const views = document.querySelectorAll('[id^="view-"]');
            const filterBtns = document.querySelectorAll('.filter-btn');
            const jenisBtns = document.querySelectorAll('.jenis-btn');

            function hideAllViews() {
                views.forEach(v => v.style.display = 'none');
            }

            function resetButtons() {
                [...filterBtns, ...jenisBtns].forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
            }

            function showView(viewName, activeBtn) {
                activeSection = viewName; // <== INI KUNCINYA

                hideAllViews();
                resetButtons();

                const target = document.getElementById('view-' + viewName);
                if (target) {
                    target.style.display = 'block';
                }

                activeBtn.classList.remove('btn-outline-primary');
                activeBtn.classList.add('btn-primary');
            }

            filterBtns.forEach(btn => {
                btn.classList.add('btn-outline-primary');
                btn.addEventListener('click', () => {
                    showView(btn.dataset.view, btn);
                });
            });

            jenisBtns.forEach(btn => {
                btn.classList.add('btn-outline-primary');
                btn.addEventListener('click', () => {
                    showView(btn.dataset.jenis.toLowerCase(), btn);
                });
            });

            // DEFAULT → PENGADAAN
            document.querySelector('.filter-btn[data-view="pengadaan"]')?.click();

            document.getElementById('btnExportPdf')?.addEventListener('click', function(e) {
                e.preventDefault();

                let url = "{{ route('laporan_tahunan.export', $laporan->id_laporan_tahunan) }}";

                if (activeSection) {
                    url += '?section=' + activeSection;
                }

                window.location.href = url;
            });

        });
    </script>
@endsection
