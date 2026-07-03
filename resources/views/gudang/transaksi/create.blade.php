@extends('layouts.app')

@section('title', 'Tambah Transaksi Gudang')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <style>
        .barang-row {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 16px 18px 12px;
            transition: box-shadow .2s;
        }

        .barang-row:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, .06);
        }

        /* Label */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #6c757d;
            display: block;
            white-space: nowrap;
        }

        /* Baris field — flex container */
        .detail-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 10px;
            align-items: flex-end;
        }

        /* Kolom-kolom */
        .barang-col {
            flex: 3 1 0;
            min-width: 0;
        }

        .qty-col {
            flex: 0 0 90px;
        }

        .satuan-col {
            flex: 0 0 130px;
        }

        .harga-col {
            flex: 0 0 130px;
        }

        .subtotal-col {
            flex: 0 0 150px;
        }

        .action-col {
            flex: 0 0 auto;
            display: flex;
            gap: 6px;
            align-items: flex-end;
            /* tombol sejajar bawah dengan input */
            padding-bottom: 0;
        }

        /* Kurangi lebar kolom supaya tidak overflow */
        .qty-col {
            flex: 0 0 80px;
        }

        .satuan-col {
            flex: 0 0 120px;
        }

        .harga-col {
            flex: 0 0 120px;
        }

        .subtotal-col {
            flex: 0 0 140px;
        }

        /* Pastikan row tidak clip tombol */
        .barang-row {
            overflow: visible;
        }

        /* Input & select */
        .barang-row .form-control,
        .barang-row .form-select {
            height: 44px;
            border-radius: 10px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Tombol aksi */
        .action-col .btn {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Subtotal */
        .subtotal {
            background: #f8f9fa;
            font-weight: 700;
            color: #198754;
        }

        /* Info konversi — SELALU di luar detail-row */
        .info-konversi {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-top: 8px;
            min-height: 16px;
            line-height: 1.4;
        }

        @media (max-width: 992px) {
            .detail-row {
                flex-wrap: wrap;
            }

            .barang-col,
            .qty-col,
            .satuan-col,
            .harga-col,
            .subtotal-col {
                flex: 0 0 100%;
            }

            .action-col {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Tambah Transaksi Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gudang.transaksi.index') }}">Transaksi Barang Gudang</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Transaksi Gudang</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                {{-- ALERT SUCCESS --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ALERT ERROR --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('gudang.transaksi.store') }}" method="POST">
                    @csrf

                    {{-- JENIS TRANSAKSI --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" required>
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="masuk">Barang Masuk</option>
                            <option value="keluar">Barang Keluar</option>
                            <option value="penyesuaian">Penyesuaian</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="tipe-group" style="display:none;">
                        <label class="form-label">Tipe Penyesuaian</label>
                        <select name="tipe_penyesuaian" id="tipe_penyesuaian" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="tambah">Tambah Stok</option>
                            <option value="kurang">Kurangi Stok</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="alasan-group" style="display:none;">
                        <label class="form-label">Alasan</label>
                        <input type="text" name="alasan" id="alasan" class="form-control"
                            placeholder="Contoh: Pemakaian proyek / Rusak / Opname">
                    </div>

                    <hr>

                    {{-- DETAIL BARANG --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Detail Barang</label>

                        <div id="barang-wrapper">
                            <div class="barang-row mb-3">

                                {{-- BARIS FIELD --}}
                                <div class="detail-row">

                                    {{-- BARANG --}}
                                    <div class="barang-col">
                                        <label class="form-label">Barang</label>
                                        <select name="items[0][id_barang]" class="form-select barang-select" required>
                                            <option value="" disabled selected>-- Pilih Barang --</option>
                                            @foreach ($barang as $b)
                                                <option value="{{ $b->id_barang }}" data-stok="{{ $b->stok_akhir }}"
                                                    data-satuan="{{ $b->satuan }}"
                                                    data-satuan-dasar="{{ $b->satuan_dasar }}"
                                                    data-konversi="{{ $b->konversi_satuan }}">
                                                    {{ $b->nama_barang }} (stok: {{ $b->stok_akhir }}
                                                    {{ $b->satuan_dasar }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- QTY --}}
                                    <div class="qty-col">
                                        <label class="form-label">Qty</label>
                                        <input type="number" name="items[0][jumlah]" class="form-control jumlah"
                                            min="1" disabled required>
                                    </div>

                                    {{-- SATUAN --}}
                                    <div class="satuan-col">
                                        <label class="form-label">Satuan</label>
                                        <select name="items[0][satuan_pilih]" class="form-select satuan-select" disabled>
                                            <option value="">-</option>
                                        </select>
                                    </div>

                                    {{-- HARGA (tampil saat masuk) --}}
                                    <div class="harga-col d-none">
                                        <label class="form-label">Harga</label>
                                        <input type="number" name="items[0][harga_satuan]" class="form-control harga"
                                            min="0" placeholder="0" disabled>
                                    </div>

                                    {{-- SUBTOTAL (tampil saat masuk) --}}
                                    <div class="subtotal-col d-none">
                                        <label class="form-label">Subtotal</label>
                                        <input type="text" class="form-control subtotal" readonly>
                                    </div>

                                    {{-- TOMBOL — label invisible supaya sejajar --}}
                                    {{-- TOMBOL — hapus label invisible, cukup flex-end --}}
                                    <div class="action-col">
                                        <button type="button" class="btn btn-success add-row" disabled>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger remove-row" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                </div>{{-- /detail-row --}}

                                {{-- INFO KONVERSI — di luar flex, pasti turun ke bawah --}}
                                <small class="info-konversi"></small>

                            </div>
                        </div>
                    </div>

                    {{-- TOTAL --}}
                    <div class="text-end mb-3">
                        <h5>Total Biaya:
                            <span id="total-biaya">Rp 0</span>
                        </h5>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('gudang.transaksi.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script>
        function updateBarangOptions() {
            const jenis = getJenis();

            const selectedValues = Array.from(
                    document.querySelectorAll('.barang-select:not([disabled])')
                )
                .map(sel => sel.value)
                .filter(val => val !== '');

            document.querySelectorAll('.barang-select').forEach(select => {
                Array.from(select.options).forEach(option => {
                    if (!option.value) return;

                    const stok = parseInt(option.dataset.stok);

                    // RULE 1: stok 0 hanya boleh untuk BARANG MASUK
                    if (stok === 0 && jenis !== 'masuk') {
                        option.disabled = true;
                        return;
                    }

                    // RULE 2: tidak boleh pilih barang yang sama di row lain
                    if (
                        selectedValues.includes(option.value) &&
                        option.value !== select.value
                    ) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        function formatRupiah(val) {
            return 'Rp ' + val.toLocaleString('id-ID');
        }

        function getJenis() {
            return document.getElementById('jenis_transaksi').value;
        }

        /* =====================
           JENIS TRANSAKSI DIPILIH
        ===================== */
        document.getElementById('jenis_transaksi').addEventListener('change', function() {
            toggleAlasan();
            document.querySelectorAll('.barang-row').forEach(row => {
                const barang = row.querySelector('.barang-select');
                const jumlah = row.querySelector('.jumlah');

                resetRow(row);
                jumlah.disabled = true;
                barang.disabled = false; // disable hanya di sini
                toggleHarga(row);
            });
            hitungTotal();
        });


        /* =====================
           BARANG DIPILIH
        ===================== */
        document.addEventListener('change', function(e) {
            const row = e.target.closest('.barang-row');
            if (!row) return;

            if (e.target.classList.contains('barang-select')) {

                const option = e.target.options[e.target.selectedIndex];

                const satuan = option.dataset.satuan;
                const satuanDasar = option.dataset.satuanDasar;
                const konversi = parseInt(option.dataset.konversi) || 1;
                const stok = parseInt(option.dataset.stok) || 0;

                const satuanSelect = row.querySelector('.satuan-select');

                // reset
                satuanSelect.innerHTML = '';

                // option satuan utama
                satuanSelect.innerHTML += `
                    <option value="${satuan}">
                        ${satuan}
                    </option>
                `;

                // option satuan dasar
                if (satuan !== satuanDasar) {
                    satuanSelect.innerHTML += `
                        <option value="${satuanDasar}">
                            ${satuanDasar}
                        </option>
                    `;
                }

                satuanSelect.disabled = false;

                row.querySelector('.jumlah').disabled = false;
                const jumlah = row.querySelector('.jumlah');
                // default pertama pakai satuan utama
                jumlah.max = konversi > 1 ?
                    Math.floor(stok / konversi) :
                    stok;

                row.querySelector('.info-konversi').innerText =
                    `1 ${satuan} = ${konversi} ${satuanDasar} | Stok tersedia: ${stok} ${satuanDasar}`;
            }

            toggleHarga(row);
            hitungTotal();
        });
        document.addEventListener('change', function(e) {

            if (!e.target.classList.contains('satuan-select')) return;

            const row = e.target.closest('.barang-row');

            const barang = row.querySelector('.barang-select');
            const option = barang.options[barang.selectedIndex];

            const satuan = option.dataset.satuan;
            const satuanDasar = option.dataset.satuanDasar;
            const konversi = parseInt(option.dataset.konversi) || 1;
            const stok = parseInt(option.dataset.stok) || 0;

            const jumlah = row.querySelector('.jumlah');

            // kalau pakai satuan dasar
            if (e.target.value === satuanDasar) {
                jumlah.max = stok;
            }

            // kalau pakai satuan besar
            else {
                jumlah.max = Math.floor(stok / konversi);
            }
        });

        /* =====================
           TOGGLE HARGA
        ===================== */
        function toggleHarga(row) {

            const hargaCol = row.querySelector('.harga-col');
            const subtotalCol = row.querySelector('.subtotal-col');
            const harga = row.querySelector('.harga');

            if (getJenis() === 'masuk') {

                // tampilkan kolom
                hargaCol.classList.remove('d-none');
                subtotalCol.classList.remove('d-none');

                harga.disabled = false;
                harga.required = true;

            } else {

                // sembunyikan kolom
                hargaCol.classList.add('d-none');
                subtotalCol.classList.add('d-none');

                harga.disabled = true;
                harga.required = false;

                harga.value = '';
                row.querySelector('.subtotal').value = '';
            }
        }

        /* =====================
           HITUNG SUBTOTAL
        ===================== */
        function hitungSubtotal(row) {
            const jumlah = parseInt(row.querySelector('.jumlah').value) || 0;
            const harga = parseInt(row.querySelector('.harga').value) || 0;
            const subtotal = jumlah * harga;

            row.querySelector('.subtotal').value =
                getJenis() === 'masuk' ? formatRupiah(subtotal) : '';

            return subtotal;
        }

        /* =====================
           HITUNG TOTAL
        ===================== */
        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.barang-row').forEach(row => {
                total += hitungSubtotal(row);
            });
            document.getElementById('total-biaya').innerText = formatRupiah(total);
        }

        /* =====================
           INPUT JUMLAH / HARGA
        ===================== */
        document.addEventListener('input', function(e) {
            const row = e.target.closest('.barang-row');
            if (!row) return;

            if (e.target.classList.contains('jumlah') && getJenis() === 'keluar') {
                const max = parseInt(e.target.max);
                if (parseInt(e.target.value) > max) {
                    e.target.value = max;
                }
            }

            hitungTotal();
        });


        /* =====================
           RESET BARIS
        ===================== */
        function resetRow(row) {

            row.querySelector('.info-konversi').innerText = '';

            row.querySelector('.barang-select').value = '';

            row.querySelector('.jumlah').value = '';
            row.querySelector('.jumlah').disabled = true;

            row.querySelector('.harga').value = '';

            row.querySelector('.subtotal').value = '';

            const satuanSelect = row.querySelector('.satuan-select');

            satuanSelect.innerHTML = '<option value="">-</option>';
            satuanSelect.disabled = true;
        }


        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;

            document.querySelectorAll('.barang-row').forEach(row => {
                if (!rowLengkap(row)) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Lengkapi semua data barang dengan benar');
            }
        });

        function rowLengkap(row) {
            const barang = row.querySelector('.barang-select').value;
            const jumlah = row.querySelector('.jumlah').value;
            const harga = row.querySelector('.harga').value;

            if (!barang || !jumlah) return false;

            if (getJenis() === 'masuk' && (!harga || harga <= 0)) {
                return false;
            }

            return true;
        }

        function updateRowButton(row) {
            const addBtn = row.querySelector('.add-row');
            const removeBtn = row.querySelector('.remove-row');

            if (rowLengkap(row)) {
                addBtn.disabled = false;
                removeBtn.disabled = false;
            } else {
                addBtn.disabled = true;
                removeBtn.disabled = true;
            }
        }

        document.addEventListener('input', function(e) {
            const row = e.target.closest('.barang-row');
            if (!row) return;

            updateRowButton(row);
            hitungTotal();
        });

        document.addEventListener('change', function(e) {
            const row = e.target.closest('.barang-row');
            if (!row) return;

            updateRowButton(row);
            hitungTotal();
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.add-row')) return;

            const wrapper = document.getElementById('barang-wrapper');
            const rows = wrapper.querySelectorAll('.barang-row');
            const index = rows.length;

            const clone = rows[rows.length - 1].cloneNode(true);

            clone.querySelectorAll('select, input').forEach(el => {

                el.value = '';

                if (el.classList.contains('barang-select')) {
                    el.disabled = false;
                } else {
                    el.disabled = true;
                }

                if (el.name) {
                    el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
                }
            });

            clone.querySelector('.satuan-select').innerHTML =
                '<option value="">-</option>';

            clone.querySelector('.barang-select').disabled = false;

            // reset tombol
            clone.querySelector('.add-row').disabled = true;
            clone.querySelector('.remove-row').disabled = true;

            wrapper.appendChild(clone);
            updateBarangOptions();

            // aktifkan row sebelumnya tapi kunci add-nya
            rows[rows.length - 1].querySelector('.add-row').disabled = true;
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.remove-row')) return;

            const wrapper = document.getElementById('barang-wrapper');
            const rows = wrapper.querySelectorAll('.barang-row');

            if (rows.length === 1) {
                alert('Minimal harus ada satu barang');
                return;
            }

            const row = e.target.closest('.barang-row');
            row.remove();

            updateBarangOptions();
            hitungTotal();
        });

        function toggleAlasan() {
            const jenis = getJenis();
            const alasanGroup = document.getElementById('alasan-group');
            const alasanInput = document.getElementById('alasan');

            const tipeGroup = document.getElementById('tipe-group');
            const tipeSelect = document.getElementById('tipe_penyesuaian');

            if (jenis === 'keluar' || jenis === 'penyesuaian') {
                alasanGroup.style.display = 'block';
                alasanInput.required = true;
            } else {
                alasanGroup.style.display = 'none';
                alasanInput.required = false;
                alasanInput.value = '';
            }

            if (jenis === 'penyesuaian') {
                tipeGroup.style.display = 'block';
                tipeSelect.required = true;
            } else {
                tipeGroup.style.display = 'none';
                tipeSelect.required = false;
                tipeSelect.value = '';
            }
        }
    </script>

@endsection
