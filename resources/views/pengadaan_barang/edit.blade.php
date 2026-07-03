@extends('layouts.app')

@section('title', 'Edit Pengadaan Barang & Jasa')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit Pengadaan Barang & Jasa</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>

                    <a href="{{ route('pengadaan-barang.index') }}">
                        Pengadaan Barang/Jasa
                    </a>

                    <span class="separator">/</span>
                    <span class="current">Edit</span>
                </nav>
            </div>

            <div class="card mt-4 p-4">

                <form action="{{ route('pengadaan-barang.update', $pengadaan->id_pengadaan) }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- DATA PENGAJU --}}
                    <div class="form-group mb-3">
                        <label>Nama Pengaju</label>
                        <input type="text" name="nama_pengaju" class="form-control"
                            value="{{ old('nama_pengaju', $pengadaan->nama_pengaju) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Email Pengaju</label>
                        <input type="email" name="email_pengaju" class="form-control"
                            value="{{ old('email_pengaju', $pengadaan->email_pengaju) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Divisi</label>

                        <select name="id_divisi" class="form-select" required>

                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ $d->id_divisi == $pengadaan->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Tanggal Kebutuhan</label>

                        <input type="date" name="tanggal_kebutuhan" class="form-control"
                            value="{{ old('tanggal_kebutuhan', \Carbon\Carbon::parse($pengadaan->tanggal_kebutuhan)->format('Y-m-d')) }}"
                            required>
                    </div>

                    <div class="form-group mb-4">
                        <label>Alasan</label>

                        <textarea name="alasan" class="form-control" rows="3" required>{{ old('alasan', $pengadaan->alasan) }}</textarea>
                    </div>

                    <hr>

                    <h5 class="mb-3">Detail Pengadaan</h5>

                    @foreach ($pengadaan->details as $i => $detail)
                        <div class="border rounded p-3 mb-4">

                            <input type="hidden" name="items[{{ $i }}][jenis]" value="{{ $detail->jenis }}">

                            <div class="mb-2">
                                <strong>
                                    {{ strtoupper($detail->jenis) }}
                                </strong>
                            </div>

                            @if ($detail->jenis == 'barang')
                                <div class="form-group mb-3">
                                    <label>Nama Barang</label>

                                    <input type="text" class="form-control" name="items[{{ $i }}][nama]"
                                        value="{{ $detail->nama_barang }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Merk</label>

                                    <input type="text" class="form-control" name="items[{{ $i }}][merk]"
                                        value="{{ $detail->merk }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Tipe / Model</label>

                                    <input type="text" class="form-control"
                                        name="items[{{ $i }}][tipe_model]" value="{{ $detail->tipe_model }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Spesifikasi</label>

                                    <textarea class="form-control" rows="3" name="items[{{ $i }}][spesifikasi]">{{ $detail->spesifikasi }}</textarea>
                                </div>
                            @else
                                <div class="form-group mb-3">
                                    <label>Kategori Jasa</label>

                                    <input type="text" class="form-control"
                                        name="items[{{ $i }}][kategori_jasa]"
                                        value="{{ $detail->kategori_jasa }}" required>
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label>Jumlah</label>

                                <input type="number" class="form-control" name="items[{{ $i }}][jumlah]"
                                    value="{{ $detail->jumlah }}" min="1" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Harga Satuan</label>

                                <input type="text" class="form-control harga-satuan"
                                    name="items[{{ $i }}][harga_satuan]"
                                    value="{{ number_format((int) $detail->harga_satuan, 0, ',', '.') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Catatan Item</label>

                                <textarea class="form-control" rows="2" name="items[{{ $i }}][catatan]">{{ $detail->catatan }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Upload File Tambahan</label>

                                <input type="file" name="items[{{ $i }}][files][]" class="form-control"
                                    multiple>
                            </div>

                            @if ($detail->files->count())
                                <div class="mt-3">

                                    <strong>File Saat Ini:</strong>

                                    <ul class="mt-2">
                                        @foreach ($detail->files as $file)
                                            <li>
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                                                    {{ $file->file_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                </div>
                            @endif

                        </div>
                    @endforeach

                    <div class="form-group mb-4">
                        <label>Catatan Pengadaan</label>

                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $pengadaan->catatan) }}</textarea>
                    </div>

                    <div class="text-end">

                        <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-secondary">
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

            document.querySelectorAll('.harga-satuan').forEach(function(input) {

                input.addEventListener('input', function() {

                    let value = this.value.replace(/\D/g, '');

                    if (value === '') {
                        this.value = '';
                        return;
                    }

                    this.value = new Intl.NumberFormat('id-ID').format(value);
                });

            });

        });
    </script>
@endsection
