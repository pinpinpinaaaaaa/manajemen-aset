@extends('layouts.app')

@section('title', 'Edit Pemindahan Aset')

@section('content')

    <main class="main-content">
        <div class="content-padding">

            {{-- HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Edit Pemindahan Aset</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('pemindahan_aset.index') }}">Pemindahan Aset</a>
                    <span class="separator">/</span>
                    <span class="current">Edit</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <form action="{{ route('pemindahan_aset.update', $data->id_pemindahan) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label class="form-label">Alasan</label>

                        <textarea name="alasan" class="form-control" rows="3">{{ old('alasan', $data->alasan) }}</textarea>
                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead>
                                <tr>
                                    <th>Aset</th>
                                    <th>Asal</th>
                                    <th>Gedung Tujuan</th>
                                    <th>Ruangan Tujuan</th>
                                    <th>Biaya</th>
                                    <th>Pelaksana</th>
                                    <th>Vendor</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($data->details as $detail)
                                    <tr>

                                        <td>
                                            {{ $detail->aset->nama_aset }}
                                            <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                        </td>

                                        <td>
                                            {{ $detail->gedungAsal->nama_gedung ?? '-' }}
                                            <br>
                                            <small>
                                                {{ $detail->ruanganAsal->nama_ruangan ?? '-' }}
                                            </small>
                                        </td>

                                        <td>
                                            <select name="to_gedung[{{ $detail->id }}]" class="form-select gedungTujuan"
                                                data-detail="{{ $detail->id }}" required>

                                                @foreach ($gedung as $g)
                                                    <option value="{{ $g->id_gedung }}"
                                                        {{ $g->id_gedung == $detail->to_gedung ? 'selected' : '' }}>
                                                        {{ $g->nama_gedung }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </td>

                                        <td>

                                            <select name="to_ruangan[{{ $detail->id }}]"
                                                class="form-select ruanganTujuan" id="ruangan_{{ $detail->id }}"
                                                required>

                                                <option value="{{ $detail->to_ruangan }}" selected>
                                                    {{ $detail->ruanganTujuan->nama_ruangan ?? '-' }}
                                                </option>

                                            </select>

                                        </td>

                                        <td>
                                            <input type="number" class="form-control" name="biaya[{{ $detail->id }}]"
                                                value="{{ $detail->biaya }}">
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

                                            <select name="id_vendor[{{ $detail->id }}]" class="form-select vendorField"
                                                id="vendor_{{ $detail->id }}"
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

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    <div class="text-end">

                        <a href="{{ route('pemindahan_aset.index') }}" class="btn btn-secondary">
                            Batal
                        </a>

                        <button class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </main>


    {{-- ================= JS DINAMIS ================= --}}
    <script>
        document.querySelectorAll('.gedungTujuan')
            .forEach(select => {

                select.addEventListener('change', function() {

                    const detailId =
                        this.dataset.detail;

                    fetch(`/get-ruangan/${this.value}`)
                        .then(r => r.json())
                        .then(data => {

                            const ruangan =
                                document.getElementById(
                                    'ruangan_' + detailId
                                );

                            ruangan.innerHTML =
                                '<option value="">-- Pilih Ruangan --</option>';

                            data.forEach(item => {

                                ruangan.innerHTML += `
                        <option value="${item.id_ruangan}">
                            ${item.nama_ruangan}
                        </option>
                    `;

                            });

                        });

                });

            });

        document.querySelectorAll('.pelaksanaType')
            .forEach(select => {

                select.addEventListener('change', function() {

                    const detailId =
                        this.dataset.detail;

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
