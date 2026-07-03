<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | SIMASTER</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #25252b;
            padding: 1rem;
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjB0ZWNofGVufDF8fHx8MTc1ODIwNDQxMHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral');
            background-size: cover;
            background-position: center;
        }

        @property --a { syntax:'<angle>'; inherits:false; initial-value:0deg; }

        .box {
            position: relative;
            width: 800px;
            height: 200px;
            background: repeating-conic-gradient(from var(--a), #ebca56 0%, #ebca56 5%, transparent 5%, transparent 40%, #ebca56 50%);
            filter: drop-shadow(0 15px 50px #000);
            border-radius: 20px;
            animation: rotating 4s linear infinite;
            display: flex; justify-content:center; align-items:center;
            transition: 0.5s;
        }

        @keyframes rotating {
            0% { --a:0deg; }
            100% { --a:360deg; }
        }

        .box::before {
            content:"";
            position:absolute;
            width:100%; height:100%;
            background: repeating-conic-gradient(from var(--a), #ebca56 0%, #ebca56 5%, transparent 5%, transparent 40%, #ebca56 50%);
            filter: drop-shadow(0 15px 50px #000);
            border-radius:20px;
            animation: rotating 4s linear infinite;
            animation-delay:-1s;
        }

        .box::after {
            content:"";
            position:absolute;
            inset:4px;
            background:#2d2d39;
            border-radius:15px;
            border:8px solid #25252b;
        }

        .box:hover { height:600px; }
        .box:hover .content-inner { transform:translateY(0); }

        .content {
            position:absolute;
            inset:60px;
            display:flex; justify-content:center; align-items:center; flex-direction:column;
            border-radius:10px;
            background:rgba(0,0,0,0.2);
            color:#fff;
            z-index:1000;
            transition:0.5s;
            overflow:hidden;
        }

        .content-inner {
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:20px;
            width:100%;
            padding:0 2rem;
            transform:translateY(170px);
            transition:0.5s;
            text-align:center;
        }

        .logo-img { width:256px; margin-bottom:0.5rem; }

        h1 { font-size:2rem; text-transform:uppercase; letter-spacing:0.1em; font-weight:600; }
        .highlight { color:#ebca56; text-shadow:0 0 20px #ebca56; }

        .description { color:rgba(255,255,255,0.9); }

        .menu-grid {
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:1.5rem;
            width:100%;
            max-width:600px;
        }

        .menu-card {
            background:rgba(255,255,255,0.1);
            backdrop-filter:blur(4px);
            padding:1.5rem;
            border:2px solid rgba(235,202,86,0.5);
            border-radius:15px;
            cursor:pointer;
            transition:.3s;
            color:white;
            text-decoration:none;
        }
        .menu-card:hover { background:rgba(235,202,86,0.2); transform:scale(1.05); }

        .icon-box {
            width:56px; height:56px;
            background:#ebca56;
            border-radius:12px;
            margin:0 auto 12px;
            display:flex; justify-content:center; align-items:center;
        }

        .menu-title { font-weight:600; }
        .menu-desc { font-size:0.85rem; opacity:.8; }

        .footer { margin-top:1rem; font-size:.85rem; opacity:.7; }

        @media (max-width:768px) {
            .box {
                width: 92%;
                max-width: 450px;
                /* Auto-expand tanpa hover — hover tidak works di touchscreen */
                height: auto !important;
                min-height: 520px;
            }

            .content {
                inset: 16px;
                padding: 20px 0;
            }

            .content-inner {
                transform: translateY(0) !important;
                padding: 0 1rem;
                gap: 16px;
            }

            .logo-img {
                width: 180px;
            }

            h1 {
                font-size: 1.25rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .menu-card {
                padding: 1rem;
            }

            .icon-box {
                width: 44px;
                height: 44px;
            }
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="content">
            <div class="content-inner">

                <img src="{{ asset('storage/logo_white.png') }}" class="logo-img" alt="Logo SIMASTER">

                <h1>WELCOME TO <span class="highlight">SIMASTER</span></h1>
                <p class="description">Silakan pilih menu untuk melanjutkan</p>

                <div class="menu-grid">

                    <a href="{{ route('login') }}" class="menu-card">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="#000"
                                 stroke-width="2" stroke-linecap="round">
                                <circle cx="14" cy="10" r="4"></circle>
                                <path d="M8 22c0-4 12-4 12 0"></path>
                            </svg>
                        </div>
                        <div class="menu-title">Login Admin</div>
                        <div class="menu-desc">Masuk ke dashboard</div>
                    </a>

                    <a href="{{ route('menu.form') }}" class="menu-card">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                 fill="none" stroke="#000" stroke-width="2" stroke-linecap="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <line x1="9" y1="13" x2="19" y2="13"></line>
                                <line x1="9" y1="17" x2="19" y2="17"></line>
                            </svg>
                        </div>
                        <div class="menu-title">Menu Formulir</div>
                        <div class="menu-desc">Isi form layanan publik</div>
                    </a>

                </div>

                <div class="footer">
                    © 2025 Sistem Informasi Umum Manajemen Aset
                </div>

            </div>
        </div>
    </div>
</body>
</html>
