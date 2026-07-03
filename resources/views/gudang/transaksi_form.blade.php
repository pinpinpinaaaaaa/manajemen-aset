@extends('layouts.app')

@section('title', 'Tambah Transaksi Gudang')

@section('content')
    <main class="form-container">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Tambah Transaksi Gudang</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('gudang.laporan_transaksi') }}">Laporan Transaksi</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('gudang.transaksi.store') }}" method="POST">
                    @csrf

                    {{-- JENIS TRANSAKSI --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <select name="jenis_transaksi" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="masuk">Barang Masuk</option>
                            <option value="keluar">Barang Keluar</option>
                            <option value="penyesuaian">Penyesuaian</option>
                        </select>
                    </div>

                    {{-- REFERENSI --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Referensi / Alasan</label>
                        <input type="text" name="referensi" class="form-control"
                            placeholder="Contoh: PMB001 / Stock opname">
                    </div>

                    <hr>

                    {{-- DETAIL BARANG --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Detail Barang</label>

                        <div id="barang-wrapper">

                            <div class="barang-row d-flex align-items-center gap-2 mb-2">

                                <div class="flex-fill">
                                    <select name="items[0][id_barang]" class="form-select" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->id_barang }}">
                                                {{ $b->nama_barang }} (stok: {{ $b->stok_akhir }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="width: 140px">
                                    <input type="number" name="items[0][jumlah]" class="form-control" min="1"
                                        placeholder="Jumlah" required>
                                </div>

                                <button type="button" class="btn btn-success add-row" disabled>
                                    <i class="fas fa-plus"></i>
                                </button>

                                <button type="button" class="btn btn-danger remove-row" disabled>
                                    <i class="fas fa-times"></i>
                                </button>

                            </div>

                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('gudang.laporan_transaksi') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </main>
    <script>
        let index = 1;

        function updateRowButtons(row) {
            const select = row.querySelector('select');
            const input = row.querySelector('input[type="number"]');
            const addBtn = row.querySelector('.add-row');
            const delBtn = row.querySelector('.remove-row');

            const isFilled = select.value && input.value && input.value > 0;

            addBtn.disabled = !isFilled;
            delBtn.disabled = !isFilled;
        }

        // pantau perubahan
        document.addEventListener('input', function(e) {
            const row = e.target.closest('.barang-row');
            if (row) updateRowButtons(row);
        });

        document.addEventListener('change', function(e) {
            const row = e.target.closest('.barang-row');
            if (row) updateRowButtons(row);
        });

        document.addEventListener('click', function(e) {

            /* TAMBAH BARIS */
            if (e.target.closest('.add-row')) {

                const wrapper = document.getElementById('barang-wrapper');

                const row = document.createElement('div');
                row.className = 'barang-row d-flex align-items-center gap-2 mb-2';

                row.innerHTML = `
            <div class="flex-fill">
                <select name="items[${index}][id_barang]" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id_barang }}">
                            {{ $b->nama_barang }} (stok: {{ $b->stok_akhir }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width:140px">
                <input type="number"
                       name="items[${index}][jumlah]"
                       class="form-control"
                       min="1"
                       placeholder="Jumlah"
                       required>
            </div>

            <button type="button" class="btn btn-success add-row" disabled>
                <i class="fas fa-plus"></i>
            </button>

            <button type="button" class="btn btn-danger remove-row" disabled>
                <i class="fas fa-times"></i>
            </button>
        `;

                wrapper.appendChild(row);
                index++;
            }

            /* HAPUS BARIS */
            if (e.target.closest('.remove-row')) {
                const rows = document.querySelectorAll('.barang-row');
                if (rows.length > 1) {
                    e.target.closest('.barang-row').remove();
                }
            }
        });
    </script>

@endsection
