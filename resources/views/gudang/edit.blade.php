@extends('layouts.app')

@section('title', 'Edit Barang Gudang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit Barang Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gudang.index') }}">Gudang</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $barang->id_barang }}</span>
                </nav>
            </div>

            <div class="card p-4 shadow-sm rounded-lg mt-4">

                <form action="{{ route('gudang.update', $barang->id_barang) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form-errors />

                    {{-- Nama Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang"
                            class="form-control @error('nama_barang') is-invalid @enderror"
                            value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jenis Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($enumValues as $j)
                                <option value="{{ $j }}"
                                    {{ old('jenis', $barang->jenis) === $j ? 'selected' : '' }}>
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
                            value="{{ old('satuan', $barang->satuan) }}" required>

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
                        // Barang lama punya konversi kalau satuan != satuan_dasar
                        $punyaKonversi = $barang->satuan !== $barang->satuan_dasar;
                        // Setelah validation failure, override dari old() jika ada
                        $konversiChecked = old('satuan') !== null
                            ? (old('satuan_dasar') !== old('satuan'))
                            : $punyaKonversi;
                    @endphp

                    {{-- Checkbox konversi — pre-checked kalau barang lama punya konversi --}}
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="adaKonversi"
                                {{ $konversiChecked ? 'checked' : '' }}>
                            <label class="form-check-label" for="adaKonversi">
                                Barang ini punya satuan konversi
                                <small class="text-muted">(misal: 1 pack = 10 lembar)</small>
                            </label>
                        </div>
                    </div>

                    {{-- Section konversi — tampil kalau barang lama punya konversi --}}
                    <div id="konversiOptions" style="{{ $konversiChecked ? '' : 'display:none;' }}">

                        <div class="form-group mb-3">
                            <label class="form-label">Jumlah Isi</label>
                            <input type="number" name="konversi_satuan" class="form-control" min="1"
                                value="{{ old('konversi_satuan', $barang->konversi_satuan) }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Satuan Dasar</label>
                            <input list="satuanList" name="satuan_dasar" class="form-control"
                                value="{{ old('satuan_dasar', $barang->satuan_dasar) }}"
                                placeholder="pcs / lembar / buah / dll">
                        </div>
                    </div>

                    {{-- Limit Stok --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Limit Stok Minimum</label>
                        <input type="number" name="limit_stok" class="form-control"
                            value="{{ old('limit_stok', $barang->limit_stok) }}" min="0">
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control"
                            rows="3">{{ old('keterangan', $barang->keterangan) }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </main>

    <script>
        const adaKonversi     = document.getElementById('adaKonversi');
        const konversiOptions = document.getElementById('konversiOptions');
        const satuanSelect    = document.getElementById('satuanSelect');

        adaKonversi.addEventListener('change', function() {
            if (this.checked) {
                konversiOptions.style.display = 'block';
            } else {
                konversiOptions.style.display = 'none';
                // Auto-set: satuan dasar = satuan utama, konversi = 1
                document.querySelector('[name="satuan_dasar"]').value =
                    satuanSelect.value.trim();
                document.querySelector('[name="konversi_satuan"]').value = 1;
            }
        });
    </script>

@endsection
