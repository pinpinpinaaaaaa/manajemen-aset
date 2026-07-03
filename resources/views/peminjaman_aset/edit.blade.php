@extends('layouts.app')

@section('title', 'Edit Peminjaman Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit Peminjaman Aset</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>

                    <a href="{{ route('peminjaman_aset.index') }}">
                        Peminjaman Aset
                    </a>

                    <span class="separator">/</span>
                    <span class="current">Edit</span>
                </nav>
            </div>

            <div class="card mt-4 p-4">

                <form action="{{ route('peminjaman_aset.update', $peminjaman->id_peminjaman) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label>Nama Pengaju</label>
                        <input type="text" name="nama_pengaju" class="form-control"
                            value="{{ old('nama_pengaju', $peminjaman->nama_pengaju) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Email Pengaju</label>
                        <input type="email" name="email_pengaju" class="form-control"
                            value="{{ old('email_pengaju', $peminjaman->email_pengaju) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Divisi</label>

                        <select name="id_divisi" class="form-select" required>
                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ $d->id_divisi == $peminjaman->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label>Alasan</label>

                        <textarea name="alasan" class="form-control" rows="3" required>{{ old('alasan', $peminjaman->alasan) }}</textarea>
                    </div>

                    <h5 class="mb-3">Detail Aset</h5>

                    @php
                        $groupedDetails = $peminjaman->details
                            ->groupBy(function ($detail) {
                                return $detail->aset->nama_aset .
                                    '|' .
                                    $detail->aset->id_jenis_barang .
                                    '|' .
                                    $detail->tanggal_pinjam .
                                    '|' .
                                    $detail->tanggal_jatuh_tempo;
                            })
                            ->values();
                    @endphp

                    @foreach ($groupedDetails as $i => $group)
                        @php
                            $detail = $group->first();
                            $jumlah = $group->count();
                        @endphp
                        <div class="border rounded p-3 mb-3">

                            <div class="mb-3">
                                <label>Aset</label>

                                <select name="items[{{ $i }}][id_jenis_barang]" class="form-select" required>

                                    @foreach ($asetGrouped as $kategori => $items)
                                        <optgroup label="{{ $kategori }}">

                                            @foreach ($items as $aset)
                                                <option value="{{ $aset['id_jenis_barang'] }}"
                                                    data-nama="{{ $aset['nama_aset'] }}"
                                                    {{ $detail->aset->id_jenis_barang == $aset['id_jenis_barang'] ? 'selected' : '' }}>

                                                    {{ $aset['nama_aset'] }}
                                                    - {{ $aset['jenis_barang'] }}
                                                    (Sisa: {{ $aset['total_unit'] }})
                                                </option>
                                            @endforeach

                                        </optgroup>
                                    @endforeach

                                </select>

                                <input type="hidden" name="items[{{ $i }}][nama_aset]"
                                    value="{{ $detail->aset->nama_aset }}">
                            </div>

                            <div class="mb-3">
                                <label>Jumlah</label>

                                <input type="number" class="form-control" name="items[{{ $i }}][jumlah]"
                                    value="{{ $jumlah }}" required>
                            </div>

                            <div class="mb-3">
                                <label>Tanggal Pinjam</label>

                                <input type="date" class="form-control"
                                    name="items[{{ $i }}][tanggal_pinjam]"
                                    value="{{ $detail->tanggal_pinjam }}" required>
                            </div>

                            <div class="mb-3">
                                <label>Tanggal Kembali</label>

                                <input type="date" class="form-control"
                                    name="items[{{ $i }}][tanggal_jatuh_tempo]"
                                    value="{{ $detail->tanggal_jatuh_tempo }}" required>
                            </div>

                        </div>
                    @endforeach
                    <div class="text-end">
                        <a href="{{ route('peminjaman_aset.index') }}" class="btn btn-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            async function updateSisa(row) {

                const select = row.querySelector('select[name*="[id_jenis_barang]"]');
                const tanggalPinjam = row.querySelector('input[name*="[tanggal_pinjam]"]').value;
                const tanggalTempo = row.querySelector('input[name*="[tanggal_jatuh_tempo]"]').value;

                if (!select || !tanggalPinjam || !tanggalTempo) {
                    return;
                }

                const option = select.options[select.selectedIndex];

                const namaAset = option.dataset.nama;
                const idJenisBarang = select.value;

                const url = new URL(
                    "{{ route('peminjaman_aset.cekKetersediaan') }}",
                    window.location.origin
                );

                url.searchParams.append('nama_aset', namaAset);
                url.searchParams.append('id_jenis_barang', idJenisBarang);
                url.searchParams.append('tanggal_pinjam', tanggalPinjam);
                url.searchParams.append('tanggal_jatuh_tempo', tanggalTempo);

                try {

                    const response = await fetch(url);
                    const data = await response.json();

                    const text = option.textContent;

                    option.textContent = text.replace(
                        /\(Sisa:.*?\)/,
                        `(Sisa: ${data.sisa})`
                    );

                } catch (error) {
                    console.error(error);
                }
            }

            document.querySelectorAll('.item-row').forEach(row => {

                const select = row.querySelector('select[name*="[id_jenis_barang]"]');
                const pinjam = row.querySelector('input[name*="[tanggal_pinjam]"]');
                const tempo = row.querySelector('input[name*="[tanggal_jatuh_tempo]"]');

                updateSisa(row);

                select.addEventListener('change', () => updateSisa(row));
                pinjam.addEventListener('change', () => updateSisa(row));
                tempo.addEventListener('change', () => updateSisa(row));

            });

        });
    </script>
@endsection
