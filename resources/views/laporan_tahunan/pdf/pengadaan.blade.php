@extends('pdf.layout')

@section('content')

    @include('pdf.header')

    <h2 style="margin-bottom:5px;">
        Laporan Pengadaan Barang dan Jasa
    </h2>

    <p style="margin-top:0; margin-bottom:10px;">
        Tahun {{ $laporan->tahun }}
    </p>

    {{-- ================= RINGKASAN ================= --}}
    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="50%">
                <strong>Total Pengadaan</strong><br>
                {{ $totalPengadaan }} Pengajuan
            </td>
            <td width="50%">
                <strong>Total Biaya</strong><br>
                Rp {{ number_format($totalBiayaPengadaan, 0, ',', '.') }}
            </td>
        </tr>
    </table>


    {{-- ================= TABEL PENGADAAN ================= --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th width="5%">No</th>
                <th width="15%">ID Pengadaan</th>
                <th width="20%">Pengaju</th>
                <th width="25%">Barang/Jasa</th>
                <th width="10%">Jumlah Item</th>
                <th width="15%">Biaya</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengadaanList as $i => $pengadaan)
                <tr>
                    <td align="center">
                        {{ $i + 1 }}
                    </td>

                    <td>
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

@endsection
