@extends('layouts.app')

@section('title', 'Permintaan Barang Gudang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Permintaan Barang Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Permintaan Barang Gudang</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('form-permintaan-barang.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Permintaan
                    </a>

                    <a href="{{ route('permintaan-barang.riwayat') }}" class="btn btn-outline">
                        <i class="fas fa-history"></i> Riwayat
                    </a>

                    <button class="btn btn-outline" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input id="searchInput" class="search-input" placeholder="Cari nama / divisi...">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>

                            <th class="filterable" data-key="pengaju">
                                Pengaju <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="divisi">
                                Divisi <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-lg-table-cell">Tgl Kebutuhan</th>

                            <th class="d-none d-md-table-cell">Total Item</th>

                            <th class="filterable d-none d-lg-table-cell" data-key="status">
                                Status <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable" data-key="approval">
                                Approval <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($permintaan as $i => $p)
                            @php
                                $today = \Carbon\Carbon::today();
                                $tgl = \Carbon\Carbon::parse($p->tanggal_kebutuhan);

                                $isOverdue = $p->status !== 'Selesai' && $tgl->lt($today);
                                $isToday = $p->status !== 'Selesai' && $tgl->isSameDay($today);
                            @endphp
                            <tr class="
                                    {{ $isOverdue ? 'table-danger' : '' }}
                                    {{ !$isOverdue && $isToday ? 'table-warning' : '' }}
                                "
                                data-name="{{ strtolower($p->nama_pengaju . ' ' . ($p->divisi->nama_divisi ?? '')) }}"
                                data-pengaju="{{ strtolower($p->nama_pengaju) }}"
                                data-divisi="{{ strtolower($p->divisi->nama_divisi ?? '') }}"
                                data-jenis="{{ strtolower($p->jenis_permintaan) }}"
                                data-status="{{ strtolower($p->status) }}"
                                data-approval="{{ strtolower($p->decision_status) }}">
                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_permintaan }}</td>
                                <td>{{ $p->nama_pengaju }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi ?? '-' }}</td>

                                <td class="d-none d-lg-table-cell">
                                    {{ \Carbon\Carbon::parse($p->tanggal_kebutuhan)->format('d M Y') }}
                                </td>

                                <td class="d-none d-md-table-cell">{{ $p->details->count() }}</td>

                                <td class="d-none d-lg-table-cell">
                                    @php
                                        $color = match ($p->status) {
                                            'Belum Diproses' => 'secondary',
                                            'Sedang Diproses' => 'warning',
                                            'Tersedia' => 'info',
                                            'Selesai' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ $p->status }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $acolor = match ($p->decision_status) {
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $acolor }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                                    </span>
                                </td>

                                <td style="white-space:nowrap">

                                    {{-- Detail --}}
                                    <a href="{{ route('permintaan-barang.show', $p->id_permintaan) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('permintaan-barang.edit', $p->id_permintaan) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('permintaan-barang.destroy', $p->id_permintaan) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus permintaan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    {{-- Approve / Reject --}}
                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <form method="POST"
                                            action="{{ route('permintaan-barang.approve', $p->id_permintaan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success">
                                                Setujui
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $p->id_permintaan }}">
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- Proses --}}
                                    @if ($p->decision_status == 'disetujui' && $p->status == 'Belum Diproses')
                                        <form method="POST"
                                            action="{{ route('permintaan-barang.process', $p->id_permintaan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-warning">
                                                <i class="fas fa-cogs"></i> Proses
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tersedia --}}
                                    @if ($p->status == 'Sedang Diproses')
                                        <form method="POST"
                                            action="{{ route('permintaan-barang.markAvailable', $p->id_permintaan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-info"
                                                onclick="return confirm('Tandai barang sudah tersedia?')">
                                                <i class="fas fa-box-open"></i> Tersedia
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Selesai --}}
                                    @if ($p->status == 'Tersedia')
                                        <form method="POST"
                                            action="{{ route('permintaan-barang.complete', $p->id_permintaan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Konfirmasi barang sudah diserahkan?')">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        </form>
                                    @endif

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada permintaan
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
        @foreach ($permintaan as $p)
            @if ($p->decision_status == 'menunggu_persetujuan')
                <div class="modal fade" id="rejectModal{{ $p->id_permintaan }}" tabindex="-1">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">

                            <form method="POST" action="{{ route('permintaan-barang.reject', $p->id_permintaan) }}">

                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Tolak Permintaan Barang
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label class="form-label">
                                            Catatan Penolakan
                                        </label>

                                        <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="submit" class="btn btn-danger">
                                        Tolak Permintaan
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        @endforeach

        <div class="mt-3 content-padding">
            {{ $permintaan->withQueryString()->links() }}
        </div>
    </main>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();
            applyFilters(q);
        });

        let activeFilters = {};
        let currentKey = null;

        document.querySelectorAll('.filterable').forEach(th => {
            th.addEventListener('click', function() {

                document.getElementById('filterPopup').style.display = 'none';

                currentKey = this.dataset.key;

                const popup = document.getElementById('filterPopup');
                const rect = this.getBoundingClientRect();

                popup.style.left = rect.left + 'px';
                popup.style.top = rect.bottom + window.scrollY + 'px';
                popup.style.display = 'block';

                document.getElementById('filterTitle').innerText =
                    this.childNodes[0].textContent.trim();

                const values = new Set();
                document.querySelectorAll('tbody tr').forEach(row => {
                    values.add(row.dataset[currentKey] || '');
                });

                const container = document.getElementById('filterOptions');
                container.innerHTML = '';

                values.forEach(v => {
                    const val = v === '' ? '__EMPTY__' : v;
                    const checked = !activeFilters[currentKey] ||
                        activeFilters[currentKey].includes(val);

                    container.innerHTML += `
                <label style="display:block;margin-bottom:4px;">
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

                        applyFilters();
                    };
                });
            });
        });

        function applyFilters(searchQuery = '') {
            document.querySelectorAll('tbody tr').forEach(row => {

                let show = true;

                // FILTER KOLOM
                for (const key in activeFilters) {
                    const raw = row.dataset[key] || '';
                    const v = raw === '' ? '__EMPTY__' : raw;

                    if (!activeFilters[key].includes(v)) {
                        show = false;
                    }
                }

                // SEARCH
                if (searchQuery) {
                    const name = row.dataset.name || '';
                    if (!name.includes(searchQuery)) {
                        show = false;
                    }
                }

                row.style.display = show ? '' : 'none';
            });

            // highlight header aktif
            document.querySelectorAll('.filterable').forEach(th => {
                const key = th.dataset.key;
                th.classList.toggle('active-filter', activeFilters[key]);
            });
        }

        document.addEventListener('click', e => {
            if (!e.target.closest('.filterable') &&
                !e.target.closest('#filterPopup')) {
                document.getElementById('filterPopup').style.display = 'none';
            }
        });

        document.getElementById('selectAllFilter').onclick = () => {
            document.querySelectorAll('#filterOptions input')
                .forEach(i => i.checked = true);

            activeFilters[currentKey] = [...document.querySelectorAll('#filterOptions input')]
                .map(i => i.value);

            applyFilters();
        };

        document.getElementById('unselectAllFilter').onclick = () => {
            delete activeFilters[currentKey];
            applyFilters();
        };
    </script>

@endsection
