@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN MAINTENANCE ASET
    </h2>

    <p style="text-align:center; font-size:12px; margin-bottom:20px;">
        Tanggal Cetak:
        {{ \Carbon\Carbon::now()->format('d M Y') }}
    </p>

    <p style="text-align:center; font-size:12px; margin-bottom:20px;">
        Periode:
        @if (!empty($start_date) && !empty($end_date))
            {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
            —
            {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
        @else
            Semua Periode
        @endif
    </p>

    {{-- ================= SUMMARY ================= --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="6" style="margin-bottom:20px; font-size:12px;">

        <tr>
            <td><strong>Total Biaya</strong></td>
            <td>Rp{{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</td>

            <td><strong>Selesai</strong></td>
            <td>{{ $totalSelesai ?? 0 }}</td>
        </tr>

        <tr>
            <td><strong>Ditolak</strong></td>
            <td>{{ $totalDitolak ?? 0 }}</td>

            <td></td>
            <td></td>
        </tr>

    </table>


    {{-- ================= TABLE ================= --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:11px;">

        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Aset</th>
                <th>Lokasi</th>
                <th>Laporan</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Durasi</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Approval</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($maintenance as $i => $m)
                <tr>

                    <td>{{ $i + 1 }}</td>

                    <td>{{ $m->id_maintenance }}</td>

                    <td>
                        {{ $m->aset->nama_aset ?? '-' }}
                    </td>

                    {{-- LOKASI --}}
                    <td>
                        {{ $m->gedung->nama_gedung ?? 'G#' . $m->id_gedung }}
                        -
                        {{ $m->ruangan->nama_ruangan ?? 'R#' . $m->id_ruangan }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($m->tanggal_laporan)->format('d M Y') }}
                    </td>

                    <td>
                        {{ $m->tanggal_mulai ? \Carbon\Carbon::parse($m->tanggal_mulai)->format('d M Y') : '-' }}
                    </td>

                    <td>
                        {{ $m->tanggal_selesai ? \Carbon\Carbon::parse($m->tanggal_selesai)->format('d M Y') : '-' }}
                    </td>

                    <td>
                        {{ $m->durasi_jam ? $m->durasi_jam . ' jam' : '-' }}
                    </td>

                    <td>
                        Rp{{ number_format($m->biaya ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ ucfirst($m->status ?? '-') }}
                    </td>

                    <td>
                        {{ ucfirst($m->decision_status ?? '-') }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center;">
                        Tidak ada data maintenance
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
@endsection
