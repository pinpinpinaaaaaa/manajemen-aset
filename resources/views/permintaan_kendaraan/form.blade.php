<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Kendaraan | SIMASTER</title>
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
            margin-top: 14px;
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

        .action-column {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .row-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr 80px;
            gap: 14px;
            align-items: stretch;
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

        <h2>Form Permintaan Kendaraan</h2>
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

        <form action="{{ route('form-permintaan-kendaraan.store') }}" method="POST" enctype="multipart/form-data">
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

            <div style="margin-top:10px;">
                <label>Email</label>
                <input type="email" name="email">
            </div>

            {{-- ================= DETAIL ================= --}}
            <label style="margin-top:14px;">Detail Kegiatan</label>

            <div class="item-card">

                <div class="extra-barang">
                    <div class="row-barang">

                        <div>
                            <label>Tanggal Mulai</label>
                            <input type="text" name="tanggal_mulai" class="tglMulai" required>
                        </div>

                        <div>
                            <label>Tanggal Selesai</label>
                            <input type="text" name="tanggal_selesai" class="tglSelesai" required>
                        </div>

                    </div>
                </div>

                <div class="row-bottom">

                    <div>
                        <label>Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="jamMulai" required>
                    </div>

                    <div>
                        <label>Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="jamSelesai" required>
                    </div>

                </div>

                <div class="row-top">

                    <div>
                        <label>Jumlah Kendaraan</label>

                        <input type="number" name="jumlah" class="jumlahInput" min="1" required>

                        <small class="info-kendaraan text-muted"></small>
                    </div>

                    <div>
                        <label>Keperluan</label>
                        <input type="text" name="keperluan" required>
                    </div>

                </div>

                <div class="extra-barang">

                    <div class="row-barang">

                        <div>
                            <label>Tempat Jemput</label>
                            <input type="text" name="tempat_jemput" required>
                        </div>

                        <div>
                            <label>Tempat Tujuan</label>
                            <input type="text" name="tempat_tujuan" required>
                        </div>

                    </div>

                </div>
            </div>

            <label>Catatan (Opsional)</label>
            <textarea name="catatan"></textarea>

            <button type="submit">Kirim Pengadaan</button>
        </form>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const card = document.querySelector('.item-card');

        const tglMulai = card.querySelector('.tglMulai');
        const tglSelesai = card.querySelector('.tglSelesai');

        const jamMulai = card.querySelector('.jamMulai');
        const jamSelesai = card.querySelector('.jamSelesai');

        const jumlahInput = card.querySelector('.jumlahInput');
        const info = card.querySelector('.info-kendaraan');

        // ================= DATE PICKER =================

        const fpMulai = flatpickr(tglMulai, {
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr) {

                fpSelesai.set('minDate', dateStr);

                if (tglSelesai.value && tglSelesai.value < dateStr) {
                    tglSelesai.value = '';
                }
            }
        });

        const fpSelesai = flatpickr(tglSelesai, {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        // ================= VALIDASI JAM =================

        function updateJamConstraint() {

            if (!jamMulai.value) return;

            let [h, m] = jamMulai.value.split(":");
            h = parseInt(h);

            if (tglMulai.value === tglSelesai.value) {

                let minHour = h + 1;
                let minMinute = m;

                if (minHour >= 24) {
                    minHour = 23;
                    minMinute = "59";
                }

                const minTime =
                    `${minHour.toString().padStart(2,'0')}:${minMinute}`;

                jamSelesai.min = minTime;

                if (jamSelesai.value && jamSelesai.value < minTime) {
                    jamSelesai.value = minTime;
                }

            } else {

                jamSelesai.min = "00:00";
            }
        }

        jamMulai.addEventListener('change', updateJamConstraint);
        tglSelesai.addEventListener('change', updateJamConstraint);

        // ================= CEK KETERSEDIAAN =================

        function cekKetersediaan() {

            if (!tglMulai.value ||
                !tglSelesai.value ||
                !jamMulai.value ||
                !jamSelesai.value) return;

            fetch(
                    `/cek-kendaraan?tanggal_mulai=${tglMulai.value}&tanggal_selesai=${tglSelesai.value}&jam_mulai=${jamMulai.value}&jam_selesai=${jamSelesai.value}`
                )
                .then(res => res.json())
                .then(data => {

                    jumlahInput.max = data.sisa;

                    if (data.sisa <= 0) {

                        jumlahInput.value = '';
                        jumlahInput.disabled = true;
                        jumlahInput.max = 0;

                        info.innerHTML =
                            `Tidak ada kendaraan tersedia`;

                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak tersedia',
                            text: 'Tidak ada kendaraan tersedia di waktu tersebut'
                        });

                    } else {

                        jumlahInput.disabled = false;

                        jumlahInput.placeholder = `max ${data.sisa}`;
                        jumlahInput.max = data.sisa;

                        info.innerHTML = `Tersedia ${data.sisa} kendaraan`;
                    }
                });
        }

        [tglMulai, tglSelesai, jamMulai, jamSelesai]
        .forEach(el => {
            el.addEventListener('change', cekKetersediaan);
        });

        // ================= VALIDASI SUBMIT =================

        jumlahInput.addEventListener('input', function() {

            const max = parseInt(this.max);

            if (!max) return;

            if (parseInt(this.value) > max) {
                this.value = max;
            }

            if (parseInt(this.value) < 1 && this.value !== '') {
                this.value = 1;
            }
        });

        document.querySelector("form")
            .addEventListener("submit", function(e) {

                if (
                    tglMulai.value === tglSelesai.value &&
                    jamSelesai.value <= jamMulai.value
                ) {

                    e.preventDefault();

                    Swal.fire({
                        icon: 'error',
                        title: 'Jam tidak valid',
                        text: 'Jam selesai minimal 1 jam setelah jam mulai'
                    });
                }
            });
    </script>
</body>

</html>
