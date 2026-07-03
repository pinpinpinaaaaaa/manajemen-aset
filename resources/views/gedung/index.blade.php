@extends('layouts.app')

@section('title', 'Manajemen Gedung')

@section('content')
    <style>
        .building-carousel {
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
            <div class="page-header">
                <h1 class="page-title">Gedung</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Gedung</span>
                </nav>
            </div>

            <div class="building-card">
                <div class="building-card-header">
                    <h2>List Gedung</h2>
                </div>
                <div class="building-card-content">
                    <div class="controls-row">
                        <div class="controls-left">
                            <a href="{{ route('gedung.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Gedung
                            </a>
                            <button class="btn btn-outline" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt"></i> Reload
                            </button>
                            <a href="{{ route('aset.export') }}" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Data Aset
                            </a>
                        </div>
                        <div class="controls-right">
                            <x-per-page-selector :perPage="$perPage" />
                            <span class="search-label">Search:</span>
                            <input type="text" class="search-input-small" id="buildingSearch"
                                placeholder="Cari Gedung...">
                        </div>
                    </div>

                    <div class="buildings-grid" id="buildingsGrid">
                        @forelse ($gedung as $building)
                            <div class="building-item" data-name="{{ strtolower($building->nama_gedung) }}">
                                @if ($building->gambar->count())
                                    <div class="building-carousel" id="carousel-{{ $building->id_gedung }}">

                                        @foreach ($building->gambar as $index => $gambar)
                                            <img src="{{ asset('storage/' . $gambar->gambar) }}"
                                                class="building-image carousel-image {{ $index == 0 ? 'active' : '' }}"
                                                alt="{{ $building->nama_gedung }}">
                                        @endforeach

                                        @if ($building->gambar->count() > 1)
                                            <button class="carousel-btn prev"
                                                onclick="changeSlide('{{ $building->id_gedung }}', -1)">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>

                                            <button class="carousel-btn next"
                                                onclick="changeSlide('{{ $building->id_gedung }}', 1)">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        @endif

                                    </div>
                                @else
                                    <img src="{{ asset('images/default-building.jpg') }}" class="building-image">
                                @endif

                                <div class="building-content">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <h3 class="building-title">{{ $building->nama_gedung }}</h3>
                                        @php
                                            $badgeClass = match ($building->status) {
                                                'aktif' => 'badge badge-active',
                                                'maintenance' => 'badge badge-maintenance',
                                                default => 'badge badge-inactive',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ ucfirst($building->status) }}</span>
                                    </div>

                                    <p class="building-subtitle">ID: {{ $building->id_gedung }}</p>

                                    <div class="building-stats">
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $building->total_ruangan ?? 0 }}</span>
                                            <span>Total Ruangan</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $building->ruangan_tersedia ?? 0 }}</span>
                                            <span>Tersedia</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $building->ruangan_maintenance ?? 0 }}</span>
                                            <span>Maintenance</span>
                                        </div>
                                    </div>

                                    <div class="building-actions">
                                        <a href="{{ route('gedung.dashboard', $building->id_gedung) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-door-open"></i> <span class="btn-label">Detail</span>
                                        </a>
                                        <a href="{{ route('gedung.edit', $building->id_gedung) }}"
                                            class="btn btn-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('gedung.destroy', $building->id_gedung) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                title="Delete"onclick="return confirm('Yakin hapus gedung ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1;">
                                @include('components.empty', [
                                    'title' => 'Belum ada gedung',
                                    'message' => 'Tambahkan gedung untuk ditampilkan pada daftar.',
                                    'action' => [
                                        'url' => route('gedung.create'),
                                        'icon' => 'fas fa-plus',
                                        'label' => 'Tambah Gedung',
                                    ],
                                ])
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $gedung->withQueryString()->links() }}
        </div>

    </main>

    <script>
        // Filter pencarian gedung
        document.getElementById('buildingSearch').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.building-item').forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(query) ? '' : 'none';
            });
        });

        const carouselIndex = {};

        function changeSlide(id, direction) {

            const carousel = document.getElementById('carousel-' + id);

            if (!carousel) return;

            const images = carousel.querySelectorAll('.carousel-image');

            if (!images.length) return;

            if (!(id in carouselIndex)) {
                carouselIndex[id] = 0;
            }

            images[carouselIndex[id]].classList.remove('active');

            carouselIndex[id] =
                (carouselIndex[id] + direction + images.length) %
                images.length;

            images[carouselIndex[id]].classList.add('active');
        }

        document.querySelectorAll('.building-carousel').forEach(carousel => {

            const id = carousel.id.replace('carousel-', '');

            setInterval(() => {
                changeSlide(id, 1);
            }, 3000);

        });
    </script>
@endsection
