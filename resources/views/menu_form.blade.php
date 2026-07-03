<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Sistem Formulir Digital</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;

            /* BACKGROUND SAMA DENGAN LOGIN */
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjB0ZWNofGVufDF8fHx8MTc1ODIwNDQxMHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            min-height: 100vh;
            position: relative;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        /* CARD TAMPILAN SAMA DENGAN LOGIN */
        .welcome-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 900px;
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(235, 202, 86, 0.3);
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2.5rem;
            text-align: center;
        }

        .logo-img {
            max-width: 260px;
            margin-bottom: 1rem;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #1E2226;
            margin-bottom: .5rem;
        }

        .description {
            font-size: 1rem;
            color: #1E2226;
            opacity: .9;
            margin-bottom: 2rem;
        }

        /* GRID MENU */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(235, 202, 86, 0.3);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            cursor: pointer;
            transition: .25s;
            backdrop-filter: blur(6px);
        }

        .menu-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: #ebca56;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .icon-box svg {
            width: 28px;
            height: 28px;
            display: block;
            margin: auto;
        }


        .menu-title {
            font-size: 1rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 6px;
        }

        .menu-desc {
            font-size: .875rem;
            color: #333;
        }

        .footer {
            margin-top: 2rem;
            font-size: .875rem;
            color: #111;
        }

        /* Tombol back */
        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 20;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(235, 202, 86, 0.4);
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            transition: .2s;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body>

    <div class="overlay"></div>

    <div class="welcome-card">
        <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-img">

        <h1 class="title">WELCOME TO <span style="color:#ebca56;">SIMASTER</span></h1>
        <p class="description">Silakan pilih menu untuk melanjutkan</p>

        <div class="menu-grid">

            <div class="menu-card" onclick="window.location.href='{{ route('form-permintaan-barang.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#000"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 2h6v4H9z" />
                        <path d="M9 2v4" />
                        <path d="M15 2v4" />
                        <rect x="5" y="4" width="14" height="18" rx="2" />
                        <line x1="9" y1="10" x2="15" y2="10" />
                        <line x1="9" y1="14" x2="15" y2="14" />
                    </svg>
                </div>
                <div class="menu-title">Permintaan Barang Gudang</div>
                <div class="menu-desc">Permintaan kebutuhan Barang Gudang</div>
            </div>

            <div class="menu-card" onclick="window.location.href='{{ route('form-pengadaan-barang.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#000"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.6 13.4a2 2 0 0 0 2 1.6h9.4a2 2 0 0 0 2-1.6L23 6H6" />
                    </svg>
                </div>
                <div class="menu-title">Pengadaan Barang & Jasa</div>
                <div class="menu-desc">Pengajuan kebutuhan Sarpras dan Jasa</div>
            </div>

            <div class="menu-card" onclick="window.location.href='{{ route('form_peminjaman_aset.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7h-3l-2-3h-6l-2 3h-3v13h16z" />
                        <path d="M9 13l2 2 4-4" />
                    </svg>
                </div>
                <div class="menu-title">Peminjaman Aset Kantor</div>
                <div class="menu-desc">Pengajuan peminjaman aset kantor</div>
            </div>

            <div class="menu-card" onclick="window.location.href='{{ route('form-peminjaman-ruangan.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 4h3a2 2 0 0 1 2 2v14" />
                        <path d="M2 20h3" />
                        <path d="M13 20h9" />
                        <path d="M10 12v.01" />
                        <path d="M13 4.562v16.157a1 1 0 0 1-1.242.97L5 20V5.562a2 2 0 0 1 1.515-1.94l4-1" />
                    </svg>
                </div>
                <div class="menu-title">Peminjaman Ruangan</div>
                <div class="menu-desc">Booking ruang rapat & kegiatan</div>
            </div>

            <div class="menu-card" onclick="window.location.href='{{ route('form-permintaan-kendaraan.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M19 17h2v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5L3.6 10" />
                        <circle cx="7" cy="17" r="2" />
                        <circle cx="17" cy="17" r="2" />
                    </svg>
                </div>
                <div class="menu-title">Permintaan Kendaraan</div>
                <div class="menu-desc">Reservasi mobil operasional</div>
            </div>

            <div class="menu-card" onclick="window.location.href='{{ route('form-ekspedisi.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="#000" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 7h13v10H3z" />
                        <path d="M16 11h4l3 3v3h-7V11z" />
                        <circle cx="7" cy="19" r="2" />
                        <circle cx="17" cy="19" r="2" />
                        <path d="M1 11h4" />
                        <path d="M1 15h3" />
                    </svg>
                </div>
                <div class="menu-title">Ekspedisi</div>
                <div class="menu-desc">Pengiriman dokumen</div>
            </div>

            <div class="menu-card" onclick="window.location.href='{{ route('form-pengaduan-kerusakan.create') }}'">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="#000" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <div class="menu-title">Pengaduan Kerusakan</div>
                <div class="menu-desc">Laporan fasilitas bermasalah</div>
            </div>

            <div class="menu-card">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="#000" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
                <div class="menu-title">Cek Status Layanan</div>
                <div class="menu-desc">Pantau progres permohonan Anda</div>
            </div>


        </div>

        <div class="footer">
            © 2025 Sistem Informasi Umum Manajemen Aset
        </div>

    </div>

</body>

</html>
