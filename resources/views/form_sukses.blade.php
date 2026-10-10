<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Berhasil | SIMASTER</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjB0ZWNofGVufDF8fHx8MTc1ODIwNDQxMHww&ixlib=rb-4.1.0&q=80&w=1080');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            position: relative;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }

        .card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 520px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(235, 202, 86, 0.35);
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .check-circle {
            width: 72px;
            height: 72px;
            background: #d1fae5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .check-circle svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1E2226;
            margin-bottom: .375rem;
        }

        .modul-label {
            font-size: .875rem;
            color: #666;
            margin-bottom: 1.75rem;
        }

        .id-section {
            background: #f8f9fa;
            border: 1.5px solid #e5e7eb;
            border-radius: .875rem;
            padding: 1.25rem 1rem;
            margin-bottom: 1.25rem;
        }

        .id-hint {
            font-size: .75rem;
            color: #888;
            margin-bottom: .5rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 600;
        }

        .id-value {
            font-family: 'Courier New', monospace;
            font-size: 1.375rem;
            font-weight: 700;
            color: #1E2226;
            letter-spacing: .05em;
            margin-bottom: .875rem;
        }

        .btn-copy {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            padding: .5rem 1.125rem;
            background: #ebca56;
            color: #1E2226;
            border: none;
            border-radius: .625rem;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }
        .btn-copy:hover { background: #d4b342; }
        .btn-copy.copied { background: #d1fae5; color: #065f46; }

        .info-box {
            background: #fffbeb;
            border: 1px solid rgba(235, 202, 86, 0.5);
            border-radius: .625rem;
            padding: .75rem 1rem;
            font-size: .8125rem;
            color: #7a5f00;
            margin-bottom: 1.75rem;
            text-align: left;
        }

        .info-box svg {
            display: inline;
            vertical-align: middle;
            margin-right: .25rem;
        }

        .actions {
            display: flex;
            gap: .75rem;
            flex-direction: column;
        }

        .btn-primary {
            display: block;
            padding: .75rem;
            background: #1E2226;
            color: #fff;
            border-radius: .75rem;
            font-size: .9375rem;
            font-weight: 600;
            text-decoration: none;
            transition: .2s;
        }
        .btn-primary:hover { background: #374151; }

        .btn-secondary {
            display: block;
            padding: .75rem;
            background: transparent;
            color: #555;
            border: 1.5px solid #d1d5db;
            border-radius: .75rem;
            font-size: .9375rem;
            text-decoration: none;
            transition: .2s;
        }
        .btn-secondary:hover { border-color: #9ca3af; color: #333; }
    </style>
</head>

<body>
    <div class="overlay"></div>

    <div class="card">
        <div class="check-circle">
            <svg fill="none" stroke="#059669" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1>Permohonan Berhasil Dikirim!</h1>
        <p class="modul-label">{{ $modul }}</p>

        <div class="id-section">
            <div class="id-hint">ID Permohonan Anda</div>
            <div class="id-value" id="idValue">{{ $id }}</div>
            <button class="btn-copy" id="btnCopy" onclick="copyId()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
                <span id="copyText">Salin ID</span>
            </button>
        </div>

        <div class="info-box">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Simpan ID ini. Gunakan ID atau email Anda untuk memantau status permohonan kapan saja.
        </div>

        <div class="actions">
            <a href="{{ route('cek-status') }}?cari={{ urlencode($id) }}" class="btn-primary">
                Cek Status Sekarang →
            </a>
            <a href="{{ route('menu.form') }}" class="btn-secondary">
                Kembali ke Menu
            </a>
        </div>
    </div>

    <script>
        function copyId() {
            const id = document.getElementById('idValue').textContent.trim();
            navigator.clipboard.writeText(id).then(() => {
                const btn = document.getElementById('btnCopy');
                const txt = document.getElementById('copyText');
                btn.classList.add('copied');
                txt.textContent = 'Tersalin!';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    txt.textContent = 'Salin ID';
                }, 2000);
            });
        }
    </script>
</body>
</html>
