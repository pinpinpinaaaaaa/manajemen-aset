@extends('layouts.app')

@section('title')
    {{ request('jenis') == 'sarana'
        ? (isset($gedung)
            ? 'Sarana - ' . $gedung->nama_gedung
            : 'Semua Sarana')
        : (isset($gedung)
            ? 'Daftar Aset - ' . $gedung->nama_gedung
            : 'Semua Aset') }}
@endsection

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">
                    {{ request('jenis') == 'sarana'
                        ? (isset($gedung)
                            ? 'Sarana - ' . $gedung->nama_gedung
                            : 'Semua Sarana')
                        : (isset($gedung)
                            ? 'Daftar Aset - ' . $gedung->nama_gedung
                            : 'Semua Aset') }}
                </h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ url('/aset') }}">Aset</a>

                    @if (request('jenis') == 'sarana')
                        <span class="separator">/</span>
                        <span class="current">Sarana</span>
                    @endif

                    @if (isset($gedung))
                        <span class="separator">/</span>
                        <span class="current">{{ $gedung->nama_gedung }}</span>
                    @endif
                </nav>

            </div>

            <div class="stat-cards-grid">
                <x-stat-card label="Total" :value="$stats['total']" />
                <x-stat-card label="Layak" :value="$stats['layak']" />
                <x-stat-card label="Pemantauan" :value="$stats['pemantauan']" />
                <x-stat-card label="Perlu Perbaikan" :value="$stats['perbaikan']" />
            </div>

            <div class="controls-section">
                <div class="controls-left">
                    <a href="{{ route('aset.create', [
                        'id_gedung' => $gedung->id_gedung ?? null,
                        'jenis' => request('jenis'),
                    ]) }}"
                        class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah
                    </a>


                    <button class="btn btn-outline" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i>
                        Reload</button>

                    <a href="{{ route('aset.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Data Aset
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
                    <input type="text" id="searchInput" placeholder="Cari aset..." class="search-input">
                </div>
            </div>

            <div id="contentArea">

                <div id="tableView" class="table-container" style="display:block;">
                    @php
                        function kelayakanBadge($kelayakan)
                        {
                            return match ($kelayakan) {
                                1, 2 => 'badge-active', // Layak
                                3 => 'badge-warning', // Pemantauan
                                4 => 'badge-danger', // Perbaikan
                                5 => 'badge-dark', // Disposal
                                default => 'badge-secondary',
                            };
                        }

                        function kelayakanLabel($kelayakan)
                        {
                            return match ($kelayakan) {
                                1, 2 => 'Layak',
                                3 => 'Perlu Pemantauan',
                                4 => 'Perlu Perbaikan',
                                5 => 'Pemusnahan',
                                default => '-',
                            };
                        }
                    @endphp

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">No</th>
                                <th class="d-none d-lg-table-cell">Foto</th>
                                <th class="d-none d-lg-table-cell">Kode</th>
                                <th>Nama</th>
                                <th class="filterable" data-key="gedung">Gedung <i class="fas fa-filter filter-icon"></i>
                                </th>
                                <th class="filterable d-none d-md-table-cell" data-key="ruangan">Ruangan <i class="fas fa-filter filter-icon"></i>
                                </th>
                                <th class="filterable d-none d-md-table-cell" data-key="jenis">Jenis <i class="fas fa-filter filter-icon"></i></th>
                                <th class="filterable d-none d-lg-table-cell" data-key="kategori">Kategori <i
                                        class="fas fa-filter filter-icon"></i></th>
                                <th class="filterable d-none d-md-table-cell" data-key="kelayakan">Kelayakan <i
                                        class="fas fa-filter filter-icon"></i></th>
                                <th class="filterable" data-key="status">Status <i class="fas fa-filter filter-icon"></i>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $i => $a)
                                <tr data-name="{{ strtolower($a->nama_aset) }}"
                                    data-gedung="{{ strtolower($a->gedung->nama_gedung ?? '') }}"
                                    data-ruangan="{{ strtolower($a->ruangan->nama_ruangan ?? '') }}"
                                    data-jenis="{{ strtolower($a->jenisBarang->jenis ?? '') }}"
                                    data-kategori="{{ strtolower($a->jenisBarang->kategori ?? '') }}"
                                    data-kelayakan="{{ strtolower($a->keterangan_kelayakan) }}"
                                    data-status="{{ strtolower($a->status) }}">
                                    <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                    <td class="d-none d-lg-table-cell"><img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/default-asset.jpg') }}"
                                            width="60" height="60" style="object-fit:cover; border-radius:6px;"></td>
                                    <td class="d-none d-lg-table-cell">{{ $a->kode_aset }}</td>
                                    <td>{{ $a->nama_aset }}</td>
                                    <td>{{ $a->gedung->nama_gedung ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ $a->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge badge-secondary">
                                            {{ ucfirst($a->jenisBarang->jenis ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ ucfirst($a->jenisBarang->kategori ?? '-') }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ kelayakanBadge($a->kelayakan) }}">
                                            {{ $a->keterangan_kelayakan }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                    {{ $a->status == 'tersedia'
                                        ? 'badge-active'
                                        : ($a->status == 'terpakai'
                                            ? 'badge-secondary'
                                            : ($a->status == 'maintenance'
                                                ? 'badge-warning'
                                                : 'badge-danger')) }}">
                                            {{ ucfirst($a->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('aset.show', $a->id_aset) }}" class="btn btn-sm btn-primary"><i
                                                class="fas fa-eye"></i>Detail</a>
                                        <a href="{{ route('aset.edit', $a->id_aset) }}" class="btn btn-sm btn-secondary"><i
                                                class="fas fa-edit"></i></a>
                                        <form action="{{ route('aset.destroy', $a->id_aset) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus aset?')"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11">
                                        @include('components.empty', [
                                            'title' => 'Belum ada aset',
                                            'message' => 'Tambahkan aset untuk ditampilkan pada daftar.',
                                            'action' => [
                                                'url' => route('aset.create', [
                                                    'id_gedung' => $gedung->id_gedung ?? null,
                                                ]),
                                                'icon' => 'fas fa-plus',
                                                'label' => 'Tambah Aset',
                                            ],
                                        ])
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
                            <button id="unselectAllFilter" class="btn btn-sm btn-outline">Clear</button>
                        </div>
                    </div>
                </div>

                <div id="cardsView" class="buildings-grid" style="display:none;">
                    @forelse ($data as $a)
                        <div class="building-item room-card" data-name="{{ strtolower($a->nama_aset) }}">
                            <img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/default-asset.jpg') }}"
                                alt="{{ $a->nama_aset }}" class="building-image"
                                onerror="this.src='{{ asset('images/default-asset.jpg') }}'">

                            <div class="building-content">

                                <div
                                    style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <h3 class="building-title">{{ $a->nama_aset }}</h3>
                                    <span class="badge {{ kelayakanBadge($a->kelayakan) }}">
                                        {{ $a->keterangan_kelayakan }}
                                    </span>
                                </div>

                                <p class="building-subtitle">Kode: {{ $a->kode_aset }}</p>
                                <p class="building-subtitle">Ruangan: {{ $a->ruangan->nama_ruangan ?? '-' }}</p>
                                <p class="building-subtitle">Gedung: {{ $a->gedung->nama_gedung ?? '-' }}</p>
                                <p class="building-subtitle">
                                    Jenis:
                                    <span class="badge badge-secondary">
                                        {{ ucfirst($a->jenisBarang->jenis ?? '-') }}{{ ucfirst(string: $a->jenis) }}
                                    </span>
                                </p>
                                <p class="building-subtitle">
                                    Status:
                                    <span
                                        class="badge 
                                    {{ $a->status == 'tersedia'
                                        ? 'badge-active'
                                        : ($a->status == 'terpakai'
                                            ? 'badge-secondary'
                                            : ($a->status == 'maintenance'
                                                ? 'badge-warning'
                                                : 'badge-danger')) }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                </p>

                                <div class="building-actions">
                                    <a href="{{ route('aset.show', $a->id_aset) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('aset.edit', $a->id_aset) }}" class="btn btn-secondary"><i
                                            class="fas fa-edit"></i></a>
                                    <form action="{{ route('aset.destroy', $a->id_aset) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus aset ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1;">
                            @include('components.empty', [
                                'title' => 'Belum ada aset',
                                'message' => 'Tambahkan aset untuk ditampilkan pada daftar.',
                                'action' => [
                                    'url' => route('aset.create', ['id_gedung' => $gedung->id_gedung ?? null]),
                                    'icon' => 'fas fa-plus',
                                    'label' => 'Tambah Aset',
                                ],
                            ])
                        </div>
                    @endforelse
                </div>

            </div>

            <div class="mt-3">
                {{ $data->withQueryString()->links() }}
            </div>
        </div>
    </main>

    <script>
        function setViewMode(mode) {
            document.getElementById('tableView').style.display = mode === 'table' ? 'block' : 'none';
            document.getElementById('cardsView').style.display = mode === 'cards' ? 'grid' : 'none';
            document.getElementById('tableViewBtn').classList.toggle('active', mode === 'table');
            document.getElementById('cardsViewBtn').classList.toggle('active', mode === 'cards');
        }
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();

            document.querySelectorAll('#tableView tbody tr').forEach(row => {
                row.style.display =
                    row.dataset.name?.includes(q) ? '' : 'none';
            });

            document.querySelectorAll('#cardsView [data-name]').forEach(card => {
                card.style.display =
                    card.dataset.name?.includes(q) ? '' : 'none';
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
                document.querySelectorAll('#tableView tbody tr').forEach(r => {
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

            document.querySelectorAll('#tableView tbody tr').forEach(row => {
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
