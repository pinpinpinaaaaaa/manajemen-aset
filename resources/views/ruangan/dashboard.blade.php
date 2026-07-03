@extends('layouts.app')

@section('title', 'Dashboard ' . $ruangan->nama_ruangan)

@section('content')
    <style>
        .modal-dialog-scrollable .modal-content {
            max-height: 90vh !important;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto !important;
            max-height: calc(90vh - 130px) !important;
        }

        .dashboard-carousel {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .carousel-slide {
            display: none;
        }

        .carousel-slide.active {
            display: block;
        }

        .compact-info-section {
            display: flex;
            gap: 24px;
            align-items: stretch;
        }

        .compact-image {
            flex: 1;
            min-height: 320px;
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
            color: white;
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
                <h1 class="page-title">Dashboard {{ $ruangan->nama_ruangan }}</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gedung.index') }}">Gedung</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gedung.dashboard', $ruangan->id_gedung) }}">
                        {{ $ruangan->gedung->nama_gedung }}
                    </a>
                    <span class="separator">/</span>
                    <span class="current">{{ $ruangan->nama_ruangan }}</span>
                </nav>
            </div>

            <div class="building-header-compact">
                <div class="compact-info-section">
                    <div class="compact-image">

                        @if ($ruangan->gambar->count())

                            <div class="dashboard-carousel" id="ruangan-carousel">

                                @foreach ($ruangan->gambar as $index => $gambar)
                                    <img src="{{ asset('storage/' . $gambar->foto) }}"
                                        class="carousel-slide {{ $index == 0 ? 'active' : '' }}"
                                        alt="{{ $ruangan->nama_ruangan }}">
                                @endforeach

                                @if ($ruangan->gambar->count() > 1)
                                    <button class="carousel-btn prev" onclick="changeCarousel('ruangan-carousel', -1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <button class="carousel-btn next" onclick="changeCarousel('ruangan-carousel', 1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @endif

                            </div>
                        @else
                            <img src="{{ asset('images/default-room.jpg') }}" alt="{{ $ruangan->nama_ruangan }}">

                        @endif

                    </div>

                    <div class="compact-text-info">
                        <h2>{{ $ruangan->nama_ruangan }}</h2>
                        <p class="building-description">
                            Ruangan di gedung <b>{{ $ruangan->gedung->nama_gedung ?? 'Tidak diketahui' }}</b>
                        </p>

                        <div class="stat-cards-grid">
                            <x-stat-card label="Total Aset Sarana" :value="$totalAset" />
                            <x-stat-card label="Total Biaya Maintenance" :value="'Rp ' . number_format($totalBiayaMaintenance, 0, ',', '.')" />
                            <x-stat-card label="Status Ruangan" :value="ucfirst($ruangan->status)" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="controls-section mt-4">
                <div class="controls-left">

                    <a href="{{ route('aset.create', ['id_ruangan' => $ruangan->id_ruangan]) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Aset
                    </a>

                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#maintenanceRuanganModal">
                        <i class="fas fa-tools"></i> Maintenance Ruangan
                    </button>

                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                        data-bs-target="#pemindahanRuanganModal">
                        <i class="fas fa-truck"></i> Pindahkan Aset
                    </button>

                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                    <div class="view-toggle">
                        <button class="btn btn-sm active" id="cardsViewBtn" onclick="setViewMode('cards')">
                            Cards
                        </button>

                        <button class="btn btn-sm btn-outline" id="tableViewBtn" onclick="setViewMode('table')">
                            Table
                        </button>
                    </div>

                </div>

                <div class="controls-right">
                    <span class="search-label">Search:</span>
                    <input type="text" id="searchInput" placeholder="Cari aset..." class="search-input">
                </div>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <h3>Daftar Aset di Ruangan Ini</h3>

                <div id="cardsView" class="buildings-grid">
                    @forelse($ruangan->aset as $a)
                        <div class="building-item room-card" data-name="{{ strtolower($a->nama_aset) }}">

                            <img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/default-asset.jpg') }}"
                                alt="{{ $a->nama_aset }}" class="building-image"
                                onerror="this.src='{{ asset('images/default-asset.jpg') }}'">

                            <div class="building-content">
                                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                    <h3 class="building-title">{{ $a->nama_aset }}</h3>

                                    @php
                                        $ket = strtolower($a->keterangan_kelayakan);

                                        $ket = strtolower($a->keterangan_kelayakan);

                                        $badgeClass = match ($ket) {
                                            'layak' => 'badge badge-active', // HIJAU
                                            'perlu pemantauan' => 'badge badge-info',
                                            'perlu perbaikan' => 'badge badge-warning',
                                            'lelang', 'hibahkan', 'dijual', 'dimusnahkan' => 'badge badge-danger',
                                            default => 'badge badge-secondary',
                                        };

                                    @endphp


                                    <span
                                        class="{{ $badgeClass }}">{{ ucfirst($a->keterangan_kelayakan ?? '-') }}</span>
                                </div>
                                <p class="building-subtitle">Harga: Rp {{ number_format($a->harga, 0, ',', '.') }}</p>
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
                                    <a href="{{ route('aset.edit', $a->id_aset) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('aset.destroy', $a->id_aset) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="color:#dc2626;"
                                            onclick="return confirm('Yakin ingin menghapus aset ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @empty
                        <p class="text-muted">Tidak ada aset di ruangan ini.</p>
                    @endforelse
                </div>

                <div id="tableView" class="table-container" style="display:none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Kode</th>
                                <th>Nama Aset</th>
                                <th>Kelayakan</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Cek Fisik Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ruangan->aset as $index => $a)
                                <tr data-name="{{ strtolower($a->nama_aset) }}">
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/default-asset.jpg') }}"
                                            width="60" height="60" style="object-fit:cover; border-radius:6px;"
                                            onerror="this.src='{{ asset('images/default-asset.jpg') }}'">
                                    </td>

                                    <td>{{ $a->kode_aset ?? '-' }}</td>

                                    <td>{{ $a->nama_aset }}</td>

                                    @php
                                        $ket = strtolower($a->keterangan_kelayakan);
                                        $badgeClass = match ($ket) {
                                            'layak' => 'badge badge-active',
                                            'perlu pemantauan' => 'badge badge-secondary',
                                            'perlu perbaikan' => 'badge badge-warning',
                                            'lelang', 'hibahkan', 'dijual', 'dimusnahkan' => 'badge badge-danger',
                                            default => 'badge badge-secondary',
                                        };

                                    @endphp

                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($a->keterangan_kelayakan ?? '-') }}
                                        </span>
                                    </td>

                                    <td>Rp {{ number_format($a->nilai ?? 0, 0, ',', '.') }}</td>

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
                                        {{ $a->last_checked_at ? \Carbon\Carbon::parse($a->last_checked_at)->format('d M Y H:i') : '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('aset.show', $a->id_aset) }}" class="btn btn-sm btn-primary"><i
                                                class="fas fa-eye"></i></a>
                                        <a href="{{ route('aset.edit', $a->id_aset) }}"
                                            class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>

                                        <form action="{{ route('aset.destroy', $a->id_aset) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin hapus aset ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
        <div class="modal fade" id="maintenanceRuanganModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-tools"></i>
                                Maintenance Aset dari {{ $ruangan->nama_ruangan }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>


                        <div class="modal-body">

                            <div class="mb-3">
                                <label>Pelaksana</label>

                                <select id="pelaksanaType" name="pelaksana_type" class="form-control">
                                    <option value="internal">Internal</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>


                            <div class="mb-4" id="vendorBox" style="display:none;">
                                <label>Vendor</label>

                                <select name="id_vendor" class="form-control">
                                    <option value="">
                                        Pilih Vendor
                                    </option>

                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id_vendor }}">
                                            {{ $vendor->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <hr>

                            <h5>Pilih Aset yang Akan Maintenance</h5>


                            @foreach ($ruangan->aset->whereNotIn('keterangan_kelayakan', ['Perlu pemantauan', 'Perlu perbaikan', 'Lelang', 'Hibahkan', 'Dijual', 'Dimusnahkan']) as $index => $aset)
                                <div class="card p-3 mb-3">

                                    <div class="form-check">
                                        <input class="form-check-input aset-checkbox" type="checkbox"
                                            data-target="detail-{{ $index }}"
                                            name="details[{{ $index }}][dipilih]" value="1">

                                        <label>
                                            <b>{{ $aset->nama_aset }}</b>
                                            ({{ $aset->kode_aset }})
                                        </label>
                                    </div>


                                    <input type="hidden" name="details[{{ $index }}][id_aset]"
                                        value="{{ $aset->id_aset }}">


                                    <div id="detail-{{ $index }}" class="detail-form" style="display:none">

                                        <hr>

                                        <label>Kerusakan</label>
                                        <textarea class="form-control" name="details[{{ $index }}][kerusakan]">
        </textarea>


                                        <label class="mt-3">
                                            Foto Sebelum Maintenance
                                        </label>

                                        <input type="file" class="form-control"
                                            name="details[{{ $index }}][foto_before]">


                                        <label class="mt-3">
                                            Lampiran
                                        </label>

                                        <input type="file" class="form-control"
                                            name="details[{{ $index }}][lampiran]">

                                    </div>

                                </div>
                            @endforeach


                            <div class="mb-3">

                                <label>
                                    Catatan Maintenance
                                </label>

                                <textarea name="catatan" class="form-control" rows="3">
                        </textarea>

                            </div>


                        </div>


                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                Batal

                            </button>


                            <button type="submit" class="btn btn-warning">

                                <i class="fas fa-tools"></i>
                                Ajukan Maintenance

                            </button>

                        </div>


                    </form>

                </div>
            </div>
        </div>
        <div class="modal fade" id="pemindahanRuanganModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('pemindahan_aset.store') }}" method="POST">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-truck"></i>
                                Pindahkan Aset dari {{ $ruangan->nama_ruangan }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>


                        <div class="modal-body">

                            <h5>Pilih Aset yang Akan Dipindahkan</h5>


                            @foreach ($ruangan->aset->whereNotIn('keterangan_kelayakan', ['Lelang', 'Hibahkan', 'Dijual', 'Dimusnahkan'])->filter(function ($aset) {
            return $aset->jenisBarang && $aset->jenisBarang->bisa_dipindah;
        }) as $index => $aset)
                                <div class="card p-3 mb-3">

                                    <div class="form-check">
                                        <input class="form-check-input aset-checkbox" type="checkbox"
                                            data-target="pindah-detail-{{ $index }}"
                                            name="details[{{ $index }}][dipilih]" value="1">

                                        <label class="form-check-label">
                                            <b>{{ $aset->nama_aset }}</b>
                                            ({{ $aset->kode_aset }})
                                        </label>
                                    </div>


                                    <input type="hidden" name="details[{{ $index }}][id_aset]"
                                        value="{{ $aset->id_aset }}">


                                    <div id="pindah-detail-{{ $index }}" class="detail-form"
                                        style="display:none">

                                        <hr>


                                        <div class="mb-3">
                                            <label>Gedung Tujuan</label>

                                            <select class="form-control" name="details[{{ $index }}][id_gedung]">

                                                <option value="">
                                                    Pilih Gedung
                                                </option>

                                                @foreach ($gedungs as $gedung)
                                                    <option value="{{ $gedung->id_gedung }}">
                                                        {{ $gedung->nama_gedung }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <div class="mb-3">
                                            <label>Ruangan Tujuan</label>

                                            <select class="form-control" name="details[{{ $index }}][id_ruangan]">

                                                <option value="">
                                                    Pilih Ruangan
                                                </option>

                                                @foreach ($ruangans as $r)
                                                    <option value="{{ $r->id_ruangan }}">
                                                        {{ $r->nama_ruangan }}
                                                        - {{ $r->gedung->nama_gedung }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <div class="mb-3">
                                            <label>Alasan Pemindahan</label>

                                            <textarea class="form-control" rows="3" name="details[{{ $index }}][alasan]">
                                    </textarea>
                                        </div>

                                    </div>

                                </div>
                            @endforeach


                            <div class="mb-3">

                                <label>Catatan Pemindahan</label>

                                <textarea name="catatan" class="form-control" rows="3">
                        </textarea>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>


                            <button type="submit" class="btn btn-info">

                                <i class="fas fa-truck"></i>
                                Ajukan Pemindahan

                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </main>


    <script>
        document.querySelectorAll(".aset-checkbox")
            .forEach(function(checkbox) {

                checkbox.addEventListener("change", function() {

                    const target =
                        document.getElementById(
                            this.dataset.target
                        );

                    if (this.checked) {
                        target.style.display = "block";
                    } else {
                        target.style.display = "none";
                    }

                });

            });

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

        // Search Filter
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#cardsView .building-item, #tableView tbody tr').forEach(el => {
                const name = el.getAttribute('data-name');
                el.style.display = name && name.includes(q) ? '' : 'none';
            });
        });

        const carouselState = {};

        function changeCarousel(id, direction) {

            const container = document.getElementById(id);

            if (!container) return;

            const slides = container.querySelectorAll('.carousel-slide');

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

        setInterval(() => {
            changeCarousel('ruangan-carousel', 1);
        }, 3000);
        const pelaksana = document.getElementById('pelaksanaType');
        const vendorBox = document.getElementById('vendorBox');

        pelaksana.addEventListener('change', function() {
            if (this.value === 'vendor') {
                vendorBox.style.display = 'block';
            } else {
                vendorBox.style.display = 'none';
            }
        });
    </script>

@endsection
