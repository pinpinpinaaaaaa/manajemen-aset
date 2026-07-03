@extends('layouts.app')

@section('title', 'Edit APAR')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Edit APAR</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('apar.index') }}">APAR</a>
                    <span class="separator">/</span>
                    <span class="current">Edit APAR</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('apar.update', $apar->id_apar) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-4">
                        <label for="id_gedung" class="form-label">Gedung</label>
                        <select name="id_gedung" id="id_gedung" class="form-select" required>
                            <option value="">-- Pilih Gedung --</option>
                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}"
                                    {{ $apar->id_gedung == $g->id_gedung ? 'selected' : '' }}>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="id_ruangan" class="form-label">Ruangan</label>
                        <select name="id_ruangan" id="id_ruangan" class="form-select" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id_ruangan }}"
                                    {{ $apar->id_ruangan == $r->id_ruangan ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control"
                            placeholder="Contoh: Dekat pintu masuk / Samping lift"
                            value="{{ old('lokasi', $apar->lokasi) }}">

                        @error('lokasi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <option value="refill" {{ $apar->jenis == 'refill' ? 'selected' : '' }}>Refill</option>
                            <option value="sekali pakai" {{ $apar->jenis == 'sekali pakai' ? 'selected' : '' }}>Sekali
                                Pakai</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ukuran" class="form-label">Ukuran (kg)</label>
                        <input type="number" name="ukuran" class="form-control" value="{{ $apar->ukuran }}" required>
                    </div>

                    {{-- MERK --}}
                    <div class="form-group mb-3">
                        <label for="merk" class="form-label">Merk</label>
                        <input type="text" name="merk" class="form-control" placeholder="Contoh: Yamato / GuardALL"
                            value="{{ old('merk', $apar->merk) }}">

                        @error('merk')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- MEDIA ISI --}}
                    <div class="form-group mb-3">
                        <label for="media_isi" class="form-label">Media Isi</label>
                        <input type="text" name="media_isi" class="form-control"
                            placeholder="Contoh: Powder / CO2 / Foam" value="{{ old('media_isi', $apar->media_isi) }}">

                        @error('media_isi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4" id="refill-group">
                        <label class="form-label">Tanggal Refill</label>
                        <input type="date" name="tanggal_refill" id="tanggal_refill" class="form-control"
                            value="{{ $apar->tanggal_refill }}" style="width: 100%">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Tanggal Kadaluarsa</label>
                        <input type="date" name="expired_date" class="form-control" value="{{ $apar->expired_date }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="form-control">{{ $apar->keterangan }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Foto APAR</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto" accept="image/*" hidden>
                            </label>
                            <span class="custom-file-text">
                                {{ $apar->foto ? basename($apar->foto) : 'Belum ada file dipilih' }}
                            </span>
                        </div>

                        @if ($apar->foto)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $apar->foto) }}" width="200" class="rounded shadow">
                            </div>
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                        @endif

                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('apar.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    {{-- Script: Dropdown dinamis --}}
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
                        ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
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
        document.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.addEventListener('focus', function() {
                this.showPicker(); // Buka datepicker browser
            });
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
                    refillInput.value = null;
                }
            }

            jenis.addEventListener('change', toggleRefill);
            toggleRefill(); // auto sync dari data lama
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
