@extends('layouts.app')

@section('title', 'Pemindahan Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Pemindahan Aset</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Pemindahan Aset</span>
                </nav>
            </div>

            <div class="controls-section">

                <div class="controls-left">
                    <a href="{{ route('pemindahan_aset.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ajukan Pemindahan
                    </a>

                    <a href="{{ route('pemindahan_aset.laporan') }}" class="btn btn-outline">
                        <i class="fas fa-clipboard-list"></i> Riwayat
                    </a>

                    <button class="btn btn-outline" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input id="searchInput" class="search-input" placeholder="Cari aset / lokasi...">
                </div>

            </div>


            <div id="tableView" class="table-container">
                <table class="data-table">

                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>

                            <th class="filterable" data-key="aset">
                                Aset <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-lg-table-cell" data-key="asal">
                                Dari <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-lg-table-cell" data-key="tujuan">
                                Ke <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-md-table-cell">Status</th>
                            <th>Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pemindahan as $i => $p)
                            @php
                                $asetList = $p->details->pluck('aset.nama_aset')->filter()->implode(', ');

                                $asalList = $p->details
                                    ->pluck('ruanganAsal.nama_ruangan')
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');

                                $tujuanList = $p->details
                                    ->pluck('ruanganTujuan.nama_ruangan')
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');

                                $status = $p->details->every(fn($d) => $d->status == 'Sudah dipindahkan')
                                    ? 'Sudah dipindahkan'
                                    : 'Belum dipindahkan';
                            @endphp

                            <tr data-name="{{ strtolower($asetList . ' ' . $p->id_pemindahan) }}"
                                data-aset="{{ strtolower($asetList) }}" data-asal="{{ strtolower($asalList) }}"
                                data-tujuan="{{ strtolower($tujuanList) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>

                                <td class="d-none d-lg-table-cell">{{ $p->id_pemindahan }}</td>

                                <td>
                                    @foreach ($p->details as $detail)
                                        <div>
                                            • {{ $detail->aset->nama_aset ?? '-' }}
                                        </div>
                                    @endforeach
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @foreach ($p->details as $detail)
                                        <div>
                                            {{ $detail->ruanganAsal->nama_ruangan ?? '-' }}
                                        </div>
                                    @endforeach
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @foreach ($p->details as $detail)
                                        <div>
                                            {{ $detail->ruanganTujuan->nama_ruangan ?? '-' }}
                                        </div>
                                    @endforeach
                                </td>

                                <td class="d-none d-md-table-cell">
                                    @php
                                        $status = $p->details->every(fn($d) => $d->status == 'Sudah dipindahkan')
                                            ? 'Sudah dipindahkan'
                                            : 'Belum dipindahkan';

                                        $color = $status == 'Sudah dipindahkan' ? 'success' : 'secondary';
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ $status }}
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


                                <td style="white-space:nowrap">

                                    <a href="{{ route('pemindahan_aset.show', $p->id_pemindahan) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('pemindahan_aset.edit', $p->id_pemindahan) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('pemindahan_aset.destroy', $p->id_pemindahan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <form method="POST"
                                            action="{{ route('pemindahan_aset.approve', $p->id_pemindahan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Setujui</button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-danger btnReject"
                                            data-id="{{ $p->id_pemindahan }}">
                                            Tolak
                                        </button>
                                    @endif

                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada data pemindahan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

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

            <div class="mt-3">
                {{ $pemindahan->withQueryString()->links() }}
            </div>
        </div>
    </main>


    <script>
        document.querySelectorAll('.btnReject').forEach(btn => {

            btn.addEventListener('click', function() {

                const id = this.dataset.id;

                document.getElementById('rejectForm').action =
                    `/pemindahan_aset/${id}/reject`;

                new bootstrap.Modal(
                    document.getElementById('rejectModal')
                ).show();

            });

        });
        /* ================================
                       SEARCH
                    ================================ */
        document.getElementById('searchInput').addEventListener('keyup', function() {

            const q = this.value.toLowerCase();

            document.querySelectorAll('#tableView tbody tr').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = name.includes(q) ? '' : 'none';
            });

        });


        /* ================================
           EXCEL STYLE FILTER (SAMA RUANGAN)
        ================================ */
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

                // ambil unique values dari tabel
                const values = new Set();

                document.querySelectorAll('#tableView tbody tr').forEach(r => {
                    values.add(r.dataset[key] || '');
                });

                const container = document.getElementById('filterOptions');
                container.innerHTML = '';

                values.forEach(v => {

                    const val = v === '' ? '__EMPTY__' : v;
                    const checked = !activeFilters[key] ||
                        activeFilters[key].includes(val);

                    container.innerHTML += `
                <label>
                    <input type="checkbox" value="${val}" ${checked ? 'checked' : ''}>
                    ${v || '(Kosong)'}
                </label>
            `;
                });

                // auto apply saat checkbox berubah
                container.querySelectorAll('input').forEach(i => {

                    i.onchange = () => {

                        const selected = [];

                        container.querySelectorAll('input:checked')
                            .forEach(c => selected.push(c.value));

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

            // highlight filter aktif
            document.querySelectorAll('.filterable').forEach(th => {

                const key = th.dataset.key;

                if (activeFilters[key] && activeFilters[key].length) {
                    th.classList.add('active-filter');
                } else {
                    th.classList.remove('active-filter');
                }

            });

        }


        /* ================================
           CLOSE POPUP JIKA KLIK LUAR
        ================================ */
        document.addEventListener('click', e => {

            if (!e.target.closest('.filterable') &&
                !e.target.closest('#filterPopup')) {

                document.getElementById('filterPopup').style.display = 'none';
            }

        });


        /* ================================
           SELECT ALL
        ================================ */
        document.getElementById('selectAllFilter').onclick = () => {

            const inputs = document.querySelectorAll('#filterOptions input');

            inputs.forEach(i => i.checked = true);

            const selected = [];
            inputs.forEach(i => selected.push(i.value));

            activeFilters[currentKey] = selected;

            applyExcelFilters();

        };


        /* ================================
           UNSELECT ALL
        ================================ */
        document.getElementById('unselectAllFilter').onclick = () => {

            document.querySelectorAll('#filterOptions input')
                .forEach(i => i.checked = false);

            delete activeFilters[currentKey];

            applyExcelFilters();

        };
    </script>

@endsection
