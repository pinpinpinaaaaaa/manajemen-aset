@extends('pdf.layout')

@section('content')

    {{-- ========================= DATA PENGADAAN ========================= --}}

    <h2 style="margin-bottom:5px; text-align:center;">
        DATA PENGADAAN BARANG DAN JASA
    </h2>

    <p style="margin-top:0; text-align:center;">
        Tahun {{ $laporan->tahun }}
    </p>

    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="50%">
                <strong>Total Pengadaan</strong><br>
                {{ $totalPengadaan }}
            </td>
            <td width="50%">
                <strong>Total Biaya Pengadaan</strong><br>
                Rp {{ number_format($totalBiayaPengadaan, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th width="5%">No</th>
                <th width="15%">ID Pengadaan</th>
                <th width="15%">Pengaju</th>
                <th width="30%">Barang/Jasa</th>
                <th width="10%">Jumlah Item</th>
                <th width="15%">Total Biaya</th>
                <th width="10%">Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($pengadaanList as $i => $pengadaan)
                <tr>
                    <td align="center">
                        {{ $i + 1 }}
                    </td>

                    <td align="center">
                        {{ $pengadaan->id_pengadaan }}
                    </td>

                    <td>
                        {{ $pengadaan->nama_pengaju }}
                    </td>

                    <td>
                        @foreach ($pengadaan->details as $detail)
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

                    <td align="center">
                        {{ $pengadaan->details->count() }}
                    </td>

                    <td>
                        Rp {{ number_format($pengadaan->total_biaya, 0, ',', '.') }}
                    </td>

                    <td align="center">
                        {{ $pengadaan->status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center">
                        Tidak ada data pengadaan pada tahun ini
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>


    {{-- ========================= DATA GUDANG ========================= --}}
    <h2 style="margin-bottom:5px; text-align:center;">DATA GUDANG</h2>
    <p style="margin-top:0; text-align:center;">Tahun {{ $laporan->tahun }}</p>

    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="33%">
                <strong>Jenis Barang</strong><br>{{ $jumlahJenisBarang }}
            </td>
            <td width="33%">
                <strong>Total Transaksi</strong><br>{{ $laporan->summary->total_transaksi_gudang }}
            </td>
            <td width="33%">
                <strong>Nilai Transaksi</strong><br>
                Rp {{ number_format($totalNilaiTransaksi, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th>No</th>
                <th>Nama Barang</th>
                <th>Stok Awal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Stok Akhir</th>
                <th>Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gudangList as $i => $item)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td align="center">{{ $item['nama_barang'] ?? '-' }}</td>
                    <td align="center">{{ $item['stok_awal'] ?? 0 }}</td>
                    <td align="center">{{ $item['stok_masuk'] ?? 0 }}</td>
                    <td align="center">{{ $item['stok_keluar'] ?? 0 }}</td>
                    <td align="center">{{ $item['stok_akhir'] ?? 0 }}</td>
                    <td align="center">
                        Rp {{ number_format($item['nilai_transaksi'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>

    {{-- ========================= DATA MAINTENANCE ========================= --}}
    <h2 style="margin-bottom:5px; text-align:center;">DATA MAINTENANCE</h2>
    <p style="margin-top:0; text-align:center;">Tahun {{ $laporan->tahun }}</p>

    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="33%">
                <strong>Total Maintenance</strong><br>
                {{ $totalMaintenance }}
            </td>
            <td width="33%">
                <strong>Aset Termaintenance</strong><br>
                {{ $asetTermaintenance }}
            </td>
            <td width="33%">
                <strong>Total Biaya</strong><br>
                Rp {{ number_format($totalBiayaMaintenance, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Aset</th>
                <th width="20%">Lokasi</th>
                <th width="23%">Kerusakan</th>
                <th width="20%">Biaya</th>
            </tr>
        </thead>

        <tbody>
            @php $no = 1; @endphp

            @forelse ($maintenanceList as $maintenance)
                @foreach ($maintenance->details as $detail)
                    <tr>
                        <td align="center">{{ $no++ }}</td>

                        <td align="center">
                            {{ $maintenance->tanggal_laporan?->format('d M Y') }}
                        </td>

                        <td>
                            {{ $detail->aset?->nama_aset ?? '-' }}
                        </td>

                        <td>
                            {{ $maintenance->gedung?->nama_gedung ?? '-' }}<br>
                            <small>
                                {{ $maintenance->ruangan?->nama_ruangan ?? '-' }}
                            </small>
                        </td>

                        <td>
                            {{ $detail->kerusakan ?? '-' }}
                        </td>

                        <td align="right">
                            Rp {{ number_format($detail->biaya ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach

            @empty
                <tr>
                    <td colspan="6" align="center">
                        Tidak ada data maintenance
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>

    {{-- ========================= DATA PEMUSNAHAN ========================= --}}
    <h2 style="margin-bottom:5px; text-align:center;">DATA PEMUSNAHAN</h2>
    <p style="margin-top:0; text-align:center;">Tahun {{ $laporan->tahun }}</p>

    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="33%">
                <strong>Total Pemusnahan</strong><br>{{ $totalPemusnahan }}
            </td>
            <td width="33%">
                <strong>Biaya Keluar</strong><br>
                Rp {{ number_format($totalBiayaKeluarPemusnahan, 0, ',', '.') }}
            </td>
            <td width="33%">
                <strong>Nilai Masuk</strong><br>
                Rp {{ number_format($totalNilaiMasukPemusnahan, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Aset</th>
                <th>Lokasi</th>
                <th>Metode</th>
                <th>Biaya Keluar</th>
                <th>Nilai Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pemusnahanList as $i => $p)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td align="center">
                        {{ \Carbon\Carbon::parse($p->tanggal_pemusnahan)->format('d M Y') }}
                    </td>
                    <td align="center">
                        {{ $p->aset?->nama_aset ?? '-' }}
                    </td>
                    <td align="center">
                        {{ $p->gedung?->nama_gedung ?? '-' }}<br>
                        <small>{{ $p->ruangan?->nama_ruangan ?? '-' }}</small>
                    </td>
                    <td align="center">
                        {{ $p->metode ?? '-' }}
                    </td>
                    <td align="center">
                        Rp {{ number_format($p->biaya_keluar ?? 0, 0, ',', '.') }}
                    </td>
                    <td align="center">
                        Rp {{ number_format($p->nilai_masuk ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
