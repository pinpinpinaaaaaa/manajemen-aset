@extends('layouts.app')

@section('title', 'Edit Laporan Pemusnahan')

@section('content')
    @php
        $isReadonly = true;
    @endphp

    <main class="main-content">
        <div class="content-padding">

            {{-- HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Edit Laporan Pemusnahan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('laporan_pemusnahan.index') }}" class="breadcrumb-link">Pemusnahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $laporan->id_pemusnahan }}</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('laporan_pemusnahan.update', $laporan->id_pemusnahan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form-errors />

                    {{-- GEDUNG --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Gedung</label>
                        <select class="form-select" disabled>
                            <option selected>
                                {{ $aset->gedung->nama_gedung }}
                            </option>
                        </select>
                    </div>

                    {{-- RUANGAN --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Ruangan</label>
                        <select class="form-select" disabled>
                            <option selected>
                                {{ $aset->ruangan->nama_ruangan }}
                            </option>
                        </select>
                    </div>

                    {{-- ASET --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Aset</label>
                        <select class="form-select" disabled>
                            <option selected>
                                {{ $aset->nama_aset }}
                            </option>
                        </select>

                        {{-- hidden supaya tetap terkirim --}}
                        <input type="hidden" name="id_aset" value="{{ $aset->id_aset }}">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" name="tanggal_pemusnahan"
                            class="form-control @error('tanggal_pemusnahan') is-invalid @enderror"
                            value="{{ old('tanggal_pemusnahan', \Carbon\Carbon::parse($laporan->tanggal_pemusnahan)->format('Y-m-d')) }}"
                            required>
                        @error('tanggal_pemusnahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- METODE --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Metode Pemusnahan</label>
                        <select name="metode" id="metodeSelect"
                            class="form-select @error('metode') is-invalid @enderror" required>
                            <option value="">-- Pilih Metode --</option>
                            @foreach (['Lelang', 'Hibahkan', 'Dijual', 'Dimusnahkan'] as $m)
                                <option value="{{ $m }}"
                                    {{ old('metode', $laporan->metode) == $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                        @error('metode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- BIAYA KELUAR --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Biaya Keluar (Opsional)</label>
                        <input type="number" name="biaya_keluar"
                            class="form-control @error('biaya_keluar') is-invalid @enderror"
                            min="0"
                            value="{{ old('biaya_keluar', $laporan->biaya_keluar) }}">
                        @error('biaya_keluar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- NILAI MASUK --}}
                    <div class="form-group mb-4" id="nilaiMasukWrapper" style="display:none;">
                        <label class="form-label">Nilai Masuk</label>
                        <input type="number" name="nilai_masuk" id="nilaiMasukInput"
                            class="form-control @error('nilai_masuk') is-invalid @enderror"
                            min="0"
                            value="{{ old('nilai_masuk', $laporan->nilai_masuk) }}">
                        @error('nilai_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- PELAKSANA --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Pelaksana</label>
                        <select name="pelaksana_type" id="pelaksanaType"
                            class="form-select @error('pelaksana_type') is-invalid @enderror">
                            <option value="">-- Pilih --</option>

                            <option value="internal"
                                {{ old('pelaksana_type', $laporan->pelaksana_type) == 'internal' ? 'selected' : '' }}>
                                Internal
                            </option>

                            <option value="vendor"
                                {{ old('pelaksana_type', $laporan->pelaksana_type) == 'vendor' ? 'selected' : '' }}>
                                Vendor
                            </option>

                            <option value="lainnya"
                                {{ old('pelaksana_type', $laporan->pelaksana_type) == 'lainnya' ? 'selected' : '' }}>
                                Lainnya
                            </option>
                        </select>
                        @error('pelaksana_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4" id="vendorSelectWrapper" style="display:none;">
                        <label>Vendor</label>
                        <select name="id_vendor"
                            class="form-select @error('id_vendor') is-invalid @enderror">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id_vendor }}"
                                    {{ old('id_vendor', $laporan->id_vendor) == $v->id_vendor ? 'selected' : '' }}>
                                    {{ $v->nama_perusahaan }} ({{ $v->bidang_usaha }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_vendor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4" id="vendorManualWrapper" style="display:none;">
                        <label>Vendor Manual</label>
                        <input type="text" name="vendor_manual"
                            class="form-control @error('vendor_manual') is-invalid @enderror"
                            value="{{ old('vendor_manual', $laporan->vendor_manual) }}">
                        @error('vendor_manual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- CATATAN --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" rows="3"
                            class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $laporan->catatan) }}</textarea>
                        @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('laporan_pemusnahan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- SCRIPT BIAYA --}}
    <script>
        const metodeSelect = document.getElementById('metodeSelect');
        const nilaiMasukWrapper = document.getElementById('nilaiMasukWrapper');
        const nilaiMasukInput = document.getElementById('nilaiMasukInput');

        function toggleNilaiMasuk() {
            const metode = metodeSelect.value;

            if (metode === 'Lelang' || metode === 'Dijual') {
                nilaiMasukWrapper.style.display = 'block';
                nilaiMasukInput.setAttribute('required', 'required');
            } else {
                nilaiMasukWrapper.style.display = 'none';
                nilaiMasukInput.removeAttribute('required');
                nilaiMasukInput.value = '';
            }
        }

        metodeSelect.addEventListener('change', toggleNilaiMasuk);
        toggleNilaiMasuk();

        const pelaksana = document.getElementById('pelaksanaType');
        const vendorSelect = document.getElementById('vendorSelectWrapper');
        const vendorManual = document.getElementById('vendorManualWrapper');

        function toggleVendor() {
            vendorSelect.style.display = 'none';
            vendorManual.style.display = 'none';

            if (pelaksana.value === 'vendor') {
                vendorSelect.style.display = 'block';
            }

            if (pelaksana.value === 'lainnya') {
                vendorManual.style.display = 'block';
            }
        }

        pelaksana.addEventListener('change', toggleVendor);
        toggleVendor();
    </script>
@endsection
