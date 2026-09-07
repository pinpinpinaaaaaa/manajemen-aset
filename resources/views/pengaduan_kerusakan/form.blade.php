<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Kerusakan | SIMASTER</title>
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

        <h2>Form Pengaduan Kerusakan</h2>
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

        <form action="{{ route('form-pengaduan-kerusakan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div>
                    <label>Nama Pelapor</label>
                    <input type="text" name="nama_pelapor" required
                           value="{{ old('nama_pelapor') }}"
                           class="{{ $errors->has('nama_pelapor') ? 'is-invalid' : '' }}">
                    @error('nama_pelapor')
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

            <div>
                <label>Email</label>
                <input type="email" name="email_pelapor"
                       value="{{ old('email_pelapor') }}"
                       class="{{ $errors->has('email_pelapor') ? 'is-invalid' : '' }}">
                @error('email_pelapor')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- ================= ITEM KERUSAKAN ================= --}}
            <label style="margin-top:14px;">Detail Kerusakan</label>

            @if ($errors->has('items') || $errors->has('items.*') || $errors->hasAny(collect(range(0,9))->map(fn($i) => "items.$i.id_aset")->toArray()))
                <div style="color:#dc2626;font-size:.85rem;margin-top:4px;margin-bottom:8px;">
                    &#9888; Periksa kembali setiap item kerusakan di bawah.
                </div>
            @endif
            @error('items')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <div id="itemContainer"></div>

            <div class="btn-add" onclick="addItem()">+ Tambah Kerusakan</div>

            <button type="submit">Kirim Pengaduan</button>
        </form>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Opsi gedung di-embed dari server agar tersedia saat addItem() dipanggil tanpa AJAX
        const gedungOptions = @json($gedung->map(fn($g) => ['id' => $g->id_gedung, 'nama' => $g->nama_gedung]));

        async function addItem(oldData = null) {
            const container = document.getElementById("itemContainer");
            const index = document.querySelectorAll(".item-card").length;

            const el = document.createElement("div");
            el.className = "item-card";

            const gedungOptionsHtml = gedungOptions
                .map(g => `<option value="${g.id}">${g.nama}</option>`)
                .join('');

            el.innerHTML = `
                <div class="row-top">
                    <div>
                        <label>Gedung</label>
                        <select name="items[${index}][id_gedung]" class="gedungItem" required>
                            <option value="">-- Pilih Gedung --</option>
                            ${gedungOptionsHtml}
                        </select>
                    </div>
                    <div>
                        <label>Ruangan</label>
                        <select name="items[${index}][id_ruangan]" class="ruanganItem" required disabled>
                            <option value="">-- Pilih Gedung dulu --</option>
                        </select>
                    </div>
                </div>

                <div class="row-top">
                    <div>
                        <label>Aset</label>
                        <select name="items[${index}][id_aset]" class="asetItem" required disabled>
                            <option value="">-- Pilih Ruangan dulu --</option>
                        </select>
                    </div>
                    <div>
                        <label>Kategori</label>
                        <select name="items[${index}][kategori_kerusakan]" required>
                            <option value="">-- Pilih --</option>
                            <option value="ringan">Ringan</option>
                            <option value="sedang">Sedang</option>
                            <option value="berat">Berat</option>
                        </select>
                    </div>
                </div>

                <div class="item-catatan">
                    <label>Deskripsi Kerusakan</label>
                    <textarea name="items[${index}][keluhan]" required></textarea>
                </div>

                <div class="item-upload">
                    <label>Foto</label>
                    <input type="file" name="items[${index}][foto]" accept="image/*" capture="environment" required>
                </div>

                <button type="button" class="btn-trash" onclick="removeItem(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;

            container.appendChild(el);
            initDynamicSelect(el);

            if (!oldData) return;

            // Restore textarea dan select sederhana
            const keluhanEl = el.querySelector(`[name="items[${index}][keluhan]"]`);
            const kategoriEl = el.querySelector(`[name="items[${index}][kategori_kerusakan]"]`);
            if (keluhanEl)  keluhanEl.value  = oldData.keluhan             || '';
            if (kategoriEl) kategoriEl.value = oldData.kategori_kerusakan  || '';

            // Note bahwa foto harus diupload ulang
            const uploadDiv = el.querySelector('.item-upload');
            if (uploadDiv) {
                uploadDiv.insertAdjacentHTML('beforeend',
                    '<small style="color:#dc2626;font-size:.8rem;display:block;margin-top:6px;">&#9888; Foto harus di-upload ulang.</small>'
                );
            }

            // Restore cascading: gedung → ruangan → aset
            if (!oldData.id_gedung) return;
            const gedungSel  = el.querySelector('.gedungItem');
            const ruanganSel = el.querySelector('.ruanganItem');
            const asetSel    = el.querySelector('.asetItem');
            gedungSel.value  = oldData.id_gedung;

            try {
                const ruanganData = await fetch(`/get-ruangan/${oldData.id_gedung}`).then(r => r.json());
                ruanganSel.disabled = false;
                ruanganSel.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                ruanganData.forEach(r => {
                    ruanganSel.innerHTML += `<option value="${r.id_ruangan}">${r.nama_ruangan}</option>`;
                });
                if (oldData.id_ruangan) ruanganSel.value = oldData.id_ruangan;

                if (!oldData.id_ruangan) return;
                const asetData = await fetch(`/get-aset/${oldData.id_ruangan}`).then(r => r.json());
                asetSel.disabled = false;
                asetSel.innerHTML = '<option value="">-- Pilih Aset --</option>';
                asetData.forEach(a => {
                    asetSel.innerHTML += `<option value="${a.id_aset}">${a.nama_aset}</option>`;
                });
                if (oldData.id_aset) asetSel.value = oldData.id_aset;

            } catch (e) {
                console.error('Gagal restore item lama:', e);
            }
        }

        function removeItem(btn) {
            const items = document.querySelectorAll(".item-card");
            if (items.length > 1) {
                btn.closest(".item-card").remove();
            } else {
                alert("Minimal 1 item harus ada");
            }
        }

        function initDynamicSelect(card) {
            const gedung  = card.querySelector('.gedungItem');
            const ruangan = card.querySelector('.ruanganItem');
            const aset    = card.querySelector('.asetItem');

            gedung.addEventListener('change', function() {
                fetch(`/get-ruangan/${this.value}`)
                    .then(res => res.json())
                    .then(data => {
                        ruangan.disabled = false;
                        ruangan.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
                        data.forEach(r => {
                            ruangan.innerHTML += `<option value="${r.id_ruangan}">${r.nama_ruangan}</option>`;
                        });
                        aset.innerHTML = '<option value="">-- Pilih Ruangan dulu --</option>';
                        aset.disabled = true;
                    });
            });

            ruangan.addEventListener('change', function() {
                fetch(`/get-aset/${this.value}`)
                    .then(res => res.json())
                    .then(data => {
                        aset.disabled = false;
                        aset.innerHTML = '<option value="">-- Pilih Aset --</option>';
                        data.forEach(a => {
                            aset.innerHTML += `<option value="${a.id_aset}">${a.nama_aset}</option>`;
                        });
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', async function() {
            const oldItemsRaw = @json(old('items', []));
            const oldItems = Array.isArray(oldItemsRaw) ? oldItemsRaw : Object.values(oldItemsRaw);

            if (oldItems.length === 0) {
                addItem();
            } else {
                for (const item of oldItems) {
                    await addItem(item);
                }
            }
        });
    </script>
</body>

</html>
