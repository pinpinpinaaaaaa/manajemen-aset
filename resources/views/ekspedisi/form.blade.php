<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekspedisi | SIMASTER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjB0ZWNofGVufDF8fHx8MTc1ODIwNDQxMHww&ixlib=rb-4.1.0&q=80&w=1080');
            background-size: cover; background-position: center;
            background-repeat: no-repeat; background-attachment: fixed;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; position: relative; padding: 1rem;
        }

        .overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.55); }

        .btn-back {
            position: fixed; top: 20px; left: 20px; z-index: 20;
            background: rgba(255,255,255,0.85); border-radius: 14px;
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(235,202,86,0.4); cursor: pointer;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25); transition: .25s;
        }
        .btn-back:hover { background: #fff; transform: translateX(-2px); }

        .card-form {
            position: relative; z-index: 10; width: 100%; max-width: 720px;
            background: rgba(255,255,255,0.9); padding: 3rem 3rem 2.5rem;
            border-radius: 22px; backdrop-filter: blur(14px);
            border: 1px solid rgba(235,202,86,0.4);
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            animation: fadeIn .55s ease;
        }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; } }

        .logo-img { max-width: 256px; margin-bottom: 1rem; }
        .logo-container { display:flex; justify-content:center; align-items:center; margin-bottom:1.3rem; }

        h2 { text-align:center; margin:0 0 1.4rem; font-size:1.75rem; font-weight:700; color:#1e2226; }

        /* ===== MODE TOGGLE ===== */
        .mode-toggle {
            display: flex; gap: 10px; margin-bottom: 1.8rem;
            background: #f1f5f9; border-radius: 14px; padding: 6px;
        }
        .mode-btn {
            flex: 1; padding: 12px; border: none; border-radius: 10px;
            font-size: .95rem; font-weight: 600; cursor: pointer;
            transition: .2s; background: transparent; color: #64748b;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .mode-btn.active {
            background: #fff; color: #1e2226;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        .mode-btn.active.mengantar { border-bottom: 3px solid #ebca56; }
        .mode-btn.active.mengambil { border-bottom: 3px solid #3b82f6; }

        /* ===== FORM GRID ===== */
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.4rem; margin-bottom:.8rem; }
        @media(max-width:650px) { .form-grid { grid-template-columns:1fr; } }

        label { font-weight:600; font-size:.95rem; margin-bottom:6px; display:block; }

        input, select, textarea {
            width:100%; padding:14px; border-radius:14px;
            border:1px solid #ddd; background:#fafafa;
            font-size:1rem; transition:.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color:#ebca56; background:#fff;
            box-shadow:0 0 0 3px rgba(235,202,86,.35); outline:none;
        }
        input[readonly], select.locked {
            background:#f1f5f9 !important; color:#64748b; cursor:not-allowed;
        }
        textarea { min-height:90px; resize:vertical; }

        /* ===== ITEM CARDS ===== */
        .item-card {
            background:#fff; border-radius:16px; padding:20px;
            margin-bottom:16px; border:1px solid #e5e7eb;
        }
        .row-top { display:grid; grid-template-columns:150px 1fr; gap:14px; margin-bottom:14px; }
        .row-bottom { display:grid; grid-template-columns:1fr 1fr 50px; gap:14px; align-items:end; margin-bottom:14px; }
        .row-bottom .btn-trash { display:flex; justify-content:center; align-items:center; }
        .btn-trash { border:none; background:none; font-size:17px; cursor:pointer; color:#e00000; }
        @media(max-width:768px) {
            .row-top { grid-template-columns:1fr; }
            .row-bottom { grid-template-columns:1fr 1fr; }
        }

        .item-upload { margin-top:10px; }
        .item-upload label { margin-bottom:8px; font-size:.9rem; color:#444; }
        .item-upload input[type="file"] { padding:12px; background:#fafafa; border:1px dashed #cbd5e1; }

        /* ===== BUTTONS ===== */
        .btn-add {
            display:inline-block; margin:4px 0 20px; padding:11px 18px;
            background:#ebca56; border-radius:12px; cursor:pointer;
            font-weight:700; transition:.25s; border:none; font-size:.95rem;
        }
        .btn-add:hover { opacity:.85; transform:translateY(-1px); }

        button[type=submit] {
            width:100%; padding:15px; margin-top:6px;
            font-size:1.15rem; font-weight:700; border:none;
            border-radius:14px; background:linear-gradient(90deg,#f3d46d,#ebca56);
            cursor:pointer; transition:.26s;
        }
        button[type=submit]:hover { opacity:.95; transform:translateY(-1px); }

        /* ===== SECTIONS ===== */
        .form-section { margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid #ececec; }
        .form-section:last-of-type { border-bottom:none; padding-bottom:0; }

        .section-title {
            font-size:1rem; font-weight:700; color:#1e2226;
            margin-bottom:16px;
            display:flex; align-items:center; gap:10px;
        }
        .section-title i { color:#ebca56; }
        .section-title i.blue { color:#3b82f6; }

        .form-group { margin-bottom:16px; }
        .form-group:last-child { margin-bottom:0; }

        /* ===== CHECKBOX ===== */
        .checkbox-wrapper {
            display:flex; align-items:center; gap:12px;
            margin:12px 0 16px; background:#fff8dc;
            border:1px solid rgba(235,202,86,.4);
            padding:14px 16px; border-radius:14px;
        }
        .checkbox-wrapper input[type="checkbox"] { width:18px; height:18px; accent-color:#ebca56; cursor:pointer; }
        .checkbox-wrapper label { margin:0; cursor:pointer; font-weight:600; color:#444; }

        /* ===== INFO BADGE (readonly pre-filled) ===== */
        .prefilled-badge {
            background:#f0f9ff; border:1px solid #bae6fd;
            border-radius:14px; padding:10px 14px;
            font-size:.88rem; color:#0369a1; margin-bottom:16px;
            display:flex; align-items:center; gap:8px;
        }
        .prefilled-badge i { color:#0ea5e9; }

        /* ===== ERROR ===== */
        .error-banner {
            background:#fff0f0; border:1px solid #f87171;
            border-radius:14px; padding:16px 20px; margin-bottom:24px; color:#991b1b;
        }
        .error-banner strong { display:block; margin-bottom:8px; font-size:.95rem; }
        .error-banner ul { margin:0; padding-left:18px; }
        .error-banner li { margin-bottom:3px; font-size:.88rem; }
        .field-error { color:#dc2626; font-size:.8rem; display:block; margin-top:4px; }
        .required-mark { color:#dc2626; margin-left:2px; font-size:.85em; }
        input.is-invalid, select.is-invalid, textarea.is-invalid {
            border-color:#f87171 !important; background:#fff5f5 !important;
        }

        /* TomSelect */
        .ts-wrapper { border-radius:14px !important; border:1px solid #ddd !important; background:#fafafa !important; padding:0 !important; height:50px; display:flex; align-items:center; }
        .ts-wrapper .ts-control { border:none !important; background:transparent !important; padding:4px 12px !important; display:flex; align-items:center; }
        .ts-wrapper.focus { border-color:#ebca56 !important; box-shadow:0 0 0 3px rgba(235,202,86,.35) !important; background:#fff !important; }
        .ts-wrapper, .ts-dropdown, .ts-wrapper input { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif !important; font-size:1rem !important; }
        .ts-dropdown .option { padding:10px 14px; }

        .label-section { font-size:1rem; font-weight:700; margin-bottom:14px; color:#1e2226; display:block; }

        @media(max-width:768px) { .card-form { padding:2rem 1.2rem; } }
    </style>
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
            <script>Swal.fire({ icon:'success', title:'Berhasil!', text:'{{ session('success') }}', timer:2000, showConfirmButton:false });</script>
        @endif
        @if (session('error'))
            <script>Swal.fire({ icon:'error', title:'Gagal!', text:'{{ session('error') }}' });</script>
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
            <input type="hidden" name="jenis_ekspedisi" id="jenis_ekspedisi" value="{{ old('jenis_ekspedisi', 'mengantar') }}">

            {{-- ===== MODE TOGGLE ===== --}}
            <div class="mode-toggle">
                <button type="button" class="mode-btn mengantar {{ old('jenis_ekspedisi', 'mengantar') === 'mengantar' ? 'active' : '' }}"
                    onclick="setMode('mengantar')" id="btn-mengantar">
                    <i class="fa-solid fa-paper-plane"></i> Mengantar
                </button>
                <button type="button" class="mode-btn mengambil {{ old('jenis_ekspedisi', 'mengantar') === 'mengambil' ? 'active' : '' }}"
                    onclick="setMode('mengambil')" id="btn-mengambil">
                    <i class="fa-solid fa-hand-holding-box"></i> Mengambil
                </button>
            </div>

            {{-- ===== DATA PENGAJU ===== --}}
            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-user"></i> Data Pengaju</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Pengaju<span class="required-mark">*</span></label>
                        <input type="text" id="nama_pengaju" name="nama_pengaju" required
                            value="{{ old('nama_pengaju') }}"
                            class="{{ $errors->has('nama_pengaju') ? 'is-invalid' : '' }}">
                        @error('nama_pengaju') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Divisi Pengaju<span class="required-mark">*</span></label>
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

                <div class="form-group">
                    <label>Email Pengaju</label>
                    <input type="email" id="email_pengaju" name="email_pengaju"
                        value="{{ old('email_pengaju') }}"
                        class="{{ $errors->has('email_pengaju') ? 'is-invalid' : '' }}">
                    @error('email_pengaju') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ===== DATA PENGIRIM ===== --}}
            <div class="form-section">
                <div class="section-title" id="label-pengirim">
                    <i class="fa-solid fa-building" id="icon-pengirim"></i>
                    <span id="text-pengirim">Data Pengirim</span>
                </div>

                {{-- Mode: Mengantar — pengirim internal, bisa sama dgn pengaju --}}
                <div id="pengirim-mengantar">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="sameAsPengaju">
                        <label for="sameAsPengaju">Pengirim sama dengan Pengaju</label>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Pengirim<span class="required-mark">*</span></label>
                            <input type="text" id="nama_pengirim_int" name="nama_pengirim"
                                value="{{ old('nama_pengirim') }}"
                                class="{{ $errors->has('nama_pengirim') ? 'is-invalid' : '' }}">
                            @error('nama_pengirim') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Divisi Pengirim<span class="required-mark">*</span></label>
                            <select id="divisi_pengirim_int" name="id_divisi_pengirim"
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
                        <div class="form-group">
                            <label>Email Pengirim</label>
                            <input type="email" id="email_pengirim_int" name="email_pengirim"
                                value="{{ old('email_pengirim') }}">
                        </div>
                        <div class="form-group">
                            <label>No HP Pengirim</label>
                            <input type="text" id="no_hp_pengirim_int" name="no_hp_pengirim"
                                placeholder="08xxxxxxxxxx" inputmode="numeric"
                                value="{{ old('no_hp_pengirim') }}">
                        </div>
                    </div>
                </div>

                {{-- Mode: Mengambil — pengirim eksternal --}}
                <div id="pengirim-mengambil" style="display:none;">
                    <div class="prefilled-badge">
                        <i class="fa-solid fa-circle-info"></i>
                        Isi data pihak yang mengirimkan barang/dokumen ke LM FEB UI.
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Pengirim<span class="required-mark">*</span></label>
                            <input type="text" id="nama_pengirim_ext"
                                value="{{ old('jenis_ekspedisi') === 'mengambil' ? old('nama_pengirim') : '' }}"
                                placeholder="Nama pengirim / penanggung jawab">
                        </div>
                        <div class="form-group">
                            <label>Instansi Pengirim<span class="required-mark">*</span></label>
                            <input type="text" id="instansi_pengirim_ext"
                                value="{{ old('jenis_ekspedisi') === 'mengambil' ? old('instansi_pengirim') : '' }}"
                                placeholder="Nama instansi / organisasi pengirim">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Email Pengirim</label>
                            <input type="email" id="email_pengirim_ext"
                                value="{{ old('jenis_ekspedisi') === 'mengambil' ? old('email_pengirim') : '' }}">
                        </div>
                        <div class="form-group">
                            <label>No HP Pengirim</label>
                            <input type="text" id="no_hp_pengirim_ext"
                                placeholder="08xxxxxxxxxx" inputmode="numeric"
                                value="{{ old('jenis_ekspedisi') === 'mengambil' ? old('no_hp_pengirim') : '' }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Pengirim</label>
                        <textarea id="alamat_pengirim_ext" placeholder="Alamat lengkap pengirim">{{ old('jenis_ekspedisi') === 'mengambil' ? old('alamat_pengirim') : '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===== ISI KIRIMAN ===== --}}
            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-box"></i> <span id="label-isi-kiriman">Isi Kiriman</span></div>

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

                <button type="button" class="btn-add" onclick="addItem()">+ Tambah Item</button>
            </div>

            {{-- ===== INFORMASI KEGIATAN ===== --}}
            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-clipboard-list"></i> Informasi Kegiatan</div>

                <div class="form-group">
                    <label>Judul Kegiatan<span class="required-mark">*</span></label>
                    <input type="text" name="judul_kegiatan" required
                        value="{{ old('judul_kegiatan') }}"
                        class="{{ $errors->has('judul_kegiatan') ? 'is-invalid' : '' }}">
                    @error('judul_kegiatan') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="keterangan">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            {{-- ===== DATA PENERIMA ===== --}}
            <div class="form-section">
                <div class="section-title" id="label-penerima">
                    <i class="fa-solid fa-location-dot" id="icon-penerima"></i>
                    <span id="text-penerima">Data Penerima</span>
                </div>

                {{-- Pre-fill badge untuk mode Mengambil --}}
                <div id="penerima-prefill-badge" style="display:none;">
                    <div class="prefilled-badge">
                        <i class="fa-solid fa-circle-info"></i>
                        Penerima otomatis diisi sebagai LM FEB UI. Lengkapi nama penerima dan alamat.
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Instansi Tujuan<span class="required-mark">*</span></label>
                        <input type="text" name="instansi_penerima" id="instansi_penerima" required
                            value="{{ old('instansi_penerima') }}"
                            class="{{ $errors->has('instansi_penerima') ? 'is-invalid' : '' }}">
                        @error('instansi_penerima') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Nama Penerima<span class="required-mark">*</span></label>
                        <input type="text" name="nama_penerima" id="nama_penerima" required
                            value="{{ old('nama_penerima') }}"
                            class="{{ $errors->has('nama_penerima') ? 'is-invalid' : '' }}">
                        @error('nama_penerima') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Email Penerima</label>
                        <input type="email" name="email_penerima" id="email_penerima" placeholder="email@tujuan.com"
                            value="{{ old('email_penerima') }}"
                            class="{{ $errors->has('email_penerima') ? 'is-invalid' : '' }}">
                        @error('email_penerima') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>No HP Penerima</label>
                        <input type="text" name="no_hp_penerima" placeholder="08xxxxxxxxxx" inputmode="numeric"
                            value="{{ old('no_hp_penerima') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Tujuan<span class="required-mark">*</span></label>
                    <textarea name="alamat_penerima" id="alamat_penerima" required
                        class="{{ $errors->has('alamat_penerima') ? 'is-invalid' : '' }}">{{ old('alamat_penerima') }}</textarea>
                    @error('alamat_penerima') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Cara Penyerahan: hanya untuk mode Mengantar --}}
                <div class="form-group" id="cara-penyerahan-wrap">
                    <label>Diserahkan Kepada<span class="required-mark">*</span></label>
                    <select name="cara_penyerahan" id="cara_penyerahan"
                        class="{{ $errors->has('cara_penyerahan') ? 'is-invalid' : '' }}">
                        <option value="">-- Pilih penerima dokumen --</option>
                        <option value="langsung" {{ old('cara_penyerahan') === 'langsung' ? 'selected' : '' }}>Langsung ke penerima</option>
                        <option value="resepsionis" {{ old('cara_penyerahan') === 'resepsionis' ? 'selected' : '' }}>Resepsionis</option>
                        <option value="satpam" {{ old('cara_penyerahan') === 'satpam' ? 'selected' : '' }}>Satpam</option>
                        <option value="mailing_box" {{ old('cara_penyerahan') === 'mailing_box' ? 'selected' : '' }}>Mailing Box</option>
                    </select>
                    @error('cara_penyerahan') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" id="btn-submit">Kirim</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        // ===== REAL FIELD NAMES (submitted to server) =====
        // In mengambil mode we redirect the "ext" inputs into these hidden fields at submit
        // Instead we use a different approach: bind real name fields dynamically

        const LMFEBUI_INSTANSI  = 'LM FEB UI';
        const LMFEBUI_NAMA      = '';  // will be filled from pengaju
        const LMFEBUI_EMAIL     = '';  // will be filled from pengaju

        // ===== MODE SWITCH =====
        function setMode(mode) {
            document.getElementById('jenis_ekspedisi').value = mode;

            const btnM  = document.getElementById('btn-mengantar');
            const btnA  = document.getElementById('btn-mengambil');
            btnM.classList.toggle('active', mode === 'mengantar');
            btnA.classList.toggle('active', mode === 'mengambil');

            const isMengantar = mode === 'mengantar';

            // pengirim sections
            document.getElementById('pengirim-mengantar').style.display = isMengantar ? '' : 'none';
            document.getElementById('pengirim-mengambil').style.display = isMengantar ? 'none' : '';

            // required attributes for pengirim fields
            const namaPengirimInt   = document.getElementById('nama_pengirim_int');
            const divisiPengirimInt = document.getElementById('divisi_pengirim_int');
            namaPengirimInt.required   = isMengantar;
            divisiPengirimInt.required = isMengantar;
            namaPengirimInt.name       = isMengantar ? 'nama_pengirim' : '';
            divisiPengirimInt.name     = isMengantar ? 'id_divisi_pengirim' : '';
            document.getElementById('email_pengirim_int').name  = isMengantar ? 'email_pengirim' : '';
            document.getElementById('no_hp_pengirim_int').name  = isMengantar ? 'no_hp_pengirim' : '';

            const namaPengirimExt    = document.getElementById('nama_pengirim_ext');
            const instansiPengirimExt = document.getElementById('instansi_pengirim_ext');
            namaPengirimExt.required    = !isMengantar;
            instansiPengirimExt.required = !isMengantar;
            namaPengirimExt.name         = !isMengantar ? 'nama_pengirim' : '';
            instansiPengirimExt.name     = !isMengantar ? 'instansi_pengirim' : '';
            document.getElementById('email_pengirim_ext').name   = !isMengantar ? 'email_pengirim' : '';
            document.getElementById('no_hp_pengirim_ext').name   = !isMengantar ? 'no_hp_pengirim' : '';
            document.getElementById('alamat_pengirim_ext').name  = !isMengantar ? 'alamat_pengirim' : '';

            // cara penyerahan: only for mengantar
            const caraPenyerahanWrap = document.getElementById('cara-penyerahan-wrap');
            const caraPenyerahan     = document.getElementById('cara_penyerahan');
            caraPenyerahanWrap.style.display = isMengantar ? '' : 'none';
            caraPenyerahan.required  = isMengantar;

            // penerima section label + pre-fill
            document.getElementById('penerima-prefill-badge').style.display = isMengantar ? 'none' : '';
            const instansiPenerima = document.getElementById('instansi_penerima');
            const namaPenerima     = document.getElementById('nama_penerima');
            const emailPenerima    = document.getElementById('email_penerima');
            const alamatPenerima   = document.getElementById('alamat_penerima');

            if (!isMengantar) {
                // pre-fill penerima as LM FEB UI
                if (!instansiPenerima.value || instansiPenerima.dataset.autoFilled === '1') {
                    instansiPenerima.value = LMFEBUI_INSTANSI;
                    instansiPenerima.dataset.autoFilled = '1';
                }
                // sync nama & email from pengaju
                if (!namaPenerima.value || namaPenerima.dataset.autoFilled === '1') {
                    namaPenerima.value = document.getElementById('nama_pengaju').value || '';
                    namaPenerima.dataset.autoFilled = '1';
                }
                if (!emailPenerima.value || emailPenerima.dataset.autoFilled === '1') {
                    emailPenerima.value = document.getElementById('email_pengaju').value || '';
                    emailPenerima.dataset.autoFilled = '1';
                }

                document.getElementById('text-penerima').textContent = 'Data Penerima (LM FEB UI)';
                document.getElementById('text-pengirim').textContent = 'Data Pengirim (Pihak Luar)';
                document.getElementById('icon-pengirim').className   = 'fa-solid fa-building blue';
                document.getElementById('icon-penerima').className   = 'fa-solid fa-location-dot';
                document.getElementById('label-isi-kiriman').textContent = 'Isi Barang / Dokumen yang Diambil';
                document.getElementById('btn-submit').textContent = 'Kirim Permintaan Pengambilan';
            } else {
                document.getElementById('text-penerima').textContent = 'Data Penerima';
                document.getElementById('text-pengirim').textContent = 'Data Pengirim';
                document.getElementById('icon-pengirim').className   = 'fa-solid fa-building';
                document.getElementById('icon-penerima').className   = 'fa-solid fa-location-dot';
                document.getElementById('label-isi-kiriman').textContent = 'Isi Kiriman';
                document.getElementById('btn-submit').textContent = 'Kirim Ekspedisi';
            }
        }

        // ===== CHECKBOX: SAMA DENGAN PENGAJU =====
        const checkbox = document.getElementById("sameAsPengaju");

        function syncPengirim() {
            if (!checkbox.checked) return;
            document.getElementById("nama_pengirim_int").value   = document.getElementById("nama_pengaju").value;
            document.getElementById("divisi_pengirim_int").value = document.getElementById("divisi_pengaju").value;
            document.getElementById("email_pengirim_int").value  = document.getElementById("email_pengaju").value;
        }

        checkbox.addEventListener("change", function() {
            const namaPengirim   = document.getElementById("nama_pengirim_int");
            const divisiPengirim = document.getElementById("divisi_pengirim_int");
            const emailPengirim  = document.getElementById("email_pengirim_int");

            if (this.checked) {
                syncPengirim();
                namaPengirim.readOnly  = true;
                emailPengirim.readOnly = true;
                divisiPengirim.style.pointerEvents    = 'none';
                divisiPengirim.style.backgroundColor  = '#f1f5f9';
            } else {
                namaPengirim.readOnly  = false;
                emailPengirim.readOnly = false;
                divisiPengirim.style.pointerEvents   = 'auto';
                divisiPengirim.style.backgroundColor = '';
            }
        });

        document.getElementById("nama_pengaju").addEventListener("input", syncPengirim);
        document.getElementById("divisi_pengaju").addEventListener("change", syncPengirim);
        document.getElementById("email_pengaju").addEventListener("input", syncPengirim);

        // ===== ITEMS =====
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
            if (document.querySelectorAll(".item-card").length > 1) {
                btn.closest(".item-card").remove();
            } else {
                alert("Minimal 1 item harus ada");
            }
        }

        // phone number sanitise
        document.addEventListener("input", function(e) {
            if (e.target.name && e.target.name.includes('no_hp')) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            }
        });

        // ===== INIT =====
        (function() {
            const initialMode = document.getElementById('jenis_ekspedisi').value || 'mengantar';
            setMode(initialMode);
        })();
    </script>
</body>
</html>
