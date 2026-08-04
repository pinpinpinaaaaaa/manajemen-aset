@extends('pdf.layout-surat')

@section('content')
    <div style="
        border:1px solid #000;
        padding:15px;
        font-size:13px;
    ">

        {{-- PENERIMA --}}
        <div style="margin-bottom:15px;">

            <h3 style="margin-bottom:8px;">
                Kepada Yth. :
            </h3>

            <strong style="font-size:16px;">
                {{ $data->instansi_penerima }}
            </strong>

            <br><br>

            {{ $data->alamat_penerima }}

            <br><br>

            <strong style="font-size:12px;">
                {{ $data->nama_penerima }}
            </strong>

        </div>

        <hr>

        {{-- ISI --}}
        <div style="margin-top:15px;">

            <strong>{{ $data->judul_kegiatan }}</strong>

            <strong>Isi Paket:</strong>

            <ul style="margin-top:8px;">

                @foreach ($data->dokumen as $d)
                    <li>
                        {{ $d->nama_dokumen }} - ({{ $d->jumlah }}) rangkap
                    </li>
                @endforeach

                @foreach ($data->barang as $b)
                    <li>
                        {{ $b->nama_barang }}
                        ({{ $b->jumlah }})
                    </li>
                @endforeach

            </ul>

        </div>

        <hr>

        {{-- PENGIRIM --}}
        <div style="margin-top:15px;">

            <h3 style="margin-bottom:8px;">
                Dari :
            </h3>

            <strong style="font-size:16px;">
                Lembaga Management Fakultas Ekonomi dan Bisnis Universitas Indonesia (LM FEB UI)
            </strong>

            <br><br>

            <strong style="font-size:12px;">
                {{ $data->nama_pengirim }} (Divisi {{ $data->divisi_pengirim?->nama_divisi }})
            </strong>

        </div>

        {{-- RESI --}}
        @if (optional($data->pengiriman)->no_resi)
            <div style="margin-top:15px;">

                <strong>No Resi:</strong>

                {{ $data->pengiriman->no_resi }}

            </div>
        @endif

    </div>
@endsection
