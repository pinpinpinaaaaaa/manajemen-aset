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

                    <x-form-errors />

                    <div class="form-group mb-4">
                        <label class="form-label">Alasan</label>

                        <textarea name="alasan"
                            class="form-control @error('alasan') is-invalid @enderror"
                            rows="3">{{ old('alasan', $data->alasan) }}</textarea>
                        @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            @php $oldToGedung = old('to_gedung.'.$detail->id, $detail->to_gedung); @endphp
                                            <select name="to_gedung[{{ $detail->id }}]"
                                                class="form-select gedungTujuan @error('to_gedung.'.$detail->id) is-invalid @enderror"
                                                data-detail="{{ $detail->id }}" required>

                                                @foreach ($gedung as $g)
                                                    <option value="{{ $g->id_gedung }}"
                                                        {{ $g->id_gedung == $oldToGedung ? 'selected' : '' }}>
                                                        {{ $g->nama_gedung }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </td>

                                        <td>
                                            @php $oldToRuangan = old('to_ruangan.'.$detail->id, $detail->to_ruangan); @endphp
                                            <select name="to_ruangan[{{ $detail->id }}]"
                                                class="form-select ruanganTujuan @error('to_ruangan.'.$detail->id) is-invalid @enderror"
                                                id="ruangan_{{ $detail->id }}" required>

                                                <option value="{{ $oldToRuangan }}" selected>
                                                    {{ $detail->ruanganTujuan->nama_ruangan ?? '-' }}
                                                </option>

                                            </select>

                                        </td>

                                        <td>
                                            <input type="number"
                                                class="form-control @error('biaya.'.$detail->id) is-invalid @enderror"
                                                name="biaya[{{ $detail->id }}]"
                                                value="{{ old('biaya.'.$detail->id, $detail->biaya) }}">
                                        </td>

                                        <td>
                                            @php $oldPelaksana = old('pelaksana_type.'.$detail->id, $detail->pelaksana_type); @endphp
                                            <select name="pelaksana_type[{{ $detail->id }}]"
                                                class="form-select pelaksanaType @error('pelaksana_type.'.$detail->id) is-invalid @enderror"
                                                data-detail="{{ $detail->id }}">

                                                <option value="internal"
                                                    {{ $oldPelaksana == 'internal' ? 'selected' : '' }}>
                                                    Internal
                                                </option>

                                                <option value="vendor"
                                                    {{ $oldPelaksana == 'vendor' ? 'selected' : '' }}>
                                                    Vendor
                                                </option>

                                                <option value="lainnya"
                                                    {{ $oldPelaksana == 'lainnya' ? 'selected' : '' }}>
                                                    Lainnya
                                                </option>

                                            </select>

                                        </td>

                                        <td>
                                            @php $oldVendor = old('id_vendor.'.$detail->id, $detail->id_vendor); @endphp
                                            <select name="id_vendor[{{ $detail->id }}]"
                                                class="form-select vendorField @error('id_vendor.'.$detail->id) is-invalid @enderror"
                                                id="vendor_{{ $detail->id }}"
                                                {{ $oldPelaksana == 'vendor' ? '' : 'disabled' }}>

                                                <option value="">
                                                    -- Pilih Vendor --
                                                </option>

                                                @foreach ($vendors as $v)
                                                    <option value="{{ $v->id_vendor }}"
                                                        {{ $oldVendor == $v->id_vendor ? 'selected' : '' }}>
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
