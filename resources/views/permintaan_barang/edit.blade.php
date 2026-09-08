@extends('layouts.app')

@section('title', 'Edit Permintaan Barang Gudang')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            {{-- ================= HEADER ================= --}}
            <div class="page-header">
                <h1 class="page-title">Edit Permintaan Barang Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}" class="breadcrumb-link">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('permintaan-barang.index') }}" class="breadcrumb-link">Permintaan Barang Gudang</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Permintaan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <form action="{{ route('permintaan-barang.update', $permintaan->id_permintaan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-form-errors />

                    <div class="form-group mb-3">
                        <label>Divisi</label>
                        <select name="id_divisi"
                            class="form-select @error('id_divisi') is-invalid @enderror" required>
                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ old('id_divisi', $permintaan->id_divisi) == $d->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Nama Pengaju</label>
                        <input type="text" name="nama_pengaju"
                            class="form-control @error('nama_pengaju') is-invalid @enderror"
                            value="{{ old('nama_pengaju', $permintaan->nama_pengaju) }}" required>
                        @error('nama_pengaju') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email_pengaju"
                            class="form-control @error('email_pengaju') is-invalid @enderror"
                            value="{{ old('email_pengaju', $permintaan->email_pengaju) }}" required>
                        @error('email_pengaju') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label>Tanggal Kebutuhan</label>
                        <input type="date" name="tanggal_kebutuhan"
                            class="form-control @error('tanggal_kebutuhan') is-invalid @enderror"
                            value="{{ old('tanggal_kebutuhan', \Carbon\Carbon::parse($permintaan->tanggal_kebutuhan)->format('Y-m-d')) }}"
                            required>
                        @error('tanggal_kebutuhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr>

                    {{-- ================= DETAIL ITEM ================= --}}
                    <h2 class="mb-3">Detail Item</h2>

                    <div id="items-wrapper">

                        @foreach ($permintaan->details as $i => $detail)
                            <div class="border rounded p-3 mb-3 item-row">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label>Barang</label>

                                        <select name="items[{{ $i }}][id_barang]" class="form-select" required>

                                            <option value="">Pilih Barang</option>

                                            @foreach ($gudang as $barang)
                                                <option value="{{ $barang->id_barang }}"
                                                    data-max="{{ $barang->stok_akhir - $barang->stok_dipesan }}"
                                                    {{ $detail->id_barang == $barang->id_barang ? 'selected' : '' }}>
                                                    {{ $barang->nama_barang }}
                                                    (Stok: {{ $barang->stok_akhir - $barang->stok_dipesan }})
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Jumlah</label>

                                        <input type="number" name="items[{{ $i }}][jumlah]"
                                            class="form-control jumlah-input" min="1" value="{{ $detail->jumlah }}"
                                            required>
                                    </div>

                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-item w-100">
                                            Hapus
                                        </button>
                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    <button type="button" id="btn-add-item" class="btn btn-success mb-4">
                        + Tambah Barang
                    </button>

                    {{-- ================= CATATAN ================= --}}
                    <div class="form-group mb-3">
                        <label>Alasan</label>
                        <textarea name="alasan"
                            class="form-control @error('alasan') is-invalid @enderror"
                            rows="3">{{ old('alasan', $permintaan->alasan) }}</textarea>
                        @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label>Catatan</label>
                        <textarea name="catatan"
                            class="form-control @error('catatan') is-invalid @enderror"
                            rows="3">{{ old('catatan', $permintaan->catatan) }}</textarea>
                        @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('permintaan-barang.index') }}" class="btn btn-secondary">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let index = {{ count($permintaan->details) }};

            document.getElementById('btn-add-item').addEventListener('click', function() {

                const html = `
        <div class="border rounded p-3 mb-3 item-row">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Barang</label>

                    <select
                        name="items[${index}][id_barang]"
                        class="form-select"
                        required>

                        <option value="">Pilih Barang</option>

                        @foreach ($gudang as $barang)
                        <option
                            value="{{ $barang->id_barang }}"
                            data-max="{{ $barang->stok_akhir - $barang->stok_dipesan }}">
                            {{ $barang->nama_barang }}
                            (Stok: {{ $barang->stok_akhir - $barang->stok_dipesan }})
                        </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Jumlah</label>

                    <input
                        type="number"
                        name="items[${index}][jumlah]"
                        class="form-control jumlah-input"
                        min="1"
                        required>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button
                        type="button"
                        class="btn btn-danger remove-item w-100">
                        Hapus
                    </button>
                </div>

            </div>

        </div>
        `;

                document
                    .getElementById('items-wrapper')
                    .insertAdjacentHTML('beforeend', html);

                index++;
            });

            document.addEventListener('click', function(e) {

                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('.item-row').remove();
                }

            });

        });

        function validateJumlah(row) {

            const barangSelect = row.querySelector('select');
            const jumlahInput = row.querySelector('.jumlah-input');

            if (!barangSelect || !jumlahInput) return;

            const selected = barangSelect.options[barangSelect.selectedIndex];

            const max = parseInt(selected.dataset.max || 0);

            jumlahInput.setAttribute('max', max);

            if (parseInt(jumlahInput.value || 0) > max) {
                jumlahInput.value = max;
            }

            if (parseInt(jumlahInput.value || 0) < 1) {
                jumlahInput.value = 1;
            }
        }

        document.addEventListener('change', function(e) {

            if (e.target.matches('select[name*="[id_barang]"]')) {
                validateJumlah(e.target.closest('.item-row'));
            }

        });

        document.addEventListener('input', function(e) {

            if (e.target.classList.contains('jumlah-input')) {
                validateJumlah(e.target.closest('.item-row'));
            }

        });
    </script>
@endsection
