@extends('layouts.app')

@section('title', 'Edit Laporan Pemusnahan')

@section('content')
<main class="form-container">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Edit Laporan Pemusnahan</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <a href="{{ route('laporan_pemusnahan.index') }}">Laporan Pemusnahan</a>
                <span class="separator">/</span>
                <span class="current">{{ $laporan->id_pemusnahan }}</span>
            </nav>
        </div>

        <div class="card mt-4 p-4 shadow-sm rounded-lg">
            <form action="{{ route('laporan_pemusnahan.update', $laporan->id_pemusnahan) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===========================
                    INFORMASI ASET
                ============================ --}}
                <div class="form-group mb-4">
                    <label class="form-label">Aset yang Dimusnahkan</label>
                    <select name="id_aset" class="form-select" required>
                        <option value="">-- Pilih Aset --</option>
                        @foreach($aset as $a)
                            <option value="{{ $a->id_aset }}" {{ $laporan->id_aset == $a->id_aset ? 'selected' : '' }}>
                                {{ $a->nama_aset }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ===========================
                    TANGGAL PEMUSNAHAN
                ============================ --}}
                <div class="form-group mb-4">
                    <label class="form-label">Tanggal Pemusnahan</label>
                    <input type="date" name="tanggal_pemusnahan" class="form-control" 
                        value="{{ $laporan->tanggal_pemusnahan ? \Carbon\Carbon::parse($laporan->tanggal_pemusnahan)->format('Y-m-d') : '' }}" 
                        required>
                </div>

                {{-- ===========================
                    METODE PEMUSNAHAN
                ============================ --}}
                <div class="form-group mb-4">
                    <label class="form-label">Metode Pemusnahan</label>
                    <select name="metode" class="form-select" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="Dibakar" {{ $laporan->metode == 'Dibakar' ? 'selected' : '' }}>Dibakar</option>
                        <option value="Dihancurkan" {{ $laporan->metode == 'Dihancurkan' ? 'selected' : '' }}>Dihancurkan</option>
                        <option value="Didaur Ulang" {{ $laporan->metode == 'Didaur Ulang' ? 'selected' : '' }}>Didaur Ulang</option>
                        <option value="Metode Lain" {{ $laporan->metode == 'Metode Lain' ? 'selected' : '' }}>Metode Lain</option>
                    </select>
                </div>

                {{-- ===========================
                    CATATAN
                ============================ --}}
                <div class="form-group mb-4">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika ada...">{{ $laporan->catatan ?? '' }}</textarea>
                </div>

                {{-- ===========================
                    TOMBOL AKSI
                ============================ --}}
                <div class="text-end">
                    <a href="{{ route('laporan_pemusnahan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</main>
@endsection
