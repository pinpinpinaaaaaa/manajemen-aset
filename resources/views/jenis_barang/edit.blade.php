@extends('layouts.app')

@section('title', 'Edit Jenis Barang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit Jenis Barang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('jenis_barang.index') }}">Jenis Barang</a>
                    <span class="separator">/</span>
                    <span class="current">Edit</span>
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

                <form action="{{ route('jenis_barang.update', $jenisBarang->id_jenis_barang) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ============================
                    JENIS
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Jenis</label>

                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>

                            <option value="sarana" {{ old('jenis', $jenisBarang->jenis) == 'sarana' ? 'selected' : '' }}>
                                Sarana
                            </option>

                            <option value="prasarana"
                                {{ old('jenis', $jenisBarang->jenis) == 'prasarana' ? 'selected' : '' }}>
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

                        <select name="kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>

                            <option value="it" {{ old('kategori', $jenisBarang->kategori) == 'it' ? 'selected' : '' }}>
                                IT
                            </option>

                            <option value="elektronik"
                                {{ old('kategori', $jenisBarang->kategori) == 'elektronik' ? 'selected' : '' }}>
                                Elektronik
                            </option>

                            <option value="non elektronik"
                                {{ old('kategori', $jenisBarang->kategori) == 'non elektronik' ? 'selected' : '' }}>
                                Non Elektronik
                            </option>
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

                        <input type="text" name="nama_barang"
                            value="{{ old('nama_barang', $jenisBarang->nama_barang) }}" class="form-control"
                            placeholder="Contoh: Printer Laser">

                        @error('nama_barang')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ============================
                    PREFIX KODE
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Prefix Kode</label>

                        <input type="text" name="prefix_kode"
                            value="{{ old('prefix_kode', $jenisBarang->prefix_kode) }}" class="form-control" maxlength="3"
                            placeholder="Contoh: PRN">

                        @error('prefix_kode')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ============================
                    BISA DIPINDAHKAN
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Bisa Dipindahkan?</label>

                        <select name="bisa_dipindah" class="form-select">
                            <option value="1"
                                {{ old('bisa_dipindah', $jenisBarang->bisa_dipindah) == 1 ? 'selected' : '' }}>
                                Ya
                            </option>

                            <option value="0"
                                {{ old('bisa_dipindah', $jenisBarang->bisa_dipindah) == 0 ? 'selected' : '' }}>
                                Tidak
                            </option>
                        </select>

                        <small class="text-muted">
                            Contoh: Meja = Ya, Dinding = Tidak
                        </small>

                        @error('bisa_dipindah')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ============================
                    ACTION
                ============================= --}}
                    <div class="text-end">
                        <a href="{{ route('jenis_barang.index') }}" class="btn btn-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Update
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
