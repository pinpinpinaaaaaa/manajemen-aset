@extends('pdf.layout')

@section('content')

    <h2 style="margin-bottom:5px; text-align:center;">DATA PEMUSNAHAN</h2>
    <p style="margin-top:0; text-align:center;">Tahun {{ $laporan->tahun }}</p>

    {{-- ================= RINGKASAN PEMUSNAHAN ================= --}}
    @php
        $totalAktif = $pemusnahanList->count();
    @endphp

    <table width="100%" cellspacing="0" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="33%">
                <strong>Total Pemusnahan</strong><br>{{ $totalPemusnahan }}
            </td>
            <td width="33%">
                <strong>Biaya Keluar</strong><br>
                Rp {{ number_format($totalBiayaKeluarPemusnahan,0,',','.') }}
            </td>
            <td width="33%">
                <strong>Nilai Masuk</strong><br>
                Rp {{ number_format($totalNilaiMasukPemusnahan,0,',','.') }}
            </td>
        </tr>
    </table>

    {{-- ================= TABEL PEMUSNAHAN ================= --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th >No</th>
                <th >Tanggal</th>
                <th >Aset</th>
                <th >Lokasi</th>
                <th >Metode</th>
                <th >Biaya Keluar</th>
                <th >Nilai Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pemusnahanList as $i => $p)
                <tr>
                    <td align="center">{{ $i+1 }}</td>
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
                        Rp {{ number_format($p->biaya_keluar ?? 0,0,',','.') }}
                    </td>
                    <td align="center">
                        Rp {{ number_format($p->nilai_masuk ?? 0,0,',','.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
