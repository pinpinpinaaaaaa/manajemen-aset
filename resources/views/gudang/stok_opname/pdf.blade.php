@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center;">
        LAPORAN STOK OPNAME
    </h2>

    <p style="text-align:center; font-size:12px;">
        Kode: {{ $header->kode_opname }}
    </p>

    <p style="text-align:center; font-size:12px;">
        Tanggal: {{ \Carbon\Carbon::parse($header->created_at)->format('d M Y') }}
    </p>

    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:12px;">
        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($details as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td>{{ $d->stok_sistem }}</td>
                    <td>{{ $d->stok_fisik ?? '-' }}</td>
                    <td>{{ $d->selisih ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table width="100%" border="1" cellpadding="5">
        <tr>
            <td><strong>Total Selisih</strong></td>
            <td>{{ $totalSelisih }}</td>
        </tr>
    </table>
@endsection
