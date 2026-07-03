@extends('layouts.app')

@section('title', 'Edit Pengajuan Pemeliharaan Aset')

@section('content')
    <main class="main-content">
        <div class="content-padding">
            <div class="page-header">
                <h1 class="page-title">Edit Pengajuan Pemeliharaan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('maintenance.index') }}" class="breadcrumb-link">Pemeliharaan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Pengajuan Pemeliharaan Aset</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <form action="{{ route('maintenance.update', $maintenance->id_maintenance) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead>
                                <tr>
                                    <th>Aset</th>
                                    <th>Kerusakan</th>
                                    <th>Status</th>
                                    <th>Biaya</th>
                                    <th>Pelaksana</th>
                                    <th>Vendor</th>
                                    <th>Foto Before</th>
                                    <th>Foto After</th>
                                    <th>Lampiran</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($maintenance->details as $detail)
                                    <tr>

                                        <td>
                                            {{ $detail->aset->nama_aset }}

                                            <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                        </td>

                                        <td>
                                            <textarea class="form-control" name="kerusakan[{{ $detail->id }}]" rows="2">{{ $detail->kerusakan }}</textarea>
                                        </td>

                                        <td>

                                            <select name="status[{{ $detail->id }}]" class="form-select">

                                                <option value="Perlu Perbaikan"
                                                    {{ $detail->status == 'Perlu Perbaikan' ? 'selected' : '' }}>
                                                    Perlu Perbaikan
                                                </option>

                                                <option value="Sedang Diperbaiki"
                                                    {{ $detail->status == 'Sedang Diperbaiki' ? 'selected' : '' }}>
                                                    Sedang Diperbaiki
                                                </option>

                                                <option value="Selesai"
                                                    {{ $detail->status == 'Selesai' ? 'selected' : '' }}>
                                                    Selesai
                                                </option>

                                            </select>

                                        </td>

                                        <td>
                                            <input type="number" class="form-control" min="0"
                                                name="biaya[{{ $detail->id }}]" value="{{ $detail->biaya }}">
                                        </td>

                                        <td>

                                            <select name="pelaksana_type[{{ $detail->id }}]"
                                                class="form-select pelaksanaType" data-detail="{{ $detail->id }}">

                                                <option value="internal"
                                                    {{ $detail->pelaksana_type == 'internal' ? 'selected' : '' }}>
                                                    Internal
                                                </option>

                                                <option value="vendor"
                                                    {{ $detail->pelaksana_type == 'vendor' ? 'selected' : '' }}>
                                                    Vendor
                                                </option>

                                                <option value="lainnya"
                                                    {{ $detail->pelaksana_type == 'lainnya' ? 'selected' : '' }}>
                                                    Lainnya
                                                </option>

                                            </select>

                                        </td>

                                        <td>

                                            <select name="id_vendor[{{ $detail->id }}]" id="vendor_{{ $detail->id }}"
                                                class="form-select vendorField"
                                                {{ $detail->pelaksana_type == 'vendor' ? '' : 'disabled' }}>

                                                <option value="">
                                                    -- Pilih Vendor --
                                                </option>

                                                @foreach ($vendors as $v)
                                                    <option value="{{ $v->id_vendor }}"
                                                        {{ $detail->id_vendor == $v->id_vendor ? 'selected' : '' }}>

                                                        {{ $v->nama_perusahaan }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </td>

                                        <td>

                                            @if ($detail->foto_before)
                                                <img src="{{ asset('storage/' . $detail->foto_before) }}" width="60"
                                                    class="mb-2">
                                            @endif

                                            <input type="file" class="form-control"
                                                name="foto_before[{{ $detail->id }}]">

                                        </td>

                                        <td>

                                            @if ($detail->foto_after)
                                                <img src="{{ asset('storage/' . $detail->foto_after) }}" width="60"
                                                    class="mb-2">
                                            @endif

                                            <input type="file" class="form-control"
                                                name="foto_after[{{ $detail->id }}]">

                                        </td>

                                        <td>

                                            @if ($detail->lampiran)
                                                <a href="{{ asset('storage/' . $detail->lampiran) }}" target="_blank">

                                                    Lampiran Lama

                                                </a>

                                                <br>
                                            @endif

                                            <input type="file" class="form-control"
                                                name="lampiran[{{ $detail->id }}]">

                                        </td>

                                        <td>

                                            <textarea class="form-control" name="catatan[{{ $detail->id }}]" rows="2">{{ $detail->catatan }}</textarea>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    <div class="text-end mt-3">

                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
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


    {{-- ================= JS DINAMIS ================= --}}
    <script>
        document.querySelectorAll('.pelaksanaType')
            .forEach(select => {

                select.addEventListener('change', function() {

                    const detailId = this.dataset.detail;

                    const vendor =
                        document.getElementById(
                            'vendor_' + detailId
                        );

                    vendor.disabled =
                        this.value !== 'vendor';

                    if (this.value !== 'vendor') {
                        vendor.value = '';
                    }

                });

            });
    </script>
@endsection
