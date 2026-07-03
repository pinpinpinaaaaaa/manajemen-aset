@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN RIWAYAT EKSPEDISI
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
            <td><strong>Total Ekspedisi</strong></td>
            <td>{{ $laporan->count() }}</td>

            <td><strong>Selesai</strong></td>
            <td>{{ $totalSelesai ?? 0 }}</td>
        </tr>
        <tr>
            <td><strong>Ditolak</strong></td>
            <td>{{ $totalDitolak ?? 0 }}</td>

            <td><strong>Diproses</strong></td>
            <td>
                {{ $laporan->filter(fn($l) => $l->decision_status !== 'ditolak' && optional($l->pengiriman)->status_pengiriman !== 'selesai')->count() }}
            </td>
        </tr>
    </table>

    {{-- ===================== TABLE ===================== --}}
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:10px; border-collapse: collapse;">

        <thead style="background:#f0f0f0;">
            <tr>
                <th width="3%">No</th>
                <th width="8%">ID</th>

                <th width="10%">Nama Pengaju</th>
                <th width="10%">Divisi Pengaju</th>

                <th width="15%">Data Pengirim</th>
                <th width="18%">Data Penerima</th>

                <th width="15%">Detail Isi</th>

                <th width="7%">Approval</th>
                <th width="7%">Pengiriman</th>
                <th width="7%">Kirim</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($laporan as $i => $l)
                <tr>
                    <td style="text-align:center;">
                        {{ $i + 1 }}
                    </td>

                    <td>
                        {{ $l->id_ekspedisi }}
                    </td>

                    {{-- NAMA PENGAJU --}}
                    <td>
                        {{ $l->nama_pengaju }}
                    </td>

                    {{-- DIVISI PENGAJU --}}
                    <td>
                        {{ $l->divisi_pengaju->nama_divisi ?? '-' }}
                    </td>

                    {{-- DATA PENGIRIM --}}
                    <td>
                        <strong>{{ $l->nama_pengirim }}</strong><br>

                        Email:
                        {{ $l->email_pengirim ?? '-' }}
                        <br>

                        HP:
                        {{ $l->no_hp_pengirim ?? '-' }}
                        <br>

                        Divisi:
                        {{ $l->divisi_pengirim->nama_divisi ?? '-' }}
                    </td>

                    {{-- DATA PENERIMA --}}
                    <td>
                        <strong>{{ $l->nama_penerima }}</strong><br>

                        Email:
                        {{ $l->email_penerima ?? '-' }}
                        <br>

                        HP:
                        {{ $l->no_hp_penerima ?? '-' }}
                        <br>

                        Instansi:
                        {{ $l->instansi_penerima ?? '-' }}
                        <br>

                        Alamat:
                        {{ $l->alamat_penerima }}
                    </td>

                    {{-- DETAIL --}}
                    <td>
                        @php
                            $ada = false;
                        @endphp

                        @foreach ($l->dokumen as $d)
                            • {{ $d->nama_dokumen }}<br>
                            @php $ada = true; @endphp
                        @endforeach

                        @foreach ($l->barang as $b)
                            • {{ $b->nama_barang }} ({{ $b->jumlah }})<br>
                            @php $ada = true; @endphp
                        @endforeach

                        @if (!$ada)
                            -
                        @endif
                    </td>

                    {{-- APPROVAL --}}
                    <td style="text-align:center;">
                        {{ ucfirst(explode('_', $l->decision_status)[0]) }}

                        @if ($l->approved_by)
                            <br>
                            <small>{{ $l->approved_by }}</small>
                        @endif
                    </td>

                    {{-- STATUS PENGIRIMAN --}}
                    <td style="text-align:center;">
                        {{ ucfirst(optional($l->pengiriman)->status_pengiriman ?? '-') }}

                        @if (optional($l->pengiriman)->no_resi)
                            <br>
                            <small>
                                {{ $l->pengiriman->no_resi }}
                            </small>
                        @endif
                    </td>

                    {{-- TGL KIRIM --}}
                    <td style="text-align:center;">
                        {{ optional($l->pengiriman)->waktu_dikirim
                            ? \Carbon\Carbon::parse($l->pengiriman->waktu_dikirim)->format('d M Y')
                            : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">
                        Tidak ada data riwayat ekspedisi
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
