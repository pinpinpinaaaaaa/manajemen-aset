<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page {
        size: A5;
        margin: 15mm;
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        margin: 0;
    }
    .label-box {
        border: 1px solid #000;
        padding: 12px;
    }
    h3 {
        margin: 0 0 6px 0;
        font-size: 14px;
    }
    hr {
        border: none;
        border-top: 1px solid #000;
        margin: 12px 0;
    }
    ul {
        margin: 6px 0;
        padding-left: 18px;
    }
</style>
</head>
<body>
    <div class="label-box">

        {{-- PENERIMA --}}
        <div style="margin-bottom:12px;">

            <h3>Kepada Yth. :</h3>

            <strong style="font-size:15px;">
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
        <div style="margin-top:12px;">

            @if($data->judul_kegiatan)
                <strong>{{ $data->judul_kegiatan }}</strong><br>
            @endif

            <strong>Isi Paket:</strong>

            <ul>

                @foreach ($data->dokumen as $d)
                    <li>{{ $d->nama_dokumen }} - ({{ $d->jumlah }}) rangkap</li>
                @endforeach

                @foreach ($data->barang as $b)
                    <li>{{ $b->nama_barang }} ({{ $b->jumlah }})</li>
                @endforeach

            </ul>

        </div>

        <hr>

        {{-- PENGIRIM --}}
        <div style="margin-top:12px;">

            <h3>Dari :</h3>

            <strong style="font-size:15px;">
                Lembaga Management Fakultas Ekonomi dan Bisnis Universitas Indonesia (LM FEB UI)
            </strong>

            <br><br>

            <strong style="font-size:12px;">
                {{ $data->nama_pengirim }} (Divisi {{ $data->divisi_pengirim?->nama_divisi }})
            </strong>

        </div>

        {{-- RESI --}}
        @if (optional($data->pengiriman)->no_resi)
            <div style="margin-top:12px;">
                <strong>No Resi:</strong>
                {{ $data->pengiriman->no_resi }}
            </div>
        @endif

    </div>
</body>
</html>
