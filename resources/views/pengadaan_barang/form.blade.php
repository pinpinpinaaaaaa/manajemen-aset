<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengadaan Barang & Jasa | SIMASTER</title>
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

        .item-catatan {
            margin-top: 16px;
            margin-bottom: 16px;
        }

        .item-catatan textarea {
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

        <h2>Form Pengadaan Barang & Jasa</h2>
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

        <form action="{{ route('form-pengadaan-barang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div>
                    <label>Nama Pengaju</label>
                    <input type="text" name="nama_pengaju" required>
                </div>
                <div>
                    <label>Divisi</label>
                    <select name="id_divisi" required>
                        <option value="">-- Pilih Divisi --</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id_divisi }}">{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label>Email</label>
                    <input type="email" name="email_pengaju" required>
                </div>
                <div>
                    <label>Tanggal Kebutuhan</label>
                    <input type="text" id="tanggal_kebutuhan" name="tanggal_kebutuhan" required>
                </div>
            </div>

            {{-- ================= ITEM ================= --}}
            <label style="margin-top:14px;">Item Pengadaan</label>

            <div id="itemContainer">

                <div class="item-card">

                    <div class="row-top">
                        <select name="items[0][jenis]" onchange="toggleJenis(this)" required>
                            <option value="" disabled selected>Pilih Jenis</option>
                            <option value="barang">Barang</option>
                            <option value="jasa">Jasa</option>
                        </select>

                        <input type="text" class="input-nama" name="items[0][nama]" placeholder="Nama Barang"
                            required>
                    </div>

                    <div class="row-bottom">
                        <input type="number" name="items[0][jumlah]" placeholder="Qty / Pax" min="1">

                        <input type="text" name="items[0][harga_satuan]" placeholder="Harga / Pax"
                            oninput="hitungSubtotal(this)">

                        <input type="text" name="items[0][subtotal]" placeholder="Subtotal" readonly>

                        <button type="button" class="btn-trash" onclick="removeItem(this)">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                    <div class="extra-barang"></div>

                    <div class="item-catatan">
                        <textarea name="items[0][catatan]" placeholder="Catatan item"></textarea>
                    </div>

                    <div class="item-upload">
                        <label>File Pendukung (Opsional)</label>
                        <input type="file" name="items[0][files][]" multiple>
                    </div>

                </div>

            </div>

            <div class="btn-add" onclick="addItem()">+ Tambah Item</div>

            {{-- ================= ALASAN ================= --}}
            <label>Alasan</label>
            <textarea name="alasan" required></textarea>

            <button type="submit">Kirim Pengadaan</button>
        </form>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr("#tanggal_kebutuhan", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        function addItem() {
            const container = document.getElementById("itemContainer");
            const index = document.querySelectorAll(".item-card").length;

            const el = document.createElement("div");
            el.className = "item-card";

            el.innerHTML = `
                <div class="row-top">
                    <select name="items[${index}][jenis]" onchange="toggleJenis(this)" required>
                        <option value="" disabled selected>Jenis</option>
                        <option value="barang">Barang</option>
                        <option value="jasa">Jasa</option>
                    </select>

                    <input type="text" class="input-nama" name="items[${index}][nama]" placeholder="Nama Barang & Jasa" required>
                </div>

                <div class="row-bottom">
                    <input type="number" name="items[${index}][jumlah]" placeholder="Qty / Pax" min="1">

                    <input type="text" name="items[${index}][harga_satuan]" placeholder="Harga / Pax"
                        oninput="hitungSubtotal(this)">

                    <input type="text" name="items[${index}][subtotal]" placeholder="Subtotal" readonly>

                    <button type="button" class="btn-trash" onclick="removeItem(this)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>

                <div class="extra-barang"></div>

                <div class="item-catatan">
                    <textarea name="items[${index}][catatan]" placeholder="Catatan item"></textarea>
                </div>

                <div class="item-upload">
                    <label>File Pendukung (Opsional)</label>
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

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function hitungSubtotal(el) {
            const card = el.closest('.item-card');

            const hargaInput = card.querySelector('[name*="harga_satuan"]');
            const jumlahInput = card.querySelector('[name*="jumlah"]');
            const subtotalInput = card.querySelector('[name*="subtotal"]');

            let rawHarga = hargaInput.value.replace(/\D/g, '');
            let jumlah = parseInt(jumlahInput.value) || 0;

            let harga = parseInt(rawHarga) || 0;
            let subtotal = harga * jumlah;

            // format tanpa ganggu user ngetik
            hargaInput.value = rawHarga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(rawHarga) : '';
            subtotalInput.value = subtotal ? 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal) : '';
        }

        function toggleJenis(select) {
            const card = select.closest('.item-card');
            const jumlah = card.querySelector('[name*="jumlah"]');
            const extra = card.querySelector('.extra-barang');
            let inputNama = card.querySelector('.input-nama');

            if (select.value === 'jasa') {
                jumlah.readOnly = false;
                jumlah.placeholder = "Jumlah (orang / hari / unit)";

                // dropdown + custom input
                inputNama.outerHTML = `
                    <input list="jasaList" class="input-nama" name="${inputNama.name.replace('[nama]', '[kategori_jasa]')}" placeholder="Pilih / ketik jasa" required>

                    <datalist id="jasaList">
                        <option value="akomodasi hotel">
                        <option value="akomodasi transportasi">
                        <option value="surveyor">
                        <option value="web_specialist">
                    </datalist>
                `;

                // hapus field barang
                extra.innerHTML = '';

            } else {
                jumlah.readOnly = false;

                inputNama.outerHTML = `
                    <input type="text" class="input-nama" name="${inputNama.name.replace('[kategori_jasa]', '[nama]')}" placeholder="Nama Barang" required>
                `;

                // tambah field barang
                extra.innerHTML = `
                    <div class="row-barang">
                        <input type="text" name="${select.name.replace('[jenis]', '[merk]')}" placeholder="Merk">
                        <input type="text" name="${select.name.replace('[jenis]', '[tipe_model]')}" placeholder="Tipe / Model">
                    </div>
                    <textarea name="${select.name.replace('[jenis]', '[spesifikasi]')}" placeholder="Spesifikasi"></textarea>
                `;
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
    </script>
</body>

</html>
