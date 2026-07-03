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

                    @if (isset($permintaanId))
                        <input type="hidden" name="permintaan_id" value="{{ $permintaanId }}">
                    @endif

                    @if (isset($detailId))
                        <input type="hidden" name="detail_id" value="{{ $detailId }}">
                    @endif


                    {{-- Nama Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                            value="{{ old('nama_barang', $namaBarang ?? '') }}" required>
                    </div>

                    {{-- Jenis Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>

                            @foreach ($enumValues as $j)
                                <option value="{{ $j }}">
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
                    </div>

                    {{-- Satuan --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Satuan</label>
                        <input list="satuanList" name="satuan" id="satuanSelect" class="form-control"
                            placeholder="pcs / pack / dus / box / dll" required>

                        <datalist id="satuanList">
                            <option value="pcs">
                            <option value="lembar">
                            <option value="pack">
                            <option value="dus">
                            <option value="box">
                            <option value="roll">
                        </datalist>
                    </div>

                    {{-- Jika pack --}}
                    <div id="konversiOptions" style="display:none;">

                        <div class="form-group mb-3">
                            <label class="form-label">Jumlah Isi</label>
                            <input type="number" name="konversi_satuan" id="konversiInput" class="form-control"
                                min="1" value="1">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Satuan Dasar</label>
                            <input list="satuanList" name="satuan_dasar" class="form-control"
                                placeholder="pcs / lembar / botol">
                        </div>

                    </div>

                    {{-- Hidden default untuk pcs / lembar --}}
                    <input type="hidden" name="auto_satuan_dasar" id="autoSatuanDasar" value="pcs">

                    {{-- Limit Stok --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Limit Stok Minimum</label>
                        <input type="number" name="limit_stok" class="form-control" min="0">
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
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
        const satuanSelect = document.getElementById('satuanSelect');
        const konversiOptions = document.getElementById('konversiOptions');

        const satuanDasar = [
            'pcs',
            'lembar',
            'botol',
            'meter',
            'ml',
            'gram'
        ];

        satuanSelect.addEventListener('change', function() {

            let value = this.value.toLowerCase();

            if (satuanDasar.includes(value)) {
                konversiOptions.style.display = 'none';
            } else {
                konversiOptions.style.display = 'block';
            }

        });
    </script>

@endsection
