@extends('layouts.app')

@section('title', 'Edit Ekspedisi')

@section('content')
    <style>
        /* ================= DOKUMEN & BARANG SPACING ================= */

        #dokumenWrapper,
        #barangWrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
            /* jarak antar card */
            margin-top: 20px;
        }

        .dokumen-item,
        .barang-item {
            border-radius: 20px !important;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .dokumen-item:hover,
        .barang-item:hover {
            transform: translateY(-2px);
        }

        .dokumen-item .card-body,
        .barang-item .card-body {
            padding: 10px !important;
        }

        .dokumen-item .form-label,
        .barang-item .form-label {
            font-weight: 700 !important;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .dokumen-item .form-control,
        .barang-item .form-control {
            min-height: 48px;
            border-radius: 12px;
        }

        .dokumen-item .card-header,
        .barang-item .card-header {
            padding-top: 18px !important;
        }

        .dokumen-item .card-header .fw-semibold,
        .barang-item .card-header .fw-semibold {
            font-weight: 700 !important;
            font-size: 15px;
        }

        hr {
            opacity: 0.08;
        }
    </style>

    <main class="main-content">
        <div class="content-padding">

            {{-- ================= HEADER ================= --}}
            <div class="page-header">
                <h1 class="page-title">Edit Ekspedisi</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('ekspedisi.index') }}" class="breadcrumb-link">Ekspedisi</a>
                    <span class="separator">/</span>
                    <span class="current">Edit</span>
                </nav>
            </div>

            {{-- ================= CARD ================= --}}
            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <form action="{{ route('ekspedisi.update', $data->id_ekspedisi) }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <x-form-errors />

                    {{-- ================= PENGAJU ================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Nama Pengaju</label>

                        <input type="text" name="nama_pengaju"
                            class="form-control @error('nama_pengaju') is-invalid @enderror"
                            value="{{ old('nama_pengaju', $data->nama_pengaju) }}" required>
                        @error('nama_pengaju') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Email Pengaju</label>

                        <input type="email" name="email_pengaju"
                            class="form-control @error('email_pengaju') is-invalid @enderror"
                            value="{{ old('email_pengaju', $data->email_pengaju) }}" required>
                        @error('email_pengaju') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Divisi Pengaju</label>

                        <select name="id_divisi_pengaju"
                            class="form-select @error('id_divisi_pengaju') is-invalid @enderror" required>

                            <option value="" disabled>-- Pilih Divisi --</option>

                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ old('id_divisi_pengaju', $data->id_divisi_pengaju) == $d->id_divisi ? 'selected' : '' }}>

                                    {{ $d->nama_divisi }}

                                </option>
                            @endforeach

                        </select>
                        @error('id_divisi_pengaju') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    {{-- ================= PENGIRIM ================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Nama Pengirim</label>

                        <input type="text" name="nama_pengirim"
                            class="form-control @error('nama_pengirim') is-invalid @enderror"
                            value="{{ old('nama_pengirim', $data->nama_pengirim) }}" required>
                        @error('nama_pengirim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Email Pengirim</label>

                        <input type="email" name="email_pengirim"
                            class="form-control @error('email_pengirim') is-invalid @enderror"
                            value="{{ old('email_pengirim', $data->email_pengirim) }}">
                        @error('email_pengirim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">No HP Pengirim</label>

                        <input type="text" name="no_hp_pengirim"
                            class="form-control @error('no_hp_pengirim') is-invalid @enderror"
                            value="{{ old('no_hp_pengirim', $data->no_hp_pengirim) }}">
                        @error('no_hp_pengirim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Divisi Pengirim</label>

                        <select name="id_divisi_pengirim"
                            class="form-select @error('id_divisi_pengirim') is-invalid @enderror" required>

                            <option value="" disabled>-- Pilih Divisi --</option>

                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ old('id_divisi_pengirim', $data->id_divisi_pengirim) == $d->id_divisi ? 'selected' : '' }}>

                                    {{ $d->nama_divisi }}

                                </option>
                            @endforeach

                        </select>
                        @error('id_divisi_pengirim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    {{-- ================= PENERIMA ================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Instansi Penerima</label>

                        <input type="text" name="instansi_penerima" class="form-control"
                            value="{{ old('instansi_penerima', $data->instansi_penerima) }}">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Nama Penerima</label>

                        <input type="text" name="nama_penerima"
                            class="form-control @error('nama_penerima') is-invalid @enderror"
                            value="{{ old('nama_penerima', $data->nama_penerima) }}" required>
                        @error('nama_penerima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Email Penerima</label>

                        <input type="email" name="email_penerima"
                            class="form-control @error('email_penerima') is-invalid @enderror"
                            value="{{ old('email_penerima', $data->email_penerima) }}">
                        @error('email_penerima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">No HP Penerima</label>

                        <input type="text" name="no_hp_penerima"
                            class="form-control @error('no_hp_penerima') is-invalid @enderror"
                            value="{{ old('no_hp_penerima', $data->no_hp_penerima) }}">
                        @error('no_hp_penerima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Alamat Penerima</label>

                        <textarea name="alamat_penerima" rows="4"
                            class="form-control @error('alamat_penerima') is-invalid @enderror"
                            required>{{ old('alamat_penerima', $data->alamat_penerima) }}</textarea>
                        @error('alamat_penerima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    {{-- ================= KEGIATAN ================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Judul Kegiatan</label>

                        <input type="text" name="judul_kegiatan"
                            class="form-control @error('judul_kegiatan') is-invalid @enderror"
                            value="{{ old('judul_kegiatan', $data->judul_kegiatan) }}" required>
                        @error('judul_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Keterangan</label>

                        <textarea name="keterangan" rows="4"
                            class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $data->keterangan) }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    {{-- ================= DOKUMEN ================= --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>
                            <h5 class="mb-1 fw-bold">Dokumen</h5>
                            <small class="text-muted">
                                Daftar dokumen yang akan dikirim
                            </small>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm px-3 py-2 shadow-sm rounded-pill"
                            onclick="addDokumen()">

                            <i class="fas fa-plus me-1"></i>
                            Tambah Dokumen
                        </button>

                    </div>

                    <div id="dokumenWrapper">

                        @foreach ($data->dokumen as $i => $doc)
                            <div class="dokumen-item card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

                                <div
                                    class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">

                                    <div class="fw-semibold text-dark">
                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                        Dokumen #{{ $i + 1 }}
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        onclick="removeDokumen(this)">

                                        <i class="fas fa-trash-alt me-1"></i>
                                        Hapus
                                    </button>

                                </div>

                                <div class="card-body p-4">

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-semibold">
                                            Nama Dokumen
                                        </label>

                                        <input type="text" name="dokumen[{{ $i }}][nama]"
                                            class="form-control" value="{{ $doc->nama_dokumen }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label fw-semibold">
                                            Jenis Dokumen
                                        </label>

                                        <input type="text" name="dokumen[{{ $i }}][jenis]"
                                            class="form-control" value="{{ $doc->jenis_dokumen }}">
                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>


                    <hr class="my-5">


                    {{-- ================= BARANG ================= --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>
                            <h5 class="mb-1 fw-bold">Barang</h5>
                            <small class="text-muted">
                                Daftar barang yang akan dikirim
                            </small>
                        </div>

                        <button type="button" class="btn btn-success btn-sm px-3 py-2 shadow-sm rounded-pill"
                            onclick="addBarang()">

                            <i class="fas fa-plus me-1"></i>
                            Tambah Barang
                        </button>

                    </div>

                    <div id="barangWrapper">

                        @foreach ($data->barang as $i => $barang)
                            <div class="barang-item card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

                                <div
                                    class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">

                                    <div class="fw-semibold text-dark">
                                        <i class="fas fa-box-open text-success me-2"></i>
                                        Barang #{{ $i + 1 }}
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        onclick="removeBarang(this)">

                                        <i class="fas fa-trash-alt me-1"></i>
                                        Hapus
                                    </button>

                                </div>

                                <div class="card-body p-4">

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">

                                                <label class="form-label fw-semibold">
                                                    Nama Barang
                                                </label>

                                                <input type="text" name="barang[{{ $i }}][nama]"
                                                    class="form-control" value="{{ $barang->nama_barang }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">

                                                <label class="form-label fw-semibold">
                                                    Jumlah
                                                </label>

                                                <input type="number" name="barang[{{ $i }}][jumlah]"
                                                    class="form-control" min="1" value="{{ $barang->jumlah }}"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">

                                                <label class="form-label fw-semibold">
                                                    Berat
                                                </label>

                                                <input type="number" step="0.01"
                                                    name="barang[{{ $i }}][berat]" class="form-control"
                                                    value="{{ $barang->berat }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">

                                                <label class="form-label fw-semibold">
                                                    Satuan
                                                </label>

                                                <input type="text" name="barang[{{ $i }}][satuan]"
                                                    class="form-control" value="{{ $barang->satuan }}">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- ================= ACTION ================= --}}
                    <div class="text-end">

                        <a href="{{ route('ekspedisi.index') }}" class="btn btn-secondary">

                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">

                            Update Ekspedisi
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </main>

    {{-- ================= SCRIPT ================= --}}
    <script>
        let dokumenIndex = {{ count($data->dokumen) }};
        let barangIndex = {{ count($data->barang) }};

        function addDokumen() {

            const wrapper = document.getElementById('dokumenWrapper');

            wrapper.innerHTML += `
            <div class="dokumen-item card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

                <div class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">

                    <div class="fw-bold text-dark">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Dokumen Baru
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-danger rounded-pill px-3"
                        onclick="removeDokumen(this)">

                        <i class="fas fa-trash-alt me-1"></i>
                        Hapus
                    </button>

                </div>

                <div class="card-body p-4">

                    <div class="form-group mb-4">
                        <label class="form-label fw-bold mb-2">
                            Nama Dokumen
                        </label>

                        <input type="text"
                            name="dokumen[${dokumenIndex}][nama]"
                            class="form-control form-control-lg"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold mb-2">
                            Jenis Dokumen
                        </label>

                        <input type="text"
                            name="dokumen[${dokumenIndex}][jenis]"
                            class="form-control form-control-lg">
                    </div>

                </div>

            </div>
        `;

            dokumenIndex++;
        }

        function removeDokumen(btn) {
            btn.closest('.dokumen-item').remove();
        }

        function addBarang() {

            const wrapper = document.getElementById('barangWrapper');

            wrapper.innerHTML += `
            <div class="barang-item card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

                <div class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">

                    <div class="fw-bold text-dark">
                        <i class="fas fa-box-open text-success me-2"></i>
                        Barang Baru
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-danger rounded-pill px-3"
                        onclick="removeBarang(this)">

                        <i class="fas fa-trash-alt me-1"></i>
                        Hapus
                    </button>

                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="form-group">

                                <label class="form-label fw-bold mb-2">
                                    Nama Barang
                                </label>

                                <input type="text"
                                    name="barang[${barangIndex}][nama]"
                                    class="form-control form-control-lg"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label class="form-label fw-bold mb-2">
                                    Jumlah
                                </label>

                                <input type="number"
                                    name="barang[${barangIndex}][jumlah]"
                                    class="form-control form-control-lg"
                                    min="1"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label class="form-label fw-bold mb-2">
                                    Berat
                                </label>

                                <input type="number"
                                    step="0.01"
                                    name="barang[${barangIndex}][berat]"
                                    class="form-control form-control-lg">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label class="form-label fw-bold mb-2">
                                    Satuan
                                </label>

                                <input type="text"
                                    name="barang[${barangIndex}][satuan]"
                                    class="form-control form-control-lg">
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        `;

            barangIndex++;
        }

        function removeBarang(btn) {
            btn.closest('.barang-item').remove();
        }
    </script>

@endsection
