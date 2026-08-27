@extends('layouts.app')

@section('title', 'Detail Peminjaman Aset')

@section('content')

    <style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .custom-table th {
            background-color: #9ea1a3;
            color: #000000;
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .custom-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            background-color: #ffffff;
            vertical-align: middle;
        }

        .custom-table tbody tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .row-terlambat td {
            background-color: #ffe5e5 !important;
        }

        .badge-rusak-ringan {
            background: #ffc107;
            color: black;
        }

        .badge-rusak-berat {
            background: #dc3545;
            color: white;
        }

        .badge-baik {
            background: #198754;
            color: white;
        }

        .show-badge-info {
            background-color: #0dcaf0;
            color: #fff;
        }

        .show-badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }
    </style>

    <main class="main-content">
        <div class="content-padding show-page">

            {{-- HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Detail Peminjaman Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('peminjaman_aset.index') }}">Peminjaman Aset</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $data->id_peminjaman }}</span>
                </nav>
            </div>

            {{-- INFO RINGKAS --}}
            @php
                $totalItem = $data->details->sum('jumlah');
                $terlambat = $data->details
                    ->filter(function ($d) {
                        return $d->status_pengembalian == 'dipinjam' &&
                            \Carbon\Carbon::parse($d->tanggal_jatuh_tempo)->lt(\Carbon\Carbon::today());
                    })
                    ->count();

                $dikembalikan = $data->details->where('status_pengembalian', 'dikembalikan')->count();
            @endphp

            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">{{ $data->nama_pengaju }}</h2>
                    <p class="show-info-subtitle">{{ $data->divisi->nama_divisi ?? '-' }}</p>
                </div>

                <div class="show-info-right text-end">
                    @php
                        $statusClass = match ($data->status) {
                            'Belum Diproses' => 'show-badge-secondary',
                            'Sedang Dipinjam' => 'show-badge-info',
                            'Selesai' => 'show-badge-success',
                            default => 'show-badge-secondary',
                        };
                    @endphp

                    <span class="show-badge {{ $statusClass }}">
                        {{ $data->status }}
                    </span>

                    <div class="fw-bold mt-2">
                        {{ $totalItem }} Unit
                    </div>
                    <small class="text-muted">
                        {{ $dikembalikan }} / {{ $totalItem }} Dikembalikan •
                        {{ $terlambat }} Terlambat
                    </small>
                </div>
            </div>

            {{-- INFORMASI PEMINJAMAN --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i> Informasi Peminjaman
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $data->id_peminjaman }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $data->nama_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $data->email_pengaju }}</td>
                    </tr>
                    <tr>
                        <th>Divisi</th>
                        <td>{{ $data->divisi->nama_divisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td>{{ $data->alasan }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $data->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- DETAIL ASET --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-box text-warning"></i> Detail Aset
                </h5>
                @php
                    $grouped = $data->details->groupBy(function ($item) {
                        return $item->aset->nama_aset . '|' . $item->tanggal_pinjam . '|' . $item->tanggal_jatuh_tempo;
                    });
                @endphp

                <table class="show-detail-table custom-table">
                    <thead>
                        <tr>
                            <th>Nama Aset</th>
                            <th>Jumlah</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Lama (Hari)</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th>Tgl Kembali</th>
                            <th>Kondisi</th>
                            <th>Catatan Pengembalian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($grouped as $group)
                            @php
                                $first = $group->first();
                                $rowspan = $group->count();

                                $pinjam = \Carbon\Carbon::parse($first->tanggal_pinjam);
                                $tempo = \Carbon\Carbon::parse($first->tanggal_jatuh_tempo);
                                $lama = $pinjam->diffInDays($tempo);
                            @endphp

                            @foreach ($group as $i => $detail)
                                <tr>
                                    @if ($i == 0)
                                        {{-- NAMA ASET --}}
                                        <td rowspan="{{ $rowspan }}">
                                            {{ $first->aset->nama_aset }}
                                        </td>

                                        {{-- JUMLAH --}}
                                        <td rowspan="{{ $rowspan }}">
                                            {{ $rowspan }} Unit
                                        </td>

                                        {{-- TGL PINJAM --}}
                                        <td rowspan="{{ $rowspan }}">
                                            {{ $pinjam->format('d M Y') }}
                                        </td>

                                        {{-- JATUH TEMPO --}}
                                        <td rowspan="{{ $rowspan }}">
                                            {{ $tempo->format('d M Y') }}
                                        </td>

                                        {{-- LAMA --}}
                                        <td rowspan="{{ $rowspan }}">
                                            {{ $lama }} Hari
                                        </td>
                                    @endif

                                    {{-- KODE --}}
                                    <td>
                                        {{ $detail->aset->kode_aset }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        {{ ucfirst($detail->status_pengembalian) }}
                                    </td>

                                    {{-- TGL KEMBALI --}}
                                    <td>
                                        {{ $detail->tanggal_dikembalikan ? \Carbon\Carbon::parse($detail->tanggal_dikembalikan)->format('d M Y') : '-' }}
                                    </td>

                                    {{-- KONDISI --}}
                                    <td>
                                        {{ $detail->kondisi_kembali ?? '-' }}
                                    </td>

                                    {{-- CATATAN --}}
                                    <td>
                                        {{ $detail->catatan_pengembalian ?? '-' }}
                                    </td>

                                    {{-- AKSI --}}
                                    <td style="white-space:nowrap">
                                        @if ($detail->status_pengembalian == 'menunggu' && $data->decision_status == 'disetujui')
                                            <form method="POST" action="{{ route('peminjaman_aset.serahkanItem', $detail->id) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary"
                                                        onclick="return confirm('Serahkan aset ini ke peminjam?')">
                                                    Serahkan
                                                </button>
                                            </form>
                                        @elseif ($detail->status_pengembalian == 'dipinjam')
                                            <button type="button" class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#kembalikanModal{{ $detail->id }}">
                                                Kembalikan
                                            </button>
                                        @elseif ($detail->status_pengembalian == 'dikembalikan')
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @else
                                            <span class="badge bg-secondary">Menunggu persetujuan</span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- METADATA --}}
            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock text-dark"></i> Metadata
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ optional($data->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ optional($data->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            {{-- ACTION --}}
            <div class="show-action">
                <a href="{{ route('peminjaman_aset.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>

                @if ($data->status === 'Belum Diproses')
                    <a href="{{ route('peminjaman_aset.edit', $data->id_peminjaman) }}" class="btn btn-primary">
                        Edit
                    </a>
                @endif
            </div>

        </div>
        @foreach ($data->details as $detail)
            @if ($detail->status_pengembalian == 'dipinjam')
                <div class="modal fade" id="kembalikanModal{{ $detail->id }}" tabindex="-1">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">

                            <form method="POST" action="{{ route('peminjaman_aset.kembalikan', $detail->id) }}">

                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Pengembalian Aset
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label class="form-label">
                                            Kondisi Aset
                                        </label>

                                        <select name="kondisi_kembali" class="form-select" required>

                                            <option value="">
                                                Pilih Kondisi
                                            </option>

                                            <option value="baik">
                                                Baik
                                            </option>

                                            <option value="rusak_ringan">
                                                Rusak Ringan
                                            </option>

                                            <option value="rusak_berat">
                                                Rusak Berat
                                            </option>

                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">
                                            Catatan Pengembalian
                                        </label>

                                        <textarea name="catatan_pengembalian" class="form-control" rows="4"
                                            placeholder="Masukkan catatan pengembalian (opsional)"></textarea>
                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="submit" class="btn btn-success">
                                        Simpan Pengembalian
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        @endforeach
    </main>
@endsection
