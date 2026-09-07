<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Aset | SIMASTER</title>
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
            grid-template-columns: 1fr 120px 52px;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        @media(max-width:600px) {
            .barang-row {
                grid-template-columns: 1fr 1fr 52px;
            }
        }

        .aset-row {
            display: grid;
            grid-template-columns: 2fr 1fr 52px;
            grid-template-rows: auto auto;
            gap: 12px 1rem;
            margin-bottom: 20px;
            align-items: center;
        }

        /* Tombol trash di tengah antar 2 row */
        .aset-row .btn-trash {
            grid-row: 1 / span 2;
            grid-column: 3;
            height: 100%;
        }

        .aset-fields {
            flex: 1;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 12px;
        }

        .aset-fields select,
        .aset-fields input {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            width: 100%;
        }

        .btn-trash {
            height: 45px;
            width: 45px;
            border-radius: 10px;
            border: none;
            color: #e00000;
            cursor: pointer;
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

        /* ================= ERROR DISPLAY ================= */
        .field-error { color:#dc2626; font-size:.8rem; display:block; margin-top:4px; }
        input.is-invalid, select.is-invalid, textarea.is-invalid {
            border-color:#f87171 !important;
            background:#fff5f5 !important;
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

        <h2>Form Peminjaman Aset</h2>

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
            <div style="background:#fff0f0;border:1px solid #f87171;border-radius:10px;padding:14px 18px;margin-bottom:1.4rem;color:#842029;">
                <strong style="display:block;margin-bottom:8px;">&#9888; Ada yang perlu diperbaiki:</strong>
                <ul style="margin:0;padding-left:1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom:3px;font-size:.88rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('form_peminjaman_aset.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div>
                    <label>Nama Peminjam</label>
                    <input type="text" name="nama_pengaju" required
                           value="{{ old('nama_pengaju') }}"
                           class="{{ $errors->has('nama_pengaju') ? 'is-invalid' : '' }}">
                    @error('nama_pengaju')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label>Divisi</label>
                    <select name="id_divisi" required
                            class="{{ $errors->has('id_divisi') ? 'is-invalid' : '' }}">
                        <option value="" {{ !old('id_divisi') ? 'selected' : '' }}>-- Pilih Divisi --</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id_divisi }}" {{ old('id_divisi') == $d->id_divisi ? 'selected' : '' }}>
                                {{ $d->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_divisi')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <label>Email Peminjam</label>
            <input type="email" name="email_pengaju" required
                   value="{{ old('email_pengaju') }}"
                   class="{{ $errors->has('email_pengaju') ? 'is-invalid' : '' }}">
            @error('email_pengaju')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <label>Daftar Aset yang Dipinjam</label>

            <div id="asetContainer">

                <div class="aset-row">

                    <select class="asetSelect" name="items[0][id_jenis_barang]" required
                            data-old="{{ old('items.0.id_jenis_barang') }}">

                        <option value="">-- Pilih Aset --</option>

                        @foreach ($asetGrouped as $kategori => $items)
                            <optgroup label="{{ ucfirst($kategori) }}">
                                @foreach ($items as $item)
                                    <option value="{{ $item['id_jenis_barang'] }}" data-nama="{{ $item['nama_aset'] }}"
                                        data-stok="{{ $item['total_unit'] }}">

                                        {{ $item['nama_aset'] }}
                                        - {{ $item['jenis_barang'] }}
                                        ({{ $item['total_unit'] }} unit)
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    <input type="hidden" name="items[0][nama_aset]" class="nama_aset_hidden"
                           value="{{ old('items.0.nama_aset') }}">

                    <input type="number" name="items[0][jumlah]" min="1" placeholder="Jumlah" required
                           value="{{ old('items.0.jumlah') }}">

                    <input type="text" name="items[0][tanggal_pinjam]" class="tanggal_pinjam"
                        placeholder="Tanggal Pinjam" required
                        value="{{ old('items.0.tanggal_pinjam') }}">

                    <input type="text" name="items[0][tanggal_jatuh_tempo]" class="tanggal_kembali"
                        placeholder="Tanggal Kembali" required
                        value="{{ old('items.0.tanggal_jatuh_tempo') }}">

                    <button type="button" class="btn-trash" onclick="removeItem(this)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    <small class="stok-info text-muted">
                        Pilih tanggal terlebih dahulu
                    </small>

                </div>

            </div>

            <div class="btn-add" onclick="addItem()">+ Tambah Aset</div>

            <label>Keperluan</label>
            <textarea name="alasan" required
                      class="{{ $errors->has('alasan') ? 'is-invalid' : '' }}">{{ old('alasan') }}</textarea>
            @error('alasan')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <button type="submit">Kirim Permintaan</button>

        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('change', function(e) {

            if (!e.target.classList.contains('asetSelect')) return;

            const row = e.target.closest('.aset-row');

            const option = e.target.options[e.target.selectedIndex];

            row.querySelector('.nama_aset_hidden').value =
                option.dataset.nama || '';
        });

        // Embed aset options dari server agar tersedia di addItem() tanpa Blade
        const asetGroupedOptions = @json($asetGrouped->map(fn($items, $kat) => $items->map(fn($it) => [
            'id'    => $it['id_jenis_barang'],
            'nama'  => $it['nama_aset'],
            'jenis' => $it['jenis_barang'],
            'stok'  => $it['total_unit'],
            'kat'   => $kat,
        ]))->flatten(1)->values());

        function buildAsetOptions() {
            // Group by kategori
            const groups = {};
            asetGroupedOptions.forEach(item => {
                if (!groups[item.kat]) groups[item.kat] = [];
                groups[item.kat].push(item);
            });
            return Object.entries(groups).map(([kat, items]) =>
                `<optgroup label="${kat}">${items.map(it =>
                    `<option value="${it.id}" data-nama="${it.nama}" data-stok="${it.stok}">${it.nama} - ${it.jenis} (${it.stok} unit)</option>`
                ).join('')}</optgroup>`
            ).join('');
        }

        function initTS(el) {
            return new TomSelect(el, {
                searchField: ["text"],
                allowEmptyOption: true,
            });
        }
        // Init TomSelect pada select pertama (hardcoded di HTML)
        document.querySelectorAll("#asetContainer .asetSelect").forEach(initTS);

        let index = 1;

        function addItem(oldData = null) {

            const container = document.getElementById("asetContainer");

            const row = document.createElement("div");
            row.className = "aset-row";

            const oldJumlah    = oldData ? (oldData.jumlah             || '') : '';
            const oldTglPinjam = oldData ? (oldData.tanggal_pinjam     || '') : '';
            const oldTglKembali= oldData ? (oldData.tanggal_jatuh_tempo|| '') : '';

            row.innerHTML = `
                <select class="asetSelect"
                        name="items[${index}][id_jenis_barang]"
                        required>
                    <option value="">-- Pilih Aset --</option>
                    ${buildAsetOptions()}
                </select>

                <input type="hidden"
                    name="items[${index}][nama_aset]"
                    class="nama_aset_hidden"
                    value="${oldData ? (oldData.nama_aset || '') : ''}">

                <input type="number"
                    name="items[${index}][jumlah]"
                    min="1"
                    placeholder="Jumlah"
                    required
                    value="${oldJumlah}">

                <input type="text"
                    name="items[${index}][tanggal_pinjam]"
                    class="tanggal_pinjam"
                    placeholder="Tanggal Pinjam"
                    required
                    value="${oldTglPinjam}">

                <input type="text"
                    name="items[${index}][tanggal_jatuh_tempo]"
                    class="tanggal_kembali"
                    placeholder="Tanggal Kembali"
                    required
                    value="${oldTglKembali}">

                <button type="button" class="btn-trash" onclick="removeItem(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <small class="stok-info text-muted">
                    Pilih tanggal terlebih dahulu
                </small>
            `;

            container.appendChild(row);

            initDateRange(row);
            const ts = initTS(row.querySelector(".asetSelect"));
            initStokLimit(row);
            initAvailability(row);

            // Restore TomSelect + hidden field nilai lama
            if (oldData && oldData.id_jenis_barang) {
                ts.setValue(oldData.id_jenis_barang, true);
                const sel = row.querySelector('.asetSelect');
                const opt = sel.options[sel.selectedIndex];
                const hidden = row.querySelector('.nama_aset_hidden');
                if (opt && hidden && !hidden.value) hidden.value = opt.dataset.nama || '';
            }

            index++;
        }

        function removeItem(btn) {
            const rows = document.querySelectorAll("#asetContainer .aset-row");
            if (rows.length > 1) btn.closest(".aset-row").remove();
            else alert("Minimal 1 aset harus dipilih.");
        }


        function initDateRange(row) {

            const startInput = row.querySelector(".tanggal_pinjam");
            const endInput = row.querySelector(".tanggal_kembali");

            const startPicker = flatpickr(startInput, {
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr) {
                    if (dateStr) {
                        endPicker.set("minDate", dateStr);

                        // kalau tanggal kembali lebih kecil, reset
                        if (endInput.value < dateStr) {
                            endInput.value = "";
                        }
                    }
                }
            });

            const endPicker = flatpickr(endInput, {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
        }

        document.querySelectorAll("#asetContainer .aset-row").forEach(function(row) {
            initDateRange(row);
            initStokLimit(row);
            initAvailability(row);
        });

        // Restore old items setelah TomSelect sudah di-init di atas
        document.addEventListener('DOMContentLoaded', function() {
            // Restore TomSelect item 0 — gunakan el.tomselect (sudah di-init, jangan init ulang)
            document.querySelectorAll(".asetSelect").forEach(function(el) {
                const ts = el.tomselect;
                if (!ts) return;
                const oldVal = el.dataset.old;
                if (oldVal) {
                    ts.setValue(oldVal, true);
                    const opt = el.options[el.selectedIndex];
                    const row = el.closest('.aset-row');
                    const hidden = row ? row.querySelector('.nama_aset_hidden') : null;
                    if (opt && hidden && !hidden.value) hidden.value = opt.dataset.nama || '';
                }
            });

            // Tambah item lama dari index 1 ke atas
            const oldItemsRaw = @json(old('items', []));
            const oldItems = Array.isArray(oldItemsRaw) ? oldItemsRaw : Object.values(oldItemsRaw);
            for (let i = 1; i < oldItems.length; i++) {
                addItem(oldItems[i]);
            }
        });

        function initStokLimit(row) {

            const select = row.querySelector(".asetSelect");
            const jumlahInput =
                row.querySelector("input[name*='[jumlah]']");

            select.addEventListener("change", function() {

                const selectedOption = select.options[select.selectedIndex];
                const stok = selectedOption.getAttribute("data-stok");

                if (stok) {
                    jumlahInput.max = stok;
                    jumlahInput.value = "";
                    jumlahInput.placeholder = "Maks: " + stok;
                } else {
                    jumlahInput.removeAttribute("max");
                }
            });

            // Cegah ketik melebihi max
            jumlahInput.addEventListener("input", function() {
                const max = parseInt(jumlahInput.max);
                if (max && parseInt(jumlahInput.value) > max) {
                    jumlahInput.value = max;
                }
            });
        }

        function cekAvailability(row) {

            const select = row.querySelector(".asetSelect");
            const option = select.options[select.selectedIndex];

            const start = row.querySelector(".tanggal_pinjam").value;
            const end = row.querySelector(".tanggal_kembali").value;

            const jumlahInput = row.querySelector("input[name*='[jumlah]']");

            if (!select.value || !start || !end) return;

            fetch("/cek-ketersediaan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                    },
                    body: JSON.stringify({
                        nama_aset: option.dataset.nama,
                        id_jenis_barang: select.value,
                        tanggal_pinjam: start,
                        tanggal_jatuh_tempo: end
                    })
                })
                .then(res => res.json())
                .then(data => {

                    const info = row.querySelector('.stok-info');

                    if (data.sisa <= 0) {

                        jumlahInput.disabled = true;
                        jumlahInput.value = "";
                        jumlahInput.max = 0;
                        jumlahInput.placeholder = "Tidak tersedia";

                        info.innerHTML =
                            `Tidak tersedia pada tanggal tersebut`;

                    } else {

                        jumlahInput.disabled = false;
                        jumlahInput.max = data.sisa;

                        if (
                            jumlahInput.value &&
                            parseInt(jumlahInput.value) > data.sisa
                        ) {
                            jumlahInput.value = data.sisa;
                        }

                        jumlahInput.placeholder =
                            "Maks " + data.sisa + " unit";

                        info.innerHTML =
                            `Tersedia ${data.sisa} dari ${data.total} unit`;
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }

        function initAvailability(row) {

            const select = row.querySelector(".asetSelect");
            const startInput = row.querySelector(".tanggal_pinjam");
            const endInput = row.querySelector(".tanggal_kembali");

            select.addEventListener("change", function() {

                const option =
                    select.options[select.selectedIndex];

                row.querySelector(".nama_aset_hidden").value =
                    option.dataset.nama || '';

                cekAvailability(row);
            });

            startInput.addEventListener(
                "change",
                () => cekAvailability(row)
            );

            endInput.addEventListener(
                "change",
                () => cekAvailability(row)
            );
        }

        document.querySelector("form").addEventListener("submit", function(e) {

            let valid = true;

            document.querySelectorAll(".tanggal_pinjam").forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                }
            });

            document.querySelectorAll(".tanggal_kembali").forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();

                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal belum diisi',
                    text: 'Tanggal pinjam dan tanggal kembali wajib diisi.'
                });
            }
        });
    </script>

</body>


</html>
