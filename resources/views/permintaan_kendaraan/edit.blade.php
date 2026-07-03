@extends('layouts.app')

@section('title', 'Edit Permintaan Barang/Jasa')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            {{-- ================= HEADER ================= --}}
            <div class="page-header">
                <h1 class="page-title">Edit Permintaan Barang/Jasa</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('permintaan-barang.index') }}" class="breadcrumb-link">Permintaan Barang & Jasa</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Permintaan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <form action="{{ route('permintaan-barang.update', $permintaan->id_permintaan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ================= DATA UMUM ================= --}}
                    <div class="form-group mb-3">
                        <label>Jenis Permintaan</label>
                        <select class="form-select not-editable" disabled>
                            <option value="barang" {{ $permintaan->jenis_permintaan == 'barang' ? 'selected' : '' }}>
                                Barang
                            </option>
                            <option value="jasa" {{ $permintaan->jenis_permintaan == 'jasa' ? 'selected' : '' }}>
                                Jasa
                            </option>
                        </select>

                        <input type="hidden" name="jenis_permintaan" value="{{ $permintaan->jenis_permintaan }}">
                    </div>

                    <div class="form-group mb-3">
                        <label>Divisi</label>
                        <select name="id_divisi" class="form-select" required>
                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ $d->id_divisi == $permintaan->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Nama Pengaju</label>
                        <input type="text" name="nama_pengaju" class="form-control"
                            value="{{ $permintaan->nama_pengaju }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email_pengaju" class="form-control"
                            value="{{ $permintaan->email_pengaju }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label>Tanggal Kebutuhan</label>
                        <input type="date" name="tanggal_kebutuhan" class="form-control"
                            value="{{ \Carbon\Carbon::parse($permintaan->tanggal_kebutuhan)->format('Y-m-d') }}" required>
                    </div>

                    <hr>

                    {{-- ================= DETAIL ITEM ================= --}}
                    <h2 class="mb-3">Detail Item</h2>

                    <div id="items-wrapper">

                        @foreach ($permintaan->details as $i => $detail)
                            <div class="border p-3 mb-4 rounded">
                                <input type="hidden" name="items[{{ $i }}][id_detail]"
                                    value="{{ $detail->id_detail }}">

                                {{-- BARANG --}}
                                @if ($detail->jenis_item == 'barang')
                                    <div class="form-group mb-3">
                                        <label>Barang</label>
                                        <select name="items[{{ $i }}][barang]" class="form-select">
                                            <option value="">-- Pilih Barang --</option>

                                            @foreach ($gudang as $g)
                                                <option value="gudang|{{ $g->id_barang }}"
                                                    {{ $detail->id_barang == $g->id_barang ? 'selected' : '' }}>
                                                    {{ $g->nama_barang }}
                                                </option>
                                            @endforeach

                                            @if ($detail->sumber_barang == 'custom')
                                                <option value="custom|{{ $detail->id_barang }}" selected>
                                                    {{ $detail->nama_barang_custom }} (Custom)
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                @endif

                                {{-- JASA --}}
                                @if ($detail->jenis_item == 'jasa')
                                    <div class="form-group mb-3">
                                        <label>Nama Jasa</label>
                                        <input type="text" name="items[{{ $i }}][nama_jasa]"
                                            class="form-control" value="{{ $detail->nama_jasa }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Deskripsi</label>
                                        <textarea name="items[{{ $i }}][deskripsi_jasa]" class="form-control" rows="2">{{ $detail->deskripsi_jasa }}</textarea>
                                    </div>
                                @endif

                                <div class="form-group mb-3">
                                    <label>Jumlah</label>
                                    <input type="number" name="items[{{ $i }}][jumlah]" class="form-control"
                                        value="{{ $detail->jumlah }}" min="1">
                                </div>

                                {{-- JUMLAH BELI (KHUSUS BARANG CUSTOM) --}}
                                @if ($detail->jenis_item == 'barang' && $detail->sumber_barang == 'custom')
                                    <div class="form-group mb-3">
                                        <label>Jumlah Beli</label>
                                        <input type="number" name="items[{{ $i }}][jumlah_beli]"
                                            class="form-control" value="{{ $detail->jumlah_beli }}"
                                            min="{{ $detail->jumlah }}" required>
                                    </div>
                                @endif

                                <div class="form-group mb-3">
                                    <label>Harga Satuan</label>
                                    <input type="number" name="items[{{ $i }}][harga_satuan]"
                                        class="form-control" value="{{ $detail->harga_satuan }}" min="0">
                                </div>

                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <input type="number" class="form-control" value="{{ $detail->subtotal }}" disabled>
                                </div>

                            </div>
                        @endforeach

                    </div>

                    {{-- ================= CATATAN ================= --}}
                    <div class="form-group mb-3">
                        <label>Alasan</label>
                        <textarea name="alasan" class="form-control" rows="3">{{ $permintaan->alasan }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ $permintaan->catatan }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label>Link Lampiran (Opsional)</label>
                        <input type="url" name="lampiran" class="form-control"
                            placeholder="https://drive.google.com/..." value="{{ $permintaan->lampiran ?? '' }}">
                        <small class="text-muted">
                            Gunakan link Google Drive / OneDrive / Vendor jika ada dokumen pendukung.
                        </small>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('permintaan-barang.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
@endsection
