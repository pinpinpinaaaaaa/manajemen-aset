<style>
body {
    font-family: 'Aptos', sans-serif;
}
</style>

@include('pdf.header')

<h3>DATA GUDANG</h3>
<p>Tahun {{ $laporan->tahun }}</p>

<table width="100%" cellpadding="6" style="margin-bottom:15px;">
    <tr>
        <td><strong>Jenis Barang</strong><br>{{ $jumlahJenisBarang }}</td>
        <td><strong>Total Transaksi</strong><br>{{ $laporan->summary->total_transaksi_gudang }}</td>
        <td><strong>Nilai Transaksi</strong><br>Rp {{ number_format($totalNilaiTransaksi,0,',','.') }}</td>
    </tr>
</table>

<table width="100%" border="1" cellpadding="6">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Stok Awal</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Stok Akhir</th>
            <th>Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($gudangList as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item['nama_barang'] }}</td>
                <td>{{ $item['stok_awal'] }}</td>
                <td>{{ $item['stok_masuk'] }}</td>
                <td>{{ $item['stok_keluar'] }}</td>
                <td>{{ $item['stok_akhir'] }}</td>
                <td>Rp {{ number_format($item['nilai_transaksi'],0,',','.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="page-break-after: always;"></div>
