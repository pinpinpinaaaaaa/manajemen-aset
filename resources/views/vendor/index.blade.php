@extends('layouts.app')

@section('title', 'Data Vendor')

@section('content')

    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Data Vendor</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Vendor</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('vendor.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Vendor
                    </a>
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari perusahaan / bidang..." class="search-input">
                </div>
            </div>

            <div id="tableView" class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>

                            <th class="filterable" data-key="perusahaan">
                                Perusahaan <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="bidang">
                                Bidang Usaha <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-lg-table-cell" data-key="email">
                                Email <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($vendor as $index => $v)
                            <tr data-name="{{ strtolower($v->nama_perusahaan . ' ' . $v->bidang_usaha) }}"
                                data-perusahaan="{{ strtolower($v->nama_perusahaan) }}"
                                data-bidang="{{ strtolower($v->bidang_usaha) }}"
                                data-email="{{ strtolower($v->email_perusahaan) }}"
                                data-tahun="{{ optional($v->submitted_at)->format('Y') }}">
                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                                <td>{{ $v->nama_perusahaan }}</td>
                                <td class="d-none d-md-table-cell">{{ $v->bidang_usaha }}</td>
                                <td class="d-none d-lg-table-cell">{{ $v->email_perusahaan }}</td>
                                <td>
                                    <a href="{{ route('vendor.show', $v) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.edit', $v) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('vendor.destroy', $v) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus vendor ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Tidak ada data vendor
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div id="filterPopup" class="excel-filter" style="display:none;">
                    <strong id="filterTitle"></strong>
                    <div id="filterOptions" class="filter-options"></div>
                    <div class="filter-actions">
                        <button id="selectAllFilter" class="btn btn-sm btn-outline">Select All</button>
                        <button id="unselectAllFilter" class="btn btn-sm btn-outline">Unselect</button>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                {{ $vendor->withQueryString()->links() }}
            </div>
        </div>
    </main>

    <script>
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
           EXCEL FILTER STYLE (SAMA RUANGAN)
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

                // ambil unique value dari table
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

            // highlight icon aktif
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
