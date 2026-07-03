@extends('layouts.app')

@section('title', 'Pengadaan Barang & Jasa')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Pengadaan Barang & Jasa</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Pengadaan</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('form-pengadaan-barang.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Pengadaan
                    </a>

                    <a href="{{ route('pengadaan-barang.riwayat') }}" class="btn btn-outline">
                        <i class="fas fa-history"></i> Riwayat
                    </a>

                    <button class="btn btn-outline" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input id="searchInput" class="search-input" placeholder="Cari pengaju / divisi...">
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
                            <th class="d-none d-lg-table-cell">Total Biaya</th>

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
                        @forelse($pengadaan as $i => $p)
                            @php
                                $today = \Carbon\Carbon::today();
                                $tgl = \Carbon\Carbon::parse($p->tanggal_kebutuhan);

                                $isOverdue = $p->status !== 'Selesai' && $tgl->lt($today);
                                $isToday = $p->status !== 'Selesai' && $tgl->isSameDay($today);
                            @endphp

                            <tr class="{{ $isOverdue ? 'table-danger' : '' }} {{ !$isOverdue && $isToday ? 'table-warning' : '' }}"
                                data-name="{{ strtolower($p->nama_pengaju . ' ' . ($p->divisi->nama_divisi ?? '')) }}"
                                data-pengaju="{{ strtolower($p->nama_pengaju) }}"
                                data-divisi="{{ strtolower($p->divisi->nama_divisi ?? '') }}"
                                data-status="{{ strtolower($p->status) }}"
                                data-approval="{{ strtolower($p->decision_status) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_pengadaan }}</td>
                                <td>{{ $p->nama_pengaju }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi ?? '-' }}</td>

                                <td class="d-none d-lg-table-cell">
                                    {{ \Carbon\Carbon::parse($p->tanggal_kebutuhan)->format('d M Y') }}
                                </td>

                                <td class="d-none d-md-table-cell">{{ $p->details->count() }}</td>

                                <td class="d-none d-lg-table-cell">
                                    Rp {{ number_format($p->total_biaya, 0, ',', '.') }}
                                </td>

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

                                    <a href="{{ route('pengadaan-barang.show', $p->id_pengadaan) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('pengadaan-barang.edit', $p->id_pengadaan) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('pengadaan-barang.destroy', $p->id_pengadaan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    {{-- APPROVAL --}}
                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <form method="POST"
                                            action="{{ route('pengadaan-barang.approve', $p->id_pengadaan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Setujui</button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $p->id_pengadaan }}">
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- PROSES --}}
                                    @if ($p->decision_status == 'disetujui' && $p->status == 'Belum Diproses')
                                        <form method="POST"
                                            action="{{ route('pengadaan-barang.process', $p->id_pengadaan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-info">Proses</button>
                                        </form>
                                    @endif

                                    {{-- SELESAI --}}
                                    @if ($p->status == 'Sedang Diproses')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#completeModal{{ $p->id_pengadaan }}">
                                            Selesai
                                        </button>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Belum ada data pengadaan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pengadaan->withQueryString()->links() }}
            </div>

        </div>

        @foreach ($pengadaan as $p)
            @if ($p->decision_status == 'menunggu_persetujuan')
                <div class="modal fade" id="rejectModal{{ $p->id_pengadaan }}" tabindex="-1">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">

                            <form method="POST" action="{{ route('pengadaan-barang.reject', $p->id_pengadaan) }}">

                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Tolak Pengadaan
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label class="form-label">
                                            Alasan Penolakan
                                        </label>

                                        <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="submit" class="btn btn-danger">
                                        Tolak Pengadaan
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        @endforeach

        @foreach ($pengadaan as $p)
            @if ($p->status == 'Sedang Diproses')
                <div class="modal fade" id="completeModal{{ $p->id_pengadaan }}" tabindex="-1">

                    <div class="modal-dialog modal-xl modal-dialog-centered">

                        <div class="modal-content">

                            <form method="POST" action="{{ route('pengadaan-barang.complete', $p->id_pengadaan) }}">

                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Selesaikan Pengadaan
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="alert alert-info">
                                        Tentukan lokasi dan kategori aset untuk
                                        setiap barang yang akan dibuat.
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">

                                            <thead>
                                                <tr>
                                                    <th style="width:25%">Barang</th>
                                                    <th>Distribusi Aset</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach ($p->details as $detail)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $detail->nama_barang }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                Total {{ $detail->jumlah }} unit
                                                            </small>
                                                        </td>

                                                        <td colspan="3">

                                                            <div id="detail-{{ $detail->id }}">

                                                                <div class="row g-2 mb-2 distribusi-row">

                                                                    <div class="col-md-2">
                                                                        <input type="number" min="1"
                                                                            max="{{ $detail->jumlah }}"
                                                                            class="form-control qty-input"
                                                                            name="items[{{ $detail->id }}][0][qty]"
                                                                            required>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <select
                                                                            name="items[{{ $detail->id }}][0][id_jenis_barang]"
                                                                            class="form-select" required>

                                                                            <option value="">Jenis</option>

                                                                            @foreach ($jenisBarang as $jenis)
                                                                                <option
                                                                                    value="{{ $jenis->id_jenis_barang }}">
                                                                                    {{ $jenis->nama_barang }}
                                                                                </option>
                                                                            @endforeach

                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <select
                                                                            name="items[{{ $detail->id }}][0][id_gedung]"
                                                                            class="form-select gedung-select" required>

                                                                            <option value="">Gedung</option>

                                                                            @foreach ($gedung as $g)
                                                                                <option value="{{ $g->id_gedung }}">
                                                                                    {{ $g->nama_gedung }}
                                                                                </option>
                                                                            @endforeach

                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <select
                                                                            name="items[{{ $detail->id }}][0][id_ruangan]"
                                                                            class="form-select ruangan-select" required>

                                                                            <option value="">Ruangan</option>

                                                                            @foreach ($ruangan as $r)
                                                                                <option value="{{ $r->id_ruangan }}"
                                                                                    data-gedung="{{ $r->id_gedung }}">
                                                                                    {{ $r->nama_ruangan }}
                                                                                </option>
                                                                            @endforeach

                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-row">
                                                                            ×
                                                                        </button>
                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <button type="button"
                                                                class="btn btn-sm btn-success add-row mt-2"
                                                                data-detail="{{ $detail->id }}"
                                                                data-max="{{ $detail->jumlah }}">
                                                                + Tambah Lokasi
                                                            </button>

                                                            <small class="text-primary d-block mt-1 remaining-info">
                                                                Sisa: {{ $detail->jumlah }}
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>

                                        </table>
                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="submit" class="btn btn-success">
                                        Selesaikan Pengadaan
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        @endforeach
        <script>
            document.addEventListener('change', function(e) {

                if (e.target.classList.contains('gedung-select')) {

                    const gedungId = e.target.value;

                    const row = e.target.closest('.distribusi-row');

                    const ruanganSelect =
                        row.querySelector('.ruangan-select');

                    ruanganSelect.value = '';

                    ruanganSelect.querySelectorAll('option')
                        .forEach(option => {

                            if (!option.dataset.gedung) {
                                option.hidden = false;
                                return;
                            }

                            option.hidden =
                                option.dataset.gedung != gedungId;
                        });
                }

            });

            function updateRemaining(detailId) {

                const container =
                    document.getElementById('detail-' + detailId);

                const max =
                    parseInt(
                        document.querySelector(
                            `.add-row[data-detail="${detailId}"]`
                        ).dataset.max
                    );

                let used = 0;

                container.querySelectorAll('.qty-input')
                    .forEach(input => {

                        used += parseInt(input.value || 0);

                    });

                const remain = max - used;

                const info =
                    container.parentElement
                    .querySelector('.remaining-info');

                info.innerHTML = `Sisa: ${remain}`;

                container.querySelectorAll('.qty-input')
                    .forEach(input => {

                        const current =
                            parseInt(input.value || 0);

                        input.max = current + remain;

                    });

                const addBtn =
                    document.querySelector(
                        `.add-row[data-detail="${detailId}"]`
                    );

                addBtn.disabled = remain <= 0;
            }

            document.addEventListener('input', function(e) {

                if (e.target.classList.contains('qty-input')) {

                    const detailId =
                        e.target.name.match(/\[(\d+)\]/)[1];

                    let val =
                        parseInt(e.target.value || 0);

                    let max =
                        parseInt(e.target.max);

                    if (val > max) {
                        e.target.value = max;
                    }

                    updateRemaining(detailId);
                }

            });

            document.addEventListener('click', function(e) {

                if (e.target.classList.contains('add-row')) {

                    const detailId = e.target.dataset.detail;

                    const container =
                        document.getElementById('detail-' + detailId);

                    const index =
                        container.querySelectorAll('.distribusi-row').length;

                    const clone =
                        container.querySelector('.distribusi-row').cloneNode(true);

                    clone.querySelectorAll('input, select').forEach(el => {

                        el.value = '';

                        el.name = el.name.replace(
                            /\[\d+\]\[(\w+)\]$/,
                            '[' + index + '][$1]'
                        );
                    });

                    container.appendChild(clone);

                    updateRemaining(detailId);
                }

                if (e.target.classList.contains('remove-row')) {

                    const row =
                        e.target.closest('.distribusi-row');

                    const container =
                        row.parentElement;

                    if (container.querySelectorAll('.distribusi-row').length > 1) {

                        row.remove();

                        const detailId =
                            container.id.replace('detail-', '');

                        updateRemaining(detailId);
                    }
                }

            });
        </script>
    </main>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();

            document.querySelectorAll('tbody tr').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });
    </script>

@endsection
