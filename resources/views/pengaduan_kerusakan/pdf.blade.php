@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN PENGADUAN KERUSAKAN ASET
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
            <td><strong>Total Pengaduan</strong></td>
            <td>{{ $data->count() }}</td>

            <td><strong>Disetujui</strong></td>
            <td>{{ $totalDisetujui ?? 0 }}</td>
        </tr>
        <tr>
            <td><strong>Ditolak</strong></td>
            <td>{{ $totalDitolak ?? 0 }}</td>

            <td><strong>Menunggu</strong></td>
            <td>{{ $data->where('decision_status', 'menunggu_persetujuan')->count() }}</td>
        </tr>
    </table>

    {{-- ===================== TABLE ===================== --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:10px; border-collapse: collapse;">
        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>ID Pengaduan</th>
                <th>Pelapor</th>
                <th>Divisi</th>
                <th>Lokasi</th>
                <th>Detail Kerusakan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $p)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>

                    <td>{{ $p->id_pengaduan }}</td>

                    <td>
                        {{ $p->nama_pelapor }} <br>
                        <small>{{ $p->email_pelapor ?? '-' }}</small>
                    </td>

                    <td>{{ $p->divisi->nama_divisi ?? '-' }}</td>

                    <td>
                        {{ $p->gedung->nama_gedung ?? '-' }} <br>
                        {{ $p->ruangan->nama_ruangan ?? '-' }}
                    </td>

                    <td>
                        @foreach ($p->details as $d)
                            • <strong>{{ $d->aset->nama_aset ?? 'Aset tidak ditemukan' }}</strong><br>

                            Keluhan: {{ $d->keluhan }} <br>

                            Kategori: {{ ucfirst($d->kategori_kerusakan) }} <br><br>
                        @endforeach
                    </td>

                    <td>{{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">
                        Tidak ada data pengaduan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
