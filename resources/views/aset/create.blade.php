@extends('layouts.app')

@section('title', 'Tambah Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Tambah Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>

                    {{-- Jika jenis Sarana --}}
                    @if (isset($jenis) && $jenis == 'sarana')
                        <a href="{{ route('aset.index', ['jenis' => 'sarana']) }}">Sarana</a>
                        <span class="separator">/</span>
                        <span class="current">Tambah Aset</span>

                        {{-- Jika berasal dari gedung --}}
                    @elseif(isset($selectedGedung) && $selectedGedung)
                        <a href="{{ route('gedung.index') }}">Gedung</a>
                        <span class="separator">/</span>

                        {{-- Link ke dashboard gedung --}}
                        <a href="{{ route('gedung.dashboard', $selectedGedung->id_gedung) }}">
                            {{ $selectedGedung->nama_gedung }}
                        </a>

                        {{-- Jika juga berasal dari ruangan --}}
                        @if (isset($selectedRuangan) && $selectedRuangan)
                            <span class="separator">/</span>

                            {{-- Link ke dashboard ruangan --}}
                            <a href="{{ route('ruangan.dashboard', $selectedRuangan->id_ruangan) }}">
                                {{ $selectedRuangan->nama_ruangan }}
                            </a>
                        @endif

                        <span class="separator">/</span>
                        <span class="current">Tambah Aset</span>

                        {{-- Default --}}
                    @else
                        <a href="{{ route('aset.index') }}">Aset</a>
                        <span class="separator">/</span>
                        <span class="current">Tambah Aset</span>
                    @endif
                </nav>

            </div>


            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('aset.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ============================
                    GEDUNG
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Gedung</label>
                        @if (isset($selectedGedung) && $selectedGedung)
                            <input type="text" class="form-control" value="{{ $selectedGedung->nama_gedung }}" readonly>
                            <input type="hidden" name="id_gedung" id="id_gedung" value="{{ $selectedGedung->id_gedung }}">
                        @else
                            <select name="id_gedung" id="id_gedung" class="form-select" onchange="loadRuangan(this.value)">
                                <option value="" disabled selected>-- Pilih Gedung --</option>
                                @foreach ($gedung as $g)
                                    <option value="{{ $g->id_gedung }}"
                                        {{ old('id_gedung') == $g->id_gedung ? 'selected' : '' }}>
                                        {{ $g->nama_gedung }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('id_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- ============================
                    RUANGAN
                ============================= --}}
                    <div class="form-group mb-4" id="ruanganWrap">
                        <label class="form-label">Ruangan</label>
                        @if (isset($selectedRuangan) && $selectedRuangan)
                            <input type="text" class="form-control" value="{{ $selectedRuangan->nama_ruangan }}"
                                readonly>
                            <input type="hidden" name="id_ruangan" value="{{ $selectedRuangan->id_ruangan }}">
                        @else
                            <select name="id_ruangan" id="id_ruangan" class="form-select">
                                <option value="" disabled selected>-- Pilih Ruangan --</option>
                            </select>
                        @endif
                        @error('id_ruangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- ============================
                    STATUS (KHUSUS SARANA)
                ============================= --}}
                    <div class="form-group mb-4" id="statusGroup" style="display:none;">
                        <label class="form-label">Status Aset</label>
                        <select name="status" id="status" class="form-select">
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terpakai" {{ old('status') == 'terpakai' ? 'selected' : '' }}>Terpakai</option>
                        </select>

                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="form-group mb-4">
                        <label class="form-label">Jenis Barang</label>
                        <select id="barang" name="barang" class="form-select" onchange="generateKodeAset()">
                            <option value="" disabled selected>-- Pilih Jenis Barang --</option>

                            @foreach (\App\Models\JenisBarang::all() as $jb)
                                <option value="{{ $jb->id_jenis_barang }}" data-prefix="{{ $jb->prefix_kode }}"
                                    data-jenis="{{ $jb->jenis }}">
                                    {{ $jb->nama_barang }} ({{ $jb->jenis }})
                                </option>
                            @endforeach
                        </select>

                        @error('barang')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="form-group mb-4" id="kodeGroup" style="display:none;">
                        <label class="form-label">Kode Aset Otomatis</label>
                        <input type="text" id="kode_aset_display" class="form-control" readonly>
                        <input type="hidden" name="kode_aset" id="kode_aset">
                    </div>

                    {{-- ============================
                    DETAIL ASET
                ============================= --}}
                    <div class="form-group mb-3">
                        <label for="nama_aset" class="form-label">Nama Aset</label>
                        <input type="text" name="nama_aset" value="{{ old('nama_aset') }}" class="form-control">
                        @error('nama_aset')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                        <input type="number" name="tahun_perolehan" value="{{ old('tahun_perolehan') }}"
                            class="form-control">
                        @error('tahun_perolehan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="form-group mb-3">
                        <label class="form-label">Nilai Aset (Rp)</label>
                        <input type="number" name="nilai" value="{{ old('nilai') }}" class="form-control">
                        @error('nilai')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- ============================
                    KELAYAKAN
                ============================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Kelayakan (1–5)</label>
                        <select name="kelayakan" id="kelayakan" class="form-select" onchange="updateKeterangan()">
                            <option value="" disabled selected>-- Pilih Nilai --</option>
                            <option value="1" {{ old('kelayakan') == 1 ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('kelayakan') == 2 ? 'selected' : '' }}>2</option>
                        </select>

                        @error('kelayakan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="form-group mb-3">
                        <label class="form-label">Keterangan Kelayakan</label>
                        <div id="keteranganContainer">
                            <input type="text" name="keterangan_kelayakan" id="keterangan_kelayakan"
                                class="form-control" style="width: 100%" readonly>
                        </div>

                        {{-- Taruh error DI SINI --}}
                        @error('keterangan_kelayakan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Foto Aset</label>

                        <div class="custom-file-wrapper">
                            <label class="custom-file-button">
                                Pilih File
                                <input type="file" name="foto" accept="image/*" hidden>
                            </label>
                            <span class="custom-file-text">
                                Belum ada file dipilih
                            </span>
                        </div>

                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('aset.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function show(el) {
            el.style.display = '';
        }

        function hide(el) {
            el.style.display = 'none';
        }

        function loadRuangan(idGedung) {
            const ruanganSelect = document.getElementById('id_ruangan');
            ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';

            if (!idGedung) return;

            fetch(`/api/ruangan-by-gedung/${idGedung}`)
                .then(res => res.json())
                .then(data => {
                    ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                    data.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id_ruangan;
                        opt.textContent = r.nama_ruangan;
                        ruanganSelect.appendChild(opt);
                    });
                })
                .catch(err => {
                    console.error('Gagal load ruangan:', err);
                    ruanganSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        }


        function generateKodeAset() {
            const select = document.getElementById('barang');
            const id = select.value;
            const prefix = select.options[select.selectedIndex].dataset.prefix;
            const kodeInput = document.getElementById('kode_aset');
            const kodeDisplay = document.getElementById('kode_aset_display');
            const kodeGroup = document.getElementById('kodeGroup');

            if (!id) {
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
            const fotoBefore = document.getElementById('fotoBeforeGroup');

            if (val === "1" || val === "2") kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" value="Layak" style="width: 100%" readonly>`;
            else if (val === "3") kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" value="Perlu pemantauan" style="width: 100%" readonly>`;
            else if (val === "4") kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" value="Perlu perbaikan" style="width: 100%" readonly>`;
            else if (val === "5") kont.innerHTML =
                `<label class="form-label">Keterangan</label><select name="keterangan_kelayakan" class="form-select"><option value="">-- Pilih Keterangan --</option><option value="Lelang">Lelang</option><option value="Hibahkan">Hibahkan</option><option value="Dijual">Dijual</option><option value="Dimusnahkan">Dimusnahkan</option></select> style="width: 100%"`;
            else kont.innerHTML =
                `<label class="form-label">Keterangan</label><input type="text" name="keterangan_kelayakan" class="form-control" style="width: 100%" readonly>`;

            if (val === "4") show(fotoBefore);
            else hide(fotoBefore);
        }
        document.addEventListener("DOMContentLoaded", function() {
            const idGedung = document.getElementById('id_gedung')?.value;
            const selectGedung = document.getElementById('id_gedung');
            const selectRuangan = document.getElementById('id_ruangan');
            const jenis = document.getElementById('jenis')?.value;

            // ================
            // AUTO LOAD RUANGAN
            // ================
            if (idGedung && selectRuangan && selectRuangan.tagName === 'SELECT') {
                loadRuangan(idGedung);
            }

            // ================================
            // AUTO SHOW KATEGORI UNTUK SARANA
            // ================================
            if (jenis === 'sarana') {
                show(document.getElementById('kategoriGroup'));
            }

            // ================================
            // TAMPILKAN FOTO BEFORE JIKA KELAYAKAN = 4
            // ================================
            if (document.getElementById('kelayakan').value == "4") {
                show(document.getElementById('fotoBeforeGroup'));
            }

            // ================================
            // AUTO SHOW STATUS JIKA SARANA
            // ================================
            if (jenis === 'sarana') {
                show(document.getElementById('statusGroup'));
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

        document.getElementById('barang').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const jenis = selected.dataset.jenis;

            const statusGroup = document.getElementById('statusGroup');

            if (jenis === 'sarana') {
                show(statusGroup);
            } else {
                hide(statusGroup);

                const status = document.getElementById('status');
                if (status) status.value = '';
            }
        });
    </script>
@endsection
