@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN RIWAYAT PERMINTAAN KENDARAAN
    </h2>

    <p style="text-align:center; font-size:12px; margin-bottom:5px;">
        Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y') }}
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

    {{-- ===================== SUMMARY ===================== --}}
    <table width="100%" cellpadding="6" cellspacing="0" border="1" style="margin-bottom:20px; font-size:12px;">
        <tr>
            <td><strong>Total Permintaan</strong></td>
            <td>{{ $laporan->count() }}</td>

            <td><strong>Selesai</strong></td>
            <td>{{ $totalSelesai ?? 0 }}</td>
        </tr>
        <tr>
            <td><strong>Ditolak</strong></td>
            <td>{{ $totalDitolak ?? 0 }}</td>

            <td><strong>Dipakai</strong></td>
            <td>{{ $laporan->where('status', 'dipakai')->count() }}</td>
        </tr>
    </table>

    {{-- ===================== TABLE ===================== --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:10px; border-collapse: collapse;">

        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Detail Perjalanan</th>
                <th>Kendaraan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($laporan as $i => $l)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>

                    <td>{{ $l->id_permohonan }}</td>

                    <td>{{ $l->nama ?? '-' }}</td>

                    <td>{{ $l->divisi->nama_divisi ?? '-' }}</td>

                    {{-- DETAIL PERJALANAN --}}
                    <td>
                        @foreach ($l->details as $d)
                            - {{ $d->keperluan }} <br>
                            {{ $d->tempat_jemput }} → {{ $d->tempat_tujuan }} <br><br>
                        @endforeach
                    </td>

                    {{-- KENDARAAN --}}
                    <td>
                        @foreach ($l->details as $d)
                            @foreach ($d->items as $item)
                                - {{ $item->kendaraan->plat_nomor ?? '-' }}
                                ({{ $item->kendaraan->merk ?? '' }}
                                {{ $item->kendaraan->model ?? '' }})
                                <br>
                            @endforeach
                        @endforeach
                    </td>

                    {{-- TANGGAL --}}
                    <td>
                        @foreach ($l->details as $d)
                            {{ \Carbon\Carbon::parse($d->tanggal_mulai)->format('d M Y') }}
                            <br>
                        @endforeach
                    </td>

                    {{-- JAM --}}
                    <td>
                        @foreach ($l->details as $d)
                            {{ $d->jam_mulai }} - {{ $d->jam_selesai }} <br>
                        @endforeach
                    </td>

                    {{-- STATUS --}}
                    <td>{{ ucfirst($l->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">
                        Tidak ada data riwayat permintaan kendaraan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
