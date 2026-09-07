@extends('layouts.app')

@section('title', 'Ajukan Pemeliharaan Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Ajukan Pemeliharaan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('maintenance.index') }}">Pemeliharaan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Pengajuan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <x-form-errors />

                <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ===========================
                    PILIH GEDUNG, RUANGAN, ASET
                ============================ --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Daftar Aset Yang Akan Di-maintenance</h5>

                        <button type="button" id="btnTambahAset" class="btn btn-success btn-sm">
                            + Tambah Aset
                        </button>
                    </div>

                    @error('details')
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                        </div>
                    @enderror

                    @if ($errors->any())
                        <div class="alert alert-info py-2 mb-3 small">
                            <i class="fas fa-info-circle me-1"></i>
                            Daftar aset perlu diisi ulang setelah ada error — foto dan lampiran juga perlu diunggah ulang.
                        </div>
                    @endif

                    <div id="asetContainer">

                    </div>

                    {{-- ===========================
                    PELAKSANA
                    =========================== --}}
                    <div class="row mb-4">

                        <div class="col-md-6 form-group">
                            <label class="form-label">Pelaksana</label>
                            <select name="pelaksana_type" id="pelaksanaSelect"
                                class="form-select {{ $errors->has('pelaksana_type') ? 'is-invalid' : '' }}">
                                <option value="" disabled {{ !old('pelaksana_type') ? 'selected' : '' }}>-- Pilih --</option>
                                <option value="internal" {{ old('pelaksana_type') === 'internal' ? 'selected' : '' }}>Internal</option>
                                <option value="vendor"   {{ old('pelaksana_type') === 'vendor'   ? 'selected' : '' }}>Vendor</option>
                                <option value="lainnya"  {{ old('pelaksana_type') === 'lainnya'  ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('pelaksana_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group" id="vendorBox"
                             style="display:{{ old('pelaksana_type') === 'vendor' ? 'block' : 'none' }};">
                            <label class="form-label">Vendor</label>
                            <select name="id_vendor"
                                class="form-select {{ $errors->has('id_vendor') ? 'is-invalid' : '' }}">
                                <option value="" disabled selected>-- Pilih Vendor --</option>
                                @foreach ($vendors ?? [] as $v)
                                    <option value="{{ $v->id_vendor }}" {{ old('id_vendor') == $v->id_vendor ? 'selected' : '' }}>
                                        {{ $v->nama_perusahaan }} ({{ $v->bidang_usaha }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_vendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- ===========================
                    TOMBOL AKSI
                    ============================ --}}
                    <div class="text-end">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    <template id="asetTemplate">

                        <div class="card border p-3 mb-3 aset-item">

                            <div class="d-flex justify-content-between mb-3">
                                <h6 class="mb-0">Aset Maintenance</h6>

                                <button type="button" class="btn btn-danger btn-sm hapus-aset">
                                    Hapus
                                </button>
                            </div>

                            <div class="row">

                                <div class="col-md-4">
                                    <label>Gedung</label>

                                    <select class="form-select gedung-select" required>
                                        <option value="">-- Pilih Gedung --</option>

                                        @foreach ($gedung as $g)
                                            <option value="{{ $g->id_gedung }}">
                                                {{ $g->nama_gedung }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Ruangan</label>

                                    <select class="form-select ruangan-select" required disabled>
                                        <option value="">-- Pilih Ruangan --</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Aset</label>

                                    <select name="details[INDEX][id_aset]" class="form-select aset-select" required
                                        disabled>
                                        <option value="">-- Pilih Aset --</option>
                                    </select>
                                </div>

                            </div>

                            <div class="mt-3">

                                <label>Kerusakan</label>

                                <textarea name="details[INDEX][kerusakan]" class="form-control" required></textarea>

                            </div>

                            <div class="mt-3">

                                <label>Foto Kondisi Awal</label>

                                <input type="file" name="details[INDEX][foto_before]" class="form-control"
                                    accept="image/*" required>

                            </div>

                            <div class="mt-3">

                                <label>Lampiran</label>

                                <input type="file" name="details[INDEX][lampiran]" class="form-control">

                            </div>

                        </div>

                    </template>
                </form>
            </div>
        </div>
    </main>

    {{-- ===========================
    SCRIPT DINAMIS
============================ --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let index = 0;

            const container = document.getElementById("asetContainer");
            const template = document.getElementById("asetTemplate");

            function tambahAset() {

                const clone = template.content.cloneNode(true);

                clone.querySelectorAll("[name]").forEach(el => {
                    el.name = el.name.replaceAll("INDEX", index);
                });

                container.appendChild(clone);

                initItem(container.lastElementChild);

                index++;
            }

            async function loadRuangan(idGedung, selectRuangan) {

                const res = await fetch(`/ruangan/by-gedung/${idGedung}`);
                const data = await res.json();

                selectRuangan.innerHTML =
                    `<option value="">-- Pilih Ruangan --</option>`;

                data.forEach(r => {
                    selectRuangan.innerHTML += `
                <option value="${r.id_ruangan}">
                    ${r.nama_ruangan}
                </option>`;
                });

                selectRuangan.disabled = false;
            }

            async function loadAset(idRuangan, selectAset) {

                const res = await fetch(`/aset/by-ruangan/${idRuangan}`);
                const data = await res.json();

                selectAset.innerHTML =
                    `<option value="">-- Pilih Aset --</option>`;

                data.forEach(a => {
                    selectAset.innerHTML += `
                <option value="${a.id_aset}">
                    ${a.nama_aset}
                    (${a.kode_aset})
                </option>`;
                });

                selectAset.disabled = false;
            }

            function initItem(card) {

                const gedung = card.querySelector(".gedung-select");
                const ruangan = card.querySelector(".ruangan-select");
                const aset = card.querySelector(".aset-select");

                gedung.addEventListener("change", function() {
                    loadRuangan(this.value, ruangan);
                });

                ruangan.addEventListener("change", function() {
                    loadAset(this.value, aset);
                });

                card.querySelector(".hapus-aset")
                    .addEventListener("click", function() {
                        card.remove();
                    });
            }

            document.getElementById("btnTambahAset")
                .addEventListener("click", tambahAset);

            tambahAset();
        });
        const pelaksana = document.getElementById("pelaksanaSelect");
        const vendorBox = document.getElementById("vendorBox");
        const vendorSelect = document.querySelector('select[name="id_vendor"]');

        pelaksana.addEventListener("change", function() {
            if (this.value === "vendor") {
                vendorBox.style.display = "block";
                vendorSelect.required = true;
            } else {
                vendorBox.style.display = "none";
                vendorSelect.required = false;
                vendorSelect.value = "";
            }
        });

        // Restore required state dari old input (vendorBox visibility sudah diset via Blade)
        if (pelaksana.value === "vendor") {
            vendorSelect.required = true;
        }
    </script>
@endsection
