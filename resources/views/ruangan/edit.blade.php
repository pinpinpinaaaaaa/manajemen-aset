@extends('layouts.app')

@section('title', 'Edit Ruangan')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Edit Ruangan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('ruangan.index') }}">Ruangan</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Ruangan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('ruangan.update', $ruangan->id_ruangan) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label for="id_gedung" class="form-label">Gedung</label>
                        <select name="id_gedung" class="form-select" required>
                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}"
                                    {{ $ruangan->id_gedung == $g->id_gedung ? 'selected' : '' }}>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-control" value="{{ $ruangan->nama_ruangan }}"
                            required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="interior" {{ $ruangan->kategori == 'interior' ? 'selected' : '' }}>Interior
                            </option>
                            <option value="eksterior" {{ $ruangan->kategori == 'eksterior' ? 'selected' : '' }}>Eksterior
                            </option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Lantai</label>
                        <input type="text" name="lantai" class="form-control" value="{{ $ruangan->lantai }}">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="tersedia" {{ $ruangan->status == 'tersedia' ? 'selected' : '' }}>Tersedia
                            </option>
                            <option value="terpakai" {{ $ruangan->status == 'terpakai' ? 'selected' : '' }}>Terpakai
                            </option>
                            <option value="non aktif" {{ $ruangan->status == 'non aktif' ? 'selected' : '' }}>Non Aktif
                            </option>
                            <option value="maintenance" {{ $ruangan->status == 'maintenance' ? 'selected' : '' }}>
                                Maintenance</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Foto Ruangan</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto[]" accept="image/*" multiple hidden>
                            </label>

                            <span class="custom-file-text">
                                {{ $ruangan->gambar->count() }}
                                foto tersimpan
                            </span>
                        </div>

                        @if ($ruangan->gambar->count())
                            <div class="row mt-3 g-3">

                                @foreach ($ruangan->gambar as $gambar)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div style="position:relative;">

                                            <img src="{{ asset('storage/' . $gambar->foto) }}"
                                                class="img-fluid rounded shadow-sm"
                                                style="height:150px;width:100%;object-fit:cover;">

                                            <button type="button" class="btn btn-danger btn-sm hapus-foto"
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

                            <small class="text-muted d-block mt-2">
                                Upload foto baru untuk mengganti seluruh foto yang ada.
                            </small>
                        @endif

                        @error('foto.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <div id="fotoDihapusContainer"></div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('ruangan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary btn-save">Simpan Perubahan</button>
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
        document.querySelectorAll('.hapus-foto').forEach(btn => {

            btn.addEventListener('click', function() {

                if (!confirm('Hapus foto ini?')) {
                    return;
                }

                const id = this.dataset.id;

                document.getElementById('fotoDihapusContainer')
                    .insertAdjacentHTML(
                        'beforeend',
                        `<input type="hidden" name="hapus_foto[]" value="${id}">`
                    );

                this.closest('.col-md-3').remove();

            });

        });
    </script>
@endsection
