@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Tambah Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('kendaraan.index') }}">Kendaraan</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('kendaraan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-4">
                        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                        <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="roda 2">Roda 2</option>
                            <option value="roda 4">Roda 4</option>
                        </select>
                        @error('jenis_kendaraan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="tipe" class="form-label">Tipe</label>
                        <select name="tipe" id="tipe" class="form-select not-editable" required readonly>
                            <option value="" disabled selected>-- Pilih Tipe --</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                        </select>
                        @error('tipe')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="plat_nomor" class="form-label">Plat Nomor</label>
                        <input type="text" name="plat_nomor" id="plat_nomor" class="form-control"
                            placeholder="Contoh: B 1234 XYZ" required>
                        @error('plat_nomor')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="tahun_pembelian" class="form-label">Tahun Pembelian</label>
                        <input type="number" name="tahun_pembelian" id="tahun_pembelian" class="form-control"
                            min="1990" max="{{ date('Y') }}" placeholder="Contoh: 2020" required>
                        @error('tahun_pembelian')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="umur_ekonomis" class="form-label">Umur Ekonomis (tahun)</label>
                        <input type="number" name="umur_ekonomis" id="umur_ekonomis" class="form-control"
                            placeholder="Contoh: 5" required>
                        @error('umur_ekonomis')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Merk</label>
                        <input type="text" name="merk" class="form-control" value="{{ old('merk') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Spesifikasi</label>
                        <textarea name="spesifikasi" class="form-control" rows="2">{{ old('spesifikasi') }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">No Rangka</label>
                        <input type="text" name="no_rangka" class="form-control" value="{{ old('no_rangka') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">No Mesin</label>
                        <input type="text" name="no_mesin" class="form-control" value="{{ old('no_mesin') }}">
                    </div>

                    <div class="form-group mb-4">
                        <label for="status_kondisi" class="form-label">Status Kondisi</label>
                        <select name="status_kondisi" id="status_kondisi" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Status Kondisi --</option>
                            <option value="aktif">Aktif</option>
                            <option value="perbaikan">Perbaikan</option>
                            <option value="non aktif">Non Aktif</option>
                        </select>
                        @error('status_kondisi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Status Penggunaan</label>
                        <select name="status_penggunaan" id="status_penggunaan" class="form-select" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="terpakai">Terpakai</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="driver_id" class="form-label">
                            Driver
                            <small class="text-muted">(opsional)</small>
                        </label>
                        <select name="driver_id" id="driver_id" class="form-select">
                            <option value="">-- Tidak ada driver --</option>

                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id_user }}"
                                    {{ old('driver_id') == $driver->id_user ? 'selected' : '' }}>
                                    {{ $driver->name }} ({{ $driver->email }})
                                </option>
                            @endforeach
                        </select>

                        @error('driver_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- FOTO --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Foto Kendaraan</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto" accept="image/*" hidden>
                            </label>
                            <span class="custom-file-text">
                                {{ old('foto') ? old('foto') : 'Belum ada file dipilih' }}
                            </span>
                        </div>

                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenis = document.getElementById('jenis_kendaraan');
            const tipe = document.getElementById('tipe');

            jenis.addEventListener('change', function() {
                if (jenis.value === "roda 2") {
                    tipe.value = "motor";
                } else if (jenis.value === "roda 4") {
                    tipe.value = "mobil";
                } else {
                    tipe.value = "";
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const status = document.getElementById('status_penggunaan');
            const driver = document.getElementById('driver_id');

            if (status && driver) {
                status.addEventListener('change', function() {
                    if (this.value === 'tersedia') {
                        driver.value = '';
                    }
                });
            }
        });
        document.querySelectorAll('.custom-file-button input').forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files.length ? this.files[0].name : 'Belum ada file dipilih';
                this.closest('.custom-file-wrapper')
                    .querySelector('.custom-file-text')
                    .textContent = fileName;
            });
        });
    </script>

@endsection
