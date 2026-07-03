@extends('layouts.app')

@section('title', 'Pengaduan Kerusakan')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Pengaduan Kerusakan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Pengaduan</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('form-pengaduan-kerusakan.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Pengaduan
                    </a>

                    <a href="{{ route('pengaduan-kerusakan.riwayat') }}" class="btn btn-outline">
                        <i class="fas fa-history"></i> Riwayat
                    </a>

                    <button class="btn btn-outline" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input id="searchInput" class="search-input" placeholder="Cari pelapor / divisi...">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>

                            <th class="filterable" data-key="pelapor">
                                Pelapor <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-md-table-cell" data-key="divisi">
                                Divisi <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-md-table-cell">Total Item</th>

                            <th class="filterable" data-key="approval">
                                Status <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-lg-table-cell">Tanggal</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $i => $p)
                            <tr data-name="{{ strtolower($p->nama_pelapor . ' ' . ($p->divisi->nama_divisi ?? '')) }}"
                                data-pelapor="{{ strtolower($p->nama_pelapor) }}"
                                data-divisi="{{ strtolower($p->divisi->nama_divisi ?? '') }}"
                                data-approval="{{ strtolower($p->decision_status) }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">{{ $p->id_pengaduan }}</td>
                                <td>{{ $p->nama_pelapor }}</td>
                                <td class="d-none d-md-table-cell">{{ $p->divisi->nama_divisi ?? '-' }}</td>

                                <td class="d-none d-md-table-cell">{{ $p->details->count() }}</td>

                                <td>
                                    @php
                                        $color = match ($p->decision_status) {
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->decision_status)) }}
                                    </span>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                </td>

                                <td style="white-space:nowrap">

                                    <a href="{{ route('pengaduan-kerusakan.show', $p->id_pengaduan) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <form action="{{ route('pengaduan-kerusakan.destroy', $p->id_pengaduan) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    {{-- APPROVAL --}}
                                    @if ($p->decision_status == 'menunggu_persetujuan')
                                        <form method="POST"
                                            action="{{ route('pengaduan-kerusakan.approve', $p->id_pengaduan) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Setujui</button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $p->id_pengaduan }}">
                                            Tolak
                                        </button>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada data pengaduan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @foreach ($data as $p)
            @if ($p->decision_status == 'menunggu_persetujuan')
                <div class="modal fade" id="rejectModal{{ $p->id_pengaduan }}" tabindex="-1">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">

                            <form method="POST" action="{{ route('pengaduan-kerusakan.reject', $p->id_pengaduan) }}">

                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Tolak Pengaduan Kerusakan
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
                                        Tolak Pengaduan
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        @endforeach

        <div class="mt-3 content-padding">
            {{ $data->withQueryString()->links() }}
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
    </script>

@endsection
