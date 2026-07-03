@extends('layouts.app')

@section('title', isset($gedung) ? 'Daftar APAR - ' . $gedung->nama_gedung : 'Semua APAR')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">
                    {{ isset($gedung) ? 'Daftar APAR - ' . $gedung->nama_gedung : 'Semua APAR' }}
                </h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ url('/gedung') }}" class="current">Apar</a>
                    @if (isset($gedung))
                        <span class="separator">/</span>
                        <span class="current">{{ $gedung->nama_gedung }}</span>
                    @endif
                </nav>
            </div>

            @isset($gedung)
                <div class="building-info-card">
                    <div class="building-info-left">
                        <h2 class="building-name">{{ $gedung->nama_gedung }}</h2>
                        <p class="building-location">{{ $gedung->lokasi ?? 'Tidak diketahui' }}</p>
                    </div>
                    <div class="building-info-right">
                        <div class="total-rooms">{{ $stats['total'] }}</div>
                        <div class="total-rooms-label">Total APAR</div>
                    </div>
                </div>
            @endisset

            <div class="stat-cards-grid">
                <x-stat-card label="Total APAR" :value="$stats['total']" />
                <x-stat-card label="Refill" :value="$stats['refill']" />
                <x-stat-card label="Sekali Pakai" :value="$stats['sekali_pakai']" />
                <x-stat-card label="Expired" :value="$stats['expired']" />
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('apar.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add APAR
                    </a>
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                    <a href="{{ route('aset.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>

                    <div class="view-toggle">
                        <button class="btn btn-sm active" id="tableViewBtn" onclick="setViewMode('table')">Table</button>
                        <button class="btn btn-sm btn-outline" id="cardsViewBtn"
                            onclick="setViewMode('cards')">Cards</button>
                    </div>
                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari APAR..." class="search-input">
                </div>
            </div>

            <div id="contentArea">

                <div id="tableView" class="table-container" style="display:block;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">No</th>
                                <th class="d-none d-lg-table-cell">Foto</th>

                                <th class="d-none d-lg-table-cell">ID APAR</th>

                                <th class="filterable" data-key="gedung">
                                    Gedung <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable" data-key="ruangan">
                                    Ruangan <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-md-table-cell" data-key="jenis">
                                    Jenis <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-lg-table-cell" data-key="ukuran">
                                    Ukuran <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-md-table-cell" data-key="refill">
                                    Tanggal Refill <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-md-table-cell" data-key="expired">
                                    Expired <i class="fas fa-filter filter-icon"></i>
                                </th>
                                <th class="d-none d-lg-table-cell">Cek Fisik Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($apar as $index => $a)
                                <tr data-gedung="{{ strtolower($a->gedung->nama_gedung ?? '') }}"
                                    data-ruangan="{{ strtolower($a->ruangan->nama_ruangan ?? '') }}"
                                    data-jenis="{{ strtolower($a->jenis) }}" data-ukuran="{{ strtolower($a->ukuran) }}"
                                    data-refill="{{ $a->tanggal_refill ? \Carbon\Carbon::parse($a->tanggal_refill)->format('Y-m') : '__EMPTY__' }}"
                                    data-expired="{{ $a->expired_date && $a->expired_date < now() ? 'expired' : 'aktif' }}"
                                    data-name="{{ strtolower($a->ruangan->nama_ruangan ?? '') }}">
                                    <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                                    <td class="d-none d-lg-table-cell">
                                        <img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/default-apar.jpg') }}"
                                            alt="Foto APAR" width="60" height="60"
                                            style="object-fit:cover; border-radius:6px;"
                                            onerror="this.src='{{ asset('images/default-apar.jpg') }}'">
                                    </td>

                                    <td class="d-none d-lg-table-cell">{{ $a->id_apar }}</td>
                                    <td>{{ $a->gedung->nama_gedung ?? '-' }}</td>
                                    <td>{{ $a->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ ucfirst($a->jenis) }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $a->ukuran }}</td>
                                    <td class="d-none d-md-table-cell">{{ $a->tanggal_refill ? \Carbon\Carbon::parse($a->tanggal_refill)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $a->expired_date ? \Carbon\Carbon::parse($a->expired_date)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ $a->last_checked_at ? \Carbon\Carbon::parse($a->last_checked_at)->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('apar.show', parameters: $a->id_apar) }}"
                                            class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('apar.edit', $a->id_apar) }}" class="btn btn-sm btn-secondary"><i
                                                class="fas fa-edit"></i></a>

                                        <form action="{{ route('apar.destroy', $a->id_apar) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin hapus APAR ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Belum ada data APAR</td>
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

                <div id="cardsView" class="buildings-grid" style="display:none;">
                    @forelse ($apar as $a)
                        <div class="building-item" data-name="{{ strtolower($a->ruangan->nama_ruangan ?? '') }}">
                            <img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/default-apar.jpg') }}"
                                alt="Foto APAR" class="building-image"
                                onerror="this.src='{{ asset('images/default-apar.jpg') }}'">

                            <div class="building-content">
                                <div
                                    style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <h3 class="building-title">{{ $a->gedung->nama_gedung ?? '-' }}</h3>
                                    <span class="badge badge-active">{{ ucfirst($a->jenis) }}</span>
                                </div>

                                <p class="building-subtitle">Ruangan: {{ $a->ruangan->nama_ruangan ?? '-' }}</p>
                                <p class="building-subtitle">Ukuran: {{ $a->ukuran }}</p>
                                <p class="building-subtitle">Expired:
                                    {{ $a->expired_date ? \Carbon\Carbon::parse($a->expired_date)->format('d M Y') : '-' }}
                                </p>

                                <div class="building-actions">
                                    <a href="{{ route('apar.show', $a->id_apar) }}" class="btn btn-primary">
                                        <i class="fas fa-door-open"></i> Detail
                                    </a>
                                    <a href="{{ route('apar.edit', $a->id_apar) }}" class="btn btn-secondary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('apar.destroy', $a->id_apar) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Hapus APAR ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1;">
                            @include('components.empty', [
                                'title' => 'Belum ada APAR',
                                'message' => 'Tambahkan APAR untuk ditampilkan pada daftar.',
                                'action' => [
                                    'url' => route('apar.create'),
                                    'icon' => 'fas fa-plus',
                                    'label' => 'Tambah APAR',
                                ],
                            ])
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-3">
                {{ $apar->withQueryString()->links() }}
            </div>
        </div>

    </main>

    <script>
        function setViewMode(mode) {
            const cardsView = document.getElementById('cardsView');
            const tableView = document.getElementById('tableView');
            const cardsBtn = document.getElementById('cardsViewBtn');
            const tableBtn = document.getElementById('tableViewBtn');

            if (mode === 'cards') {
                cardsView.style.display = 'grid';
                tableView.style.display = 'none';
                cardsBtn.classList.add('active');
                tableBtn.classList.remove('active');
            } else {
                cardsView.style.display = 'none';
                tableView.style.display = 'block';
                tableBtn.classList.add('active');
                cardsBtn.classList.remove('active');
            }
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#cardsView .building-item, #tableView tbody tr').forEach(el => {
                const name = el.getAttribute('data-name');
                el.style.display = name && name.includes(query) ? '' : 'none';
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

                document.getElementById('filterTitle').innerText =
                    this.innerText.trim();

                const values = new Set();
                document.querySelectorAll('#tableView tbody tr').forEach(r => {
                    values.add(r.dataset[currentKey]);
                });

                const container = document.getElementById('filterOptions');
                container.innerHTML = '';

                values.forEach(v => {
                    const val = v || '__EMPTY__';
                    const checked = !activeFilters[currentKey] || activeFilters[currentKey]
                        .includes(val);

                    container.innerHTML += `
                    <label>
                        <input type="checkbox" value="${val}" ${checked ? 'checked' : ''}>
                        ${v || '(Kosong)'}
                    </label>
                `;
                });

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
                    const raw = row.dataset[key] || '__EMPTY__';
                    if (!activeFilters[key].includes(raw)) {
                        show = false;
                    }
                }
                row.style.display = show ? '' : 'none';
            });

            document.querySelectorAll('.filterable').forEach(th => {
                const key = th.dataset.key;
                th.classList.toggle('active-filter', activeFilters[key]);
            });
        }

        // close popup
        document.addEventListener('click', e => {
            if (!e.target.closest('.filterable') && !e.target.closest('#filterPopup')) {
                document.getElementById('filterPopup').style.display = 'none';
            }
        });

        document.getElementById('selectAllFilter').onclick = () => {
            document.querySelectorAll('#filterOptions input').forEach(i => i.checked = true);
            activeFilters[currentKey] = [...document.querySelectorAll('#filterOptions input')].map(i => i.value);
            applyExcelFilters();
        };

        document.getElementById('unselectAllFilter').onclick = () => {
            document.querySelectorAll('#filterOptions input').forEach(i => i.checked = false);
            delete activeFilters[currentKey];
            applyExcelFilters();
        };
    </script>
@endsection
