@extends('pdf.layout-surat')

@section('content')
    <div style="text-align:center; margin-bottom:20px;">
        <h2 style="margin:0;">TANDA TERIMA EKSPEDISI</h2>

        <p style="font-size:12px; margin-top:5px;">
            ID Ekspedisi :
            <strong>{{ $data->id_ekspedisi }}</strong>
        </p>
    </div>

    <table width="100%" cellpadding="6" cellspacing="0" style="font-size:12px; margin-bottom:20px;">

        <tr>
            <td width="25%"><strong>Tanggal</strong></td>
            <td width="75%">
                {{ now()->format('d M Y') }}
            </td>
        </tr>

        <tr>
            <td><strong>Pengirim</strong></td>
            <td>
                {{ $data->nama_pengirim }}
                -
                {{ optional($data->divisi_pengirim)->nama_divisi ?? '-' }}
            </td>
        </tr>

        <tr>
            <td><strong>Instansi Tujuan</strong></td>
            <td>{{ $data->instansi_penerima }}</td>
        </tr>

        <tr>
            <td><strong>Penerima</strong></td>
            <td>{{ $data->nama_penerima }}</td>
        </tr>

        <tr>
            <td><strong>Alamat</strong></td>
            <td>{{ $data->alamat_penerima }}</td>
        </tr>

        <tr>
            <td><strong>Judul Kegiatan</strong></td>
            <td>{{ $data->judul_kegiatan }}</td>
        </tr>
    </table>

    {{-- ===================== ISI ===================== --}}
    <h4 style="margin-bottom:10px;">Isi Kiriman</h4>

    <table width="100%" border="1" cellspacing="0" cellpadding="6" style="border-collapse: collapse; font-size:11px;">

        <thead style="background:#f0f0f0;">
            <tr>
                <th width="5%">No</th>
                <th width="20%">Jenis</th>
                <th width="45%">Nama</th>
                <th width="10%">Jumlah</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @php $no = 1; @endphp

            {{-- DOKUMEN --}}
            @foreach ($data->dokumen as $d)
                <tr>
                    <td align="center">{{ $no++ }}</td>
                    <td>Dokumen</td>
                    <td>{{ $d->nama_dokumen }}</td>
                    <td align="center">1</td>
                    <td>{{ $d->jenis_dokumen ?? '-' }}</td>
                </tr>
            @endforeach

            {{-- BARANG --}}
            @foreach ($data->barang as $b)
                <tr>
                    <td align="center">{{ $no++ }}</td>
                    <td>Barang</td>
                    <td>{{ $b->nama_barang }}</td>
                    <td align="center">{{ $b->jumlah }}</td>
                    <td>
                        {{ $b->berat ?? '-' }}
                        {{ $b->satuan ?? '' }}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{-- ===================== TTD ===================== --}}
    <table width="100%" style="margin-top:50px; font-size:12px;">

        <tr>

            {{-- PENGIRIM --}}
            <td width="50%" align="center">

                Pengirim

                <br><br><br><br>

                <strong>
                    {{ $data->nama_pengirim }}
                </strong>

            </td>

            {{-- PENERIMA --}}
            <td width="50%" align="center" valign="top">

                <div style="text-align:center; font-size:12px;">

                    Diterima tanggal
                    <br>

                    <strong>
                        {{ optional($data->pengiriman)->waktu_diterima
                            ? \Carbon\Carbon::parse($data->pengiriman->waktu_diterima)->format('d M Y H:i')
                            : now()->format('d M Y H:i') }}
                    </strong>

                    <br>
                    oleh

                    <br><br>

                    {{-- =========================
                     TTD DIGITAL
                ========================== --}}
                    @if (optional($data->pengiriman)->jenis_ttd == 'digital' && optional($data->pengiriman)->ttd_digital)
                        <img src="{{ $data->pengiriman->ttd_digital }}"
                            style="
                            width:120px;
                            height:auto;
                            margin-top:5px;
                            margin-bottom:10px;
                        ">

                        {{-- =========================
                     TTD KOSONG
                ========================== --}}
                    @else
                        <div style="height:80px;"></div>
                    @endif

                    <br>

                    <strong>
                        {{ optional($data->pengiriman)->nama_penerima_ttd ?? '(....................)' }}
                    </strong>

                </div>

            </td>

        </tr>

    </table>
@endsection
