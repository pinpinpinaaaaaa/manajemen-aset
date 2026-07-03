@extends('layouts.app')

@section('title', 'Edit Gedung')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit Gedung</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gedung.index') }}">Gedung</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Gedung</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('gedung.update', $gedung->id_gedung) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- NAMA GEDUNG --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Gedung</label>
                        <input type="text" name="nama_gedung" value="{{ old('nama_gedung', $gedung->nama_gedung) }}"
                            class="form-control" required>

                        @error('nama_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- FOTO GEDUNG --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Foto Gedung</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="gambar[]" accept=".jpg,.jpeg,.png" multiple hidden>
                            </label>

                            <span class="custom-file-text">
                                Tambah foto gedung
                            </span>
                        </div>

                        <small class="text-muted">
                            Pilih satu atau lebih foto untuk ditambahkan.
                        </small>

                        @error('gambar')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror

                        @error('gambar.*')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror

                        @if ($gedung->gambar->count())
                            <div class="row mt-3">
                                @foreach ($gedung->gambar as $gambar)
                                    <div class="col-md-3 mb-3">
                                        <div style="position:relative;">

                                            <img src="{{ asset('storage/' . $gambar->gambar) }}"
                                                class="img-fluid rounded shadow-sm"
                                                style="height:180px;width:100%;object-fit:cover">

                                            <button type="button" class="btn btn-danger btn-sm hapus-gambar"
                                                data-id="{{ $gambar->id }}"
                                                style="
                                                    position:absolute;
                                                    top:8px;
                                                    right:8px;
                                                    width:32px;
                                                    height:32px;
                                                    border-radius:50%;
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                    padding:0;
                                                ">
                                                <i class="fas fa-times"></i>
                                            </button>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- FILE DENAH --}}
                    <div class="form-group mb-4">
                        <label class="form-label">File Denah Gedung</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="file_denah[]" accept=".pdf,.jpg,.jpeg,.png" multiple hidden>
                            </label>

                            <span class="custom-file-text">
                                Tambah file denah
                            </span>
                        </div>

                        <small class="text-muted">
                            PDF, JPG atau PNG. Bisa upload lebih dari satu file.
                        </small>

                        @error('file_denah')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror

                        @error('file_denah.*')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror

                        @if ($gedung->denah->count())
                            <div class="mt-3">

                                @foreach ($gedung->denah as $denah)
                                    @php
                                        $ext = strtolower(pathinfo($denah->file_denah, PATHINFO_EXTENSION));
                                    @endphp

                                    <div class="border rounded p-2 mb-2 position-relative">

                                        <button type="button" class="btn btn-danger btn-sm hapus-denah"
                                            data-id="{{ $denah->id }}"
                                            style="
                                                position:absolute;
                                                top:8px;
                                                right:8px;
                                                width:32px;
                                                height:32px;
                                                border-radius:50%;
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                                padding:0;
                                            ">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset('storage/' . $denah->file_denah) }}" width="200"
                                                class="rounded shadow-sm mb-2">
                                        @endif

                                        <div>
                                            <a href="{{ asset('storage/' . $denah->file_denah) }}" target="_blank">
                                                {{ basename($denah->file_denah) }}
                                            </a>
                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach ($statusList as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $gedung->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- BUTTON --}}
                    <div class="text-end">
                        <a href="{{ route('gedung.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>

                    <div id="gambarDihapusContainer"></div>
                    <div id="denahDihapusContainer"></div>

                </form>
            </div>
        </div>
    </main>

    <script>
        document.querySelectorAll('.custom-file-button input').forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files.length ? this.files[0].name : 'Belum ada file dipilih';
                this.closest('.custom-file-wrapper')
                    .querySelector('.custom-file-text')
                    .textContent = fileName;
            });
        });

        document.querySelectorAll('.hapus-gambar').forEach(btn => {

            btn.addEventListener('click', function() {

                if (!confirm('Hapus gambar ini?')) {
                    return;
                }

                const id = this.dataset.id;

                document.getElementById('gambarDihapusContainer')
                    .insertAdjacentHTML(
                        'beforeend',
                        `<input type="hidden" name="hapus_gambar[]" value="${id}">`
                    );

                this.closest('.col-md-3').remove();

            });

        });

        document.querySelectorAll('.hapus-denah').forEach(btn => {

            btn.addEventListener('click', function() {

                if (!confirm('Hapus file denah ini?')) {
                    return;
                }

                const id = this.dataset.id;

                document.getElementById('denahDihapusContainer')
                    .insertAdjacentHTML(
                        'beforeend',
                        `<input type="hidden" name="hapus_denah[]" value="${id}">`
                    );

                this.closest('.border').remove();

            });

        });
    </script>
@endsection
