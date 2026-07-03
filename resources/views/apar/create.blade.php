@extends('layouts.app')

@section('title', 'Tambah APAR')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Tambah APAR</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('apar.index') }}">APAR</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah APAR</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('apar.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- GEDUNG --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Gedung</label>
                        <select name="id_gedung" id="id_gedung" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Gedung --</option>
                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}"
                                    {{ old('id_gedung') == $g->id_gedung ? 'selected' : '' }}>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>

                        @error('id_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- RUANGAN --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Ruangan</label>
                        <select name="id_ruangan" id="id_ruangan" class="form-select" disabled required>
                            <option value="">-- Pilih Gedung Terlebih Dahulu --</option>
                        </select>

                        @error('id_ruangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- LOKASI --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control"
                            placeholder="Contoh: Dekat pintu masuk / Samping lift" value="{{ old('lokasi') }}">

                        @error('lokasi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- JENIS --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="refill" {{ old('jenis') == 'refill' ? 'selected' : '' }}>Refill</option>
                            <option value="sekali pakai" {{ old('jenis') == 'sekali pakai' ? 'selected' : '' }}>Sekali
                                Pakai
                            </option>
                        </select>

                        @error('jenis')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- UKURAN --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Ukuran (kg)</label>
                        <input type="number" name="ukuran" class="form-control" placeholder="Contoh: 6"
                            value="{{ old('ukuran') }}" required>

                        @error('ukuran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- MERK --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Merk</label>
                        <input type="text" name="merk" class="form-control"
                            placeholder="Contoh: APAR GuardALL / Yamato" value="{{ old('merk') }}">

                        @error('merk')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- MEDIA ISI --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Media Isi</label>
                        <input type="text" name="media_isi" class="form-control"
                            placeholder="Contoh: Powder / CO2 / Foam" value="{{ old('media_isi') }}">

                        @error('media_isi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- TANGGAL REFILL --}}
                    <div class="form-group mb-3" id="refill-group" style="display:none;">
                        <label class="form-label">Tanggal Refill</label>
                        <input type="date" name="tanggal_refill" id="tanggal_refill" class="form-control"
                            value="{{ old('tanggal_refill') }}">

                        @error('tanggal_refill')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- EXPIRED --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Tanggal Kadaluarsa</label>
                        <input type="date" name="expired_date" class="form-control" value="{{ old('expired_date') }}">

                        @error('expired_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan catatan jika ada...">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- FOTO --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Foto APAR</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto" accept="image/*" hidden>
                            </label>
                            <span class="custom-file-text">Belum ada file dipilih</span>
                        </div>

                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- BUTTON --}}
                    <div class="text-end">
                        <a href="{{ route('apar.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <style>
        #refill-group input {
            width: 100% !important;
        }
    </style>

    {{-- ================== SCRIPT ================== --}}
    <script>
        document.getElementById('id_gedung').addEventListener('change', function() {
            const gedungId = this.value;
            const ruanganSelect = document.getElementById('id_ruangan');

            ruanganSelect.innerHTML = '<option value="">Memuat ruangan...</option>';
            ruanganSelect.disabled = true;

            if (gedungId) {
                fetch(`/api/ruangan-by-gedung/${gedungId}`)
                    .then(res => res.json())
                    .then(data => {
                        ruanganSelect.innerHTML =
                            '<option value="" disabled selected>-- Pilih Ruangan --</option>';
                        data.forEach(r => {
                            const opt = document.createElement('option');
                            opt.value = r.id_ruangan;
                            opt.textContent = r.nama_ruangan;
                            ruanganSelect.appendChild(opt);
                        });
                        ruanganSelect.disabled = false;
                    })
                    .catch(() => {
                        ruanganSelect.innerHTML = '<option value="">Gagal memuat ruangan</option>';
                    });
            } else {
                ruanganSelect.innerHTML = '<option value="">-- Pilih Gedung Terlebih Dahulu --</option>';
                ruanganSelect.disabled = true;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const jenis = document.getElementById('jenis');
            const refillGroup = document.getElementById('refill-group');
            const refillInput = document.getElementById('tanggal_refill');

            function toggleRefill() {
                if (jenis.value === 'refill') {
                    refillGroup.style.display = 'block';
                    refillInput.disabled = false;
                } else {
                    refillGroup.style.display = 'none';
                    refillInput.disabled = true;
                    refillInput.value = "";
                }
            }

            jenis.addEventListener('change', toggleRefill);
            toggleRefill(); // initial load
        });

        // Auto-open datepicker
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.addEventListener('focus', () => input.showPicker());
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
