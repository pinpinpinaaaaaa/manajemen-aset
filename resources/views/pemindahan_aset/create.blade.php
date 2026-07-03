@extends('layouts.app')

@section('title', 'Ajukan Pemindahan Aset')

@section('content')
    @php
        $isReadonly = isset($aset) && $aset !== null;
    @endphp

    <main class="main-content">
        <div class="content-padding">

            {{-- ================= HEADER ================= --}}
            <div class="page-header">
                <h1 class="page-title">Ajukan Pemindahan Aset</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pemindahan_aset.index') }}" class="breadcrumb-link">Pemindahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Pengajuan</span>
                </nav>
            </div>


            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('pemindahan_aset.store') }}" method="POST">
                    @csrf

                    <h5 class="mb-3">Daftar Aset yang Dipindahkan</h5>

                    <div id="asetContainer"></div>

                    <button type="button" class="btn btn-outline-primary" id="btnTambahAset">
                        <i class="fa fa-plus"></i> Tambah Aset
                    </button>


                    {{-- ================= ALASAN ================= --}}
                    <div class="form-group mb-4">
                        <label class="form-label">
                            Alasan Pemindahan <span class="text-danger">*</span>
                        </label>

                        <textarea name="alasan" rows="3" class="form-control" required
                            placeholder="Relokasi operasional / perubahan fungsi / dll">{{ old('alasan') }}</textarea>

                        @error('alasan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="text-end">
                        <a href="{{ route('pemindahan_aset.index') }}" class="btn btn-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">
                            Ajukan Pemindahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>


    {{-- ================= SCRIPT ================= --}}
    @if (!$isReadonly)
        <script>
            // helper reset select
            function resetSelect(el, placeholder = '-- Pilih --') {
                el.innerHTML = `<option disabled selected>${placeholder}</option>`;
            }

            let asetIndex = 0;

            const gedungOptions = `
                <option value="">-- Pilih Gedung --</option>
                @foreach ($gedung as $g)
                <option value="{{ $g->id_gedung }}">
                    {{ $g->nama_gedung }}
                </option>
                @endforeach
                `;

            const vendorOptions = `
                <option value="">-- Pilih Vendor --</option>
                @foreach ($vendors as $v)
                <option value="{{ $v->id_vendor }}">
                    {{ $v->nama_perusahaan }} ({{ $v->bidang_usaha }})
                </option>
                @endforeach
                `;

            document.getElementById('btnTambahAset')
                .addEventListener('click', function() {

                    const container =
                        document.getElementById('asetContainer');

                    const card = document.createElement('div');

                    card.className =
                        'card border p-3 mb-3 aset-item';

                    card.innerHTML = `
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Gedung Asal</label>
                                <select name="from_gedung[]" class="form-select from-gedung" required>
                                    ${gedungOptions}
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ruangan Asal</label>
                                <select name="from_ruangan[]" class="form-select from-ruangan" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Aset</label>
                                <select name="id_aset[]" class="form-select aset-select" required>
                                    <option value="">-- Pilih Aset --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Gedung Tujuan</label>
                                <select name="to_gedung[]" class="form-select gedung-tujuan" required>
                                    ${gedungOptions}
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ruangan Tujuan</label>
                                <select name="to_ruangan[]" class="form-select ruangan-tujuan" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Biaya</label>
                                <input type="number" name="biaya[]" class="form-control" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pelaksana</label>
                                <select name="pelaksana_type[]" class="form-select pelaksana" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="internal">Internal</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-6 vendor-wrapper" style="display:none;">
                                <label class="form-label">Vendor</label>
                                <select name="id_vendor[]" class="form-select vendor-select">
                                    ${vendorOptions}
                                </select>
                            </div>

                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-danger btnHapusAset">
                                    Hapus
                                </button>
                            </div>

                        </div>
                        `;

                    container.appendChild(card);

                    loadAsetDropdown(card);
                });

            document.addEventListener('change', function(e) {

                if (e.target.classList.contains('from-gedung')) {

                    const card =
                        e.target.closest('.aset-item');

                    const ruangan =
                        card.querySelector('.from-ruangan');

                    ruangan.innerHTML =
                        '<option value="">-- Pilih Ruangan --</option>';

                    fetch(`/get-ruangan/${e.target.value}`)
                        .then(r => r.json())
                        .then(data => {

                            data.forEach(item => {

                                ruangan.innerHTML += `
                        <option value="${item.id_ruangan}">
                            ${item.nama_ruangan}
                        </option>
                    `;

                            });

                        });

                }

            });

            document.addEventListener('change', function(e) {

                if (e.target.classList.contains('from-ruangan')) {

                    const card =
                        e.target.closest('.aset-item');

                    const aset =
                        card.querySelector('.aset-select');

                    aset.innerHTML =
                        '<option value="">-- Pilih Aset --</option>';

                    fetch(`/get-aset-pindah/${e.target.value}`)
                        .then(r => r.json())
                        .then(data => {

                            data.forEach(item => {

                                aset.innerHTML += `
                        <option value="${item.id_aset}">
                            ${item.nama_aset}
                            (${item.kode_aset})
                        </option>
                    `;

                            });

                        });

                }

            });
            document.addEventListener('change', function(e) {

                if (
                    e.target.classList.contains(
                        'gedung-tujuan'
                    )
                ) {

                    const gedung = e.target.value;

                    const card =
                        e.target.closest('.aset-item');

                    const ruangan =
                        card.querySelector(
                            '.ruangan-tujuan'
                        );

                    ruangan.innerHTML =
                        '<option value="">-- Pilih Ruangan --</option>';

                    fetch(`/get-ruangan/${gedung}`)
                        .then(res => res.json())
                        .then(data => {

                            data.forEach(r => {

                                ruangan.innerHTML += `
                                        <option value="${r.id_ruangan}">
                                            ${r.nama_ruangan}
                                        </option>
                                    `;

                            });

                        });
                }

            });
            document.addEventListener('change', function(e) {

                if (
                    e.target.classList.contains(
                        'pelaksana'
                    )
                ) {

                    const card =
                        e.target.closest('.aset-item');

                    const wrapper =
                        card.querySelector(
                            '.vendor-wrapper'
                        );

                    const select =
                        wrapper.querySelector(
                            '.vendor-select'
                        );

                    if (e.target.value === 'vendor') {

                        wrapper.style.display = 'block';
                        select.required = true;

                    } else {

                        wrapper.style.display = 'none';
                        select.required = false;
                        select.value = '';

                    }
                }

            });

            document.addEventListener('click', function(e) {

                if (
                    e.target.classList.contains(
                        'btnHapusAset'
                    )
                ) {
                    e.target
                        .closest('.aset-item')
                        .remove();
                }

            });
        </script>
    @endif

@endsection
