@extends('layouts.app')

@section('title', 'Ekspedisi')

@section('content')
    <style>
        .table-danger {
            background-color: #ffe5e5 !important;
        }

        .table-warning {
            background-color: #fff6d6 !important;
        }

        .modal-content {
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.18);
        }

        .modal-backdrop.show {
            opacity: .45;
            backdrop-filter: blur(4px);
        }

        .modal-header {
            background: linear-gradient(to right, #f8fafc, #ffffff);
        }

        .modal-dialog {
            max-width: 700px;
        }

        .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }
    </style>
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Ekspedisi</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <span class="current">Ekspedisi</span>
                </nav>
            </div>

            <div class="controls-section">
                <div class="controls-left">

                    <a href="{{ route('form-ekspedisi.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Ekspedisi
                    </a>

                    <a href="{{ route('ekspedisi.riwayat') }}" class="btn btn-outline">
                        <i class="fas fa-history"></i> Riwayat
                    </a>

                    <button class="btn btn-outline" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Reload
                    </button>

                </div>

                <div class="controls-right">
                    <x-per-page-selector :perPage="$perPage" />
                    <span class="search-label">Search:</span>
                    <input id="searchInput" class="search-input" placeholder="Cari pengirim / tujuan...">
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">No</th>
                            <th class="d-none d-lg-table-cell">ID</th>

                            <th class="filterable" data-key="pengirim">
                                Pengirim <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable d-none d-lg-table-cell" data-key="tujuan">
                                Tujuan <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="d-none d-md-table-cell">Jenis</th>

                            <th class="filterable" data-key="approval">
                                Approval <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th class="filterable" data-key="pengiriman">
                                Pengiriman <i class="fas fa-filter filter-icon"></i>
                            </th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $i => $e)
                            <tr data-name="{{ strtolower($e->nama_pengirim . ' ' . $e->nama_penerima . ' ' . ($e->instansi_penerima ?? '')) }}"
                                data-pengirim="{{ strtolower($e->nama_pengirim) }}"
                                data-tujuan="{{ strtolower($e->nama_penerima) }}"
                                data-approval="{{ strtolower(explode('_', $e->decision_status)[0]) }}"
                                data-pengiriman="{{ strtolower(optional($e->pengiriman)->status_pengiriman ?? '') }}">

                                <td class="d-none d-md-table-cell">{{ $i + 1 }}</td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="table-id">
                                        {{ $e->id_ekspedisi }}
                                    </div>

                                    <div class="table-subtext">
                                        {{ $e->created_at->format('d M Y H:i') }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $e->nama_pengirim }}
                                    </div>

                                    <div class="table-subtext">
                                        {{ $e->divisi_pengirim->nama_divisi ?? '-' }}
                                    </div>
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    <div class="fw-semibold">
                                        {{ $e->instansi_penerima ?? '-' }}
                                    </div>

                                    <div class="table-subtext">
                                        {{ $e->nama_penerima }}
                                    </div>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    {{ ucfirst(optional($e->pengiriman)->jenis_kurir ?? '-') }}

                                </td>

                                <td>
                                    @php
                                        $color = match ($e->decision_status) {
                                            'draft' => 'secondary',
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst(explode('_', $e->decision_status)[0]) }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $status = optional($e->pengiriman)->status_pengiriman;
                                        $color = match ($status) {
                                            'belum_dikirim' => 'secondary',
                                            'dikirim' => 'info',
                                            'diterima' => 'warning',
                                            'selesai' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($status ?? '-') }}
                                    </span>
                                </td>

                                {{-- =========================
                                    AKSI
                                ========================= --}}
                                <td style="white-space:nowrap">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('ekspedisi.show', $e->id_ekspedisi) }}"
                                        class="btn btn-sm btn-primary">

                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('ekspedisi.edit', $e->id_ekspedisi) }}"
                                        class="btn btn-sm btn-warning">

                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- DELETE --}}
                                    @if (in_array($e->decision_status, ['draft', 'menunggu_persetujuan']))
                                        <form action="{{ route('ekspedisi.destroy', $e->id_ekspedisi) }}" method="POST"
                                            style="display:inline;">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin hapus data ini?')">

                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>
                                    @endif

                                    {{-- APPROVAL --}}
                                    @if ($e->decision_status == 'menunggu_persetujuan')
                                        <form method="POST" action="{{ route('ekspedisi.approve', $e->id_ekspedisi) }}"
                                            style="display:inline;">

                                            @csrf

                                            <button class="btn btn-sm btn-success">

                                                <i class="fas fa-check"></i>
                                                Setujui
                                            </button>

                                        </form>

                                        <button class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $e->id_ekspedisi }}">

                                            <i class="fas fa-times"></i>
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- =========================
                                            FORM KIRIM
                                        ========================= --}}
                                    @if ($e->decision_status == 'disetujui' && optional($e->pengiriman)->status_pengiriman == 'belum_dikirim')
                                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="modal"
                                            data-bs-target="#kirimForm{{ $e->id_ekspedisi }}">

                                            <i class="fas fa-paper-plane me-1"></i>
                                            Proses Kirim
                                        </button>
                                    @endif

                                    {{-- =========================
                                            FORM DITERIMA
                                        ========================= --}}
                                    @if (optional($e->pengiriman)->status_pengiriman == 'dikirim')
                                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="modal"
                                            data-bs-target="#terimaForm{{ $e->id_ekspedisi }}">

                                            <i class="fas fa-box-open me-1"></i>
                                            Sudah Diterima
                                        </button>
                                    @endif

                                    {{-- =========================
                                            COMPLETE
                                        ========================= --}}
                                    @if (optional($e->pengiriman)->status_pengiriman == 'diterima' &&
                                            $e->pengiriman->waktu_diterima &&
                                            now()->gte(\Carbon\Carbon::parse($e->pengiriman->waktu_diterima)->addHours(24)))
                                        <form method="POST" action="{{ route('ekspedisi.complete', $e->id_ekspedisi) }}">

                                            @csrf

                                            <button class="btn btn-sm btn-info"
                                                onclick="return confirm('Selesaikan ekspedisi ini?')">

                                                <i class="fas fa-check-circle me-1"></i>
                                                Selesai
                                            </button>

                                        </form>
                                    @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada data ekspedisi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            @foreach ($data as $e)
                {{-- =======================================================
                                        COLLAPSE FORM KIRIM
                                    ======================================================= --}}
                @if ($e->decision_status == 'disetujui' && optional($e->pengiriman)->status_pengiriman == 'belum_dikirim')
                    <div class="modal fade" id="kirimForm{{ $e->id_ekspedisi }}" tabindex="-1" aria-hidden="true">

                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4">

                                {{-- HEADER --}}
                                <div class="modal-header border-0 px-4 pt-4">

                                    <div>
                                        <h5 class="modal-title fw-bold" id="kirimLabel{{ $e->id_ekspedisi }}">
                                            Proses Pengiriman
                                        </h5>

                                        <small class="text-muted">
                                            Lengkapi data kurir sebelum paket dikirim
                                        </small>
                                    </div>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>

                                </div>

                                {{-- BODY --}}
                                <div class="modal-body px-4 pb-4">

                                    <form method="POST" action="{{ route('ekspedisi.process', $e->id_ekspedisi) }}">

                                        @csrf

                                        {{-- JENIS KURIR --}}
                                        <div class="mb-4">

                                            <label class="form-label fw-semibold">
                                                Jenis Kurir
                                            </label>

                                            <select name="jenis_kurir" class="form-select jenisKurirSelect" required>

                                                <option value="">
                                                    -- Pilih Jenis Kurir --
                                                </option>

                                                <option value="internal">
                                                    Internal
                                                </option>

                                                <option value="eksternal">
                                                    Eksternal
                                                </option>

                                            </select>

                                        </div>

                                        {{-- INTERNAL --}}
                                        <div class="internalWrapper d-none">

                                            <div class="mb-4">

                                                <label class="form-label fw-semibold">
                                                    Pilih Kurir Internal
                                                </label>

                                                <select name="id_user_kurir" class="form-select internalSelect">

                                                    <option value="">
                                                        -- Pilih Kurir --
                                                    </option>

                                                    @forelse ($users as $u)
                                                        <option value="{{ $u->id_user }}">
                                                            {{ $u->name }}
                                                        </option>
                                                    @empty
                                                        <option value="">
                                                            Tidak ada user
                                                        </option>
                                                    @endforelse

                                                </select>

                                            </div>

                                        </div>

                                        {{-- EKSTERNAL --}}
                                        <div class="eksternalWrapper d-none">

                                            <div class="mb-4">

                                                <label class="form-label fw-semibold">
                                                    Nama Ekspedisi
                                                </label>

                                                <input type="text" name="nama_jasa_ekspedisi" class="form-control"
                                                    placeholder="Contoh: JNE / J&T / SiCepat">

                                            </div>

                                            <div class="mb-4">

                                                <label class="form-label fw-semibold">
                                                    Nomor Resi
                                                </label>

                                                <input type="text" name="no_resi" class="form-control"
                                                    placeholder="Masukkan nomor resi">

                                            </div>

                                        </div>

                                        <div class="d-flex justify-content-end gap-2">

                                            <button type="button" class="btn btn-light rounded-pill px-4"
                                                data-bs-dismiss="modal">

                                                Batal
                                            </button>

                                            <button type="submit" class="btn btn-info rounded-pill px-4 text-white">

                                                <i class="fas fa-paper-plane me-1"></i>
                                                Kirim Paket
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                @if (optional($e->pengiriman)->status_pengiriman == 'dikirim')
                    <div class="modal fade" id="terimaForm{{ $e->id_ekspedisi }}" tabindex="-1" aria-hidden="true">

                        <div class="modal-dialog modal-lg modal-dialog-centered">

                            <div class="modal-content border-0">

                                <div class="modal-header border-0 px-4 py-3">

                                    <div>
                                        <h6 class="mb-1 fw-bold">
                                            Konfirmasi Penerimaan
                                        </h6>

                                        <small class="text-muted">
                                            Upload bukti penerimaan dan tanda tangan
                                        </small>
                                    </div>

                                </div>

                                <div class="modal-body px-4 pb-4">

                                    <form method="POST" enctype="multipart/form-data"
                                        action="{{ route('ekspedisi.received', $e->id_ekspedisi) }}">

                                        @csrf

                                        {{-- NAMA --}}
                                        <div class="mb-4">

                                            <label class="form-label fw-semibold">
                                                Nama Penerima
                                            </label>

                                            <input type="text" name="nama_penerima_ttd" class="form-control"
                                                placeholder="Opsional">

                                        </div>

                                        {{-- JABATAN --}}
                                        <div class="mb-4">

                                            <label class="form-label fw-semibold">
                                                Jabatan
                                            </label>

                                            <input type="text" name="jabatan_penerima" class="form-control"
                                                placeholder="Opsional">

                                            <small class="text-muted">
                                                Jika kosong maka sistem akan memberi catatan bahwa paket
                                                diterima tanpa identitas penerima.
                                            </small>

                                        </div>

                                        {{-- FOTO --}}
                                        <div class="mb-4">

                                            <label class="form-label fw-semibold">
                                                Foto Bukti
                                            </label>

                                            <input type="file" name="foto_bukti" class="form-control"
                                                accept="image/*" required>

                                        </div>

                                        {{-- TTD --}}
                                        <div class="mb-4">

                                            <label class="form-label fw-semibold">
                                                Jenis Tanda Tangan
                                            </label>

                                            <select name="jenis_ttd" class="form-select jenisTTD" required>

                                                <option value="">
                                                    -- Pilih Jenis TTD --
                                                </option>

                                                <option value="scan">
                                                    Scan TTD Basah
                                                </option>

                                                <option value="digital">
                                                    TTD Digital
                                                </option>

                                            </select>

                                        </div>

                                        {{-- SCAN --}}
                                        <div class="scanWrapper d-none">

                                            <div class="mb-4">

                                                <label class="form-label fw-semibold">
                                                    Upload TTD Scan
                                                </label>

                                                <input type="file" name="file_ttd_scan" class="form-control">

                                            </div>

                                        </div>

                                        {{-- DIGITAL --}}
                                        <div class="digitalWrapper d-none">

                                            <div class="mb-3">

                                                <label class="form-label fw-semibold">
                                                    Tanda Tangan Digital
                                                </label>

                                                <div class="border rounded-3 p-2 bg-light">

                                                    <canvas class="signature-pad" width="600" height="200"
                                                        style="width:100%; height:200px; border:1px dashed #cbd5e1; border-radius:12px;">
                                                    </canvas>

                                                </div>

                                                <input type="hidden" name="ttd_digital" class="ttd-input">

                                                <div class="d-flex gap-2 mt-3">

                                                    <button type="button"
                                                        class="btn btn-sm btn-secondary clear-signature">
                                                        <i class="fas fa-eraser me-1"></i>
                                                        Hapus TTD
                                                    </button>

                                                </div>

                                                <small class="text-muted">
                                                    Tanda tangan menggunakan mouse atau touchscreen.
                                                </small>

                                            </div>

                                        </div>

                                        <button type="submit" class="btn btn-warning rounded-pill px-4">

                                            <i class="fas fa-save me-1"></i>
                                            Simpan Penerimaan
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>
                    </div>
                @endif
            @endforeach
            @foreach ($data as $e)
                @if ($e->decision_status == 'menunggu_persetujuan')
                    <div class="modal fade" id="rejectModal{{ $e->id_ekspedisi }}" tabindex="-1">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <form method="POST" action="{{ route('ekspedisi.reject', $e->id_ekspedisi) }}">

                                    @csrf

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Tolak Ekspedisi
                                        </h5>

                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">

                                            <label class="form-label">
                                                Catatan Penolakan
                                            </label>

                                            <textarea name="keterangan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan...">{{ $e->catatan ?? '' }}</textarea>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Batal
                                        </button>

                                        <button type="submit" class="btn btn-danger">
                                            Tolak Ekspedisi
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
            {{ $data->withQueryString()->links() }}
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // =========================
        // SIGNATURE PAD
        // =========================
        document.querySelectorAll('.digitalWrapper').forEach(wrapper => {

            const canvas = wrapper.querySelector('.signature-pad');

            if (!canvas) return;

            const signaturePad = new SignaturePad(canvas);

            const hiddenInput =
                wrapper.querySelector('.ttd-input');

            // simpan base64 sebelum submit
            wrapper.closest('form')
                .addEventListener('submit', function() {

                    if (!signaturePad.isEmpty()) {

                        hiddenInput.value =
                            signaturePad.toDataURL('image/png');
                    }
                });

            // tombol clear
            wrapper.querySelector('.clear-signature')
                .addEventListener('click', function() {

                    signaturePad.clear();
                    hiddenInput.value = '';
                });
        });
    </script>
    <script>
        // =========================
        // TOGGLE JENIS KURIR
        // =========================
        document.querySelectorAll('.jenisKurirSelect').forEach(select => {

            select.addEventListener('change', function() {

                const parent = this.closest('form');

                const internal =
                    parent.querySelector('.internalWrapper');

                const eksternal =
                    parent.querySelector('.eksternalWrapper');

                internal.classList.add('d-none');
                eksternal.classList.add('d-none');

                if (this.value === 'internal') {
                    internal.classList.remove('d-none');
                }

                if (this.value === 'eksternal') {
                    eksternal.classList.remove('d-none');
                }
            });
        });


        // =========================
        // TOGGLE TTD
        // =========================
        document.querySelectorAll('.jenisTTD').forEach(select => {

            select.addEventListener('change', function() {

                const parent = this.closest('form');

                const scan =
                    parent.querySelector('.scanWrapper');

                const digital =
                    parent.querySelector('.digitalWrapper');

                scan.classList.add('d-none');
                digital.classList.add('d-none');

                if (this.value === 'scan') {
                    scan.classList.remove('d-none');
                }

                if (this.value === 'digital') {
                    digital.classList.remove('d-none');
                }
            });
        });
    </script>

@endsection
