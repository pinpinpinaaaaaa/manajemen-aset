@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN RIWAYAT PERMINTAAN BARANG GUDANG
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

            <td><strong>Diproses</strong></td>
            <td>{{ $laporan->where('status', '!=', 'Selesai')->count() }}</td>
        </tr>
    </table>

    {{-- ===================== TABLE ===================== --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:10px; border-collapse: collapse;">
        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Pengaju</th>
                <th>Divisi</th>
                <th>Detail Barang</th>
                <th>Tgl Kebutuhan</th>
                <th>Status</th>
                <th>Approval</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $i => $l)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>

                    <td>{{ $l->id_permintaan }}</td>

                    <td>{{ $l->nama_pengaju ?? '-' }}</td>

                    <td>{{ $l->divisi->nama_divisi ?? '-' }}</td>

                    <td>
                        @foreach ($l->details as $d)
                            - {{ $d->barang->nama_barang ?? 'Barang tidak ditemukan' }}
                            ({{ $d->jumlah }})
                            <br>
                        @endforeach
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($l->tanggal_kebutuhan)->format('d M Y') }}
                    </td>

                    <td>{{ $l->status ?? '-' }}</td>

                    <td>{{ $l->decision_status ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">
                        Tidak ada data riwayat permintaan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
