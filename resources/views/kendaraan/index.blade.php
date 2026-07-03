@extends('layouts.app')

@section('title', 'Daftar Kendaraan')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Daftar Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Kendaraan</span>
                </nav>
            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Total Kendaraan" :value="$stats['total']" />
                <x-stat-card label="Kendaraan Aktif" :value="$stats['aktif']" />
                <x-stat-card label="Perlu Perbaikan" :value="$stats['perbaikan']" />
                <x-stat-card label="Non Aktif" :value="$stats['non_aktif']" />
            </div>


            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('kendaraan.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Kendaraan
                    </a>
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>
                    <a href="{{ route('aset.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Data Aset
                    </a>

                    <div class="view-toggle">
                        <button class="btn btn-sm active" id="cardsViewBtn" onclick="setViewMode('cards')">Cards</button>
                        <button class="btn btn-sm btn-outline" id="tableViewBtn"
                            onclick="setViewMode('table')">Table</button>
                    </div>
                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari kendaraan..." class="search-input">
                </div>
            </div>

            <div id="contentArea">

                <div id="cardsView" class="buildings-grid">
                    @forelse ($kendaraan as $k)
                        <div class="building-item"
                            data-name="{{ strtolower($k->plat_nomor . ' ' . $k->jenis_kendaraan . ' ' . $k->tipe) }}">
                            <img src="{{ $k->foto ? asset('storage/' . $k->foto) : asset('images/default-car.jpg') }}"
                                alt="{{ $k->plat_nomor }}" class="building-image"
                                onerror="this.src='{{ asset('images/default-car.jpg') }}'">

                            <div class="building-content">

                                {{-- Badge kondisi kendaraan --}}
                                @php
                                    $badgeClass = match ($k->status_kondisi) {
                                        'aktif' => 'badge badge-active',
                                        'perbaikan' => 'badge badge-warning',
                                        'non aktif' => 'badge badge-danger',
                                        default => 'badge badge-secondary',
                                    };
                                @endphp

                                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                    <h3 class="building-title">{{ strtoupper($k->plat_nomor) }}</h3>
                                    <span class="{{ $badgeClass }}">{{ ucfirst($k->status_kondisi) }}</span>
                                </div>

                                {{-- Detail --}}
                                <p class="building-subtitle">
                                    Driver:
                                    @if ($k->driver)
                                        {{ $k->driver->name }}
                                    @else
                                        <span class="text-muted">Belum ditetapkan</span>
                                    @endif
                                </p>


                                {{-- Action --}}
                                <div class="building-actions">
                                    <a href="{{ route('kendaraan.show', $k->id_kendaraan) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('kendaraan.edit', $k->id_kendaraan) }}" class="btn btn-secondary"><i
                                            class="fas fa-edit"></i></a>
                                    <form action="{{ route('kendaraan.destroy', $k->id_kendaraan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus kendaraan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1;">
                            @include('components.empty', [
                                'title' => 'Belum ada kendaraan',
                                'message' => 'Tambahkan kendaraan untuk ditampilkan pada daftar.',
                                'action' => [
                                    'url' => route('kendaraan.create'),
                                    'icon' => 'fas fa-plus',
                                    'label' => 'Tambah Kendaraan',
                                ],
                            ])
                        </div>
                    @endforelse
                </div>

                <div id="tableView" class="table-container" style="display:none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">No</th>
                                <th class="d-none d-lg-table-cell">Foto</th>

                                <th class="filterable" data-key="plat">
                                    Plat Nomor <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-md-table-cell" data-key="jenis">
                                    Jenis <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-md-table-cell" data-key="tipe">
                                    Tipe <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-lg-table-cell" data-key="tahun">
                                    Tahun <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-lg-table-cell" data-key="umur">
                                    Umur <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable" data-key="kondisi">
                                    Kondisi <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-lg-table-cell" data-key="penggunaan">
                                    Penggunaan <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th class="filterable d-none d-lg-table-cell" data-key="driver">
                                    Driver <i class="fas fa-filter filter-icon"></i>
                                </th>

                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kendaraan as $index => $k)
                                <tr data-name="{{ strtolower($k->plat_nomor . ' ' . $k->jenis_kendaraan . ' ' . $k->tipe) }}"
                                    data-plat="{{ strtolower($k->plat_nomor) }}"
                                    data-jenis="{{ strtolower($k->jenis_kendaraan) }}"
                                    data-tipe="{{ strtolower($k->tipe) }}" data-tahun="{{ $k->tahun_pembelian }}"
                                    data-umur="{{ $k->umur_ekonomis }}"
                                    data-kondisi="{{ strtolower($k->status_kondisi) }}"
                                    data-penggunaan="{{ strtolower($k->status_penggunaan) }}"
                                    data-driver="{{ strtolower(optional($k->driver)->name) }}">
                                    <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        <img src="{{ $k->foto ? asset('storage/' . $k->foto) : asset('images/default-car.jpg') }}"
                                            alt="Foto {{ $k->plat_nomor }}" width="60" height="40"
                                            style="object-fit:cover; border-radius:6px;"
                                            onerror="this.src='{{ asset('images/default-car.jpg') }}'">
                                    </td>
                                    <td>{{ strtoupper($k->plat_nomor) }}</td>
                                    <td class="d-none d-md-table-cell">{{ ucfirst($k->jenis_kendaraan) }}</td>
                                    <td class="d-none d-md-table-cell">{{ ucfirst($k->tipe) }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $k->tahun_pembelian }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $k->umur_ekonomis }} tahun</td>
                                    <td>
                                        <span
                                            class="badge
                                        {{ $k->status_kondisi == 'aktif'
                                            ? 'badge-active'
                                            : ($k->status_kondisi == 'perbaikan'
                                                ? 'badge-warning'
                                                : 'badge-danger') }}">
                                            {{ ucfirst($k->status_kondisi) }}
                                        </span>
                                    </td>

                                    <td class="d-none d-lg-table-cell">
                                        <span
                                            class="badge
                                        {{ $k->status_penggunaan == 'tersedia' ? 'badge-active' : 'badge-danger' }}">
                                            {{ ucfirst($k->status_penggunaan) }}
                                        </span>
                                    </td>

                                    <td class="d-none d-lg-table-cell">
                                        @if ($k->driver)
                                            {{ $k->driver->name }}
                                            <small class="text-muted d-block">{{ $k->driver->email }}</small>
                                        @else
                                            <span class="text-muted">Belum ada</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('kendaraan.show', $k->id_kendaraan) }}"
                                            class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('kendaraan.edit', $k->id_kendaraan) }}"
                                            class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('kendaraan.destroy', $k->id_kendaraan) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus aset?')"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
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

            <div class="mt-3">
                {{ $kendaraan->withQueryString()->links() }}
            </div>
        </div>
    </main>

    <script>
        function setViewMode(mode) {
            const cards = document.getElementById('cardsView');
            const table = document.getElementById('tableView');
            const cardsBtn = document.getElementById('cardsViewBtn');
            const tableBtn = document.getElementById('tableViewBtn');

            if (mode === 'cards') {
                cards.style.display = 'grid';
                table.style.display = 'none';
                cardsBtn.classList.add('active');
                tableBtn.classList.remove('active');
            } else {
                cards.style.display = 'none';
                table.style.display = 'block';
                tableBtn.classList.add('active');
                cardsBtn.classList.remove('active');
            }
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#cardsView .building-item, #tableView tbody tr').forEach(el => {
                const name = el.getAttribute('data-name');
                el.style.display = name && name.includes(q) ? '' : 'none';
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
                    this.innerText.replace(/\s*$/, '');

                const values = new Set();
                document.querySelectorAll('#tableView tbody tr').forEach(row => {
                    values.add(row.dataset[currentKey]);
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
                    const raw = row.dataset[key] || '';
                    const val = raw || '__EMPTY__';

                    if (!activeFilters[key].includes(val)) {
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
