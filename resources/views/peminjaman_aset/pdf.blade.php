@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN RIWAYAT PEMINJAMAN ASET
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
            <td><strong>Total Peminjaman</strong></td>
            <td>{{ $data->count() }}</td>

            <td><strong>Total Item Dipinjam</strong></td>
            <td>
                {{ $data->sum(function ($p) {
                    return $p->details->sum('jumlah');
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
    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:11px; border-collapse: collapse;">

        <thead style="background:#f0f0f0;">
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Pengaju</th>
                <th>Divisi</th>
                <th>Detail Aset</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Approval</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $i => $l)
                <tr>
                    <td style="text-align:center;">
                        {{ $i + 1 }}
                    </td>

                    <td>{{ $l->id_peminjaman }}</td>

                    <td>{{ $l->nama_pengaju ?? '-' }}</td>

                    <td>{{ $l->divisi->nama_divisi ?? '-' }}</td>

                    <td style="font-size:10px;">
                        @foreach ($l->details as $d)
                            <div style="margin-bottom:4px;">

                                <strong>
                                    {{ $d->aset->nama_aset ?? '-' }}
                                </strong>

                                <br>

                                Qty :
                                {{ $d->jumlah }}

                                <br>

                                Status :
                                {{ ucfirst($d->status_pengembalian) }}

                                @if ($d->tanggal_dikembalikan)
                                    <br>
                                    Tgl Kembali :
                                    {{ \Carbon\Carbon::parse($d->tanggal_dikembalikan)->format('d/m/Y') }}
                                @endif

                                @if ($d->kondisi_kembali)
                                    <br>
                                    Kondisi :
                                    {{ ucwords(str_replace('_', ' ', $d->kondisi_kembali)) }}
                                @endif

                            </div>

                            @if (!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($l->created_at)->format('d M Y') }}
                    </td>

                    <td>
                        {{ ucfirst($l->status ?? '-') }}
                    </td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $l->decision_status ?? '-')) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">
                        Tidak ada data riwayat peminjaman
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
