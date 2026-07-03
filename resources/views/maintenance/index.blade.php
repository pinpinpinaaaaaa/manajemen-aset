@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Pemeliharaan Berjalan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Pemeliharaan Aset</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('maintenance.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ajukan Pemeliharaan Aset
                    </a>
                    <a href="{{ route('maintenance.laporan') }}" class="btn btn-outline">
                        <i class="fas fa-clipboard-list"></i> Riwayat
                    </a>
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>
                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari aset / gedung..." class="search-input">
                </div>
            </div>

            <div id="tableView" class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>
                            <th class="filterable" data-key="aset">Aset <i class="fas fa-filter filter-icon"></i></th>
                            <th class="filterable d-none d-md-table-cell" data-key="gedung">Gedung <i class="fas fa-filter filter-icon"></i></th>
                            <th class="filterable d-none d-md-table-cell" data-key="ruangan">Ruangan <i class="fas fa-filter filter-icon"></i></th>
                            <th class="filterable d-none d-lg-table-cell" data-key="status">Status <i class="fas fa-filter filter-icon"></i></th>
                            <th>Approval</th>
                            <th class="filterable d-none d-lg-table-cell" data-key="tanggal">Tanggal Laporan <i
                                    class="fas fa-filter filter-icon"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($maintenance as $index => $m)
                            @php
                                $namaAset = $m->details->pluck('aset.nama_aset')->filter()->implode(', ');

                                $statusList = $m->details->pluck('status')->unique()->implode(', ');
                            @endphp

                            <tr data-name="{{ strtolower($namaAset . ' ' . $statusList) }}"
                                data-aset="{{ strtolower($namaAset) }}" data-status="{{ strtolower($statusList) }}"
                                data-tanggal="{{ $m->tanggal_laporan?->format('Y-m-d') }}">

                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $m->id_maintenance }}</td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($m->details as $detail)
                                            <li>{{ $detail->aset->nama_aset ?? '-' }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ $m->details->pluck('aset.ruangan.gedung.nama_gedung')->filter()->unique()->implode(', ') }}
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ $m->details->pluck('aset.ruangan.nama_ruangan')->filter()->unique()->implode(', ') }}
                                </td>

                                {{-- STATUS --}}
                                <td class="d-none d-lg-table-cell">
                                    @foreach ($m->details as $detail)
                                        @php
                                            $color = match ($detail->status) {
                                                'Perlu Perbaikan' => 'danger',
                                                'Sedang Diperbaiki' => 'warning',
                                                'Selesai' => 'success',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge bg-{{ $color }}">
                                            {{ $detail->status }}
                                        </span>
                                    @endforeach
                                </td>

                                {{-- APPROVAL --}}
                                <td>
                                    @php
                                        $approval = match ($m->decision_status) {
                                            'menunggu_persetujuan' => 'secondary',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $approval }}">
                                        {{ ucfirst(str_replace('_', ' ', $m->decision_status)) }}
                                    </span>
                                </td>

                                <td class="d-none d-lg-table-cell">{{ $m->tanggal_laporan ? \Carbon\Carbon::parse($m->tanggal_laporan)->format('d M Y') : '-' }}
                                </td>

                                {{-- AKSI --}}
                                <td style="white-space:nowrap;">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('maintenance.show', $m->id_maintenance) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('maintenance.edit', $m->id_maintenance) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- DELETE --}}
                                    @if ($m->decision_status == 'menunggu_persetujuan')
                                        <form action="{{ route('maintenance.destroy', $m->id_maintenance) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Hapus maintenance ini?')">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif

                                    {{-- ================= WORKFLOW BUTTON ================= --}}

                                    {{-- APPROVE / REJECT --}}
                                    @if ($m->decision_status == 'menunggu_persetujuan')
                                        <form action="{{ route('maintenance.approve', $m->id_maintenance) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Setujui</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger btnReject"
                                            data-url="{{ route('maintenance.reject', $m->id_maintenance) }}">
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- MULAI PERBAIKAN --}}
                                    @if ($m->decision_status == 'disetujui' && $m->status == 'Perlu Perbaikan')
                                        <form action="{{ route('maintenance.mulai', $m->id_maintenance) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-info">Mulai</button>
                                        </form>
                                    @endif


                                    {{-- SELESAI --}}
                                    @if ($m->status == 'Sedang Diperbaiki')
                                        <button type="button" class="btn btn-sm btn-dark btnSelesai"
                                            data-url="{{ route('maintenance.selesai', $m->id_maintenance) }}">
                                            Selesai
                                        </button>
                                    @endif

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Tidak ada maintenance berjalan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- FILTER POPUP --}}
                <div id="filterPopup" class="excel-filter" style="display:none;">
                    <strong id="filterTitle"></strong>
                    <div id="filterOptions" class="filter-options"></div>
                    <div class="filter-actions">
                        <button id="selectAllFilter" class="btn btn-sm btn-outline">Select All</button>
                        <button id="unselectAllFilter" class="btn btn-sm btn-outline">Unselect</button>
                    </div>
                </div>

            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST" id="rejectForm">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Alasan Penolakan Maintenance
                            </h5>
                        </div>

                        <div class="modal-body">

                            <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-danger">
                                Tolak Maintenance
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
        <div class="modal fade" id="selesaiModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST" id="selesaiForm" enctype="multipart/form-data">

                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Selesaikan Maintenance
                            </h5>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label>Biaya Maintenance</label>

                                <input type="text" id="biaya_display" class="form-control" placeholder="Rp 0">

                                <input type="hidden" id="biaya" name="biaya">
                            </div>

                            <div class="mb-3">
                                <label>Pelaksana</label>
                                <select name="pelaksana_type" id="pelaksana_type" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="internal">Internal</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>

                            <div class="mb-3" id="vendorArea" style="display:none">

                                <label>Vendor</label>

                                <select name="id_vendor" id="id_vendor" class="form-control">

                                    <option value="">
                                        -- Pilih Vendor --
                                    </option>

                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id_vendor }}">
                                            {{ $v->nama_perusahaan }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="mb-3">
                                <label>Foto Setelah Perbaikan</label>
                                <input type="file" name="foto_after" class="form-control" accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label>Catatan</label>
                                <textarea name="catatan" class="form-control" rows="3"></textarea>
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-success">
                                Selesaikan
                            </button>

                        </div>

                    </form>

                </div>
            </div>

            <div class="mt-3">
                {{ $maintenance->withQueryString()->links() }}
            </div>
        </div>
    </main>

    <script>
        const biayaDisplay = document.getElementById('biaya_display');
        const biayaHidden = document.getElementById('biaya');

        biayaDisplay.addEventListener('input', function() {

            let angka = this.value.replace(/\D/g, '');

            biayaHidden.value = angka;

            if (angka === '') {
                this.value = '';
                return;
            }

            this.value = 'Rp ' + Number(angka)
                .toLocaleString('id-ID');
        });
        document.querySelectorAll('.btnReject').forEach(btn => {
            btn.addEventListener('click', function() {

                document.getElementById('rejectForm').action =
                    this.dataset.url;

                new bootstrap.Modal(
                    document.getElementById('rejectModal')
                ).show();
            });
        });

        document.querySelectorAll('.btnSelesai').forEach(btn => {

            btn.addEventListener('click', function() {

                document.getElementById('selesaiForm').action =
                    this.dataset.url;

                new bootstrap.Modal(
                    document.getElementById('selesaiModal')
                ).show();

            });

        });

        document.getElementById('pelaksana_type')
            .addEventListener('change', function() {

                document.getElementById('vendorArea').style.display =
                    this.value === 'vendor' ?
                    '' :
                    'none';

                document.getElementById('id_vendor').required =
                    this.value === 'vendor';

            });

        /* ================= SEARCH ================= */
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();

            document.querySelectorAll('#tableView tbody tr').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });


        /* ================= FILTER SYSTEM (SAMA SEPERTI RUANGAN) ================= */

        let activeFilters = {};
        let currentKey = null;

        document.querySelectorAll('.filterable').forEach(th => {
            th.addEventListener('click', function() {

                const key = this.dataset.key;
                currentKey = key;

                const popup = document.getElementById('filterPopup');
                const rect = this.getBoundingClientRect();

                popup.style.left = rect.left + 'px';
                popup.style.top = rect.bottom + window.scrollY + 'px';
                popup.style.display = 'block';

                document.getElementById('filterTitle').innerText =
                    this.innerText.trim();

                // Ambil unique values dari tabel
                const values = new Set();
                document.querySelectorAll('#tableView tbody tr').forEach(r => {
                    values.add(r.dataset[key] || '');
                });

                const container = document.getElementById('filterOptions');
                container.innerHTML = '';

                values.forEach(v => {
                    const val = v === '' ? '__EMPTY__' : v;
                    const checked = !activeFilters[key] || activeFilters[key].includes(val);

                    container.innerHTML += `
                    <label>
                        <input type="checkbox" value="${val}" ${checked ? 'checked' : ''}>
                        ${v || '(Kosong)'}
                    </label>
                `;
                });

                // Auto apply saat checkbox berubah
                container.querySelectorAll('input').forEach(i => {
                    i.onchange = () => {
                        const selected = [];

                        container.querySelectorAll('input:checked').forEach(c => {
                            selected.push(c.value);
                        });

                        if (selected.length) {
                            activeFilters[currentKey] = selected;
                        } else {
                            delete activeFilters[currentKey];
                        }

                        applyExcelFilters();
                    };
                });

            });
        });


        function applyExcelFilters() {
            document.querySelectorAll('#tableView tbody tr').forEach(row => {
                let show = true;

                for (const key in activeFilters) {
                    const raw = row.dataset[key] || '';
                    const v = raw === '' ? '__EMPTY__' : raw;

                    if (!activeFilters[key].includes(v)) {
                        show = false;
                    }
                }

                row.style.display = show ? '' : 'none';
            });

            // Tandai header yang aktif filter
            document.querySelectorAll('.filterable').forEach(th => {
                const key = th.dataset.key;

                if (activeFilters[key] && activeFilters[key].length) {
                    th.classList.add('active-filter');
                } else {
                    th.classList.remove('active-filter');
                }
            });
        }


        /* ================= CLOSE POPUP ================= */

        document.addEventListener('click', e => {
            if (!e.target.closest('.filterable') &&
                !e.target.closest('#filterPopup')) {
                document.getElementById('filterPopup').style.display = 'none';
            }
        });


        /* ================= SELECT ALL ================= */

        document.getElementById('selectAllFilter').onclick = () => {

            document.querySelectorAll('#filterOptions input')
                .forEach(i => i.checked = true);

            const selected = [];

            document.querySelectorAll('#filterOptions input')
                .forEach(i => selected.push(i.value));

            activeFilters[currentKey] = selected;

            applyExcelFilters();
        };


        /* ================= UNSELECT ALL ================= */

        document.getElementById('unselectAllFilter').onclick = () => {

            document.querySelectorAll('#filterOptions input')
                .forEach(i => i.checked = false);

            delete activeFilters[currentKey];

            applyExcelFilters();
        };
    </script>

@endsection
