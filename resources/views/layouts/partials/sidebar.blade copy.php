<div class="sidebar-content">

    <!-- Search Bar -->
    <div class="sidebar-search">
        <input type="text" placeholder="Search..." class="search-input" />
        <i class="fas fa-search search-icon"></i>
    </div>

    <nav>
        {{-- ================= DASHBOARD ================= --}}
        @if (canMenu('dashboard'))
        <div class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-gauge nav-icon"></i>
                <span class="nav-label">Dashboard</span>
            </a>
        </div>
        @endif

        
        <!-- Section Title -->
        <div class="sidebar-section">Manajemen Aset</div>

        <!-- Sarana Prasarana -->
        @if (
            canMenu('gedung.index') ||
            canMenu('ruangan.index') ||
            canMenu('apar.index') ||
            canMenu('aset.index')
        )
        <div class="nav-item">
            <button class="nav-link" onclick="openSaranaSubmenu(this)">
                <i class="fas fa-building-columns nav-icon"></i>
                <span class="nav-label">Sarana dan Prasarana</span>
                <i class="fas fa-chevron-right submenu-icon"></i>
            </button>

            <div class="submenu" id="ruangan-submenu">
                @if (canMenu('gedung'))
                <a href="{{ route('gedung.index') }}" class="nav-link {{ request()->is('gedung*') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <span class="nav-label">Gedung</span>
                </a>
                @endif

                @if (canMenu('ruangan'))
                <a href="{{ route('ruangan.index') }}" class="nav-link {{ request()->is('ruangan*') ? 'active' : '' }}">
                    <i class="fas fa-door-closed nav-icon"></i>
                    <span class="nav-label">Ruangan</span>
                </a>
                @endif

                @if (canMenu('apar'))
                <a href="{{ route('apar.index') }}" class="nav-link {{ request()->is('apar*') ? 'active' : '' }}">
                    <i class="fas fa-fire-extinguisher nav-icon"></i>
                    <span class="nav-label">Alat Pemadam Api Ringan (APAR)</span>
                </a>
                @endif

                @if (canMenu('sarana'))
                <a href="{{ route('aset.index', ['jenis' => 'sarana']) }}" class="nav-link {{ request('jenis') == 'sarana' ? 'active' : '' }}">
                    <i class="fas fa-chair nav-icon"></i>
                    <span class="nav-label">Sarana</span>
                </a>
                @endif
            </div>
        </div>
        @endif


        <!-- Kendaraan -->
        @if (canMenu('kendaraan.index'))
        <div class="nav-item">
            <a href="{{ route('kendaraan.index') }}" class="nav-link {{ request()->is('kendaraan*') ? 'active' : '' }}">
                <i class="fas fa-car nav-icon"></i>
                <span class="nav-label">Kendaraan</span>
            </a>
        </div>
        @endif

        {{-- ================= FORM PENGAJUAN ================= --}}
        @if (
            canMenu('pemindahan_aset.create') ||
            canMenu('laporan_pemusnahan.create') ||
            canMenu('maintenance.create')
        )
        <div class="nav-item">
            <button class="nav-link" onclick="toggleSubmenu('form-pengajuan', this)">
                <i class="fas fa-file-circle-plus nav-icon"></i>
                <span class="nav-label">Formulir Pengajuan</span>
                <i class="fas fa-chevron-right submenu-icon"></i>
            </button>

            <div class="submenu" id="form-pengajuan-submenu">

                {{-- Pemindahan Aset --}}
                @if (canMenu('pemindahan_aset.create'))
                <a href="{{ route('pemindahan-aset.create') }}"
                class="nav-link {{ request()->is('pemindahan-aset*') ? 'active' : '' }}">
                    <i class="fas fa-right-left nav-icon"></i>
                    <span class="nav-label">Pemindahan Aset</span>
                </a>
                @endif

                {{-- Pemusnahan Aset --}}
                @if (canMenu('laporan_pemusnahan.create'))
                <a href="{{ route('laporan_pemusnahan.create') }}"
                    class="nav-link {{ request()->is('laporan-pemusnahan*') ? 'active' : '' }}">
                        <i class="fas fa-trash nav-icon"></i>
                        <span class="nav-label">Pemusnahan Aset</span>
                </a>
                @endif

                {{-- Maintenance Aset --}}
                @if (canMenu('maintenance.index'))
                <a href="{{ route('maintenance.index') }}"
                class="nav-link {{ request()->is('maintenance*') ? 'active' : '' }}">
                    <i class="fas fa-screwdriver-wrench nav-icon"></i>
                    <span class="nav-label">Pemeliharaan Aset</span>
                </a>
                @endif

            </div>
        </div>
        @endif

        <!-- Section Title -->
        <div class="sidebar-section">Manajemen Gudang</div>

        <!-- Gudang -->
        @if (
            canMenu('gudang.dashboard') ||
            canMenu('gudang.index') ||
            canMenu('gudang.transaksi') ||
            canMenu('gudang.laporan_opname')
        )
        @if (canMenu('gudang.dashboard'))
        <div class="nav-item">
            <a href="{{ route('gudang.dashboard') }}"
            class="nav-link {{ request()->is('gudang/dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line nav-icon"></i>
                <span class="nav-label">Monitoring Gudang</span>
            </a>
        </div>
        @endif
        @if (canMenu('gudang.index'))
        <div class="nav-item">
            <a href="{{ route('gudang.index') }}"
            class="nav-link {{ request()->is('gudang') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked nav-icon"></i>
                <span class="nav-label">Stok Terkini</span>
            </a>
        </div>
        @endif
        @if (canMenu('gudang.transaksi.index'))
        <div class="nav-item">
            <a href="{{ route('gudang.transaksi.index') }}"
            class="nav-link {{ request()->is('gudang/transaksi/index') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                <span class="nav-label">Transaksi Barang Gudang</span>
            </a>
        </div>
        @endif
        @if (canMenu('gudang.rekapBulanan'))
        <div class="nav-item">
            <a href="{{ route('gudang.rekapBulanan') }}"
            class="nav-link {{ request()->is('gudang/transaksi/index') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check nav-icon"></i>
                <span class="nav-label">Rekap Stok Gudang</span>
            </a>
        </div>
        @endif
        @endif

        <!-- Laporan -->
        @if (
            canMenu('maintenance_list') ||
            canMenu('maintenance_laporan') ||
            canMenu('laporan_pemusnahan') ||
            canMenu('laporan_tahunan')
        )
            <!-- Section Title -->
            <div class="sidebar-section">Manajemen Laporan</div>

            {{-- ================= LAPORAN GUDANG ================= --}}
            @if (
                canMenu('gudang.laporan_transaksi') ||
                canMenu('gudang.laporan_opname')
            )
            <div class="nav-item">
                <button class="nav-link" onclick="toggleSubmenu('laporan-gudang', this)">
                    <i class="fas fa-chart-column nav-icon"></i>
                    <span class="nav-label">Laporan Gudang</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </button>

                <div class="submenu" id="laporan-gudang-submenu">
                    @if (canMenu('gudang.laporan_transaksi'))
                    <a href="{{ route('gudang.laporan_transaksi') }}"
                    class="nav-link {{ request()->is('gudang/laporan-transaksi*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice nav-icon"></i>
                        <span class="nav-label">Laporan Transaksi Gudang</span>
                    </a>
                    @endif

                    @if (canMenu('gudang.laporan_opname'))
                    <a href="{{ route('gudang.laporan_opname') }}"
                    class="nav-link {{ request()->is('gudang/laporan-opname*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check nav-icon"></i>
                        <span class="nav-label">Laporan Stok Opname</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Maintenance --}}
            @if (canMenu('maintenance_laporan'))
            <div class="nav-item">
                <a href="{{ route('maintenance.laporan') }}"
                class="nav-link {{ request()->is('maintenance/laporan') ? 'active' : '' }}">
                    <i class="fas fa-file-lines nav-icon"></i>
                    <span class="nav-label">Laporan Pemeliharaan Aset</span>
                </a>
            </div>
            @endif

            {{-- Laporan Pemusnahan --}}
            @if (canMenu('laporan_pemusnahan'))
            <div class="nav-item">
                <a href="{{ route('laporan_pemusnahan.index') }}"
                class="nav-link {{ request()->is('laporan_pemusnahan*') ? 'active' : '' }}">
                    <i class="fas fa-trash-can nav-icon"></i>
                    <span class="nav-label">Laporan Pemusnahan</span>
                </a>
            </div>
            @endif

            {{-- Laporan Tahunan --}}
            @if (canMenu('laporan_tahunan'))
            <div class="nav-item">
                <a href="{{ route('laporan_tahunan.index') }}"
                class="nav-link {{ request()->is('laporan_tahunan*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-days nav-icon"></i>
                    <span class="nav-label">Laporan Tahunan</span>
                </a>
            </div>
            @endif
        @endif

        @if (
            canMenu('permintaan_barang') ||
            canMenu('peminjaman_aset') ||
            canMenu('pemindahan_aset') ||
            canMenu('peminjaman_ruangan') ||
            canMenu('peminjaman_kendaraan') ||
            canMenu('ekspedisi') ||
            canMenu('pengaduan')
        )
            <div class="sidebar-section">Manajemen Layanan</div>

            @if (canMenu('permintaan_barang'))
            <div class="nav-item">
                <a href="{{ route('permintaan-barang.index') }}"
                class="nav-link {{ request()->is('permintaan-barang*') ? 'active' : '' }}">
                    <i class="fas fa-cart-arrow-down nav-icon"></i>
                    <span class="nav-label">Permintaan Barang Gudang dan Sarpras</span>
                </a>
            </div>
            @endif

            @if (canMenu('peminjaman_aset'))
            <div class="nav-item">
                <a href="{{ route('peminjaman_aset.index') }}"
                class="nav-link {{ request()->is('peminjaman_aset*') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding nav-icon"></i>
                    <span class="nav-label">Peminjaman Aset Kantor</span>
                </a>
            </div>
            @endif


            @if (canMenu('peminjaman_ruangan'))
            <div class="nav-item">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="fas fa-key nav-icon"></i>
                    <span class="nav-label">Peminjaman Ruangan</span>
                </a>
            </div>
            @endif

            @if (canMenu('peminjaman_kendaraan'))
            <div class="nav-item">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="fas fa-car-side nav-icon"></i>
                    <span class="nav-label">Peminjaman Kendaraan</span>
                </a>
            </div>
            @endif

            @if (canMenu('ekspedisi'))
            <div class="nav-item">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="fas fa-truck-fast nav-icon"></i>
                    <span class="nav-label">Ekspedisi</span>
                </a>
            </div>
            @endif

            @if (canMenu('pengaduan'))
            <div class="nav-item">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="fas fa-triangle-exclamation nav-icon"></i>
                    <span class="nav-label">Pengaduan Kerusakan</span>
                </a>
            </div>
            @endif
        @endif

        <!-- ================= MANAGEMENT ================= -->
        <div class="sidebar-section">Manajemen Pengguna dan Sistem</div>

        {{-- USER MANAGEMENT (SUBMENU) --}}
        @if (canMenu('users.index') || canMenu('roles.index'))
        <div class="nav-item">
            <button class="nav-link" onclick="toggleSubmenu('user-management', this)">
                <i class="fas fa-users-gear nav-icon"></i>
                <span class="nav-label">Manajemen Pengguna</span>
                <i class="fas fa-chevron-right submenu-icon"></i>
            </button>

            <div class="submenu" id="user-management-submenu">

                @if (canMenu('users.index'))
                <a href="{{ route('users.index') }}"
                class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-user nav-icon"></i>
                    <span class="nav-label">Pengguna</span>
                </a>
                @endif

                @if (canMenu('roles.index'))
                <a href="{{ route('roles.index') }}"
                class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield nav-icon"></i>
                    <span class="nav-label">Hak Akses</span>
                </a>
                @endif

            </div>
        </div>
        @endif


        {{-- JENIS BARANG --}}
        @if (canMenu('jenis_barang.index'))
        <div class="nav-item">
            <a href="{{ route('jenis_barang.index') }}"
            class="nav-link {{ request()->is('jenis-barang*') ? 'active' : '' }}">
                <i class="fas fa-tags nav-icon"></i>
                <span class="nav-label">Jenis Barang</span>
            </a>
        </div>
        @endif

        


        {{-- HISTORY ACTIVITY --}}
        @if (canMenu('pemindahan_aset'))
        <div class="nav-item">
            <a href="{{ route('audit.logs') }}"
            class="nav-link {{ request()->is('audit.logs*') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left nav-icon"></i>
                <span class="nav-label">Riwayat Aktivitas</span>
            </a>
        </div>
        @endif


    </nav>
</div>
