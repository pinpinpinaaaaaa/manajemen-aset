<div class="sidebar-content">
    <div class="logo-container">
        <span class="brand-name">SIMASTER</span>
    </div>

    <nav>
        {{-- ================= DASHBOARD ================= --}}
        @if (canMenu('dashboard'))
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-gauge nav-icon"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </div>
        @endif

        <div class="sidebar-section">Manajemen Aset</div>

        @if (canMenu('gedung.index') || canMenu('ruangan.index') || canMenu('apar.index') || canMenu('aset.index'))
            <div class="nav-item">
                <button class="nav-link" onclick="openSaranaSubmenu(this)">
                    <i class="fas fa-building-columns nav-icon"></i>
                    <span class="nav-label">Sarana dan Prasarana</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </button>

                <div class="submenu" id="ruangan-submenu">
                    @if (canMenu('gedung'))
                        <a href="{{ route('gedung.index') }}"
                            class="nav-link {{ request()->routeIs('gedung.*') ? 'active' : '' }}">
                            <i class="fas fa-building nav-icon"></i>
                            <span class="nav-label">Gedung</span>
                        </a>
                    @endif

                    @if (canMenu('ruangan'))
                        <a href="{{ route('ruangan.index') }}"
                            class="nav-link {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
                            <i class="fas fa-door-closed nav-icon"></i>
                            <span class="nav-label">Ruangan</span>
                        </a>
                    @endif

                    @if (canMenu('apar'))
                        <a href="{{ route('apar.index') }}"
                            class="nav-link {{ request()->routeIs('apar.*') ? 'active' : '' }}">
                            <i class="fas fa-fire-extinguisher nav-icon"></i>
                            <span class="nav-label">Alat Pemadam Api Ringan (APAR)</span>
                        </a>
                    @endif

                    @if (canMenu('aset.index'))
                        <a href="{{ route('aset.index', ['jenis' => 'sarana']) }}"
                            class="nav-link {{ request('jenis') == 'sarana' ? 'active' : '' }}">
                            <i class="fas fa-chair nav-icon"></i>
                            <span class="nav-label">Sarana</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif


        @if (canMenu('kendaraan.index'))
            <div class="nav-item">
                <a href="{{ route('kendaraan.index') }}"
                    class="nav-link {{ request()->routeIs('kendaraan.*') ? 'active' : '' }}">
                    <i class="fas fa-car nav-icon"></i>
                    <span class="nav-label">Kendaraan</span>
                </a>
            </div>
        @endif

        @if (canMenu('vendor.index'))
            <div class="nav-item">
                <a href="{{ route('vendor.index') }}" class="nav-link {{ request()->routeIs('vendor.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake nav-icon"></i>
                    <span class="nav-label">Vendor</span>
                </a>
            </div>
        @endif

        {{-- ================= FORM INTERNAL ================= --}}
        @if (canMenu('pemindahan_aset.index') || canMenu('laporan_pemusnahan.index') || canMenu('maintenance.index'))
            <div class="nav-item">
                <button class="nav-link" onclick="toggleSubmenu('form-pengajuan', this)">
                    <i class="fas fa-file-circle-plus nav-icon"></i>
                    <span class="nav-label">Internal Aset</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </button>

                <div class="submenu" id="form-pengajuan-submenu">

                    {{-- Pemindahan Aset --}}
                    @if (canMenu('pemindahan_aset.index'))
                        <a href="{{ route('pemindahan_aset.index') }}"
                            class="nav-link {{ request()->routeIs('pemindahan_aset.*') ? 'active' : '' }}">
                            <i class="fas fa-right-left nav-icon"></i>
                            <span class="nav-label">Pemindahan Aset</span>
                        </a>
                    @endif

                    {{-- Pemusnahan Aset --}}
                    @if (canMenu('laporan_pemusnahan.index'))
                        <a href="{{ route('laporan_pemusnahan.index') }}"
                            class="nav-link {{ request()->routeIs('laporan_pemusnahan.*') ? 'active' : '' }}">
                            <i class="fas fa-trash nav-icon"></i>
                            <span class="nav-label">Pemusnahan Aset</span>
                        </a>
                    @endif

                    {{-- Maintenance Aset --}}
                    @if (canMenu('maintenance.index'))
                        <a href="{{ route('maintenance.index') }}"
                            class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                            <i class="fas fa-screwdriver-wrench nav-icon"></i>
                            <span class="nav-label">Pemeliharaan Aset</span>
                        </a>
                    @endif

                </div>
            </div>
        @endif

        <div class="sidebar-section">Manajemen Gudang</div>

        @if (canMenu('gudang.index') || canMenu('gudang.transaksi') || canMenu('gudang.laporan_opname'))
            @if (canMenu('gudang.index'))
                <div class="nav-item">
                    <a href="{{ route('gudang.index') }}"
                        class="nav-link {{ request()->routeIs('gudang.index') ? 'active' : '' }}">
                        <i class="fas fa-chart-line nav-icon"></i>
                        <span class="nav-label">Monitoring Gudang</span>
                    </a>
                </div>
            @endif
            @if (canMenu('gudang.transaksi.index'))
                <div class="nav-item">
                    <a href="{{ route('gudang.transaksi.index') }}"
                        class="nav-link {{ request()->routeIs('gudang.transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <span class="nav-label">Transaksi Barang Gudang</span>
                    </a>
                </div>
            @endif
            @if (canMenu('gudang.stok_opname.index'))
                <div class="nav-item">
                    <a href="{{ route('gudang.stok_opname.index') }}"
                        class="nav-link {{ request()->routeIs('gudang.stok_opname.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check nav-icon"></i>
                        <span class="nav-label">Rekap Stok Gudang</span>
                    </a>
                </div>
            @endif
        @endif

        @if (canMenu('permintaan-barang') ||
                canMenu('peminjaman_aset') ||
                canMenu('pengadaan-barang') ||
                canMenu('pemindahan_aset') ||
                canMenu('peminjaman-ruangan') ||
                canMenu('permintaan-kendaraan') ||
                canMenu('ekspedisi') ||
                canMenu('pengaduan-kerusakan'))
            <div class="sidebar-section">Manajemen Layanan</div>

            @if (canMenu('permintaan-barang'))
                <div class="nav-item">
                    <a href="{{ route('permintaan-barang.index') }}"
                        class="nav-link {{ request()->routeIs('permintaan-barang.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list nav-icon"></i>
                        <span class="nav-label">Permintaan Barang Gudang</span>
                    </a>
                </div>
            @endif

            @if (canMenu('pengadaan-barang'))
                <div class="nav-item">
                    <a href="{{ route('pengadaan-barang.index') }}"
                        class="nav-link {{ request()->routeIs('pengadaan-barang.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon"></i>
                        <span class="nav-label">Pengadaan Barang & Jasa </span>
                    </a>
                </div>
            @endif

            @if (canMenu('peminjaman_aset'))
                <div class="nav-item">
                    <a href="{{ route('peminjaman_aset.index') }}"
                        class="nav-link {{ request()->routeIs('peminjaman_aset.*') ? 'active' : '' }}">
                        <i class="fas fa-hand-holding nav-icon"></i>
                        <span class="nav-label">Peminjaman Aset Kantor</span>
                    </a>
                </div>
            @endif


            @if (canMenu('peminjaman-ruangan'))
                <div class="nav-item">
                    <a href="{{ route('peminjaman-ruangan.index') }}"
                        class="nav-link {{ request()->routeIs('peminjaman-ruangan.*') ? 'active' : '' }}">
                        <i class="fas fa-building nav-icon"></i>
                        <span class="nav-label">Peminjaman Ruangan</span>
                    </a>
                </div>
            @endif

            @if (canMenu('permintaan-kendaraan'))
                <div class="nav-item">
                    <a href="{{ route('permintaan-kendaraan.index') }}"
                        class="nav-link {{ request()->routeIs('permintaan-kendaraan.*') ? 'active' : '' }}">
                        <i class="fas fa-car-side nav-icon"></i>
                        <span class="nav-label">Permintaan Kendaraan</span>
                    </a>
                </div>
            @endif

            @if (canMenu('ekspedisi'))
                <div class="nav-item">
                    <a href="{{ route('ekspedisi.index') }}"
                        class="nav-link {{ request()->routeIs('ekspedisi.*') ? 'active' : '' }}">
                        <i class="fas fa-truck nav-icon"></i>
                        <span class="nav-label">Ekspedisi</span>
                    </a>
                </div>
            @endif

            @if (canMenu('pengaduan-kerusakan'))
                <div class="nav-item">
                    <a href="{{ route('pengaduan-kerusakan.index') }}"
                        class="nav-link {{ request()->routeIs('pengaduan-kerusakan.*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-circle nav-icon"></i>
                        <span class="nav-label">Pengaduan Kerusakan</span>
                    </a>
                </div>
            @endif
        @endif

        @if (canMenu('maintenance') ||
                canMenu('laporan_pemusnahan') ||
                canMenu('laporan_tahunan'))
            <div class="sidebar-section">Manajemen Laporan</div>

            {{-- ================= LAPORAN GUDANG ================= 
            @if (canMenu('gudang.laporan_transaksi') || canMenu('gudang.laporan_opname'))
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
            @endif --}}

            {{-- Laporan Tahunan --}}
            @if (canMenu('laporan_tahunan'))
                <div class="nav-item">
                    <a href="{{ route('laporan_tahunan.index') }}"
                        class="nav-link {{ request()->routeIs('laporan_tahunan.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-days nav-icon"></i>
                        <span class="nav-label">Laporan Tahunan</span>
                    </a>
                </div>
            @endif
        @endif

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
                            class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fas fa-user nav-icon"></i>
                            <span class="nav-label">Pengguna</span>
                        </a>
                    @endif

                    @if (canMenu('roles.index'))
                        <a href="{{ route('roles.index') }}"
                            class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
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
                    class="nav-link {{ request()->routeIs('jenis_barang.*') ? 'active' : '' }}">
                    <i class="fas fa-tags nav-icon"></i>
                    <span class="nav-label">Jenis Barang</span>
                </a>
            </div>
        @endif




        {{-- HISTORY ACTIVITY --}}
        @if (canMenu('audit.logs'))
            <div class="nav-item">
                <a href="{{ route('audit.logs') }}"
                    class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                    <i class="fas fa-clock-rotate-left nav-icon"></i>
                    <span class="nav-label">Riwayat Aktivitas</span>
                </a>
            </div>
        @endif

        {{-- MANUAL PENGGUNA — muncul untuk semua user login, di luar canMenu() --}}
        <div class="sidebar-section">Bantuan</div>
        <div class="nav-item">
            <a href="{{ asset('manual/Manual-SIMASTER.pdf') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="nav-link">
                <i class="fas fa-book-open nav-icon"></i>
                <span class="nav-label">Manual Pengguna</span>
            </a>
        </div>

    </nav>
</div>
