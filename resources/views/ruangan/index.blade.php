@extends('layouts.app')

@section('title', isset($gedung) ? 'Daftar Ruangan - ' . $gedung->nama_gedung : 'Semua Ruangan')

@section('content')
    <style>
        .room-carousel {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
        }

        .carousel-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: none;
        }

        .carousel-image.active {
            display: block;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: rgba(0, 0, 0, .4);
            color: white;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
        }

        .carousel-btn.prev {
            left: 10px;
        }

        .carousel-btn.next {
            right: 10px;
        }

        .carousel-btn:hover {
            background: rgba(0, 0, 0, .7);
        }
    </style>
    <main class="main-content">
        <div class="content-padding">
            {{-- Page Header --}}
            <div class="page-header">
                <h1 class="page-title">
                    {{ isset($gedung) ? 'Daftar Ruangan - ' . $gedung->nama_gedung : 'Ruangan' }}
                </h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    @isset($gedung)
                        <a href="{{ url('/gedung') }}">Gedung</a>
                        <span class="separator">/</span>
                        <span class="current">{{ $gedung->nama_gedung }}</span>
                    @else
                        <span class="current">Ruangan</span>
                    @endisset
                </nav>
            </div>

            {{-- Summary Stats --}}
            <div class="stat-cards-grid">
                <x-stat-card label="Total Ruangan" value="0" id="statTotal" />
                <x-stat-card label="Tersedia" value="0" id="statTersedia" />
                <x-stat-card label="Terpakai" value="0" id="statTerpakai" />
                <x-stat-card label="Maintenance" value="0" id="statMaintenance" />
            </div>

            {{-- Controls --}}
            <div class="controls-section">
                <div class="controls-left">
                    @isset($gedung)
                        <a href="{{ route('ruangan.create', ['id_gedung' => $gedung->id_gedung]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Ruangan
                        </a>
                    @else
                        <a href="{{ route('ruangan.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Ruangan
                        </a>
                    @endisset

                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                    <a href="{{ route('aset.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>

                    <div class="view-toggle">
                        <button class="btn btn-sm active" id="cardsViewBtn" onclick="setViewMode('cards')">Cards</button>
                        <button class="btn btn-sm btn-outline" id="tableViewBtn"
                            onclick="setViewMode('table')">Table</button>
                    </div>
                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari ruangan..." class="search-input">
                </div>
            </div>

            {{-- CONTENT --}}
            <div id="contentArea">

                {{-- Cards --}}
                <div id="cardsView" class="buildings-grid">
                    @forelse ($ruangan as $r)
                        <div class="building-item room-card" data-name="{{ strtolower($r->nama_ruangan) }}">
                            @if ($r->gambar->count())
                                <div class="room-carousel" id="carousel-{{ $r->id_ruangan }}">

                                    @foreach ($r->gambar as $index => $gambar)
                                        <img src="{{ asset('storage/' . $gambar->foto) }}"
                                            class="building-image carousel-image {{ $index == 0 ? 'active' : '' }}"
                                            alt="{{ $r->nama_ruangan }}">
                                    @endforeach

                                    @if ($r->gambar->count() > 1)
                                        <button class="carousel-btn prev"
                                            onclick="changeRoomSlide('{{ $r->id_ruangan }}', -1)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>

                                        <button class="carousel-btn next"
                                            onclick="changeRoomSlide('{{ $r->id_ruangan }}', 1)">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    @endif

                                </div>
                            @else
                                <img src="{{ asset('images/default-room.jpg') }}" alt="{{ $r->nama_ruangan }}"
                                    class="building-image">
                            @endif

                            <div class="building-content">
                                <div
                                    style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <h3 class="building-title">{{ $r->nama_ruangan }}</h3>
                                    @php
                                        $badgeClass = match ($r->status) {
                                            'tersedia' => 'badge badge-active',
                                            'terpakai' => 'badge badge-warning',
                                            'maintenance' => 'badge badge-maintenance',
                                            'non aktif' => 'badge badge-inactive',
                                            default => 'badge badge-secondary',
                                        };
                                    @endphp
                                    <span class="{{ $badgeClass }}">{{ ucfirst($r->status) }}</span>
                                </div>

                                <p class="building-subtitle">Lantai: {{ $r->lantai ?? '-' }}</p>
                                <p class="building-subtitle">Jenis: {{ ucfirst($r->kategori) ?? '-' }}</p>
                                <p class="building-subtitle">Gedung: {{ $r->gedung->nama_gedung ?? '-' }}</p>

                                <div class="building-actions">
                                    <a href="{{ route('ruangan.dashboard', $r->id_ruangan) }}" class="btn btn-primary">
                                        <i class="fas fa-door-open"></i> Detail
                                    </a>
                                    <a href="{{ route('ruangan.edit', $r->id_ruangan) }}" class="btn btn-secondary"><i
                                            class="fas fa-edit"></i></a>
                                    <form action="{{ route('ruangan.destroy', $r->id_ruangan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus ruangan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1;">
                            @include('components.empty', [
                                'title' => 'Belum ada ruangan',
                                'message' => 'Tambahkan ruangan untuk ditampilkan pada daftar.',
                                'action' => [
                                    'url' => isset($gedung)
                                        ? route('ruangan.create', ['id_gedung' => $gedung->id_gedung])
                                        : route('ruangan.create'),
                                    'icon' => 'fas fa-plus',
                                    'label' => 'Tambah Ruangan',
                                ],
                            ])
                        </div>
                    @endempty
            </div>

            {{-- Table --}}
            <div id="tableView" class="table-container" style="display:none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">Foto</th>

                            <th class="filterable" data-key="name">
                                Nama Ruangan <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="lantai">
                                Lantai <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="jenis">
                                Jenis <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable" data-key="gedung">
                                Gedung <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable" data-key="status">
                                Status <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ruangan as $index => $r)

                            @php
                                $badgeClass = match ($r->status) {
                                    'tersedia' => 'badge badge-active',
                                    'terpakai' => 'badge badge-warning',
                                    'maintenance' => 'badge badge-maintenance',
                                    'non aktif' => 'badge badge-inactive',
                                    default => 'badge badge-secondary',
                                };
                            @endphp

                            <tr data-name="{{ strtolower($r->nama_ruangan) }}"
                                data-lantai="{{ strtolower($r->lantai ?? '') }}"
                                data-jenis="{{ strtolower($r->kategori ?? '') }}"
                                data-gedung="{{ strtolower($r->gedung->nama_gedung ?? '') }}"
                                data-status="{{ strtolower($r->status) }}">

                                {{-- Nomor --}}
                                <td class="d-none d-md-table-cell">{{ $index + 1 }}</td>

                                {{-- Foto --}}
                                <td class="d-none d-lg-table-cell">
                                    @if ($r->gambar->count())
                                        <img src="{{ asset('storage/' . $r->gambar->first()->foto) }}" width="70"
                                            height="50" style="object-fit:cover; border-radius:6px;"
                                            alt="{{ $r->nama_ruangan }}">
                                    @else
                                        <img src="{{ asset('images/default-room.jpg') }}" width="70"
                                            height="50" style="object-fit:cover; border-radius:6px;"
                                            alt="Default Room">
                                    @endif
                                </td>

                                {{-- Nama --}}
                                <td>{{ $r->nama_ruangan }}</td>

                                {{-- Lantai --}}
                                <td class="d-none d-md-table-cell">{{ $r->lantai ?? '-' }}</td>

                                {{-- Jenis --}}
                                <td class="d-none d-md-table-cell">{{ ucfirst($r->kategori ?? '-') }}</td>

                                {{-- Gedung --}}
                                <td>{{ $r->gedung->nama_gedung ?? '-' }}</td>

                                {{-- Status --}}
                                <td>
                                    <span class="{{ $badgeClass }}">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <a href="{{ route('ruangan.dashboard', $r->id_ruangan) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-door-open"></i>
                                    </a>

                                    <a href="{{ route('ruangan.edit', $r->id_ruangan) }}"
                                        class="btn btn-sm btn-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('ruangan.destroy', $r->id_ruangan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus ruangan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" style="text-align:center; padding:30px;">
                                    Tidak ada data ruangan.
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
                        <button id="unselectAllFilter" class="btn btn-sm btn-outline">Unselect All</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    function updateStats() {
        let total = 0;
        let tersedia = 0;
        let terpakai = 0;
        let maintenance = 0;

        document.querySelectorAll('#tableView tbody tr').forEach(row => {
            if (row.style.display === 'none') return;

            total++;
            const status = row.dataset.status;

            if (status === 'tersedia') tersedia++;
            if (status === 'terpakai') terpakai++;
            if (status === 'maintenance') maintenance++;
        });

        document.getElementById('statTotal').innerText = total;
        document.getElementById('statTersedia').innerText = tersedia;
        document.getElementById('statTerpakai').innerText = terpakai;
        document.getElementById('statMaintenance').innerText = maintenance;
    }

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

    let activeFilters = {};
    let currentKey = null;

    document.querySelectorAll('.filterable').forEach(th => {
        th.addEventListener('click', function(e) {
            const key = this.dataset.key;
            currentKey = key;

            const popup = document.getElementById('filterPopup');
            const rect = this.getBoundingClientRect();
            popup.style.left = rect.left + 'px';
            popup.style.top = rect.bottom + window.scrollY + 'px';
            popup.style.display = 'block';

            document.getElementById('filterTitle').innerText = this.innerText.trim();

            // ambil unique values dari tabel
            const values = new Set();
            document.querySelectorAll('#tableView tbody tr').forEach(r => {
                values.add(r.dataset[key]);
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

            // auto-apply setiap kali checkbox berubah
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

        document.querySelectorAll('.filterable').forEach(th => {
            const key = th.dataset.key;
            if (activeFilters[key] && activeFilters[key].length) {
                th.classList.add('active-filter');
            } else {
                th.classList.remove('active-filter');
            }
        });

        updateStats(); // ← PENTING
    }

    // klik luar nutup popup
    document.addEventListener('click', e => {
        if (!e.target.closest('.filterable') && !e.target.closest('#filterPopup')) {
            document.getElementById('filterPopup').style.display = 'none';
        }
    });

    document.getElementById('selectAllFilter').onclick = () => {
        document.querySelectorAll('#filterOptions input').forEach(i => i.checked = true);

        const selected = [];
        document.querySelectorAll('#filterOptions input').forEach(i => {
            selected.push(i.value);
        });
        activeFilters[currentKey] = selected;
        applyExcelFilters();
    };

    document.getElementById('unselectAllFilter').onclick = () => {
        document.querySelectorAll('#filterOptions input').forEach(i => i.checked = false);
        delete activeFilters[currentKey];
        applyExcelFilters();
    };
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();
    });

    const roomCarouselIndex = {};

    function changeRoomSlide(id, direction) {

        const carousel = document.getElementById('carousel-' + id);

        if (!carousel) return;

        const images = carousel.querySelectorAll('.carousel-image');

        if (!images.length) return;

        if (!(id in roomCarouselIndex)) {
            roomCarouselIndex[id] = 0;
        }

        images[roomCarouselIndex[id]].classList.remove('active');

        roomCarouselIndex[id] =
            (roomCarouselIndex[id] + direction + images.length) %
            images.length;

        images[roomCarouselIndex[id]].classList.add('active');
    }

    document.querySelectorAll('.room-carousel').forEach(carousel => {

        const id = carousel.id.replace('carousel-', '');

        setInterval(() => {
            changeRoomSlide(id, 1);
        }, 3000);

    });
</script>
@endsection
