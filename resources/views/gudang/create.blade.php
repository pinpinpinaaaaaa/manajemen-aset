@extends('layouts.app')

@section('title', 'Tambah Barang Gudang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Tambah Barang Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gudang.index') }}">Gudang</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Barang Gudang</span>
                </nav>
            </div>
            <div class="card p-4 shadow-sm rounded-lg mt-4">

                <form action="{{ route('gudang.store') }}" method="POST">
                    @csrf

                    <x-form-errors />

                    @if (isset($permintaanId))
                        <input type="hidden" name="permintaan_id" value="{{ $permintaanId }}">
                    @endif

                    @if (isset($detailId))
                        <input type="hidden" name="detail_id" value="{{ $detailId }}">
                    @endif


                    {{-- Nama Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang"
                            class="form-control @error('nama_barang') is-invalid @enderror"
                            value="{{ old('nama_barang', $namaBarang ?? '') }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jenis Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>

                            @foreach ($enumValues as $j)
                                <option value="{{ $j }}" {{ old('jenis') == $j ? 'selected' : '' }}>
                                    {{ strtoupper($j) }}
                                    @if ($j == 'atk')
                                        (Alat Tulis Kantor)
                                    @endif
                                    @if ($j == 'rt')
                                        (Peralatan Rumah Tangga)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Satuan --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Satuan</label>
                        <input list="satuanList" name="satuan" id="satuanSelect"
                            class="form-control @error('satuan') is-invalid @enderror"
                            value="{{ old('satuan') }}"
                            placeholder="pcs / pack / dus / lembar / buah / dll" required>

                        <datalist id="satuanList">
                            <option value="pcs">
                            <option value="lembar">
                            <option value="pack">
                            <option value="dus">
                            <option value="box">
                            <option value="roll">
                            <option value="buah">
                            <option value="unit">
                            <option value="batang">
                        </datalist>
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @php
                        // Tampilkan konversi kalau data lama menunjukkan satuan_dasar beda dengan satuan
                        $oldKonversi = old('satuan') !== null && old('satuan_dasar') !== old('satuan');
                    @endphp

                    {{-- Checkbox konversi --}}
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="adaKonversi"
                                {{ $oldKonversi ? 'checked' : '' }}>
                            <label class="form-check-label" for="adaKonversi">
                                Barang ini punya satuan konversi
                                <small class="text-muted">(misal: 1 pack = 10 lembar)</small>
                            </label>
                        </div>
                    </div>

                    {{-- Section konversi — tersembunyi secara default --}}
                    <div id="konversiOptions" style="{{ $oldKonversi ? '' : 'display:none;' }}">

                        <div class="form-group mb-3">
                            <label class="form-label">Jumlah Isi</label>
                            <input type="number" name="konversi_satuan" id="konversiInput" class="form-control"
                                min="1" value="{{ old('konversi_satuan', 1) }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Satuan Dasar</label>
                            <input list="satuanList" name="satuan_dasar" class="form-control"
                                value="{{ old('satuan_dasar') }}"
                                placeholder="pcs / lembar / buah / dll">
                        </div>

                    </div>

                    {{-- Limit Stok --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Limit Stok Minimum</label>
                        <input type="number" name="limit_stok" class="form-control" min="0"
                            value="{{ old('limit_stok') }}">
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </main>

    <script>
        const adaKonversi   = document.getElementById('adaKonversi');
        const konversiOptions = document.getElementById('konversiOptions');
        const satuanSelect  = document.getElementById('satuanSelect');

        adaKonversi.addEventListener('change', function() {
            if (this.checked) {
                konversiOptions.style.display = 'block';
            } else {
                konversiOptions.style.display = 'none';
                // Auto-set: satuan dasar = satuan utama, konversi = 1
                document.querySelector('[name="satuan_dasar"]').value =
                    satuanSelect.value.trim();
                document.getElementById('konversiInput').value = 1;
            }
        });
    </script>

@endsection
