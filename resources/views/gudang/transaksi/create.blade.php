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
            display: block;
            /* override global styles.css yang set display:flex — di sini barang-row adalah wrapper, bukan flex row */
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
            gap: 16px;
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
            min-width: 0;
            /* override global styles.css: .barang-row .form-select { min-width: 300px }
               yang dibuat untuk form lama — di sini kolom sudah ada flex-sizing sendiri */
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
            width: 100%;
            overflow-wrap: break-word;
            font-size: 12px;
            color: #6c757d;
            margin-top: 8px;
            min-height: 16px;
            line-height: 1.4;
        }

        @media (max-width: 1200px) {
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

                <form action="{{ route('gudang.transaksi.store') }}" method="POST" enctype="multipart/form-data">
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

                    <div class="form-group mb-3" id="rkat-group" style="display:none;">
                        <label class="form-label">Pos Anggaran RKAT <span class="text-danger">*</span></label>
                        <select name="rkat_anggaran_id" id="rkat_anggaran_id" class="form-select">
                            <option value="">-- Pilih Pos Anggaran --</option>
                            @foreach ($anggaranList as $a)
                                <option value="{{ $a->id }}">
                                    {{ $a->kode_kegiatan }} — {{ $a->coa_pos }} ({{ $a->nama_kegiatan }})
                                </option>
                            @endforeach
                        </select>
                        @if ($anggaranList->isEmpty())
                            <small class="text-muted">Belum ada pos anggaran tahun {{ now()->year }}.</small>
                        @endif
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

                    {{-- BIAYA TAMBAHAN (hanya masuk) --}}
                    <div id="charge-section" style="display:none;">
                        <hr>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="form-label fw-semibold mb-0">Biaya Tambahan <small class="text-muted fw-normal">(opsional)</small></label>
                            <button type="button" class="btn btn-primary px-3" id="add-charge" style="font-size:13px;font-weight:600;letter-spacing:.3px;">
                                <i class="fas fa-plus me-1"></i> Tambah Biaya
                            </button>
                        </div>
                        <div id="charge-wrapper"></div>

                        {{-- Upload Struk --}}
                        <div class="form-group mt-3">
                            <label class="form-label">Upload Struk / Invoice <small class="text-muted">(opsional, jpg/png/pdf — gambar dikompres otomatis ke ≤2MB)</small></label>
                            <input type="file" name="struk" id="struk-input" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <small id="struk-info" class="text-muted" style="display:none"></small>
                        </div>

                        {{-- Breakdown Total --}}
                        <div class="mt-3 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;" id="total-breakdown">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total Barang</span>
                                <span id="total-barang-val">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total Biaya Tambahan</span>
                                <span id="total-charge-val">Rp 0</span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Grand Total</span>
                                <span id="grand-total-val">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    {{-- TOTAL (tampil saat bukan masuk) --}}
                    <div class="text-end mb-3" id="total-simple">
                        <h5>Total Biaya: <span id="total-biaya">Rp 0</span></h5>
                    </div>

                    <div class="text-end mt-3">
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
           TIPE PENYESUAIAN DIGANTI
           (tambah ↔ kurang) — perlu update max tanpa resetRow
        ===================== */
        document.getElementById('tipe_penyesuaian').addEventListener('change', function() {
            document.querySelectorAll('.barang-row').forEach(row => {
                const barangSel = row.querySelector('.barang-select');
                if (!barangSel.value) return;

                const opt       = barangSel.options[barangSel.selectedIndex];
                const konversi  = parseInt(opt.dataset.konversi) || 1;
                const stok      = parseInt(opt.dataset.stok) || 0;
                const satuanDasar  = opt.dataset.satuanDasar;
                const satuanPilih  = row.querySelector('.satuan-select').value;
                const jumlah    = row.querySelector('.jumlah');

                if (this.value === 'kurang') {
                    jumlah.max = satuanPilih === satuanDasar
                        ? stok
                        : Math.floor(stok / konversi);
                } else {
                    jumlah.removeAttribute('max');
                }
            });
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

                // Cap max hanya untuk transaksi yang mengurangi stok
                const jenis    = getJenis();
                const tipe     = document.getElementById('tipe_penyesuaian').value;
                const isKeluar = jenis === 'keluar' ||
                    (jenis === 'penyesuaian' && tipe === 'kurang');

                if (isKeluar) {
                    jumlah.max = konversi > 1 ? Math.floor(stok / konversi) : stok;
                } else {
                    jumlah.removeAttribute('max');
                }

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

            // Cap max hanya untuk transaksi yang mengurangi stok
            const jenis    = getJenis();
            const tipe     = document.getElementById('tipe_penyesuaian').value;
            const isKeluar = jenis === 'keluar' ||
                (jenis === 'penyesuaian' && tipe === 'kurang');

            if (isKeluar) {
                if (e.target.value === satuanDasar) {
                    jumlah.max = stok;
                } else {
                    jumlah.max = Math.floor(stok / konversi);
                }
            } else {
                jumlah.removeAttribute('max');
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

            // Update breakdown jika masuk
            if (getJenis() === 'masuk') {
                document.getElementById('total-barang-val').innerText = formatRupiah(total);
                hitungGrandTotal();
            }
        }

        function hitungCharge() {
            let total = 0;
            document.querySelectorAll('.charge-jumlah').forEach(el => {
                total += parseFloat(el.value.replace(/\./g, '').replace(',', '.')) || 0;
            });
            document.getElementById('total-charge-val').innerText = formatRupiah(total);
            hitungGrandTotal();
        }

        function hitungGrandTotal() {
            let barang = 0;
            document.querySelectorAll('.barang-row').forEach(row => {
                barang += hitungSubtotal(row);
            });
            let charge = 0;
            document.querySelectorAll('.charge-jumlah').forEach(el => {
                charge += parseFloat(el.value.replace(/\./g, '').replace(',', '.')) || 0;
            });
            document.getElementById('grand-total-val').innerText = formatRupiah(barang + charge);
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

            const rkatGroup  = document.getElementById('rkat-group');
            const rkatSelect = document.getElementById('rkat_anggaran_id');
            if (jenis === 'masuk') {
                rkatGroup.style.display = 'block';
                rkatSelect.required = true;
            } else {
                rkatGroup.style.display = 'none';
                rkatSelect.required = false;
                rkatSelect.value = '';
            }

            // Toggle charge section vs simple total
            const chargeSection = document.getElementById('charge-section');
            const totalSimple   = document.getElementById('total-simple');
            if (jenis === 'masuk') {
                chargeSection.style.display = 'block';
                totalSimple.style.display   = 'none';
            } else {
                chargeSection.style.display = 'none';
                totalSimple.style.display   = 'block';
            }
        }

        // ── Charge rows ────────────────────────────────────────────────────────
        let chargeIdx = 0;

        function addChargeRow() {
            const wrapper = document.getElementById('charge-wrapper');
            const idx     = chargeIdx++;
            const div     = document.createElement('div');
            div.className = 'charge-row d-flex gap-2 mb-2 align-items-center';
            div.innerHTML = `
                <input type="text" name="charges[${idx}][nama]" class="form-control"
                       placeholder="Nama biaya (mis. Ongkir, Pajak)" style="flex:2">
                <input type="text" name="charges[${idx}][jumlah]" class="form-control charge-jumlah"
                       placeholder="0" inputmode="numeric" style="flex:1"
                       oninput="formatChargeRupiah(this); hitungCharge()">
                <button type="button" class="btn btn-sm btn-danger remove-charge">
                    <i class="fas fa-trash"></i>
                </button>`;
            wrapper.appendChild(div);
        }

        function formatChargeRupiah(input) {
            const raw = input.value.replace(/\D/g, '');
            input.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
            hitungCharge();
        }

        document.getElementById('add-charge').addEventListener('click', addChargeRow);

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-charge')) {
                e.target.closest('.charge-row').remove();
                hitungCharge();
            }
        });

        // ── Auto-compress struk image ke ≤ 2MB ────────────────────────────────
        const MAX_BYTES = 2 * 1024 * 1024; // 2 MB

        document.getElementById('struk-input').addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;

            const info = document.getElementById('struk-info');

            // PDF: hanya cek ukuran, tidak dikompres
            if (file.type === 'application/pdf') {
                if (file.size > MAX_BYTES) {
                    info.textContent = '⚠ PDF melebihi 2MB. Mohon kompres manual.';
                    info.style.color = '#dc2626';
                    info.style.display = 'block';
                } else {
                    info.style.display = 'none';
                }
                return;
            }

            // Gambar: kompres dengan Canvas
            if (!file.type.startsWith('image/')) return;

            info.textContent = 'Mengompres gambar…';
            info.style.color = '#6b7280';
            info.style.display = 'block';

            const compressed = await compressImage(file, MAX_BYTES);
            const sizeMB = (compressed.size / 1024 / 1024).toFixed(2);

            // Ganti file di input
            const dt = new DataTransfer();
            dt.items.add(compressed);
            this.files = dt.files;

            if (compressed.size < file.size) {
                info.textContent = `✓ Dikompres: ${(file.size/1024/1024).toFixed(2)}MB → ${sizeMB}MB`;
                info.style.color = '#16a34a';
            } else {
                info.textContent = `✓ Ukuran sudah kecil (${sizeMB}MB), tidak perlu dikompres.`;
                info.style.color = '#6b7280';
            }
        });

        function compressImage(file, maxBytes) {
            return new Promise(resolve => {
                const img = new Image();
                const url = URL.createObjectURL(file);
                img.onload = () => {
                    URL.revokeObjectURL(url);
                    const canvas = document.createElement('canvas');
                    let { width, height } = img;

                    // Kurangi dimensi jika masih terlalu besar setelah quality turun
                    const MAX_DIM = 2048;
                    if (width > MAX_DIM || height > MAX_DIM) {
                        const ratio = Math.min(MAX_DIM / width, MAX_DIM / height);
                        width  = Math.round(width  * ratio);
                        height = Math.round(height * ratio);
                    }

                    canvas.width  = width;
                    canvas.height = height;
                    canvas.getContext('2d').drawImage(img, 0, 0, width, height);

                    // Binary search quality terbaik yang masih ≤ maxBytes
                    let lo = 0.1, hi = 0.95, bestBlob = null;
                    const step = async () => {
                        if (hi - lo < 0.02 || bestBlob) {
                            // Satu kali lagi dengan hi untuk hasil terbaik
                            canvas.toBlob(blob => {
                                resolve(bestBlob && bestBlob.size <= maxBytes ? bestBlob : (blob || file));
                            }, 'image/jpeg', bestBlob ? undefined : hi);
                            return;
                        }
                        const mid = (lo + hi) / 2;
                        canvas.toBlob(blob => {
                            if (!blob) { resolve(file); return; }
                            if (blob.size <= maxBytes) {
                                bestBlob = blob;
                                lo = mid;
                            } else {
                                hi = mid;
                            }
                            step();
                        }, 'image/jpeg', mid);
                    };
                    step();
                };
                img.onerror = () => resolve(file);
                img.src = url;
            });
        }
    </script>

@endsection
