@extends('layouts.app')

@section('title', 'Tambah Ruangan')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Tambah Ruangan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>

                    @if ($selectedGedung)
                        <a href="{{ route('gedung.dashboard', $selectedGedung->id_gedung) }}">
                            {{ $selectedGedung->nama_gedung }}
                        </a>
                        <span class="separator">/</span>
                    @endif

                    <a href="{{ route('ruangan.index') }}">Ruangan</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Ruangan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('ruangan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- GEDUNG --}}
                    <div class="form-group mb-4">
                        <label for="id_gedung" class="form-label">Gedung</label>
                        <select name="id_gedung" id="id_gedung" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Gedung --</option>
                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}"
                                    {{ old('id_gedung', $selectedGedung ?? '') == $g->id_gedung ? 'selected' : '' }}>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- NAMA RUANGAN --}}
                    <div class="form-group mb-3">
                        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control" value="{{ old('nama_ruangan') }}"
                            placeholder="Contoh: Aula Utama" required>
                        @error('nama_ruangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- KATEGORI --}}
                    <div class="form-group mb-4">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="interior" {{ old('kategori') == 'interior' ? 'selected' : '' }}>Interior
                            </option>
                            <option value="eksterior" {{ old('kategori') == 'eksterior' ? 'selected' : '' }}>Eksterior
                            </option>
                        </select>
                        @error('kategori')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- LANTAI --}}
                    <div class="form-group mb-3">
                        <label for="lantai" class="form-label">Lantai</label>
                        <input type="number" name="lantai" class="form-control" value="{{ old('lantai') }}">
                        @error('lantai')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terpakai" {{ old('status') == 'terpakai' ? 'selected' : '' }}>Terpakai</option>
                            <option value="non aktif" {{ old('status') == 'non aktif' ? 'selected' : '' }}>Non Aktif
                            </option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance
                            </option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- FOTO --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Foto Ruangan</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih Gambar
                                <input type="file" name="foto[]" accept="image/*" multiple hidden>
                            </label>

                            <span class="custom-file-text">
                                Belum ada file dipilih
                            </span>
                        </div>

                        <small class="text-muted d-block mt-1">
                            Anda dapat memilih lebih dari satu gambar sekaligus.
                        </small>

                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        @error('foto.*')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror

                        {{-- Preview --}}
                        <div id="preview-container" class="row mt-3"></div>
                    </div>

                    {{-- BUTTON --}}
                    <div class="text-end">
                        @if ($idGedung)
                            <a href="{{ route('gedung.dashboard', ['id' => $idGedung]) }}"
                                class="btn btn-secondary">Batal</a>
                        @else
                            <a href="{{ route('gedung.index') }}" class="btn btn-secondary">Batal</a>
                        @endif
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </main>
    <script>
        document.querySelectorAll('.custom-file-button input').forEach(input => {

            input.addEventListener('change', function() {

                const textElement = this.closest('.custom-file-wrapper')
                    .querySelector('.custom-file-text');

                const previewContainer = document.getElementById('preview-container');

                previewContainer.innerHTML = '';

                if (!this.files.length) {
                    textElement.textContent = 'Belum ada file dipilih';
                    return;
                }

                const fileNames = Array.from(this.files).map(file => file.name);

                textElement.textContent =
                    `${this.files.length} file dipilih`;

                this.files.forEach(file => {

                    const reader = new FileReader();

                    reader.onload = function(e) {

                        previewContainer.innerHTML += `
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="${e.target.result}"
                                     class="card-img-top"
                                     style="height:180px;object-fit:cover;">
                                <div class="card-body p-2">
                                    <small class="text-muted d-block text-truncate">
                                        ${file.name}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                    };

                    reader.readAsDataURL(file);

                });

            });

        });
    </script>
@endsection
