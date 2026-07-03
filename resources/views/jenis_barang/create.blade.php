@extends('layouts.app')

@section('title', 'Tambah Jenis Barang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Tambah Jenis Barang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('jenis_barang.index') }}">Jenis Barang</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('jenis_barang.store') }}" method="POST">
                    @csrf

                    {{-- ============================
                        JENIS
                    ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Jenis</label>

                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="sarana" {{ old('jenis') == 'sarana' ? 'selected' : '' }}>
                                Sarana
                            </option>
                            <option value="prasarana" {{ old('jenis') == 'prasarana' ? 'selected' : '' }}>
                                Prasarana
                            </option>
                        </select>

                        @error('jenis')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ============================
                        KATEGORI
                    ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="it" {{ old('kategori') == 'it' ? 'selected' : '' }}>IT</option>
                            <option value="elektronik" {{ old('kategori') == 'elektronik' ? 'selected' : '' }}>Elektronik
                            </option>
                            <option value="non elektronik" {{ old('kategori') == 'non elektronik' ? 'selected' : '' }}>Non
                                Elektronik</option>
                        </select>
                        @error('kategori')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ============================
                        NAMA BARANG
                    ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="form-control"
                            placeholder="Contoh: Printer Laser">
                        @error('nama_barang')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ============================
                        PREFIX KODE (OPSIONAL)
                    ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">
                            Prefix Kode <small class="text-muted">(Opsional)</small>
                        </label>
                        <input type="text" name="prefix_kode" value="{{ old('prefix_kode') }}" class="form-control"
                            maxlength="3" required>
                        @error('prefix_kode')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Bisa Dipindahkan?</label>

                        <select name="bisa_dipindah" class="form-select">
                            <option value="1" {{ old('bisa_dipindah') == '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('bisa_dipindah') == '0' ? 'selected' : '' }}>Tidak</option>
                        </select>

                        <small class="text-muted">
                            Contoh: AC = Ya, Dinding = Tidak
                        </small>
                    </div>

                    {{-- ============================
                        ACTION
                    ============================= --}}
                    <div class="text-end">
                        <a href="{{ route('jenis_barang.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>
    <script>
        const jenis = document.querySelector('[name="jenis"]');
        const bisaDipindah = document.querySelector('[name="bisa_dipindah"]');

        function handleJenis() {
            if (jenis.value === 'sarana') {
                bisaDipindah.value = '1';
                bisaDipindah.disabled = true;
            } else {
                bisaDipindah.disabled = false;
            }
        }

        jenis.addEventListener('change', handleJenis);
        document.addEventListener('DOMContentLoaded', handleJenis);
    </script>
@endsection
