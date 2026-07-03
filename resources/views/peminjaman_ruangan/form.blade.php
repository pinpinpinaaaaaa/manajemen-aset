<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Ruangan | SIMASTER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .form-section {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 18px;
            padding: 1.2rem;
        }

        .ruangan-item {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            background: #fcfcfc;
        }

        .ruangan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .ruangan-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .form-grid,
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
        }

        @media(max-width:700px) {

            .form-grid,
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .aset-container {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #ddd;
        }

        .aset-item {
            display: grid;
            grid-template-columns: 2fr 120px 50px;
            gap: 10px;
            margin-bottom: 10px;
        }

        .btn-delete {
            background: #fee2e2;
            border: none;
            border-radius: 12px;
            color: #dc2626;
            cursor: pointer;
            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            transition: .2s;
        }

        .btn-delete:hover {
            background: #fecaca;
            transform: scale(1.03);
        }

        .btn-delete i {
            font-size: 15px;
        }

        .btn-secondary {
            padding: 10px 16px;
            border: none;
            border-radius: 12px;
            background: #f3f4f6;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .konsumsi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .konsumsi-item {
            display: flex;
            flex-direction: column;
            gap: .8rem;
        }

        .konsumsi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .konsumsi-item textarea {
            min-height: 90px;
        }

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

        .card-form,
        .form-section,
        .ruangan-item {
            overflow: visible !important;
        }

        .flatpickr-calendar {
            z-index: 99999 !important;
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

        .btn-add {
            display: inline-block;
            margin: 4px 0 20px;
            padding: 11px 18px;
            background: #ebca56;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            transition: .25s;

            border: none;
            outline: none;
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

        form {
            display: flex;
            flex-direction: column;
            gap: .8rem;
        }

        label {
            margin-bottom: 6px !important;
        }

        input,
        select,
        textarea {
            min-height: 52px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .aset-item {
            align-items: center;
            gap: .8rem;
            margin-bottom: .8rem;
        }

        .aset-item .btn-delete {
            width: 52px;
            height: 52px;
            flex-shrink: 0;
        }

        .btn-add,
        .btn-secondary {
            height: 48px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff8dc;
            border: 1px solid rgba(235, 202, 86, .4);
            padding: 0px 16px;
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

        <h2>Form Peminjaman Ruangan</h2>

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

        <form method="POST" action="{{ route('form-peminjaman-ruangan.store') }}">
            @csrf

            <div class="form-grid">
                <div>
                    <label>Nama Peminjam</label>
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

            <div>
                <label>Email</label>
                <input type="email" name="email_pengaju" required>
            </div>

            <div class="form-grid">
                <div>
                    <label>Jenis Kegiatan</label>
                    <select name="jenis_kegiatan" id="jenis_kegiatan" required>
                        <option value="rapat">Rapat</option>
                        <option value="pelatihan">Pelatihan</option>
                        <option value="asasmen">Asasmen</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label id="label_nama_kegiatan">Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan">
                </div>
            </div>


            <div class="form-section">

                <div class="section-title">
                    Ruangan & Jadwal
                </div>
                <div style="margin-bottom:1rem; display:flex; flex-direction:column; gap:.7rem;">

                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="sameJadwal">
                        <label for="sameJadwal">
                            Hari & waktu sama untuk semua ruangan
                        </label>
                    </div>

                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="sameAset">
                        <label for="sameAset">
                            Aset yang dibutuhkan sama untuk semua ruangan
                        </label>
                    </div>

                </div>

                <div id="ruanganContainer"></div>

                <button type="button" class="btn-add" onclick="addRuangan()">
                    + Tambah Ruangan
                </button>

            </div>

            <div class="form-section">

                <div class="section-title">
                    Konsumsi
                </div>

                <div class="konsumsi-grid">

                    <div class="konsumsi-item">

                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="konsumsi-air" onchange="toggleKonsumsi(this, 0)">

                            <label for="konsumsi-air">
                                Air Mineral
                            </label>
                        </div>

                        <input type="hidden" name="konsumsi[0][jenis_konsumsi]" value="air_mineral">

                        <input type="number" name="konsumsi[0][jumlah]" placeholder="Jumlah" disabled>

                        <textarea name="konsumsi[0][catatan]" placeholder="Contoh: Air mineral 600ml dingin" disabled></textarea>

                    </div>

                    <div class="konsumsi-item">

                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="konsumsi-snack" onchange="toggleKonsumsi(this, 1)">

                            <label for="konsumsi-snack">
                                Snack
                            </label>
                        </div>

                        <input type="hidden" name="konsumsi[1][jenis_konsumsi]" value="makanan_ringan">

                        <input type="number" name="konsumsi[1][jumlah]" placeholder="Jumlah" disabled>

                        <textarea name="konsumsi[1][catatan]" placeholder="Contoh: Snack asin tanpa kacang" disabled></textarea>

                    </div>

                    <div class="konsumsi-item">

                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="konsumsi-berat" onchange="toggleKonsumsi(this, 2)">

                            <label for="konsumsi-berat">
                                Makanan Berat
                            </label>
                        </div>

                        <input type="hidden" name="konsumsi[2][jenis_konsumsi]" value="makanan_berat">

                        <input type="number" name="konsumsi[2][jumlah]" placeholder="Jumlah" disabled>

                        <textarea name="konsumsi[2][catatan]" placeholder="Contoh: Nasi box vegetarian" disabled></textarea>

                    </div>

                </div>

            </div>

            <button type="submit">
                Ajukan Peminjaman
            </button>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const jenisKegiatan = document.getElementById('jenis_kegiatan');
        const labelNamaKegiatan = document.getElementById('label_nama_kegiatan');

        function updateLabelKegiatan() {

            if (jenisKegiatan.value === 'rapat') {
                labelNamaKegiatan.textContent = 'Peserta Rapat';
            } else {
                labelNamaKegiatan.textContent = 'Nama Kegiatan';
            }

        }

        jenisKegiatan.addEventListener('change', updateLabelKegiatan);

        // jalankan saat halaman pertama kali dibuka
        updateLabelKegiatan();

        let indexRuangan = 0;

        let sameScheduleAll = false;
        let sameAsetAll = false;

        function addRuangan() {

            const container = document.getElementById("ruanganContainer");

            const currentIndex = indexRuangan;

            const now = new Date();

            const today =
                now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0');

            const currentTime =
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0');

            const html = `
            <div class="ruangan-item" data-idx="${currentIndex}">

                <div class="ruangan-header">

                    <div class="ruangan-title">
                        Ruangan ${currentIndex + 1}
                    </div>

                    <button type="button"
                            class="btn-delete"
                            onclick="removeRuangan(this)">
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </div>

                <div class="form-row">

                    <div>
                        <label>Tanggal Mulai</label>

                        <input type="text"
                            class="tanggal-mulai tanggal-mulai-${currentIndex}"
                            name="ruangan[${currentIndex}][tanggal_mulai]"
                            required>
                    </div>

                    <div>
                        <label>Jam Mulai</label>

                        <input type="text"
                            class="jam-mulai jam-mulai-${currentIndex}"
                            name="ruangan[${currentIndex}][jam_mulai]"
                            required>
                    </div>

                </div>

                <div class="form-row">

                    <div>
                        <label>Tanggal Selesai</label>

                        <input type="text"
                            class="tanggal-selesai tanggal-selesai-${currentIndex}"
                            name="ruangan[${currentIndex}][tanggal_selesai]"
                            required>
                    </div>

                    <div>
                        <label>Jam Selesai</label>

                        <input type="text"
                            class="jam-selesai jam-selesai-${currentIndex}"
                            name="ruangan[${currentIndex}][jam_selesai]"
                            required>
                    </div>

                </div>

                <div class="form-row">

                    <div>

                        <label>Gedung</label>

                        <select onchange="loadRuangan(this, ${currentIndex})"
                                name="ruangan[${currentIndex}][id_gedung]"
                                class="gedung-select-${currentIndex}"
                                disabled
                                required>

                            <option value="">Pilih Gedung</option>

                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}">
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div>

                        <label>Ruangan Tersedia</label>

                        <select name="ruangan[${currentIndex}][id_ruangan]"
                                class="ruangan-select-${currentIndex}"
                                onchange="handleRuanganSelected(this, ${currentIndex})"
                                required>

                            <option value="">Pilih Ruangan</option>

                        </select>

                    </div>

                </div>

                <div style="margin-top:1rem;">
                    <label>Catatan Ruangan</label>

                    <textarea
                        name="ruangan[${currentIndex}][catatan]"
                        placeholder="Contoh: Ruangan disetting layout U-Shape, membutuhkan whiteboard tambahan, dll.">
                    </textarea>
                </div>

                <div class="aset-container">

                    <div class="section-title">
                        Aset Tersedia
                    </div>

                    <div id="aset-${currentIndex}"></div>

                    <button type="button"
                            class="btn-secondary"
                            onclick="addAset(${currentIndex})">

                        + Tambah Aset

                    </button>

                </div>

            </div>
            `;

            container.insertAdjacentHTML("beforeend", html);

            initFlatpickr(currentIndex);

            copyScheduleIfNeeded(currentIndex);

            copyAsetIfNeeded(currentIndex);

            indexRuangan++;

            refreshAllAsetLimit();
        }

        function enableGedung(idx) {

            const tanggalMulai = document.querySelector(`.tanggal-mulai-${idx}`).value;
            const jamMulai = document.querySelector(`.jam-mulai-${idx}`).value;
            const tanggalSelesai = document.querySelector(`.tanggal-selesai-${idx}`).value;
            const jamSelesai = document.querySelector(`.jam-selesai-${idx}`).value;

            const gedungSelect = document.querySelector(`.gedung-select-${idx}`);

            if (
                tanggalMulai &&
                jamMulai &&
                tanggalSelesai &&
                jamSelesai
            ) {
                gedungSelect.disabled = false;
            }
        }

        function loadRuangan(el, idx) {

            const tanggalMulai = document.querySelector(`.tanggal-mulai-${idx}`).value;
            const jamMulai = document.querySelector(`.jam-mulai-${idx}`).value;
            const tanggalSelesai = document.querySelector(`.tanggal-selesai-${idx}`).value;
            const jamSelesai = document.querySelector(`.jam-selesai-${idx}`).value;

            const params = new URLSearchParams({
                gedung: el.value,
                tanggal_mulai: tanggalMulai,
                jam_mulai: jamMulai,
                tanggal_selesai: tanggalSelesai,
                jam_selesai: jamSelesai
            });

            fetch(`/get-ruangan-available?${params}`)
                .then(res => res.json())
                .then(data => {

                    let select = document.querySelector(`.ruangan-select-${idx}`);

                    select.innerHTML = `<option value="">Pilih Ruangan</option>`;

                    data.forEach(r => {

                        select.innerHTML += `
                    <option value="${r.id_ruangan}">
                        ${r.nama_ruangan}
                    </option>
                `;
                    });

                })
                .catch(err => {
                    console.log(err);
                });
        }

        function loadAset(idx) {

            const ruangan = document.querySelector(`.ruangan-select-${idx}`).value;

            const tanggalMulai = document.querySelector(`.tanggal-mulai-${idx}`).value;
            const jamMulai = document.querySelector(`.jam-mulai-${idx}`).value;
            const tanggalSelesai = document.querySelector(`.tanggal-selesai-${idx}`).value;
            const jamSelesai = document.querySelector(`.jam-selesai-${idx}`).value;

            const params = new URLSearchParams({
                ruangan: ruangan,
                tanggal_mulai: tanggalMulai,
                jam_mulai: jamMulai,
                tanggal_selesai: tanggalSelesai,
                jam_selesai: jamSelesai
            });

            fetch(`/get-aset-tersedia?${params}`)
                .then(res => res.json())
                .then(data => {

                    console.log(data);

                    window['asetAvailable' + idx] = data;
                })
                .catch(err => {
                    console.log(err);
                });
        }

        function setMaxJumlah(select) {

            const max = parseInt(
                select.options[select.selectedIndex].dataset.max || 0
            );

            const input =
                select.parentElement.querySelector('input[type="number"]');

            input.max = max;

            input.placeholder = `Max ${max}`;

            input.value = '';
        }

        function toggleKonsumsi(checkbox, idx) {

            const jumlah = document.querySelector(
                `input[name="konsumsi[${idx}][jumlah]"]`
            );

            const catatan = document.querySelector(
                `textarea[name="konsumsi[${idx}][catatan]"]`
            );

            jumlah.disabled = !checkbox.checked;
            catatan.disabled = !checkbox.checked;

            if (!checkbox.checked) {
                jumlah.value = '';
                catatan.value = '';
            }
        }

        document.getElementById('sameJadwal')
            .addEventListener('change', function() {

                sameScheduleAll = this.checked;

                // langsung sync semua kalau dicentang
                if (this.checked) {

                    document.querySelectorAll('.ruangan-item').forEach(item => {

                        handleTanggalJam(parseInt(item.dataset.idx));

                    });

                }

            });

        document.getElementById('sameAset')
            .addEventListener('change', function() {

                sameAsetAll = this.checked;

                // langsung sync aset
                if (this.checked) {

                    syncAsetFromFirst();
                    refreshAllAsetLimit();

                }

            });

        function handleTanggalJam(idx) {

            enableGedung(idx);

            if (!sameScheduleAll) return;

            const tanggalMulai = document.querySelector(`.tanggal-mulai-${idx}`).value;
            const jamMulai = document.querySelector(`.jam-mulai-${idx}`).value;

            const tanggalSelesai = document.querySelector(`.tanggal-selesai-${idx}`).value;
            const jamSelesai = document.querySelector(`.jam-selesai-${idx}`).value;

            document.querySelectorAll('.ruangan-item').forEach(item => {

                const itemIdx = item.dataset.idx;

                if (parseInt(itemIdx) === idx) return;

                document.querySelector(`.tanggal-mulai-${itemIdx}`).value = tanggalMulai;
                document.querySelector(`.jam-mulai-${itemIdx}`).value = jamMulai;

                document.querySelector(`.tanggal-selesai-${itemIdx}`).value = tanggalSelesai;
                document.querySelector(`.jam-selesai-${itemIdx}`).value = jamSelesai;

                enableGedung(itemIdx);

            });
        }

        function validateMinTime(idx) {

            const now = new Date();

            const today =
                now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0');

            const currentTime =
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0');

            const tanggalMulai =
                document.querySelector(`.tanggal-mulai-${idx}`);

            const jamMulai =
                document.querySelector(`.jam-mulai-${idx}`);

            const tanggalSelesai =
                document.querySelector(`.tanggal-selesai-${idx}`);

            const jamSelesai =
                document.querySelector(`.jam-selesai-${idx}`);

            // tanggal mulai hari ini -> minimal jam sekarang
            if (tanggalMulai.value === today) {

                jamMulai.min = currentTime;

                if (jamMulai.value && jamMulai.value < currentTime) {
                    jamMulai.value = currentTime;
                }

            } else {

                jamMulai.min = '';
            }

            // tanggal selesai hari ini -> minimal jam sekarang
            if (tanggalSelesai.value === today) {

                jamSelesai.min = currentTime;

                if (jamSelesai.value && jamSelesai.value < currentTime) {
                    jamSelesai.value = currentTime;
                }

            } else {

                jamSelesai.min = '';
            }
        }

        function copyScheduleIfNeeded(idx) {

            if (!sameScheduleAll) return;

            if (idx === 0) return;

            const first = 0;

            document.querySelector(`.tanggal-mulai-${idx}`).value =
                document.querySelector(`.tanggal-mulai-${first}`).value;

            document.querySelector(`.jam-mulai-${idx}`).value =
                document.querySelector(`.jam-mulai-${first}`).value;

            document.querySelector(`.tanggal-selesai-${idx}`).value =
                document.querySelector(`.tanggal-selesai-${first}`).value;

            document.querySelector(`.jam-selesai-${idx}`).value =
                document.querySelector(`.jam-selesai-${first}`).value;

            enableGedung(idx);
        }

        function handleRuanganSelected(select, idx) {

            const selected = select.value;

            let duplicate = false;

            document.querySelectorAll('.ruangan-item').forEach(item => {

                const itemIdx = item.dataset.idx;

                if (parseInt(itemIdx) === idx) return;

                const val = document.querySelector(`.ruangan-select-${itemIdx}`)?.value;

                if (val && val === selected) {
                    duplicate = true;
                }

            });

            if (duplicate) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Ruangan sudah dipilih'
                });

                select.value = '';

                return;
            }

            loadAset(idx);
        }

        function addAset(idx) {

            const key = Date.now();

            const container = document.getElementById(`aset-${idx}`);

            let options = `<option value="">Pilih Aset</option>`;

            const asetList = window['asetAvailable' + idx] || [];

            const selectedAset = [];

            container.querySelectorAll('select').forEach(s => {

                if (s.value) {
                    selectedAset.push(s.value);
                }

            });

            asetList.forEach(a => {

                if (selectedAset.includes(a.id_jenis_barang)) {
                    return;
                }

                options += `
        <option value="${a.id_jenis_barang}"
                data-max="${a.total_tersedia}">
            ${a.nama_aset}
            (${a.total_tersedia} tersedia)
        </option>
        `;
            });

            const html = `
    <div class="aset-item">

        <select
            onchange="setMaxJumlah(this)"
            name="ruangan[${idx}][aset][${key}][id_jenis_barang]">

            ${options}

        </select>

        <input type="number"
            min="1"
            oninput="limitJumlah(this)"
            placeholder="Jumlah"
            name="ruangan[${idx}][aset][${key}][jumlah]">

        <button type="button"
                class="btn-delete"
                onclick="this.closest('.aset-item').remove()">

            <i class="fa-solid fa-trash"></i>

        </button>

    </div>
    `;

            container.insertAdjacentHTML("beforeend", html);

            if (sameAsetAll) {
                syncAsetFromFirst();
            }
        }

        function initFlatpickr(idx) {

            const tanggalMulaiPicker = flatpickr(`.tanggal-mulai-${idx}`, {
                appendTo: document.body,
                static: false,
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr) {

                    handleTanggalJam(idx);
                    validateMinTime(idx);

                    // set minimal tanggal selesai = tanggal mulai
                    tanggalSelesaiPicker.set('minDate', dateStr);

                    // kalau tanggal selesai lebih kecil → reset
                    const tanggalSelesaiInput =
                        document.querySelector(`.tanggal-selesai-${idx}`);

                    if (
                        tanggalSelesaiInput.value &&
                        tanggalSelesaiInput.value < dateStr
                    ) {
                        tanggalSelesaiInput.value = dateStr;
                    }
                }
            });

            const tanggalSelesaiPicker = flatpickr(`.tanggal-selesai-${idx}`, {
                appendTo: document.body,
                static: false,
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function() {
                    handleTanggalJam(idx);
                    validateMinTime(idx);
                }
            });

            flatpickr(`.jam-mulai-${idx}`, {
                appendTo: document.body,
                static: false,
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                onChange: function() {
                    handleTanggalJam(idx);
                    validateMinTime(idx);
                }
            });

            flatpickr(`.jam-selesai-${idx}`, {
                appendTo: document.body,
                static: false,
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                onChange: function() {
                    handleTanggalJam(idx);
                    validateMinTime(idx);
                }
            });

        }

        function setMaxJumlah(select) {

            const originalMax = parseInt(
                select.options[select.selectedIndex].dataset.max || 0
            );
            const input =
                select.parentElement.querySelector('input[type="number"]');

            input.max = originalMax;

            input.placeholder = `Max ${originalMax}`;

            if (parseInt(input.value || 0) > originalMax) {
                input.value = originalMax;
            }

            if (sameAsetAll) {
                syncAsetFromFirst();
            }
        }

        function limitJumlah(input) {

            const max = parseInt(input.max || 0);

            if (parseInt(input.value) > max) {
                input.value = max;
            }

            if (parseInt(input.value) < 1) {
                input.value = 1;
            }

            if (sameAsetAll) {
                syncAsetFromFirst();
            }
        }

        function syncAsetFromFirst() {

            if (!sameAsetAll) return;

            const firstContainer = document.getElementById('aset-0');

            if (!firstContainer) return;

            const firstItems = firstContainer.querySelectorAll('.aset-item');

            document.querySelectorAll('.ruangan-item').forEach(item => {

                const idx = parseInt(item.dataset.idx);

                if (idx === 0) return;

                const target = document.getElementById(`aset-${idx}`);

                target.innerHTML = '';

                firstItems.forEach(fi => {

                    const select = fi.querySelector('select');

                    const jumlahInput = fi.querySelector('input[type="number"]');

                    const value = select.value;

                    const jumlah = parseInt(jumlahInput.value || 0);

                    if (!value || jumlah <= 0) return;

                    const option =
                        select.options[select.selectedIndex];

                    const totalTersedia =
                        parseInt(option.dataset.max || 0);

                    // kalau stok habis jangan tampil
                    if (jumlah >= totalTersedia) {
                        return;
                    }

                    const sisa = totalTersedia - jumlah;

                    const key = Date.now() + Math.random();

                    target.innerHTML += `
            <div class="aset-item">

                <select disabled>
                    <option selected>
                        ${option.text}
                    </option>
                </select>

                <input type="hidden"
                    name="ruangan[${idx}][aset][${key}][id_jenis_barang]"
                    value="${value}">

                <input type="number"
                    readonly
                    value="${sisa}"
                    name="ruangan[${idx}][aset][${key}][jumlah]">

                <button type="button"
                        class="btn-delete"
                        disabled
                        style="opacity:.5;cursor:not-allowed">

                    <i class="fa-solid fa-lock"></i>

                </button>

            </div>
            `;
                });

            });
        }

        function copyAsetIfNeeded(idx) {

            if (!sameAsetAll) return;

            if (idx === 0) return;

            syncAsetFromFirst();
        }

        function removeRuangan(btn) {

            btn.closest('.ruangan-item').remove();

            syncAsetFromFirst();

            refreshAllAsetLimit();
        }

        function refreshAllAsetLimit() {

            document.querySelectorAll('#aset-0 .aset-item select')
                .forEach(select => {

                    setMaxJumlah(select);

                });
        }
    </script>

</body>


</html>
