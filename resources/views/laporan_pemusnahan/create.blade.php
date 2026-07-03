@extends('layouts.app')

@section('title', 'Ajukan Pemusnahan Aset')

@section('content')
    @php
        // MODE DETECTION
        $isReadonly = isset($aset) && $aset !== null;
    @endphp

    <main class="main-content">
        <div class="content-padding">

            {{-- ===========================
            BREADCRUMB
        ============================ --}}
            <div class="page-header">
                <h1 class="page-title">Ajukan Pemusnahan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('laporan_pemusnahan.index') }}" class="breadcrumb-link">Pemusnahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Laporan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('laporan_pemusnahan.store') }}" method="POST">
                    @csrf

                    {{-- ===========================
                    GEDUNG
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Gedung</label>

                        <select name="id_gedung" id="id_gedung" class="form-select" {{ $isReadonly ? 'disabled' : '' }}
                            required>

                            @if ($isReadonly)
                                <option value="{{ $aset->id_gedung }}" selected>
                                    {{ $aset->gedung->nama_gedung }}
                                </option>
                            @else
                                <option value="" disabled selected>-- Pilih Gedung --</option>
                                @foreach ($gedung as $g)
                                    <option value="{{ $g->id_gedung }}">
                                        {{ $g->nama_gedung }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        {{-- hidden supaya tetap terkirim --}}
                        @if ($isReadonly)
                            <input type="hidden" name="id_gedung" value="{{ $aset->id_gedung }}">
                        @endif

                        @error('id_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ===========================
                    RUANGAN
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Ruangan</label>

                        <select name="id_ruangan" id="id_ruangan" class="form-select" {{ $isReadonly ? 'disabled' : '' }}
                            required>

                            @if ($isReadonly)
                                <option value="{{ $aset->id_ruangan }}" selected>
                                    {{ $aset->ruangan->nama_ruangan }}
                                </option>
                            @else
                                <option value="" disabled selected>-- Pilih Ruangan --</option>
                            @endif
                        </select>

                        @if ($isReadonly)
                            <input type="hidden" name="id_ruangan" value="{{ $aset->id_ruangan }}">
                        @endif

                        @error('id_ruangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ===========================
                    ASET
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Aset</label>

                        <select name="id_aset" id="id_aset" class="form-select" {{ $isReadonly ? 'disabled' : '' }}
                            required>

                            @if ($isReadonly)
                                <option value="{{ $aset->id_aset }}" selected>
                                    {{ $aset->nama_aset }} ({{ $aset->kode_aset }})
                                </option>
                            @else
                                <option value="" disabled selected>-- Pilih Aset --</option>
                            @endif
                        </select>

                        @if ($isReadonly)
                            <input type="hidden" name="id_aset" value="{{ $aset->id_aset }}">
                        @endif

                        @error('id_aset')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ===========================
                    TANGGAL
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" min="{{ date('Y-m-d') }}" name="tanggal_pemusnahan"
                            value="{{ old('tanggal_pemusnahan') }}" class="form-control" required>
                    </div>

                    {{-- ===========================
                    METODE
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Metode Pemusnahan</label>
                        <select name="metode" id="metodeSelect" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Metode --</option>
                            <option value="Lelang">Lelang</option>
                            <option value="Hibahkan">Hibahkan</option>
                            <option value="Dijual">Dijual</option>
                            <option value="Dimusnahkan">Dimusnahkan</option>
                        </select>
                    </div>

                    {{-- ===========================
                    BIAYA KELUAR
                =========================== --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Biaya Keluar</label>
                        <input type="number" name="biaya_keluar" id="biayaKeluar" class="form-control" min="0"
                            placeholder="Opsional (Rp)">
                        <small class="text-muted">
                            Boleh kosong saat pengajuan
                        </small>
                    </div>

                    {{-- ===========================
                    NILAI MASUK (JUAL / LELANG)
                =========================== --}}
                    <div class="form-group mb-4" id="nilaiMasukWrapper" style="display:none;">
                        <label class="form-label">Nilai Masuk</label>
                        <input type="number" name="nilai_masuk" id="nilaiMasuk" class="form-control" min="0"
                            placeholder="Masukkan nominal (Rp)">
                        <small class="text-muted">
                            Opsional saat pengajuan.
                            <strong>Wajib diisi sebelum pemusnahan diselesaikan</strong>
                            (jika metode Lelang/Dijual)
                        </small>
                    </div>

                    {{-- ===========================
                    PELAKSANA
                =========================== --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Pelaksana</label>
                        <select name="pelaksana_type" id="pelaksanaType" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Pelaksana --</option>
                            <option value="internal">Internal</option>
                            <option value="vendor">Vendor</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- ===========================
                    VENDOR SELECT
                =========================== --}}
                    <div class="form-group mb-4" id="vendorSelectWrapper" style="display:none;">
                        <label class="form-label">Pilih Vendor</label>
                        <select name="id_vendor" class="form-select">
                            <option value="" disabled selected>-- Pilih Vendor --</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id_vendor }}">
                                    {{ $v->nama_perusahaan }} ({{ $v->bidang_usaha }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ===========================
                    VENDOR MANUAL
                =========================== --}}
                    <div class="form-group mb-4" id="vendorManualWrapper" style="display:none;">
                        <label class="form-label">Nama Vendor (Manual)</label>
                        <input type="text" name="vendor_manual" class="form-control"
                            placeholder="Masukkan nama vendor">
                    </div>


                    {{-- ===========================
                    CATATAN
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tambahkan catatan jika perlu...">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('laporan_pemusnahan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- ===========================
    AJAX FETCH (HANYA MODE NORMAL)
=========================== --}}
    @if (!$isReadonly)
        <script>
            document.getElementById('id_gedung').addEventListener('change', function() {
                const id = this.value;
                const ruangan = document.getElementById('id_ruangan');
                const aset = document.getElementById('id_aset');

                ruangan.innerHTML = '<option value="" disabled selected>-- Pilih Ruangan --</option>';
                aset.innerHTML = '<option value="" disabled selected>-- Pilih Aset --</option>';

                if (!id) return;

                fetch(`/get-ruangan/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(r => {
                            const opt = document.createElement('option');
                            opt.value = r.id_ruangan;
                            opt.textContent = r.nama_ruangan;
                            ruangan.appendChild(opt);
                        });
                    });
            });

            document.getElementById('id_ruangan').addEventListener('change', function() {
                const id = this.value;
                const aset = document.getElementById('id_aset');

                aset.innerHTML = '<option value="" disabled selected>-- Pilih Aset --</option>';

                if (!id) return;

                fetch(`/get-aset/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(a => {
                            const opt = document.createElement('option');
                            opt.value = a.id_aset;
                            opt.textContent = `${a.nama_aset} (${a.kode_aset})`;
                            aset.appendChild(opt);
                        });
                    });
            });

            document.querySelectorAll('input[type="date"]').forEach(input => {
                input.addEventListener('focus', () => input.showPicker());
            });

            const metodeSelect = document.getElementById('metodeSelect');
            const nilaiWrapper = document.getElementById('nilaiMasukWrapper');
            const nilaiInput = document.getElementById('nilaiMasuk');

            metodeSelect.addEventListener('change', function() {

                if (this.value === 'Lelang' || this.value === 'Dijual') {
                    nilaiWrapper.style.display = 'block';
                } else {
                    nilaiWrapper.style.display = 'none';
                    nilaiInput.value = '';
                }

            });

            // ================= PELAKSANA TOGGLE =================
            const pelaksana = document.getElementById('pelaksanaType');
            const vendorSelect = document.getElementById('vendorSelectWrapper');
            const vendorManual = document.getElementById('vendorManualWrapper');

            pelaksana.addEventListener('change', function() {

                vendorSelect.style.display = 'none';
                vendorManual.style.display = 'none';

                if (this.value === 'vendor') {
                    vendorSelect.style.display = 'block';
                }

                if (this.value === 'lainnya') {
                    vendorManual.style.display = 'block';
                }

            });
        </script>
    @endif

@endsection
