@extends('pdf.layout')

@section('content')
    <h3>DATA MAINTENANCE</h3>
    <p>Tahun {{ $laporan->tahun }}</p>

    <table width="100%" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td>
                <strong>Total Maintenance</strong><br>
                {{ $totalMaintenance }}
            </td>
            <td>
                <strong>Aset Termaintenance</strong><br>
                {{ $asetTermaintenance }}
            </td>
            <td>
                <strong>Total Biaya</strong><br>
                Rp {{ number_format($totalBiayaMaintenance, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table width="100%" border="1" cellpadding="6">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Aset</th>
                <th width="20%">Lokasi</th>
                <th width="25%">Kerusakan</th>
                <th width="15%">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($maintenanceList as $maintenance)
                @foreach ($maintenance->details as $detail)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $maintenance->tanggal_laporan?->format('d M Y') }}</td>
                        <td>{{ $detail->aset->nama_aset ?? '-' }}</td>
                        <td>
                            {{ $maintenance->gedung->nama_gedung ?? '-' }}<br>
                            <small>{{ $maintenance->ruangan->nama_ruangan ?? '-' }}</small>
                        </td>
                        <td>{{ $detail->kerusakan ?? '-' }}</td>
                        <td>Rp {{ number_format($detail->biaya ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" align="center">Tidak ada data maintenance</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
