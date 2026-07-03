@extends('layouts.app')

@section('title', 'Tambah Laporan Pemusnahan')

@section('content')
    <main class="form-container">
        <div class="content-padding">

            {{-- ===========================
            BREADCRUMB
        ============================ --}}
            <div class="page-header">
                <h1 class="page-title">Tambah Laporan Pemusnahan</h1>
                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="{{ route('laporan_pemusnahan.index') }}">Laporan Pemusnahan</a>
                    <span class="separator">/</span>
                    <span class="current">Tambah Laporan</span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">
                <form action="{{ route('laporan_pemusnahan.store') }}" method="POST">
                    @csrf

                    {{-- ===========================
                    GEDUNG
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Gedung</label>
                        <select name="id_gedung" id="id_gedung" class="form-select" required>
                            <option value="">-- Pilih Gedung --</option>
                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}"
                                    {{ old('id_gedung') == $g->id_gedung ? 'selected' : '' }}>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_gedung')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ===========================
                    RUANGAN
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Ruangan</label>
                        <select name="id_ruangan" id="id_ruangan" class="form-select" required>
                            <option value="">-- Pilih Ruangan --</option>
                        </select>
                        @error('id_ruangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ===========================
                    ASET
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Aset</label>
                        <select name="id_aset" id="id_aset" class="form-select" required>
                            <option value="">-- Pilih Aset --</option>
                        </select>
                        @error('id_aset')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ===========================
                    TANGGAL
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" name="tanggal_pemusnahan" value="{{ old('tanggal_pemusnahan') }}"
                            class="form-control" required>
                    </div>

                    {{-- ===========================
                    METODE
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Metode Pemusnahan</label>
                        <select name="metode" class="form-select" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Lelang">Lelang</option>
                            <option value="Hibahkan">Hibahkan</option>
                            <option value="Dijual">Dijual</option>
                            <option value="Dimusnahkan">Dimusnahkan</option>
                        </select>
                    </div>

                    {{-- ===========================
                    CATATAN
                ============================ --}}
                    <div class="form-group mb-4">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tambahkan catatan jika perlu...">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('laporan_pemusnahan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- ===========================
    AJAX FETCH
=========================== --}}
    <script>
        document.getElementById('id_gedung').addEventListener('change', function() {
            const id = this.value;
            const ruangan = document.getElementById('id_ruangan');
            const aset = document.getElementById('id_aset');

            ruangan.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            aset.innerHTML = '<option value="">-- Pilih Aset --</option>';

            if (!id) return;

            fetch(`/get-ruangan/${id}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id_ruangan;
                        opt.textContent = r.nama_ruangan;
                        ruangan.appendChild(opt);
                    });
                });
        });

        document.getElementById('id_ruangan').addEventListener('change', function() {
            const id = this.value;
            const aset = document.getElementById('id_aset');

            aset.innerHTML = '<option value="">-- Pilih Aset --</option>';

            if (!id) return;

            fetch(`/get-aset/${id}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(a => {
                        const opt = document.createElement('option');
                        opt.value = a.id_aset;
                        opt.textContent = a.nama_aset;
                        aset.appendChild(opt);
                    });
                });
        });
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.addEventListener('focus', () => input.showPicker());
        });
    </script>

@endsection
