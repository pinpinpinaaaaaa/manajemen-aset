@extends('pdf.layout-surat')

@section('content')
<style>
    body { font-size: 12px; font-family: 'Aptos', sans-serif; }

    .outer-border {
        border: 1.5px solid #000;
        padding: 0;
    }

    .title-row {
        text-align: center;
        border-bottom: 1.5px solid #000;
        padding: 8px 0;
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 2px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 8px 12px;
        vertical-align: top;
        border-bottom: 1px solid #ccc;
        font-size: 12px;
    }

    .info-table tr:last-child td {
        border-bottom: none;
    }

    .label-col {
        width: 22%;
        white-space: nowrap;
    }

    .colon-col {
        width: 3%;
        text-align: center;
    }

    .value-col {
        width: 75%;
    }

    .sign-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
    }

    .sign-table td {
        padding: 12px;
        vertical-align: top;
        font-size: 12px;
    }

    .stamp-box {
        border: 1.5px solid #000;
        width: 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: bold;
        font-size: 13px;
        letter-spacing: 2px;
        margin-top: 10px;
    }

    .dotted-line {
        border-bottom: 1px dotted #000;
        display: inline-block;
        min-width: 40px;
    }
</style>

<div class="outer-border">

    {{-- JUDUL --}}
    <div class="title-row">TANDA TERIMA</div>

    {{-- TABEL INFO --}}
    <table class="info-table">

        {{-- TELAH DITERIMA DARI --}}
        <tr>
            <td class="label-col">Telah diterima dari</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                {{ $data->nama_pengirim }}<br>
                @if ($data->instansi_pengirim)
                    {{ $data->instansi_pengirim }}<br>
                @endif
                @if ($data->alamat_pengirim)
                    {{ $data->alamat_pengirim }}<br>
                @else
                    {{ optional($data->divisi_pengirim)->nama_divisi ?? '' }}
                @endif
            </td>
        </tr>

        {{-- DITUJUKAN KEPADA --}}
        <tr>
            <td class="label-col">Ditujukan kepada</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                <strong>Kepada Yth.</strong><br>
                <strong>{{ $data->nama_penerima }}</strong><br>
                @if ($data->instansi_penerima)
                    <strong>{{ $data->instansi_penerima }}</strong><br>
                @endif
                @if ($data->alamat_penerima)
                    <strong>{{ $data->alamat_penerima }}</strong><br>
                @endif
            </td>
        </tr>

        {{-- BERUPA --}}
        <tr>
            <td class="label-col">Berupa</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                @php
                    $no = 1;
                    $items = [];
                    foreach ($data->dokumen as $d) {
                        $items[] = $d->nama_dokumen . ($d->jumlah > 1 ? ' (' . $d->jumlah . ' lembar)' : '');
                    }
                    foreach ($data->barang as $b) {
                        $items[] = $b->nama_barang . ' — ' . $b->jumlah . ' ' . ($b->satuan ?? 'pcs');
                    }
                @endphp

                @foreach ($items as $item)
                    {{ $loop->iteration }}. {{ $item }}<br>
                @endforeach

                @if ($data->keterangan)
                    <br><em>{{ $data->keterangan }}</em>
                @endif
            </td>
        </tr>

    </table>

    {{-- AREA TTD --}}
    <table class="sign-table" style="border-top: 1.5px solid #000;">
        <tr>

            {{-- STAMP KEUANGAN --}}
            <td width="30%" align="center" valign="middle" style="border-right: 1px solid #ccc;">
                <table border="1" cellpadding="12" cellspacing="0"
                    style="border-collapse:collapse; border-color:#000; margin: 10px auto;">
                    <tr>
                        <td style="font-weight:bold; font-size:13px; letter-spacing:3px; text-align:center; width:90px; height:70px; vertical-align:middle;">
                            KEUANGAN
                        </td>
                    </tr>
                </table>
            </td>

            {{-- TANDA TANGAN PENERIMA --}}
            <td width="70%" valign="top" style="padding: 12px 20px;">

                <span>
                    ............,
                    @php
                        $tgl = optional($data->pengiriman)->waktu_diterima
                            ? \Carbon\Carbon::parse($data->pengiriman->waktu_diterima)
                            : now();
                    @endphp
                    {{ $tgl->translatedFormat('d') }}-{{ $tgl->translatedFormat('m') }}-{{ $tgl->format('Y') }}
                </span>

                <br><br>

                <span>Diterima oleh :</span>

                <br><br>

                @if (optional($data->pengiriman)->jenis_ttd == 'digital' && optional($data->pengiriman)->ttd_digital)
                    <img src="{{ $data->pengiriman->ttd_digital }}"
                        style="width:110px; height:auto; margin: 4px 0;">
                @else
                    <div style="height: 60px;"></div>
                @endif

                <div style="border-top: 1px solid #000; display: inline-block; min-width: 160px; padding-top: 4px;">
                    {{ optional($data->pengiriman)->nama_penerima_ttd ?? '' }}
                </div>

                <br>
                <span style="font-size:11px; color:#333;">Nama Jelas</span>

                <br><br>

                <span>Jam &nbsp;&nbsp;: ........................................</span><br>
                <span>Telp &nbsp;: ........................................</span>

            </td>

        </tr>
    </table>

</div>
@endsection
