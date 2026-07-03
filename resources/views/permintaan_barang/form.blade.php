<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Barang | SIMASTER</title>
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

        <h2>Form Permintaan Barang</h2>
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

        <form id="permintaanForm" action="{{ route('form-permintaan-barang.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div>
                    <label>Nama Pengaju</label>
                    <input type="text" name="nama_pengaju" required>
                </div>
                <div>
                    <label>Divisi</label>
                    <select name="id_divisi" required>
                        <option value="" disabled selected>-- Pilih Divisi --</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id_divisi }}">{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label>Email Pengaju</label>
                    <input type="email" name="email_pengaju" required>
                </div>
                <div>
                    <label>Tanggal Kebutuhan</label>
                    <input type="text" id="tanggal_kebutuhan" name="tanggal_kebutuhan" required>
                </div>
            </div>

            <div id="sectionBarang">
                <label style="margin-top:14px; display:block;">Barang yang Diminta</label>

                <div id="barangContainer">
                    <div class="barang-row">

                        <select class="barangSelect" name="items[0][id_barang]" required>
                            <option value="" disabled selected>-- Pilih Barang --</option>
                            @foreach ($gudang as $g)
                                <option value="{{ $g->id_barang }}" data-stok="{{ $g->stok_akhir }}">
                                    {{ $g->nama_barang }} (Stok: {{ $g->stok_akhir }})
                                </option>
                            @endforeach
                        </select>

                        <input type="number" name="items[0][jumlah]" min="1" placeholder="Jumlah" required>

                        <button type="button" class="btn-trash" onclick="removeItem(this)">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                </div>

                <div class="btn-add" onclick="addItem()">+ Tambah Barang</div>
            </div>

            <label>Alasan Pengajuan</label>
            <textarea name="alasan" required></textarea>

            <button type="submit">Kirim Permintaan</button>
        </form>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr("#tanggal_kebutuhan", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        function initTS(el) {
            return new TomSelect(el, {
                searchField: ["text"],
                allowEmptyOption: true,
            });
        }
        document.querySelectorAll(".barangSelect").forEach(initTS);

        function addItem() {
            const container = document.getElementById("barangContainer");
            const index = document.querySelectorAll("#barangContainer .barang-row").length;

            const newRow = document.createElement("div");
            newRow.className = "barang-row";

            newRow.innerHTML = `
                <select class="barangSelect" name="items[${index}][id_barang]" required>
                    <option value="" disabled selected>-- Pilih Barang --</option>
                    @foreach ($gudang as $g)
                        <option value="{{ $g->id_barang }}" data-stok="{{ $g->stok_akhir }}">
                            {{ $g->nama_barang }} (Stok: {{ $g->stok_akhir }})
                        </option>
                    @endforeach
                </select>

                <input type="number" name="items[${index}][jumlah]" min="1" placeholder="Jumlah" required>

                <button type="button" class="btn-trash" onclick="removeItem(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;

            container.appendChild(newRow);
            initTS(newRow.querySelector("select"));
            attachMaxHandler(newRow);
            updateAvailableOptions();
        }

        function removeItem(btn) {
            const rows = document.querySelectorAll("#barangContainer .barang-row");
            if (rows.length > 1) {
                btn.closest(".barang-row").remove();
                updateAvailableOptions();
            } else {
                alert("Minimal 1 barang harus ada.");
            }
        }

        function attachMaxHandler(row) {
            const select = row.querySelector(".barangSelect");
            const jumlahInput = row.querySelector("input[type='number']");

            select.addEventListener("change", function() {
                const selected = this.options[this.selectedIndex];
                const stok = selected.getAttribute("data-stok");

                if (stok) {
                    jumlahInput.max = stok;
                    jumlahInput.value = "";
                }
                updateAvailableOptions();
            });

            jumlahInput.addEventListener("input", function() {
                let value = parseInt(this.value);
                let max = parseInt(this.max);

                if (!isNaN(max) && value > max) {
                    this.value = max;
                }
            });
        }

        document.querySelectorAll(".barang-row").forEach(row => {
            attachMaxHandler(row);
        });

        function updateAvailableOptions() {
            const selects = document.querySelectorAll(".barangSelect");

            let selectedValues = [];
            selects.forEach(s => {
                if (s.value) selectedValues.push(s.value);
            });

            selects.forEach(select => {
                const currentValue = select.value;
                const ts = select.tomselect;

                if (!ts) return;

                Object.values(ts.options).forEach(opt => {
                    if (!opt.value) return;

                    if (selectedValues.includes(opt.value) && opt.value !== currentValue) {
                        ts.updateOption(opt.value, {
                            ...opt,
                            disabled: true
                        });
                    } else {
                        ts.updateOption(opt.value, {
                            ...opt,
                            disabled: false
                        });
                    }
                });

                ts.refreshOptions(false);
            });
        }
        updateAvailableOptions();
    </script>
</body>

</html>
