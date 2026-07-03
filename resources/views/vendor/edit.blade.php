@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit Vendor</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('vendor.index') }}">Vendor</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Vendor</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('vendor.update', $vendor->id_vendor) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" class="form-control"
                            value="{{ old('nama_perusahaan', $vendor->nama_perusahaan) }}" required>
                        @error('nama_perusahaan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Bidang Usaha</label>
                        <input type="text" name="bidang_usaha" class="form-control"
                            value="{{ old('bidang_usaha', $vendor->bidang_usaha) }}">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Alamat Perusahaan</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $vendor->alamat) }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control"
                            value="{{ old('contact_person', $vendor->contact_person) }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">No Telp Contact Person</label>
                        <input type="text" name="no_telp_cp" class="form-control"
                            value="{{ old('no_telp_cp', $vendor->no_telp_cp) }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="email" name="email_perusahaan" class="form-control"
                            value="{{ old('email_perusahaan', $vendor->email_perusahaan) }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Akta Pendirian</label>

                        {{-- Dokumen Lama --}}
                        @if ($vendor->akta)
                            <div class="mb-2">
                                <span class="badge bg-primary">FILE</span>
                                <a href="{{ asset('storage/' . $vendor->akta) }}" target="_blank"
                                    class="btn btn-sm btn-info">
                                    Lihat File
                                </a>
                            </div>
                        @elseif($vendor->akta_link)
                            <div class="mb-2">
                                <span class="badge bg-success">LINK</span>
                                <a href="{{ $vendor->akta_link }}" target="_blank" class="btn btn-sm btn-success">
                                    Buka Link
                                </a>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="me-3">
                                <input type="radio" name="akta_type" value="file"
                                    {{ !$vendor->akta_link ? 'checked' : '' }}>
                                Upload File
                            </label>

                            <label>
                                <input type="radio" name="akta_type" value="link"
                                    {{ $vendor->akta_link ? 'checked' : '' }}>
                                Gunakan Link
                            </label>
                        </div>

                        <div class="akta-file">
                            <input type="file" name="akta" class="form-control">
                        </div>

                        <div class="akta-link mt-2" style="display:none;">
                            <input type="url" name="akta_link" class="form-control"
                                value="{{ old('akta_link', $vendor->akta_link) }}"
                                placeholder="https://drive.google.com/...">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">NIB</label>

                        {{-- Dokumen Lama --}}
                        @if ($vendor->nib)
                            <div class="mb-2">
                                <span class="badge bg-primary">FILE</span>
                                <a href="{{ asset('storage/' . $vendor->nib) }}" target="_blank"
                                    class="btn btn-sm btn-info">
                                    Lihat File
                                </a>
                            </div>
                        @elseif($vendor->nib_link)
                            <div class="mb-2">
                                <span class="badge bg-success">LINK</span>
                                <a href="{{ $vendor->nib_link }}" target="_blank" class="btn btn-sm btn-success">
                                    Buka Link
                                </a>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="me-3">
                                <input type="radio" name="nib_type" value="file"
                                    {{ !$vendor->nib_link ? 'checked' : '' }}>
                                Upload File
                            </label>

                            <label>
                                <input type="radio" name="nib_type" value="link"
                                    {{ $vendor->nib_link ? 'checked' : '' }}>
                                Gunakan Link
                            </label>
                        </div>

                        <div class="nib-file">
                            <input type="file" name="nib" class="form-control">
                        </div>

                        <div class="nib-link mt-2" style="display:none;">
                            <input type="url" name="nib_link" class="form-control"
                                value="{{ old('nib_link', $vendor->nib_link) }}"
                                placeholder="https://drive.google.com/...">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">NPWP</label>

                        {{-- Dokumen Lama --}}
                        @if ($vendor->npwp)
                            <div class="mb-2">
                                <span class="badge bg-primary">FILE</span>
                                <a href="{{ asset('storage/' . $vendor->npwp) }}" target="_blank"
                                    class="btn btn-sm btn-info">
                                    Lihat File
                                </a>
                            </div>
                        @elseif($vendor->npwp_link)
                            <div class="mb-2">
                                <span class="badge bg-success">LINK</span>
                                <a href="{{ $vendor->npwp_link }}" target="_blank" class="btn btn-sm btn-success">
                                    Buka Link
                                </a>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="me-3">
                                <input type="radio" name="npwp_type" value="file"
                                    {{ !$vendor->npwp_link ? 'checked' : '' }}>
                                Upload File
                            </label>

                            <label>
                                <input type="radio" name="npwp_type" value="link"
                                    {{ $vendor->npwp_link ? 'checked' : '' }}>
                                Gunakan Link
                            </label>
                        </div>

                        <div class="npwp-file">
                            <input type="file" name="npwp" class="form-control">
                        </div>

                        <div class="npwp-link mt-2" style="display:none;">
                            <input type="url" name="npwp_link" class="form-control"
                                value="{{ old('npwp_link', $vendor->npwp_link) }}"
                                placeholder="https://drive.google.com/...">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Pakta Integritas</label>

                        {{-- Dokumen Lama --}}
                        @if ($vendor->pakta_integritas)
                            <div class="mb-2">
                                <span class="badge bg-primary">FILE</span>
                                <a href="{{ asset('storage/' . $vendor->pakta_integritas) }}" target="_blank"
                                    class="btn btn-sm btn-info">
                                    Lihat File
                                </a>
                            </div>
                        @elseif($vendor->pakta_integritas_link)
                            <div class="mb-2">
                                <span class="badge bg-success">LINK</span>
                                <a href="{{ $vendor->pakta_integritas_link }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    Buka Link
                                </a>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="me-3">
                                <input type="radio" name="pakta_integritas_type" value="file"
                                    {{ !$vendor->pakta_integritas_link ? 'checked' : '' }}>
                                Upload File
                            </label>

                            <label>
                                <input type="radio" name="pakta_integritas_type" value="link"
                                    {{ $vendor->pakta_integritas_link ? 'checked' : '' }}>
                                Gunakan Link
                            </label>
                        </div>

                        <div class="pakta_integritas-file">
                            <input type="file" name="pakta_integritas" class="form-control">
                        </div>

                        <div class="pakta_integritas-link mt-2" style="display:none;">
                            <input type="url" name="pakta_integritas_link" class="form-control"
                                value="{{ old('pakta_integritas_link', $vendor->pakta_integritas_link) }}"
                                placeholder="https://drive.google.com/...">
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('vendor.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const docs = [
                'akta',
                'nib',
                'npwp',
                'pakta_integritas'
            ];

            docs.forEach(doc => {

                function toggleDoc() {

                    const selected = document.querySelector(
                        `input[name="${doc}_type"]:checked`
                    )?.value;

                    const fileDiv = document.querySelector(`.${doc}-file`);
                    const linkDiv = document.querySelector(`.${doc}-link`);

                    if (!fileDiv || !linkDiv) return;

                    if (selected === 'link') {
                        fileDiv.style.display = 'none';
                        linkDiv.style.display = 'block';
                    } else {
                        fileDiv.style.display = 'block';
                        linkDiv.style.display = 'none';
                    }
                }

                document
                    .querySelectorAll(`input[name="${doc}_type"]`)
                    .forEach(radio => {
                        radio.addEventListener('change', toggleDoc);
                    });

                toggleDoc();
            });

        });
    </script>
@endsection
