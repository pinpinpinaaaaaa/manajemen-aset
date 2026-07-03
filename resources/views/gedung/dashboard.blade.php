@extends('layouts.app')

@section('title', 'Dashboard ' . $gedung->nama_gedung)

@section('content')
    <style>
        .dashboard-carousel {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .carousel-slide,
        .carousel-file {
            display: none;
        }

        .carousel-slide.active,
        .carousel-file.active {
            display: block;
        }

        .compact-info-section {
            display: flex;
            gap: 24px;
            align-items: stretch;
        }

        .compact-image {
            flex: 1;
            min-height: 280px;
            min-width: 100px;
        }

        .compact-text-info {
            flex: 1;
        }

        .dashboard-carousel {
            height: 100%;
        }

        .carousel-slide {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 12px;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: rgba(0, 0, 0, .5);
            color: #fff;
            cursor: pointer;
            z-index: 10;
        }

        .carousel-btn.prev {
            left: 10px;
        }

        .carousel-btn.next {
            right: 10px;
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Dashboard {{ $gedung->nama_gedung }}</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gedung.index') }}">Gedung</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $gedung->nama_gedung }}</span>
                </nav>
            </div>

            <div class="building-header-compact">
                <div class="compact-info-section">
                    <div class="compact-image">

                        @if ($gedung->gambar->count())

                            <div class="dashboard-carousel" id="gambar-carousel">

                                @foreach ($gedung->gambar as $index => $gambar)
                                    <img src="{{ asset('storage/' . $gambar->gambar) }}"
                                        class="carousel-slide {{ $index == 0 ? 'active' : '' }}">
                                @endforeach

                                @if ($gedung->gambar->count() > 1)
                                    <button class="carousel-btn prev" onclick="changeCarousel('gambar-carousel',-1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <button class="carousel-btn next" onclick="changeCarousel('gambar-carousel',1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @endif

                            </div>
                        @else
                            <img src="{{ asset('images/default-building.jpg') }}">
                        @endif

                    </div>

                    <div class="compact-text-info">
                        <h2>{{ $gedung->nama_gedung }}</h2>

                        <div class="stat-cards-grid">
                            <x-stat-card label="Total Ruangan" :value="$totalRuangan" />
                            <x-stat-card label="Total Aset Sarana" :value="$totalAset" />
                            <x-stat-card label="Total Biaya Maintenance" :value="'Rp ' . number_format($totalBiayaMaintenance, 0, ',', '.')" />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="section-header">
                    <h3 class="section-title">Daftar Ruangan</h3>
                    <a href="{{ route('ruangan.index', ['id_gedung' => $gedung->id_gedung]) }}"
                        class="btn-primary btn-view-all">Lihat Semua</a>
                </div>

                @if ($gedung->ruangan->count() === 0)
                    {{-- EMPTY STATE RUANGAN --}}
                    <div style="grid-column: 1 / -1;">
                        @include('components.empty', [
                            'title' => 'Belum ada ruangan',
                            'message' => 'Tambahkan ruangan untuk ditampilkan pada daftar.',
                            'action' => [
                                'url' => route('ruangan.create', ['gedung' => $gedung->id_gedung]),
                                'icon' => 'fas fa-plus',
                                'label' => 'Tambah Ruangan',
                            ],
                        ])
                    </div>
                @else
                    {{-- KARTU RUANGAN --}}
                    <div class="cards-grid">
                        @foreach ($gedung->ruangan->take(6) as $r)
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $r->nama_ruangan }}</h4>
                                    <span
                                        class="badge 
                          {{ $r->status == 'aktif' ? 'badge-green' : ($r->status == 'maintenance' ? 'badge-red' : 'badge-blue') }}">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </div>
                                <div class="card-content">
                                    <p class="card-info">Tipe: {{ $r->kategori ?? '-' }}</p>
                                    <p class="card-info">Aset:
                                        {{ $r->aset->where('jenisBarang.jenis', 'sarana')->count() }} item</p>
                                </div>
                                <div class="building-actions">
                                    <a href="{{ route('ruangan.dashboard', $r->id_ruangan) }}" class="btn btn-primary">
                                        <i class="fas fa-door-open"></i> Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="section-header">
                    <h3 class="section-title">Daftar Aset</h3>
                    <a href="{{ route('aset.index', ['id_gedung' => $gedung->id_gedung]) }}" class="btn-primary">Lihat
                        Semua</a>
                </div>

                @php
                    $asetTampil = collect();

                    foreach ($gedung->ruangan as $r) {
                        foreach ($r->aset as $a) {
                            $a->nama_ruangan = $r->nama_ruangan;
                            $asetTampil->push($a);
                        }
                    }

                    $asetTampil = $asetTampil->take(6);
                @endphp

                @if ($asetTampil->count() === 0)
                    {{-- EMPTY STATE ASET --}}
                    <div style="grid-column: 1 / -1;">
                        @include('components.empty', [
                            'title' => 'Belum ada aset',
                            'message' => 'Tambahkan aset untuk ditampilkan pada daftar.',
                            'action' => [
                                'url' => route('aset.create', ['id_gedung' => $gedung->id_gedung]),
                                'icon' => 'fas fa-plus',
                                'label' => 'Tambah Aset',
                            ],
                        ])
                    </div>
                @else
                    {{-- KARTU ASET --}}
                    <div class="cards-grid">
                        @foreach ($asetTampil as $a)
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $a->nama_aset }}</h4>

                                    <span class="badge {{ $a->kondisi == 'baik' ? 'badge-green' : 'badge-red' }}">
                                        {{ ucfirst($a->kondisi) }}
                                    </span>
                                </div>

                                <div class="card-content">
                                    <p class="card-info">Kode: {{ $a->kode_aset ?? '-' }}</p>
                                    <p class="card-info">Ruangan: {{ $a->nama_ruangan }}</p>
                                    <p class="card-info">Nilai: Rp {{ number_format($a->nilai, 0, ',', '.') }}</p>
                                </div>

                                <div class="building-actions">
                                    <a href="{{ route('aset.show', $a->id_aset) }}" class="btn btn-primary">
                                        <i class="fas fa-magnifying-glass"></i>
                                        Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>


            <div>
                <div class="section-header">
                    <h3 class="section-title">Denah Gedung</h3>
                </div>

                @if ($gedung->denah->count())

                    <div class="dashboard-carousel denah-carousel" id="denah-carousel">

                        @foreach ($gedung->denah as $index => $denah)
                            @php
                                $ext = strtolower(pathinfo($denah->file_denah, PATHINFO_EXTENSION));
                            @endphp

                            <div class="carousel-file {{ $index == 0 ? 'active' : '' }}">

                                @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                    <img src="{{ asset('storage/' . $denah->file_denah) }}" class="map-image">
                                @elseif($ext === 'pdf')
                                    <iframe src="{{ asset('storage/' . $denah->file_denah) }}" width="100%"
                                        height="600">
                                    </iframe>
                                @endif

                                <div class="map-actions">
                                    <a href="{{ asset('storage/' . $denah->file_denah) }}" target="_blank"
                                        class="btn btn-primary">
                                        <i class="fas fa-search-plus"></i>
                                        Buka File
                                    </a>
                                </div>

                            </div>
                        @endforeach

                        @if ($gedung->denah->count() > 1)
                            <button class="carousel-btn prev" onclick="changeCarousel('denah-carousel',-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <button class="carousel-btn next" onclick="changeCarousel('denah-carousel',1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif

                    </div>
                @else
                    @include('components.empty', [
                        'title' => 'Denah Belum Tersedia',
                        'message' => 'Upload denah untuk mempermudah navigasi lokasi ruangan di gedung ini.',
                        'action' => [
                            'url' => route('gedung.edit', $gedung->id_gedung),
                            'icon' => 'fas fa-upload',
                            'label' => 'Upload Denah Gedung',
                        ],
                    ])

                @endif
            </div>

        </div>
    </main>
    <script>
        const carouselState = {};

        function changeCarousel(id, direction) {

            const container = document.getElementById(id);

            if (!container) return;

            const slides = container.querySelectorAll(
                '.carousel-slide, .carousel-file'
            );

            if (!slides.length) return;

            if (!(id in carouselState)) {
                carouselState[id] = 0;
            }

            slides.forEach(slide => {
                slide.classList.remove('active');
            });

            carouselState[id] =
                (carouselState[id] + direction + slides.length) %
                slides.length;

            slides[carouselState[id]]
                .classList.add('active');
        }
    </script>
@endsection
