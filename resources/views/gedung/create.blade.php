@extends('layouts.app')

@section('title', 'Tambah Gedung')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Tambah Gedung</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gedung.index') }}">Gedung</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Gedung</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('gedung.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- NAMA GEDUNG --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Gedung</label>
                        <input type="text" name="nama_gedung" value="{{ old('nama_gedung') }}" class="form-control"
                            placeholder="Contoh: Gedung Moh. Sadli" required>

                        @error('nama_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- GAMBAR GEDUNG --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Foto Gedung</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="gambar[]" accept=".jpg,.jpeg,.png" multiple hidden>
                            </label>

                            <span class="custom-file-text">
                                Belum ada file dipilih
                            </span>
                        </div>

                        @error('gambar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        @error('gambar.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <small class="text-muted d-block mt-1">
                            Bisa memilih lebih dari satu foto.
                        </small>
                    </div>

                    {{-- GAMBAR DENAH --}}
                    <div class="form-group mb-3">
                        <label class="form-label">File Denah</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="file_denah[]" accept=".pdf,.jpg,.jpeg,.png" multiple hidden>
                            </label>

                            <span class="custom-file-text">
                                Belum ada file dipilih
                            </span>
                        </div>

                        @error('file_denah')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        @error('file_denah.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <small class="text-muted d-block mt-1">
                            Bisa upload PDF, JPG atau PNG dan dapat memilih lebih dari satu file.
                        </small>
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Status --</option>
                            @foreach ($statusList as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
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
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>

        </div>
    </main>
    <script>
        document.querySelectorAll('.custom-file-button input').forEach(input => {

            input.addEventListener('change', function() {

                let text = 'Belum ada file dipilih';

                if (this.files.length === 1) {
                    text = this.files[0].name;
                } else if (this.files.length > 1) {
                    text = this.files.length + ' file dipilih';
                }

                this.closest('.custom-file-wrapper')
                    .querySelector('.custom-file-text')
                    .textContent = text;
            });

        });
    </script>
@endsection
