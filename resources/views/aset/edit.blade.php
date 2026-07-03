@extends('layouts.app')

@section('title', 'Edit Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Edit Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('aset.index') }}">Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Aset</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('aset.update', $aset->id_aset) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ============================
                    GEDUNG
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Gedung</label>
                        <select name="id_gedung" id="id_gedung" class="form-select not-editable" onchange="loadRuangan()"
                            disabled readonly>
                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}"
                                    {{ $aset->id_gedung == $g->id_gedung ? 'selected' : '' }}>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ============================
                    RUANGAN
                ============================= --}}
                    <div class="form-group mb-4" id="ruanganWrap">
                        <label class="form-label">Ruangan</label>
                        <select name="id_ruangan" id="id_ruangan" class="form-select not-editable" disabled>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id_ruangan }}"
                                    {{ $aset->id_ruangan == $r->id_ruangan ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ============================
                    STATUS (KHUSUS SARANA)
                ============================= --}}
                    <div class="form-group mb-4" id="statusGroup"
                        style="{{ $aset->jenis == 'sarana' ? '' : 'display:none;' }}">
                        <label class="form-label">Status Aset</label>
                        <select name="status" id="status" class="form-select">
                            <option value="tersedia" {{ $aset->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terpakai" {{ $aset->status == 'terpakai' ? 'selected' : '' }}>Terpakai</option>
                            <option value="maintenance" {{ $aset->status == 'maintenance' ? 'selected' : '' }}>Maintenance
                            </option>
                            <option value="non aktif" {{ $aset->status == 'non aktif' ? 'selected' : '' }}>Non Aktif
                            </option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Jenis Barang</label>
                        <select id="barang" name="barang" class="form-select">
                            @foreach (\App\Models\JenisBarang::orderBy('nama_barang')->get() as $jb)
                                <option value="{{ $jb->id_jenis_barang }}"
                                    {{ $aset->id_jenis_barang == $jb->id_jenis_barang ? 'selected' : '' }}>
                                    {{ $jb->nama_barang }}
                                    ({{ ucfirst($jb->jenis) }} - {{ ucfirst($jb->kategori) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ============================
                    DETAIL ASET
                ============================= --}}
                    <div class="form-group mb-3">
                        <label for="nama_aset" class="form-label">Nama Aset</label>
                        <input type="text" name="nama_aset" id="nama_aset" class="form-control"
                            value="{{ $aset->nama_aset }}" required>
                    </div>

                    {{-- MERK --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Merk</label>
                        <input type="text" name="merk" value="{{ old('merk') }}" class="form-control"
                            placeholder="Contoh: ASUS, Lenovo, Samsung">
                        @error('merk')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- TIPE / MODEL --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Tipe / Model</label>
                        <input type="text" name="tipe_model" value="{{ old('tipe_model') }}" class="form-control"
                            placeholder="Contoh: Vivobook 14 A1404">
                        @error('tipe_model')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- SPESIFIKASI --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Spesifikasi</label>
                        <textarea name="spesifikasi" class="form-control" rows="3"
                            placeholder="Contoh: Intel i5 Gen 12, RAM 16GB, SSD 512GB">{{ old('spesifikasi') }}</textarea>
                        @error('spesifikasi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="form-group mb-3">
                        <label class="form-label">Tahun Perolehan</label>
                        <input type="number" name="tahun_perolehan" class="form-control" min="1900"
                            max="{{ date('Y') }}" value="{{ $aset->tahun_perolehan }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Nilai Aset (Rp)</label>
                        <input type="number" name="nilai" class="form-control" value="{{ $aset->nilai }}"
                            min="0" step="1000">
                    </div>

                    {{-- ============================
                    KELAYAKAN
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Kelayakan</label>
                        <select class="form-select not-editable" disabled>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $aset->kelayakan == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>

                        <!-- kirim nilai lama, tapi user ga bisa ubah -->
                        <input type="hidden" name="kelayakan" value="{{ $aset->kelayakan }}">
                    </div>


                    <div class="form-group mb-3" id="keteranganContainer">
                        <label class="form-label">Keterangan Kelayakan</label>
                        <input type="text" name="keterangan_kelayakan" id="keterangan_kelayakan"
                            class="form-control not-editable" value="{{ $aset->keterangan_kelayakan }}" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Foto Aset</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto" accept="image/*" hidden>
                            </label>
                            <span class="custom-file-text">
                                {{ $aset->foto ? basename($aset->foto) : 'Belum ada file dipilih' }}
                            </span>
                        </div>

                        @if ($aset->foto)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $aset->foto) }}" width="200" class="rounded shadow">
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
                        <a href="{{ route('aset.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- Script sama seperti create --}}
    <script>
        function show(el) {
            el.style.display = '';
        }

        function hide(el) {
            el.style.display = 'none';
        }

        function loadRuangan() {
            const idGedung = document.getElementById('id_gedung').value;
            const ruanganSelect = document.getElementById('id_ruangan');
            ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            if (!idGedung) return;
            fetch(`/api/ruangan-by-gedung/${idGedung}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id_ruangan;
                        opt.textContent = r.nama_ruangan;
                        ruanganSelect.appendChild(opt);
                    });
                });
        }

        function generateKodeAset() {
            const select = document.getElementById('barang');
            const prefix = select.options[select.selectedIndex]?.dataset.prefix;

            const kodeInput = document.getElementById('kode_aset');
            const kodeDisplay = document.getElementById('kode_aset_display');
            const kodeGroup = document.getElementById('kodeGroup');

            if (!prefix) {
                hide(kodeGroup);
                return;
            }

            fetch(`/aset/generate-kode?prefix=${encodeURIComponent(prefix)}`)
                .then(res => res.json())
                .then(data => {
                    kodeInput.value = data.kode;
                    kodeDisplay.value = data.kode;
                    show(kodeGroup);
                });
        }


        function updateKeterangan() {
            const val = document.getElementById('kelayakan').value;
            const kont = document.getElementById('keteranganContainer');
            if (val === "1" || val === "2") kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" value="Layak" readonly>`;
            else if (val === "3") kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" value="Perlu pemantauan" readonly>`;
            else if (val === "4") kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" value="Perlu perbaikan" readonly>`;
            else if (val === "5") kont.innerHTML =
                `<label class="form-label">Keterangan</label><select name="keterangan_kelayakan" class="form-select"><option value="Lelang">Lelang</option><option value="Hibahkan">Hibahkan</option><option value="Dijual">Dijual</option><option value="Dimusnahkan">Dimusnahkan</option></select>`;
            else kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" readonly>`;
        }

        document.addEventListener('DOMContentLoaded', async function() {
            const barangTerpilih = "{{ $aset->id_jenis_barang ?? '' }}";

            // 1️⃣ Set jenis & trigger logic yang sama seperti create
            if (jenis) {
                document.getElementById('jenis').value = jenis;
                onJenisChangeFromSelect();
            }

            // 2️⃣ Jika sarana → set kategori
            if (jenis === 'sarana' && kategori) {
                const kategoriSelect = document.getElementById('kategori');
                kategoriSelect.value = kategori;

                // 3️⃣ Load jenis barang & tunggu selesai
                await loadJenisBarangAsync();

                // 4️⃣ Set barang lama
                if (barangTerpilih) {
                    document.getElementById('barang').value = barangTerpilih;
                }
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
