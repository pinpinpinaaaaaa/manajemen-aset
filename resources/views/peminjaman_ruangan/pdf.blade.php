@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN RIWAYAT PEMINJAMAN RUANGAN
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
    <table width="100%" cellpadding="6" cellspacing="0" border="1"
        style="margin-bottom:20px; font-size:12px; border-collapse: collapse;">
        <tr>
            <td><strong>Total Peminjaman</strong></td>
            <td>{{ $data->count() }}</td>

            <td><strong>Total Sesi Ruangan</strong></td>
            <td>
                {{ $data->sum(function ($p) {
                    return $p->details->count();
                }) }}
            </td>
        </tr>
        <tr>
            <td><strong>Selesai</strong></td>
            <td>{{ $totalSelesai ?? 0 }}</td>

            <td><strong>Ditolak</strong></td>
            <td>{{ $totalDitolak ?? 0 }}</td>
        </tr>
    </table>

    {{-- ===================== TABLE ===================== --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="4" style="font-size:10px; border-collapse: collapse;">

        <thead style="background:#f0f0f0;">
            <tr>
                <th width="3%">No</th>
                <th width="10%">ID</th>
                <th width="10%">Pengaju</th>
                <th width="8%">Divisi</th>
                <th width="28%">Detail Sesi</th>
                <th width="13%">Konsumsi</th>
                <th width="5%">Status</th>
                <th width="5%">Approval</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $i => $l)
                <tr>

                    <td style="text-align:center;">
                        {{ $i + 1 }}
                    </td>

                    <td>
                        {{ $l->id_peminjaman }}
                    </td>

                    <td>
                        {{ $l->nama_pengaju }}
                    </td>

                    <td>
                        {{ $l->divisi->nama_divisi ?? '-' }}
                    </td>

                    {{-- DETAIL SESI --}}
                    <td>
                        @foreach ($l->details as $detail)
                            <strong>
                                {{ $detail->ruangan->nama_ruangan ?? '-' }}
                            </strong>
                            <br>

                            Tanggal:
                            {{ \Carbon\Carbon::parse($detail->tanggal_mulai)->format('d-m-Y') }}
                            <br>

                            Jam:
                            {{ $detail->jam_mulai }}
                            -
                            {{ $detail->jam_selesai }}
                            <br>

                            <strong>Aset:</strong>
                            <br>

                            @forelse ($detail->aset as $aset)
                                • {{ $aset->aset->nama_aset ?? '-' }}
                                ({{ $aset->jumlah }})
                                <br>
                            @empty
                                -
                                <br>
                            @endforelse

                            @if (!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </td>

                    {{-- KONSUMSI --}}
                    <td>
                        @forelse ($l->konsumsi as $k)
                            • {{ $k->jenis_konsumsi }}
                            ({{ $k->jumlah }})
                            <br>
                        @empty
                            -
                        @endforelse
                    </td>

                    <td>
                        {{ $l->status }}
                    </td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $l->decision_status)) }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">
                        Tidak ada data riwayat peminjaman ruangan
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
@endsection
