<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekspedisi | SIMASTER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjB0ZWNofGVufDF8fHx8MTc1ODIwNDQxMHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;

            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
            padding: 1rem;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
        }

        /* Tombol Back */
        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 20;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 14px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(235, 202, 86, 0.4);
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            transition: .25s;
        }

        .btn-back:hover {
            background: #fff;
            transform: translateX(-2px);
        }

        /* Card */
        .card-form {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 720px;
            background: rgba(255, 255, 255, 0.9);
            padding: 3rem 3rem 2.5rem;
            border-radius: 22px;
            backdrop-filter: blur(14px);
            border: 1px solid rgba(235, 202, 86, 0.4);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            animation: fadeIn .55s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
            }
        }

        /* Logo */
        .logo-img {
            max-width: 256px;
            margin-bottom: 1rem;

        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.3rem;
        }


        h2 {
            text-align: center;
            margin: 0 0 1.8rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2226;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.6rem;
            margin-bottom: .8rem;
        }

        @media(max-width:650px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        label {
            font-weight: 600;
            font-size: .95rem;
            margin-bottom: 6px;
            display: block;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid #ddd;
            background: #fafafa;
            font-size: 1rem;
            transition: .2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #ebca56;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(235, 202, 86, .35);
            outline: none;
        }

        /* Grid Item Barang */
        .barang-row {
            display: grid;
            grid-template-columns: 120px 1fr 100px 140px 140px 50px;
            gap: 10px;
            margin-bottom: 12px;
            align-items: center;
        }

        @media(max-width:768px) {
            .barang-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .btn-trash {
            border: none;
            background: none;
            font-size: 17px;
            cursor: pointer;
            color: #e00000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-add {
            display: inline-block;
            margin: 4px 0 20px;
            padding: 11px 18px;
            background: #ebca56;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            transition: .25s;
        }

        .btn-add:hover {
            opacity: .85;
            transform: translateY(-1px);
        }

        button[type=submit] {
            width: 100%;
            padding: 15px;
            margin-top: 6px;
            font-size: 1.15rem;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            background: linear-gradient(90deg, #f3d46d, #ebca56);
            cursor: pointer;
            transition: .26s;
        }

        button[type=submit]:hover {
            opacity: .95;
            transform: translateY(-1px);
        }

        /* Styling TomSelect biar sama dengan input lain */
        .ts-wrapper {
            border-radius: 14px !important;
            border: 1px solid #ddd !important;
            background: #fafafa !important;
            padding: 0 !important;
            height: 50px;
            display: flex;
            align-items: center;
        }

        /* Style bagian dalam TomSelect */
        .ts-wrapper .ts-control {
            border: none !important;
            background: transparent !important;
            padding: 4px 12px !important;
            display: flex;
            align-items: center;
        }

        /* Focus state */
        .ts-wrapper.focus {
            border-color: #ebca56 !important;
            box-shadow: 0 0 0 3px rgba(235, 202, 86, .35) !important;
            background: #fff !important;
        }

        /* Font */
        .ts-wrapper,
        .ts-dropdown,
        .ts-wrapper input {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-size: 1rem !important;
        }

        /* Dropdown padding */
        .ts-dropdown .option {
            padding: 10px 14px;
        }

        .item-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .row-top {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .row-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 50px;
            gap: 14px;
            align-items: end;
            margin-bottom: 14px;
        }

        /* tombol delete biar center */
        .row-bottom .btn-trash {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* mobile */
        @media(max-width:768px) {
            .row-top {
                grid-template-columns: 1fr;
            }

            .row-bottom {
                grid-template-columns: 1fr 1fr;
            }
        }

        .extra-barang {
            margin-top: 14px;
            display: grid;
            gap: 14px;
        }

        .extra-barang .row-barang {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .item-keterangan {
            margin-top: 16px;
            margin-bottom: 16px;
        }

        .item-keterangan textarea {
            min-height: 90px;
        }

        /* wrapper upload file */
        .item-upload {
            margin-top: 10px;
        }

        .item-upload label {
            margin-bottom: 8px;
            font-size: .9rem;
            color: #444;
        }

        .item-upload input[type="file"] {
            padding: 12px;
            background: #fafafa;
            border: 1px dashed #cbd5e1;
        }

        /* mobile */
        @media(max-width:768px) {

            .row-top {
                grid-template-columns: 1fr;
            }

            .row-bottom {
                grid-template-columns: 1fr 1fr;
            }

            .extra-barang .row-barang {
                grid-template-columns: 1fr;
            }
        }

        /* ================= SECTION ================= */
        .form-section {
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid #ececec;
        }

        .form-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e2226;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #ebca56;
        }

        /* ================= INPUT SPACING ================= */
        .form-group {
            margin-bottom: 18px;
        }

        /* ================= CHECKBOX ================= */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0 24px;
            background: #fff8dc;
            border: 1px solid rgba(235, 202, 86, .4);
            padding: 14px 16px;
            border-radius: 14px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #ebca56;
            cursor: pointer;
        }

        .checkbox-wrapper label {
            margin: 0;
            cursor: pointer;
            font-weight: 600;
            color: #444;
        }

        /* ================= DISABLED INPUT ================= */
        input[readonly],
        select:disabled {
            background: #f1f5f9 !important;
            color: #64748b;
            cursor: not-allowed;
        }

        /* ================= TEXTAREA ================= */
        textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* ================= ITEM TITLE ================= */
        .label-section {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 14px;
            color: #1e2226;
            display: block;
        }

        /* ================= MOBILE ================= */
        @media(max-width:768px) {
            .card-form {
                padding: 2rem 1.2rem;
            }

            .checkbox-wrapper {
                align-items: flex-start;
            }
        }

        /* ================= ERROR DISPLAY ================= */
        .error-banner {
            background: #fff0f0;
            border: 1px solid #f87171;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 24px;
            color: #991b1b;
        }
        .error-banner strong {
            display: block;
            margin-bottom: 8px;
            font-size: .95rem;
        }
        .error-banner ul {
            margin: 0;
            padding-left: 18px;
        }
        .error-banner li {
            margin-bottom: 3px;
            font-size: .88rem;
        }
        .field-error {
            color: #dc2626;
            font-size: .8rem;
            display: block;
            margin-top: 4px;
        }
        input.is-invalid,
        select.is-invalid,
        textarea.is-invalid {
            border-color: #f87171 !important;
            background: #fff5f5 !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="overlay"></div>

    <div class="btn-back" onclick="window.location.href='{{ route('menu.form') }}'">
        <i class="fa-solid fa-arrow-left"></i>
    </div>

    <div class="card-form">

        <div class="logo-container">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-img">
        </div>

        <h2>Form Ekspedisi</h2>
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif

        @if ($errors->any())
            <div class="error-banner">
                <strong>Ada yang perlu diperbaiki:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('form-ekspedisi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h4 style="margin-bottom:10px;">Data Pengaju</h4>
            <div class="form-grid">
                <div>
                    <label>Nama Pengaju</label>
                    <input type="text" id="nama_pengaju" name="nama_pengaju" required
                        value="{{ old('nama_pengaju') }}"
                        class="{{ $errors->has('nama_pengaju') ? 'is-invalid' : '' }}">
                    @error('nama_pengaju') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label>Divisi Pengaju</label>
                    <select id="divisi_pengaju" name="id_divisi_pengaju" required
                        class="{{ $errors->has('id_divisi_pengaju') ? 'is-invalid' : '' }}">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id_divisi }}" {{ old('id_divisi_pengaju') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                    @error('id_divisi_pengaju') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label>Email Pengaju</label>
                <input type="email" id="email_pengaju" name="email_pengaju" required
                    value="{{ old('email_pengaju') }}"
                    class="{{ $errors->has('email_pengaju') ? 'is-invalid' : '' }}">
                @error('email_pengaju') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="checkbox-wrapper">
                <input type="checkbox" id="sameAsPengaju">
                <label for="sameAsPengaju">
                    Pengirim sama dengan Pengaju
                </label>
            </div>

            <h4 style="margin-bottom:10px;">Data Pengirim</h4>
            <div class="form-grid">
                <div>
                    <label>Nama Pengirim</label>
                    <input type="text" id="nama_pengirim" name="nama_pengirim" required
                        value="{{ old('nama_pengirim') }}"
                        class="{{ $errors->has('nama_pengirim') ? 'is-invalid' : '' }}">
                    @error('nama_pengirim') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label>Divisi Pengirim</label>
                    <select id="divisi_pengirim" name="id_divisi_pengirim" required
                        class="{{ $errors->has('id_divisi_pengirim') ? 'is-invalid' : '' }}">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id_divisi }}" {{ old('id_divisi_pengirim') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                    @error('id_divisi_pengirim') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label>Email Pengirim</label>
                    <input type="email" id="email_pengirim" name="email_pengirim" required
                        value="{{ old('email_pengirim') }}"
                        class="{{ $errors->has('email_pengirim') ? 'is-invalid' : '' }}">
                    @error('email_pengirim') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label>No HP Pengirim</label>
                    <input type="text" id="no_hp_pengirim" name="no_hp_pengirim" placeholder="08xxxxxxxxxx"
                        inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        value="{{ old('no_hp_pengirim') }}">
                </div>
            </div>

            <h4 style="margin:20px 0 10px;">Informasi Kegiatan</h4>

            <div>
                <label>Judul Kegiatan</label>
                <input type="text" name="judul_kegiatan" required
                    value="{{ old('judul_kegiatan') }}"
                    class="{{ $errors->has('judul_kegiatan') ? 'is-invalid' : '' }}">
                @error('judul_kegiatan') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <h4 style="margin-bottom:10px;">Data Penerima</h4>
            <div class="form-grid">
                <div>
                    <label>Instansi Tujuan</label>
                    <input type="text" name="instansi_penerima" required
                        value="{{ old('instansi_penerima') }}"
                        class="{{ $errors->has('instansi_penerima') ? 'is-invalid' : '' }}">
                    @error('instansi_penerima') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label>Nama Penerima</label>
                    <input type="text" name="nama_penerima" required
                        value="{{ old('nama_penerima') }}"
                        class="{{ $errors->has('nama_penerima') ? 'is-invalid' : '' }}">
                    @error('nama_penerima') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label>Email Penerima</label>
                    <input type="email" name="email_penerima" placeholder="email@tujuan.com"
                        value="{{ old('email_penerima') }}"
                        class="{{ $errors->has('email_penerima') ? 'is-invalid' : '' }}">
                    @error('email_penerima') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label>No HP Penerima</label>
                    <input type="text" name="no_hp_penerima" placeholder="08xxxxxxxxxx" inputmode="numeric"
                        pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        value="{{ old('no_hp_penerima') }}">
                </div>
            </div>

            <div>
                <label>Alamat Tujuan</label>
                <textarea name="alamat_penerima" required
                    class="{{ $errors->has('alamat_penerima') ? 'is-invalid' : '' }}">{{ old('alamat_penerima') }}</textarea>
                @error('alamat_penerima') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <label class="label-section">Isi Kiriman</label>

            @php $oldItems = old('items', [null]); @endphp
            <div id="itemContainer">

                @foreach($oldItems as $i => $oldItem)
                <div class="item-card">

                    <div class="row-top">
                        <select name="items[{{ $i }}][jenis]" required
                            class="{{ $errors->has("items.$i.jenis") ? 'is-invalid' : '' }}">
                            <option value="" disabled {{ !($oldItem['jenis'] ?? '') ? 'selected' : '' }}>Pilih Jenis</option>
                            <option value="dokumen" {{ ($oldItem['jenis'] ?? '') === 'dokumen' ? 'selected' : '' }}>Dokumen</option>
                            <option value="barang" {{ ($oldItem['jenis'] ?? '') === 'barang' ? 'selected' : '' }}>Barang</option>
                        </select>

                        <input type="text" name="items[{{ $i }}][nama]" placeholder="Nama Dokumen / Barang" required
                            value="{{ $oldItem['nama'] ?? '' }}"
                            class="{{ $errors->has("items.$i.nama") ? 'is-invalid' : '' }}">
                    </div>

                    <div class="row-bottom">
                        <input type="number" name="items[{{ $i }}][jumlah]" placeholder="Jumlah" min="1"
                            value="{{ $oldItem['jumlah'] ?? '' }}">

                        <input type="text" name="items[{{ $i }}][keterangan]" placeholder="Keterangan"
                            value="{{ $oldItem['keterangan'] ?? '' }}">

                        <div></div>

                        <button type="button" class="btn-trash" onclick="removeItem(this)">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                    <div class="item-upload">
                        <label>File (Opsional)</label>
                        <input type="file" name="items[{{ $i }}][files][]" multiple>
                        @if($oldItem)
                            <small style="color:#64748b;font-size:.8rem;">File harus di-upload ulang jika form disubmit kembali.</small>
                        @endif
                    </div>

                </div>
                @endforeach

            </div>

            <div class="btn-add" onclick="addItem()">+ Tambah Item</div>

            <label>Catatan</label>
            <textarea name="keterangan">{{ old('keterangan') }}</textarea>

            <button type="submit">Kirim Ekspedisi</button>
        </form>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.querySelectorAll('input[name*="no_hp"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        function addItem() {
            const container = document.getElementById("itemContainer");
            const index = document.querySelectorAll(".item-card").length;

            const el = document.createElement("div");
            el.className = "item-card";

            el.innerHTML = `
                <div class="row-top">
                    <select name="items[${index}][jenis]" required>
                        <option value="" disabled selected>Jenis</option>
                        <option value="dokumen">Dokumen</option>
                        <option value="barang">Barang</option>
                    </select>

                    <input type="text" name="items[${index}][nama]" placeholder="Nama Dokumen / Barang" required>
                </div>

                <div class="row-bottom">
                    <input type="number" name="items[${index}][jumlah]" placeholder="Jumlah" min="1">

                    <input type="text" name="items[${index}][keterangan]" placeholder="Keterangan">

                    <div></div>

                    <button type="button" class="btn-trash" onclick="removeItem(this)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>

                <div class="item-upload">
                    <label>File (Opsional)</label>
                    <input type="file" name="items[${index}][files][]" multiple>
                </div>
            `;

            container.appendChild(el);
        }

        function removeItem(btn) {
            const items = document.querySelectorAll(".item-card");

            if (items.length > 1) {
                btn.closest(".item-card").remove();
            } else {
                alert("Minimal 1 item harus ada");
            }
        }

        document.addEventListener("input", function(e) {
            if (e.target.name && e.target.name.includes('harga_satuan')) {
                hitungSubtotal(e.target);
            }

            if (e.target.name && e.target.name.includes('jumlah')) {
                const card = e.target.closest('.item-card');
                const harga = card.querySelector('[name*="harga_satuan"]');
                if (harga) hitungSubtotal(harga);
            }
        });
        const checkbox = document.getElementById("sameAsPengaju");

        function syncPengirim() {
            if (!checkbox.checked) return;

            document.getElementById("nama_pengirim").value =
                document.getElementById("nama_pengaju").value;

            document.getElementById("divisi_pengirim").value =
                document.getElementById("divisi_pengaju").value;

            document.getElementById("email_pengirim").value =
                document.getElementById("email_pengaju").value;
        }

        checkbox.addEventListener("change", function() {

            const namaPengirim = document.getElementById("nama_pengirim");
            const divisiPengirim = document.getElementById("divisi_pengirim");
            const emailPengirim = document.getElementById("email_pengirim");

            if (this.checked) {

                syncPengirim();

                // readonly input
                namaPengirim.readOnly = true;
                emailPengirim.readOnly = true;

                // lock select TANPA disabled
                divisiPengirim.style.pointerEvents = 'none';
                divisiPengirim.style.backgroundColor = '#f1f5f9';

            } else {

                namaPengirim.readOnly = false;
                emailPengirim.readOnly = false;

                divisiPengirim.style.pointerEvents = 'auto';
                divisiPengirim.style.backgroundColor = '';
            }
        });

        // realtime sync
        document.getElementById("nama_pengaju")
            .addEventListener("input", syncPengirim);

        document.getElementById("divisi_pengaju")
            .addEventListener("change", syncPengirim);

        document.getElementById("email_pengaju")
            .addEventListener("input", syncPengirim);
    </script>
</body>

</html>
