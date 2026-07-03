@extends('layouts.app')

@section('title', 'Permintaan Kendaraan')

@section('content')
    <style>
        .table-danger {
            background: #ffe5e5 !important;
        }

        .table-warning {
            background: #fff3cd !important;
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Permintaan Kendaraan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Permintaan Kendaraan</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('form-permintaan-kendaraan.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Permintaan
                    </a>

                    <a href="{{ route('permintaan-kendaraan.riwayat') }}" class="btn btn-outline">
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

                            <th class="filterable" data-key="nama">
                                Nama <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="divisi">
                                Divisi <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-lg-table-cell">Tanggal</th>
                            <th class="d-none d-lg-table-cell">Jam</th>
                            <th class="d-none d-lg-table-cell">Tujuan</th>
                            <th class="d-none d-md-table-cell">Jumlah Kendaraan</th>

                            <th class="filterable" data-key="status">
                                Status <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($permintaan as $i => $p)
                            @php
                                $detail = $p->details->first();

                                $isLateProcess = false;
                                $isLateComplete = false;

                                if ($detail) {
                                    $startTime = \Carbon\Carbon::parse(
                                        $detail->tanggal_mulai . ' ' . $detail->jam_mulai,
                                    );

                                    $endTime = \Carbon\Carbon::parse(
                                        $detail->tanggal_selesai . ' ' . $detail->jam_selesai,
                                    );

                                    // 1 jam sebelum mulai
                                    $warningProcess = $startTime->copy()->subHour();

                                    // 1 jam setelah selesai
                                    $warningComplete = $endTime->copy()->addHour();

                                    // BELUM DIPROSES
                                    if ($p->status == 'disetujui' && now()->greaterThanOrEqualTo($warningProcess)) {
                                        $isLateProcess = true;
                                    }

                                    // BELUM DISELESAIKAN
                                    if ($p->status == 'proses' && now()->greaterThanOrEqualTo($warningComplete)) {
                                        $isLateComplete = true;
                                    }
                                }
                            @endphp

                            <tr class="
                                    {{ $isLateProcess ? 'table-danger' : '' }}
                                    {{ $isLateComplete ? 'table-warning' : '' }}
                                "
                                data-name="{{ strtolower($p->nama . ' ' . ($p->divisi->nama_divisi ?? '')) }}"
                                data-nama="{{ strtolower($p->nama) }}"
                                data-divisi="{{ strtolower($p->divisi->nama_divisi ?? '') }}"
                                data-status="{{ strtolower($p->status) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_permohonan }}</td>
                                <td>{{ $p->nama }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi ?? '-' }}</td>

                                <td class="d-none d-lg-table-cell">
                                    @if ($detail)
                                        {{ \Carbon\Carbon::parse($detail->tanggal_mulai)->format('d M Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($detail->tanggal_selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @if ($detail)
                                        {{ $detail->jam_mulai }} - {{ $detail->jam_selesai }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="d-none d-lg-table-cell">{{ $detail->tempat_tujuan ?? '-' }}</td>

                                <td class="d-none d-md-table-cell">
                                    {{ $p->details->sum('jumlah') }}
                                </td>

                                <td>
                                    @php
                                        $color = match ($p->status) {
                                            'menunggu konfirmasi' => 'secondary',
                                            'disetujui' => 'info',
                                            'ditolak' => 'danger',
                                            'proses' => 'warning',
                                            'selesai' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>

                                <td style="white-space:nowrap">

                                    <a href="{{ route('permintaan-kendaraan.show', $p->id_permohonan) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('permintaan-kendaraan.edit', $p->id_permohonan) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('permintaan-kendaraan.destroy', $p->id_permohonan) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    {{-- APPROVAL --}}
                                    @if ($p->status == 'menunggu konfirmasi')
                                        <button class="btn btn-sm btn-success" type="button" data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $p->id_permohonan }}">

                                            <i class="fas fa-check"></i>
                                            Setujui
                                        </button>

                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $p->id_permohonan }}">

                                            <i class="fas fa-times"></i>
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- DIPAKAI --}}
                                    @if ($p->status == 'disetujui')
                                        <form method="POST"
                                            action="{{ route('permintaan-kendaraan.process', $p->id_permohonan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-info">Diproses</button>
                                        </form>
                                    @endif

                                    {{-- SELESAI --}}
                                    @if ($p->status == 'proses')
                                        <form method="POST"
                                            action="{{ route('permintaan-kendaraan.complete', $p->id_permohonan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success" onclick="return confirm('Selesaikan?')">
                                                Selesai
                                            </button>
                                        </form>
                                    @endif

                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Belum ada data permintaan kendaraan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @foreach ($permintaan as $p)
                @if ($p->status == 'menunggu konfirmasi')
                    <div class="modal fade" id="approveModal{{ $p->id_permohonan }}" tabindex="-1">

                        <div class="modal-dialog modal-lg modal-dialog-centered">

                            <div class="modal-content border-0 rounded-4">

                                <div class="modal-header border-0">

                                    <div>
                                        <h5 class="fw-bold mb-1">
                                            Assign Kendaraan
                                        </h5>

                                        <small class="text-muted">
                                            Pilih kendaraan yang tersedia
                                        </small>
                                    </div>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    <form method="POST"
                                        action="{{ route('permintaan-kendaraan.approve', $p->id_permohonan) }}">

                                        @csrf

                                        @foreach ($p->details as $detail)
                                            <div class="border rounded-4 p-3 mb-4">

                                                <div class="fw-semibold mb-2">
                                                    {{ $detail->keperluan }}
                                                </div>

                                                <div class="text-muted small mb-3">
                                                    {{ $detail->tanggal_mulai }}
                                                    {{ $detail->jam_mulai }}
                                                    -
                                                    {{ $detail->tanggal_selesai }}
                                                    {{ $detail->jam_selesai }}
                                                </div>

                                                <div class="mb-2">
                                                    <span class="badge bg-info">
                                                        Butuh {{ $detail->jumlah }} kendaraan
                                                    </span>
                                                </div>

                                                <label class="form-label">
                                                    Pilih Kendaraan
                                                </label>

                                                <div class="kendaraan-wrapper" data-max="{{ $detail->jumlah }}">

                                                    @forelse($detail->availableVehicles as $k)
                                                        <label
                                                            class="kendaraan-item d-flex align-items-center gap-2 border rounded-3 p-2 mb-2">

                                                            <input type="checkbox" name="kendaraan[{{ $detail->id }}][]"
                                                                value="{{ $k->id_kendaraan }}"
                                                                class="kendaraan-checkbox">

                                                            <div>
                                                                <div class="fw-semibold">
                                                                    {{ $k->plat_nomor }}
                                                                </div>

                                                                <small class="text-muted">
                                                                    {{ $k->merk }} {{ $k->model }}
                                                                </small>
                                                            </div>

                                                        </label>

                                                    @empty

                                                        <div class="text-muted">
                                                            Tidak ada kendaraan tersedia
                                                        </div>
                                                    @endforelse

                                                </div>

                                                <small class="text-muted">
                                                    Maksimal pilih {{ $detail->jumlah }} kendaraan
                                                </small>

                                                <small class="text-muted">
                                                    Maksimal pilih {{ $detail->jumlah }} kendaraan
                                                </small>

                                            </div>
                                        @endforeach

                                        <div class="d-flex justify-content-end gap-2">

                                            <button type="button" class="btn btn-light rounded-pill px-4"
                                                data-bs-dismiss="modal">

                                                Batal
                                            </button>

                                            <button type="submit" class="btn btn-success rounded-pill px-4">

                                                <i class="fas fa-check me-1"></i>
                                                Setujui

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>
                @endif
            @endforeach

            @foreach ($permintaan as $p)
                @if ($p->status == 'menunggu konfirmasi')
                    <div class="modal fade" id="rejectModal{{ $p->id_permohonan }}" tabindex="-1">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <form method="POST"
                                    action="{{ route('permintaan-kendaraan.reject', $p->id_permohonan) }}">

                                    @csrf

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Tolak Permintaan Kendaraan
                                        </h5>

                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label">
                                                Catatan Penolakan
                                            </label>

                                            <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan...">{{ $p->catatan ?? '' }}</textarea>

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

        </div>

        <div class="mt-3 content-padding">
            {{ $permintaan->withQueryString()->links() }}
        </div>
    </main>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const q = this.value.toLowerCase();

            document.querySelectorAll('tbody tr').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });

        document.querySelectorAll('.kendaraan-wrapper').forEach(wrapper => {

            const max = parseInt(wrapper.dataset.max);

            wrapper.querySelectorAll('.kendaraan-checkbox').forEach(checkbox => {

                checkbox.addEventListener('change', function() {

                    const checked = wrapper.querySelectorAll(
                        '.kendaraan-checkbox:checked'
                    );

                    if (checked.length > max) {

                        this.checked = false;

                        alert(`Maksimal hanya ${max} kendaraan`);
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
