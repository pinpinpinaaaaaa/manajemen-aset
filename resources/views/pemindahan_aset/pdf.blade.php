@extends('pdf.layout')

@section('content')

    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN PEMINDAHAN ASET
    </h2>

    <p style="text-align:center; font-size:12px; margin-bottom:20px;">
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
            <td><strong>Total Pemindahan</strong></td>
            <td>{{ $total ?? 0 }}</td>

            <td><strong>Disetujui</strong></td>
            <td>{{ $disetujui ?? 0 }}</td>
        </tr>

        <tr>
            <td><strong>Ditolak</strong></td>
            <td>{{ $ditolak ?? 0 }}</td>

            <td><strong>Belum Dipindahkan</strong></td>
            <td>{{ $belum ?? 0 }}</td>
        </tr>
    </table>


    {{-- ===================== TABLE ===================== --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:11px;">
        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Aset</th>
                <th>Dari</th>
                <th>Ke</th>
                <th>Tanggal</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Approval</th>
            </tr>
        </thead>

        <tbody>
            @php $no = 1; @endphp

            @forelse ($pemindahan as $p)
                @foreach ($p->details as $detail)
                    <tr>

                        <td>{{ $no++ }}</td>

                        <td>{{ $p->id_pemindahan }}</td>

                        <td>
                            {{ $detail->aset->id_aset ?? '-' }}
                            <br>
                            {{ $detail->aset->nama_aset ?? '-' }}
                        </td>

                        <td>
                            {{ $detail->gedungAsal->nama_gedung ?? '-' }}
                            -
                            {{ $detail->ruanganAsal->nama_ruangan ?? '-' }}
                        </td>

                        <td>
                            {{ $detail->gedungTujuan->nama_gedung ?? '-' }}
                            -
                            {{ $detail->ruanganTujuan->nama_ruangan ?? '-' }}
                        </td>

                        <td>
                            {{ $p->created_at->format('d M Y') }}
                        </td>

                        <td>
                            Rp{{ number_format($detail->biaya ?? 0, 0, ',', '.') }}
                        </td>

                        <td>
                            {{ $detail->status }}
                        </td>

                        <td>
                            {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                        </td>

                    </tr>
                @endforeach

            @empty

                <tr>
                    <td colspan="9" style="text-align:center;">
                        Tidak ada data pemindahan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
