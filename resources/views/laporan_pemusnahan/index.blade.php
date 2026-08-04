@extends('layouts.app')

@section('title', 'Pemusnahan Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Pemusnahan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Pemusnahan Aset</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('laporan_pemusnahan.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ajukan Pemusnahan Aset
                    </a>
                    <a href="{{ route('laporan_pemusnahan.laporan') }}" class="btn btn-outline">
                        <i class="fas fa-clipboard-list"></i> Riwayat
                    </a>
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari aset atau metode..." class="search-input">
                </div>
            </div>

            <div id="contentArea">
                <div id="tableView" class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">No</th>

                                <th class="d-none d-lg-table-cell">ID Pemusnahan</th>

                                <th class="filterable" data-key="aset">
                                    Nama Aset <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-md-table-cell" data-key="metode">
                                    Metode <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-lg-table-cell" data-key="tanggal">
                                    Tanggal Pemusnahan <i class="fas fa-filter filter-icon"></i>
                                </th>
                                <th class="d-none d-lg-table-cell">Biaya Keluar</th>
                                <th class="d-none d-lg-table-cell">Nilai Masuk</th>
                                <th class="d-none d-md-table-cell">Status</th>
                                <th>Approval</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pemusnahan as $index => $p)
                                <tr data-name="{{ strtolower(($p->aset->nama_aset ?? '') . ' ' . $p->metode) }}"
                                    data-aset="{{ strtolower($p->aset->nama_aset ?? '') }}"
                                    data-metode="{{ strtolower($p->metode) }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal_pemusnahan)->format('Y-m-d') }}"
                                    data-biaya="{{ $p->biaya }}">
                                    <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $p->id_pemusnahan }}</td>
                                    <td>{{ $p->aset->nama_aset ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ ucfirst($p->metode) }}</td>
                                    <td class="d-none d-lg-table-cell">{{ \Carbon\Carbon::parse($p->tanggal_pemusnahan)->format('d M Y') }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        Rp{{ number_format($p->biaya_keluar, 0, ',', '.') }}
                                    </td>

                                    <td class="d-none d-lg-table-cell">
                                        Rp{{ number_format($p->nilai_masuk, 0, ',', '.') }}
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        @php
                                            $color = match ($p->status) {
                                                'Belum Dimusnahkan' => 'secondary',
                                                'Sedang Dimusnahkan' => 'info',
                                                'Selesai' => 'success',
                                                default => 'dark',
                                            };
                                        @endphp

                                        <span class="badge bg-{{ $color }}">
                                            {{ $p->status ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $acolor = match ($p->decision_status) {
                                                'menunggu_persetujuan' => 'warning',
                                                'disetujui' => 'success',
                                                'ditolak' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge bg-{{ $acolor }}">
                                            {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                                        </span>
                                    </td>

                                    <td style="white-space:nowrap;">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('laporan_pemusnahan.show', $p->id_pemusnahan) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        <a href="{{ route('laporan_pemusnahan.edit', $p->id_pemusnahan) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('laporan_pemusnahan.destroy', $p->id_pemusnahan) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin hapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>


                                        {{-- ================= WORKFLOW BUTTON ================= --}}

                                        {{-- APPROVE / TOLAK --}}
                                        @if ($p->decision_status == 'menunggu_persetujuan')
                                            <form action="{{ route('laporan_pemusnahan.approve', $p->id_pemusnahan) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-sm btn-success">
                                                    Setujui
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-danger btnReject"
                                                data-id="{{ $p->id_pemusnahan }}">
                                                Tolak
                                            </button>
                                        @endif

                                        {{-- PROSES --}}
                                        @if ($p->decision_status == 'disetujui' && $p->status == 'Belum Dimusnahkan')
                                            <form action="{{ route('laporan_pemusnahan.proses', $p->id_pemusnahan) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-sm btn-info">
                                                    Proses
                                                </button>
                                            </form>
                                        @endif


                                        {{-- SELESAI --}}
                                        @if ($p->status == 'Sedang Dimusnahkan')
                                            <button type="button" class="btn btn-sm btn-dark btnSelesai"
                                                data-id="{{ $p->id_pemusnahan }}"
                                                data-pelaksana="{{ $p->pelaksana_type }}"
                                                data-vendor="{{ $p->id_vendor }}" data-metode="{{ $p->metode }}"
                                                data-nilai="{{ $p->nilai_masuk }}">
                                                Selesai
                                            </button>
                                        @endif

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Belum ada laporan pemusnahan.</td>
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
                            <button id="unselectAllFilter" class="btn btn-sm btn-outline">Unselect All</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST" id="rejectForm">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Alasan Penolakan
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
                                Tolak Pengajuan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="selesaiModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form method="POST" id="selesaiForm">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Konfirmasi Pemusnahan
                            </h5>
                        </div>

                        <div class="modal-body">

                            <input type="hidden" name="id_pemusnahan" id="id_pemusnahan">

                            <div class="mb-3">
                                <label>Pelaksana</label>
                                <select name="pelaksana_type" id="pelaksana_type" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="internal">Internal</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>

                            <div class="mb-3" id="vendorArea" style="display:none">
                                <label>Vendor</label>
                                <select name="id_vendor" id="id_vendor" class="form-control">

                                    <option value="">-- Pilih Vendor --</option>

                                    @foreach ($vendors as $v)
                                        <option value="{{ $v->id_vendor }}">
                                            {{ $v->nama_perusahaan }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="mb-3" id="nilaiArea" style="display:none">
                                <label>Nilai Masuk</label>
                                <input type="number" name="nilai_masuk" id="nilai_masuk" class="form-control">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" id="btnSubmitSelesai" class="btn btn-success">
                                Ya, Selesaikan
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <div class="mt-3">
                {{ $pemusnahan->withQueryString()->links() }}
            </div>
        </div>
    </main>

    <script>
        document.querySelectorAll('.btnSelesai').forEach(btn => {

            btn.addEventListener('click', function() {

                const id = this.dataset.id;
                const pelaksana = this.dataset.pelaksana || '';
                const vendor = this.dataset.vendor || '';
                const metode = this.dataset.metode;
                const nilai = this.dataset.nilai || '';

                document.getElementById('selesaiForm').action =
                    `/laporan_pemusnahan/${id}/selesai`;

                document.getElementById('pelaksana_type').value =
                    pelaksana;

                document.getElementById('id_vendor').value =
                    vendor;

                document.getElementById('nilai_masuk').value =
                    nilai;

                if (pelaksana === 'vendor') {
                    document.getElementById('vendorArea').style.display = '';
                } else {
                    document.getElementById('vendorArea').style.display = 'none';
                }

                if (
                    metode === 'Lelang' ||
                    metode === 'Dijual'
                ) {
                    document.getElementById('nilaiArea').style.display = '';
                } else {
                    document.getElementById('nilaiArea').style.display = 'none';
                }

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

            });
        document.querySelectorAll('.btnReject').forEach(btn => {

            btn.addEventListener('click', function() {

                const id = this.dataset.id;

                document.getElementById('rejectForm').action =
                    `/laporan_pemusnahan/${id}/tolak`;

                new bootstrap.Modal(
                    document.getElementById('rejectModal')
                ).show();

            });

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
