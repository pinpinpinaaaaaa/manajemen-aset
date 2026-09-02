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

                    {{-- Nama Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="{{ $barang->nama_barang }}"
                            required>
                    </div>

                    {{-- Jenis Barang --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($enumValues as $j)
                                <option value="{{ $j }}" {{ $barang->jenis === $j ? 'selected' : '' }}>
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
                            value="{{ $barang->satuan }}" required>

                        <datalist id="satuanList">
                            <option value="pcs">
                            <option value="lembar">
                            <option value="pack">
                            <option value="dus">
                            <option value="box">
                            <option value="roll">
                        </datalist>
                    </div>

                    @php
                        $satuanDasar = ['pcs', 'lembar', 'botol', 'meter', 'ml', 'gram'];
                    @endphp

                    {{-- OPSI KONVERSI --}}
                    <div id="konversiOptions" style="{{ in_array($barang->satuan, $satuanDasar) ? 'display:none;' : '' }}">

                        <div class="form-group mb-3">
                            <label class="form-label">Jumlah Isi</label>
                            <input type="number" name="konversi_satuan" class="form-control" min="1"
                                value="{{ $barang->konversi_satuan }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Satuan Dasar</label>
                            <input list="satuanList" name="satuan_dasar" class="form-control"
                                value="{{ $barang->satuan_dasar }}" placeholder="pcs / lembar / botol">
                        </div>
                    </div>

                    {{-- Limit Stok --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Limit Stok Minimum</label>
                        <input type="number" name="limit_stok" class="form-control" value="{{ $barang->limit_stok }}"
                            min="0">
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ $barang->keterangan }}</textarea>
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

        function cekSatuan() {
            let value = satuanSelect.value.toLowerCase();

            if (satuanDasar.includes(value)) {
                konversiOptions.style.display = 'none';
                // Set otomatis agar form ngirim nilai yang benar
                document.querySelector('[name="satuan_dasar"]').value = value;
                document.querySelector('[name="konversi_satuan"]').value = 1;
            } else {
                konversiOptions.style.display = 'block';
            }
        }

        satuanSelect.addEventListener('input', cekSatuan);

        // cek saat halaman pertama dibuka
        cekSatuan();
    </script>

@endsection
