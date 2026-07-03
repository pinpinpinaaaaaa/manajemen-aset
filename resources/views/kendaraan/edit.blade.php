@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Edit Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('kendaraan.index') }}">Kendaraan</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Kendaraan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('kendaraan.update', $kendaraan->id_kendaraan) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label class="form-label">Jenis Kendaraan</label>
                        <select name="jenis_kendaraan" class="form-select" required>
                            @foreach ($jenisOptions as $opt)
                                <option value="{{ $opt }}"
                                    {{ $kendaraan->jenis_kendaraan == $opt ? 'selected' : '' }}>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-select" required>
                            @foreach ($tipeOptions as $opt)
                                <option value="{{ $opt }}" {{ $kendaraan->tipe == $opt ? 'selected' : '' }}>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Plat Nomor</label>
                        <input type="text" name="plat_nomor" class="form-control"
                            oninput="this.value = this.value.toUpperCase()" value="{{ $kendaraan->plat_nomor }}"
                            placeholder="Contoh: B 1234 XYZ" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Tahun Pembelian</label>
                        <input type="number" name="tahun_pembelian" class="form-control"
                            value="{{ $kendaraan->tahun_pembelian }}" min="1990" max="{{ date('Y') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Umur Ekonomis (tahun)</label>
                        <input type="number" name="umur_ekonomis" class="form-control"
                            value="{{ $kendaraan->umur_ekonomis }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Merk</label>
                        <input type="text" name="merk" class="form-control" value="{{ $kendaraan->merk }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="{{ $kendaraan->model }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Spesifikasi</label>
                        <textarea name="spesifikasi" class="form-control" rows="2">{{ $kendaraan->spesifikasi }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">No Rangka</label>
                        <input type="text" name="no_rangka" class="form-control" value="{{ $kendaraan->no_rangka }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">No Mesin</label>
                        <input type="text" name="no_mesin" class="form-control" value="{{ $kendaraan->no_mesin }}">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Status Kondisi</label>
                        <select name="status_kondisi" class="form-select" required>
                            @foreach ($statusOptions as $opt)
                                <option value="{{ $opt }}"
                                    {{ $kendaraan->status_kondisi == $opt ? 'selected' : '' }}>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Status Penggunaan</label>
                        <select name="status_penggunaan" class="form-select" required>
                            @foreach ($penggunaanOptions as $opt)
                                <option value="{{ $opt }}"
                                    {{ $kendaraan->status_penggunaan == $opt ? 'selected' : '' }}>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">
                            Driver
                            <small class="text-muted">(opsional)</small>
                        </label>

                        <select name="driver_id" class="form-select">
                            <option value="">-- Tidak ada driver --</option>

                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id_user }}"
                                    {{ old('driver_id', $kendaraan->driver_id) == $driver->id_user ? 'selected' : '' }}>
                                    {{ $driver->name }} ({{ $driver->email }})
                                </option>
                            @endforeach
                        </select>

                        @error('driver_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- FOTO KENDARAAN --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Foto Kendaraan</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto" accept="image/*" hidden>
                            </label>
                            <span class="custom-file-text">
                                {{ $kendaraan->foto ? basename($kendaraan->foto) : 'Belum ada file dipilih' }}
                            </span>
                        </div>

                        @if ($kendaraan->foto)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $kendaraan->foto) }}" width="200"
                                    class="rounded shadow" onerror="this.src='{{ asset('images/default-car.jpg') }}'">
                            </div>
                            <small class="text-muted">
                                Kosongkan jika tidak ingin mengganti foto.
                            </small>
                        @endif

                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

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
    </script>
@endsection
