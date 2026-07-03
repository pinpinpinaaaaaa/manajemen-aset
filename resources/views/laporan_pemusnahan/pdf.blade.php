@extends('pdf.layout')

@section('content')

<h2 style="text-align:center; margin-bottom:5px;">
    LAPORAN PEMUSNAHAN ASET
</h2>

<p style="text-align:center; font-size:12px; margin-bottom:20px;">
    Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y') }}
</p>

<p style="text-align:center; font-size:12px; margin-bottom:20px;">
    Periode:
    @if(!empty($start_date) && !empty($end_date))
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
        <td><strong>Biaya Keluar</strong></td>
        <td>Rp{{ number_format($totalKeluar ?? 0,0,',','.') }}</td>

        <td><strong>Nilai Masuk</strong></td>
        <td>Rp{{ number_format($totalMasuk ?? 0,0,',','.') }}</td>
    </tr>
    <tr>
        <td><strong>Selesai</strong></td>
        <td>{{ $totalSelesai ?? 0 }}</td>

        <td><strong>Ditolak</strong></td>
        <td>{{ $totalDitolak ?? 0 }}</td>
    </tr>
</table>


{{-- ===================== TABLE ===================== --}}
<table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:11px;">
    <thead style="background:#f0f0f0;">
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Aset</th>
            <th>Lokasi</th>
            <th>Tanggal</th>
            <th>Metode</th>
            <th>Biaya</th>
            <th>Nilai</th>
            <th>Status</th>
            <th>Approval</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan as $i => $l)
        <tr>
            <td>{{ $i+1 }}</td>

            <td>{{ $l->id_pemusnahan }}</td>

            <td>{{ $l->aset->nama_aset ?? '-' }}</td>

            {{-- LOKASI --}}
            <td>
                {{ $l->aset->gedung->nama_gedung ?? 'Gedung #' . ($l->aset->id_gedung ?? '-') }}
                -
                {{ $l->aset->ruangan->nama_ruangan ?? 'Ruangan #' . ($l->aset->id_ruangan ?? '-') }}
            </td>

            <td>
                {{ \Carbon\Carbon::parse($l->tanggal_pemusnahan)->format('d M Y') }}
            </td>

            <td>{{ ucfirst($l->metode) }}</td>

            <td>
                Rp{{ number_format($l->biaya_keluar,0,',','.') }}
            </td>

            <td>
                Rp{{ number_format($l->nilai_masuk,0,',','.') }}
            </td>

            <td>{{ ucfirst($l->status ?? '-') }}</td>

            <td>{{ ucfirst($l->decision_status ?? '-') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="10" style="text-align:center;">
                Tidak ada data laporan pemusnahan
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
