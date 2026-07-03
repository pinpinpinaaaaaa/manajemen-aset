@extends('pdf.layout')

@section('content')
    <h2 style="text-align:center; margin-bottom:5px;">
        LAPORAN RIWAYAT PERMINTAAN BARANG & JASA
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
            <td>{{ $pengadaan->count() }}</td>

            <td><strong>Selesai</strong></td>
            <td>{{ $totalSelesai ?? 0 }}</td>
        </tr>
        <tr>
            <td><strong>Ditolak</strong></td>
            <td>{{ $totalDitolak ?? 0 }}</td>

            <td><strong>Diproses</strong></td>
            <td>{{ $pengadaan->where('status', '!=', 'Selesai')->count() }}</td>
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
                <th>Tgl Kebutuhan</th>
                <th>Detail Barang</th>
                <th>Total Biaya</th>
                <th>Status</th>
                <th>Approval</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengadaan as $i => $l)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>

                    <td>{{ $l->id_pengadaan }}</td>

                    <td>{{ $l->nama_pengaju ?? '-' }}</td>

                    <td>{{ $l->divisi->nama_divisi ?? '-' }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($l->tanggal_kebutuhan)->format('d M Y') }}
                    </td>

                    <td>
                        @foreach ($l->details as $d)
                            <table width="100%" style="font-size:9px; margin-bottom:5px;">

                                @if ($d->jenis == 'barang')
                                    <tr>
                                        <td width="30%"><strong>Barang</strong></td>
                                        <td>: {{ $d->nama_barang }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Merk</strong></td>
                                        <td>: {{ $d->merk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipe</strong></td>
                                        <td>: {{ $d->tipe_model ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Spesifikasi</strong></td>
                                        <td>: {{ $d->spesifikasi ?? '-' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td width="30%"><strong>Jasa</strong></td>
                                        <td>: {{ $d->kategori_jasa }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td><strong>Jumlah</strong></td>
                                    <td>: {{ $d->jumlah }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Harga</strong></td>
                                    <td>: Rp {{ number_format((float) $d->harga_satuan, 0, ',', '.') }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td>: Rp {{ number_format((float) $d->subtotal, 0, ',', '.') }}</td>
                                </tr>

                            </table>

                            @if (!$loop->last)
                                <hr style="margin:4px 0">
                            @endif
                        @endforeach
                    </td>

                    <td style="text-align:right;">
                        Rp {{ number_format((float) $l->total_biaya, 0, ',', '.') }}
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
