@extends('layouts.app')

@section('title', 'Daftar Permintaan Ruangan')

@section('content')
    <style>
        .data-table tbody tr.table-danger td {
            background-color: #ffe5e5 !important;
        }

        .data-table tbody tr.table-warning td {
            background-color: #fff6d6 !important;
        }
    </style>

    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Daftar Peminjaman Ruangan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Peminjaman Ruangan</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('form-peminjaman-ruangan.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Peminjaman
                    </a>

                    <a href="{{ route('peminjaman-ruangan.riwayat') }}" class="btn btn-outline">
                        <i class="fas fa-history"></i> Riwayat
                    </a>

                    <button class="btn btn-outline" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari nama pengaju / divisi..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>

                            <th class="d-none d-md-table-cell">No</th>

                            <th class="filterable d-none d-lg-table-cell" data-key="id">
                                ID
                                <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable" data-key="pengaju">
                                Pengaju
                                <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="divisi">
                                Divisi
                                <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-lg-table-cell" data-key="tanggal_kebutuhan">
                                Tanggal Kebutuhan
                                <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-md-table-cell">
                                Total Sesi
                            </th>

                            <th class="filterable d-none d-lg-table-cell" data-key="status">
                                Status
                                <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable" data-key="decision_status">
                                Status Approval
                                <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($peminjaman as $index => $p)
                            @php
                                $now = now();

                                $detailPertama = $p->details->sortBy('tanggal_mulai')->first();

                                $tanggalMulai = null;

                                if ($detailPertama) {
                                    $tanggalMulai = \Carbon\Carbon::parse(
                                        $detailPertama->tanggal_mulai . ' ' . $detailPertama->jam_mulai,
                                    );
                                }

                                // =========================
                                // KONDISI WARNING
                                // =========================

                                // MERAH:
                                // Hari H sudah mulai tapi status masih belum diproses
                                $isOverdue =
                                    $tanggalMulai && $now->gte($tanggalMulai) && $p->status == 'Belum Diproses';

                                // KUNING:
                                // H-2 jam tapi belum tersedia
                                $isWarning =
                                    $tanggalMulai &&
                                    $now->gte($tanggalMulai->copy()->subHours(2)) &&
                                    in_array($p->status, ['Belum Diproses', 'Sedang Diproses']);

                            @endphp

                            <tr class="
                                    {{ $isOverdue ? 'table-danger' : '' }}
                                    {{ !$isOverdue && $isWarning ? 'table-warning' : '' }}
                                "
                                data-id="{{ strtolower($p->id_peminjaman) }}"
                                data-name="{{ strtolower($p->nama_pengaju . ' ' . $p->divisi->nama_divisi) }}"
                                data-pengaju="{{ strtolower($p->nama_pengaju) }}"
                                data-divisi="{{ strtolower($p->divisi->nama_divisi) }}"
                                data-decision_status="{{ strtolower($p->decision_status) }}"
                                data-status="{{ strtolower($p->status) }}"
                                data-tanggal_kebutuhan="{{ $p->details->count() ? \Carbon\Carbon::parse($p->details->first()->tanggal_mulai)->format('Y-m-d') : '' }}">
                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                                <td class="d-none d-lg-table-cell">
                                    {{ $p->id_peminjaman }}
                                </td>

                                <td>
                                    {{ $p->nama_pengaju }}
                                </td>

                                <td class="d-none d-md-table-cell">
                                    {{ $p->divisi->nama_divisi }}
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @if ($p->details->count())
                                        {{ \Carbon\Carbon::parse($p->details->first()->tanggal_mulai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="d-none d-md-table-cell">
                                    {{ $p->details->count() }} sesi
                                </td>

                                {{-- STATUS OPERASIONAL --}}
                                <td class="d-none d-lg-table-cell">

                                    @if ($p->status == 'Belum Diproses')
                                        <span class="badge bg-secondary">
                                            Belum Diproses
                                        </span>
                                    @elseif($p->status == 'Sedang Diproses')
                                        <span class="badge bg-warning">
                                            Sedang Diproses
                                        </span>
                                    @elseif($p->status == 'Sudah Tersedia')
                                        <span class="badge bg-info">
                                            Sudah Tersedia
                                        </span>
                                    @elseif($p->status == 'Selesai')
                                        <span class="badge bg-success">
                                            Selesai
                                        </span>
                                    @endif

                                </td>

                                {{-- STATUS APPROVAL --}}
                                <td>

                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <span class="badge bg-warning">
                                            Menunggu
                                        </span>
                                    @elseif($p->decision_status == 'disetujui')
                                        <span class="badge bg-success">
                                            Disetujui
                                        </span>
                                    @elseif($p->decision_status == 'ditolak')
                                        <span class="badge bg-danger">
                                            Ditolak
                                        </span>
                                    @endif

                                </td>

                                <td style="white-space:nowrap">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('peminjaman-ruangan.show', $p->id_peminjaman) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <a href="{{ route('peminjaman-ruangan.edit', $p->id_peminjaman) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- DELETE --}}
                                    @if ($p->status == 'Belum Diproses')
                                        <form action="{{ route('peminjaman-ruangan.destroy', $p->id_peminjaman) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus data ini?')">

                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- APPROVE --}}
                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <form action="{{ route('peminjaman-ruangan.approve', $p->id_peminjaman) }}"
                                            method="POST" style="display:inline;">
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Setujui peminjaman ini?')">

                                                Setujui
                                            </button>
                                        </form>
                                    @endif

                                    {{-- REJECT --}}
                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $p->id_peminjaman }}">

                                            Tolak
                                        </button>
                                    @endif

                                    {{-- PROCESS --}}
                                    @if ($p->decision_status == 'disetujui' && $p->status == 'Belum Diproses')
                                        <form action="{{ route('peminjaman-ruangan.process', $p->id_peminjaman) }}"
                                            method="POST" style="display:inline;">
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-warning"
                                                onclick="return confirm('Proses peminjaman ini?')">

                                                Proses
                                            </button>
                                        </form>
                                    @endif

                                    {{-- SUDAH TERSEDIA --}}
                                    @if ($p->status == 'Sedang Diproses')
                                        <form action="{{ route('peminjaman-ruangan.tersedia', $p->id_peminjaman) }}"
                                            method="POST" style="display:inline;">
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-info"
                                                onclick="return confirm('Ruangan & aset sudah tersedia?')">

                                                Sudah Tersedia
                                            </button>
                                        </form>
                                    @endif

                                    {{-- SELESAI --}}
                                    @if ($p->status == 'Sudah Tersedia')
                                        <button type="button" class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#selesaiModal{{ $p->id_peminjaman }}">
                                            Selesai
                                        </button>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada data peminjaman
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="filterPopup" class="excel-filter" style="display:none;">
                    <div class="filter-header">
                        <strong id="filterTitle"></strong>
                    </div>
                    <div id="filterOptions" class="filter-options"></div>
                    <div class="filter-actions">
                        <button id="selectAllFilter" class="btn btn-sm btn-outline">Select All</button>
                        <button id="unselectAllFilter" class="btn btn-sm btn-outline">Clear</button>
                    </div>
                </div>
            </div>

        </div>
        @foreach ($peminjaman as $p)

            {{-- MODAL SELESAI (dengan biaya konsumsi opsional) --}}
            @if ($p->status == 'Sudah Tersedia')
                @php
                    $adaMakanan = $p->konsumsi->whereIn('jenis_konsumsi', ['makanan_ringan', 'makanan_berat'])->count() > 0;
                    $adaKonsumsi = $p->konsumsi->count() > 0;
                @endphp
                <div class="modal fade" id="selesaiModal{{ $p->id_peminjaman }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('peminjaman-ruangan.complete', $p->id_peminjaman) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Selesaikan Peminjaman</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3 p-2 bg-light rounded small">
                                        <div class="fw-semibold">{{ $p->nama_kegiatan ?: $p->jenis_kegiatan ?: 'Peminjaman Ruangan' }}</div>
                                        <div class="text-muted">{{ $p->nama_pengaju }}{{ $p->divisi ? ' — ' . $p->divisi->nama_divisi : '' }}</div>
                                    </div>

                                    @if ($adaKonsumsi)
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.04em">Konsumsi yang Diminta</label>
                                            <ul class="list-unstyled small mb-0">
                                                @foreach ($p->konsumsi as $k)
                                                    <li class="d-flex gap-2 py-1 border-bottom">
                                                        <i class="fas fa-utensils text-danger mt-1" style="font-size:.7rem"></i>
                                                        <span>
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $k->jenis_konsumsi)) }}</strong>
                                                            &times;{{ $k->jumlah }}
                                                            @if ($k->catatan) <span class="text-muted">— {{ $k->catatan }}</span> @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <hr class="my-3">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Pos Anggaran Konsumsi
                                                @if (!$adaMakanan)
                                                    <span class="text-muted fw-normal small">(opsional — hanya air mineral)</span>
                                                @endif
                                            </label>
                                            <select name="rkat_anggaran_id" class="form-select form-select-sm"
                                                {{ $adaMakanan ? 'required' : '' }}>
                                                <option value="">— Tidak dibebankan ke anggaran —</option>
                                                @foreach ($anggaranList as $ag)
                                                    <option value="{{ $ag->id }}">
                                                        [{{ $ag->tahun }}] {{ $ag->kode_kegiatan }} — {{ $ag->nama_kegiatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label fw-semibold">
                                                Total Biaya Konsumsi (Rp)
                                                @if (!$adaMakanan)
                                                    <span class="text-muted fw-normal small">(opsional)</span>
                                                @endif
                                            </label>
                                            <input type="number" name="biaya_konsumsi" class="form-control form-control-sm"
                                                placeholder="Contoh: 150000" min="0" step="500">
                                        </div>
                                    @else
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Tidak ada permintaan konsumsi. Klik <strong>Selesaikan</strong> untuk menutup peminjaman ini.
                                        </p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check me-1"></i> Selesaikan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($p->decision_status == 'menunggu_persetujuan')
                <div class="modal fade" id="rejectModal{{ $p->id_peminjaman }}" tabindex="-1">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">

                            <form method="POST" action="{{ route('peminjaman-ruangan.reject', $p->id_peminjaman) }}">

                                @csrf

                                <div class="modal-header">

                                    <h5 class="modal-title">
                                        Tolak Peminjaman Ruangan
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">

                                        <label class="form-label">
                                            Catatan Penolakan
                                        </label>

                                        <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                        Batal

                                    </button>

                                    <button type="submit" class="btn btn-danger">

                                        Tolak Peminjaman

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        @endforeach

        <div class="mt-3 content-padding">
            {{ $peminjaman->withQueryString()->links() }}
        </div>
    </main>

    <script>
        // Search function (live filter)
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.data-table tbody tr').forEach(row => {
                const name = row.getAttribute('data-name');
                row.style.display = name.includes(query) ? '' : 'none';
            });
        });
        let activeFilters = {};
        let currentKey = null;

        document.querySelectorAll('.filterable').forEach(th => {
            th.addEventListener('click', function() {
                currentKey = this.dataset.key;

                const popup = document.getElementById('filterPopup');
                const rect = this.getBoundingClientRect();
                popup.style.left = rect.left + 'px';
                popup.style.top = rect.bottom + window.scrollY + 'px';
                popup.style.display = 'block';

                document.getElementById('filterTitle').innerText = this.innerText;

                const values = new Set();
                document.querySelectorAll('.data-table tbody tr').forEach(r => {
                    values.add(r.dataset[currentKey] || '');
                });

                const container = document.getElementById('filterOptions');
                container.innerHTML = '';

                values.forEach(v => {
                    const checked = !activeFilters[currentKey] || activeFilters[currentKey]
                        .includes(v);
                    container.innerHTML += `
                    <label>
                        <input type="checkbox" value="${v}" ${checked ? 'checked' : ''}>
                        ${v || '(Kosong)'}
                    </label>
                `;
                });

                container.querySelectorAll('input').forEach(i => {
                    i.onchange = applyExcelFilters;
                });
            });
        });

        function applyExcelFilters() {
            const selected = [];
            document.querySelectorAll('#filterOptions input:checked').forEach(i => {
                selected.push(i.value);
            });

            if (selected.length) {
                activeFilters[currentKey] = selected;
            } else {
                delete activeFilters[currentKey];
            }

            document.querySelectorAll('.data-table tbody tr').forEach(row => {
                let show = true;
                for (const key in activeFilters) {
                    if (!activeFilters[key].includes(row.dataset[key] || '')) {
                        show = false;
                    }
                }
                row.style.display = show ? '' : 'none';
            });

            document.querySelectorAll('.filterable').forEach(th => {
                th.classList.toggle('active-filter', activeFilters[th.dataset.key]);
            });
        }

        document.getElementById('selectAllFilter').onclick = () => {
            document.querySelectorAll('#filterOptions input').forEach(i => i.checked = true);
            applyExcelFilters();
        };

        document.getElementById('unselectAllFilter').onclick = () => {
            document.querySelectorAll('#filterOptions input').forEach(i => i.checked = false);
            delete activeFilters[currentKey];
            applyExcelFilters();
        };

        document.addEventListener('click', e => {
            if (!e.target.closest('.filterable') && !e.target.closest('#filterPopup')) {
                document.getElementById('filterPopup').style.display = 'none';
            }
        });
    </script>

@endsection
