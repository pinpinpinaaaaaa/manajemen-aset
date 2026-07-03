@extends('layouts.app')

@section('title', 'Detail Vendor')

@section('content')
    <main class="main-content">
        <div class="content-padding show-page">

            <div class="page-header">
                <h1 class="page-title">Detail Vendor</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('vendor.index') }}" class="breadcrumb-link">Vendor</a>
                    <span class="separator">/</span>
                    <span class="current">{{ $vendor->id_vendor }}</span>
                </nav>
            </div>

            <div class="show-info-card">
                <div class="show-info-left">
                    <h2 class="show-info-title">
                        {{ $vendor->nama_perusahaan }}
                    </h2>

                    <p class="show-info-subtitle">
                        {{ $vendor->bidang_usaha ?: 'Bidang usaha belum diisi' }}
                    </p>
                </div>

                <div class="show-info-right text-end">
                    <span class="show-badge show-badge-primary">
                        Vendor
                    </span>

                    <div class="fw-bold mt-2">
                        {{ $vendor->contact_person ?: '-' }}
                    </div>

                    <small class="text-muted">
                        {{ $vendor->jabatan_cp ?: 'Contact Person' }}
                    </small>
                </div>
            </div>

            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary"></i>
                    Informasi Vendor
                </h5>

                <table class="show-detail-table">
                    <tr>
                        <th width="220">ID Vendor</th>
                        <td>{{ $vendor->id_vendor }}</td>
                    </tr>

                    <tr>
                        <th>Nama Perusahaan</th>
                        <td>{{ $vendor->nama_perusahaan }}</td>
                    </tr>

                    <tr>
                        <th>Bidang Usaha</th>
                        <td>{{ $vendor->bidang_usaha ?: '-' }}</td>
                    </tr>

                    <tr>
                        <th>Contact Person</th>
                        <td>{{ $vendor->contact_person ?: '-' }}</td>
                    </tr>

                    <tr>
                        <th>Jabatan Contact Person</th>
                        <td>{{ $vendor->jabatan_cp ?: '-' }}</td>
                    </tr>

                    <tr>
                        <th>No. Telepon Contact Person</th>
                        <td>{{ $vendor->no_telp_cp ?: '-' }}</td>
                    </tr>

                    <tr>
                        <th>Email Perusahaan</th>
                        <td>
                            @if ($vendor->email_perusahaan)
                                <a href="mailto:{{ $vendor->email_perusahaan }}">
                                    {{ $vendor->email_perusahaan }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Alamat</th>
                        <td>
                            {!! $vendor->alamat ? nl2br(e($vendor->alamat)) : '-' !!}
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>
                            {{ $vendor->created_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>
                            {{ $vendor->updated_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            <div class="show-detail-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-file-alt text-primary"></i>
                    Dokumen Vendor
                </h5>

                <table class="show-detail-table">

                    <tr>
                        <th width="220">Akta Perusahaan</th>
                        <td>
                            @if ($vendor->akta)
                                <a href="{{ asset('storage/' . $vendor->akta) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file"></i> Lihat File
                                </a>
                            @elseif ($vendor->akta_link)
                                <a href="{{ $vendor->akta_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-link"></i> Buka Link
                                </a>
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>NIB</th>
                        <td>
                            @if ($vendor->nib)
                                <a href="{{ asset('storage/' . $vendor->nib) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file"></i> Lihat File
                                </a>
                            @elseif ($vendor->nib_link)
                                <a href="{{ $vendor->nib_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-link"></i> Buka Link
                                </a>
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>NPWP</th>
                        <td>
                            @if ($vendor->npwp)
                                <a href="{{ asset('storage/' . $vendor->npwp) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file"></i> Lihat File
                                </a>
                            @elseif ($vendor->npwp_link)
                                <a href="{{ $vendor->npwp_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-link"></i> Buka Link
                                </a>
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Pakta Integritas</th>
                        <td>
                            @if ($vendor->pakta_integritas)
                                <a href="{{ asset('storage/' . $vendor->pakta_integritas) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file"></i> Lihat File
                                </a>
                            @elseif ($vendor->pakta_integritas_link)
                                <a href="{{ $vendor->pakta_integritas_link }}" target="_blank"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-link"></i> Buka Link
                                </a>
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>

                </table>
            </div>

            <div class="show-action">
                <a href="{{ route('vendor.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.edit', $vendor->id_vendor) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a>

                    <form action="{{ route('vendor.destroy', $vendor->id_vendor) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus vendor ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>
@endsection
