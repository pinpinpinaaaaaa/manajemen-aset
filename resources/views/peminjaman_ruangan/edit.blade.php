@extends('layouts.app')

@section('title', 'Edit Peminjaman Ruangan')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">
                    Edit Peminjaman Ruangan
                </h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>

                    <span class="separator">/</span>

                    <a href="{{ route('peminjaman-ruangan.index') }}">
                        Peminjaman Ruangan
                    </a>

                    <span class="separator">/</span>

                    <span class="current">
                        Edit
                    </span>
                </nav>
            </div>

            <div class="card p-4 mt-4">

                <form method="POST" action="{{ route('peminjaman-ruangan.update', $peminjaman->id_peminjaman) }}">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label>Nama Pengaju</label>
                        <input type="text" name="nama_pengaju" class="form-control"
                            value="{{ old('nama_pengaju', $peminjaman->nama_pengaju) }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Email Pengaju</label>
                        <input type="email" name="email_pengaju" class="form-control"
                            value="{{ old('email_pengaju', $peminjaman->email_pengaju) }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Divisi</label>

                        <select name="id_divisi" class="form-select" required>

                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ $peminjaman->id_divisi == $d->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Jenis Kegiatan</label>

                        <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-select" required>

                            <option value="rapat" {{ $peminjaman->jenis_kegiatan == 'rapat' ? 'selected' : '' }}>
                                Rapat
                            </option>

                            <option value="pelatihan" {{ $peminjaman->jenis_kegiatan == 'pelatihan' ? 'selected' : '' }}>
                                Pelatihan
                            </option>

                            <option value="asasmen" {{ $peminjaman->jenis_kegiatan == 'asasmen' ? 'selected' : '' }}>
                                Asasmen
                            </option>

                            <option value="lainnya" {{ $peminjaman->jenis_kegiatan == 'lainnya' ? 'selected' : '' }}>
                                Lainnya
                            </option>

                        </select>
                    </div>

                    <div class="mb-3" id="namaKegiatanBox">
                        <label>Nama Kegiatan</label>

                        <input type="text" name="nama_kegiatan" class="form-control"
                            value="{{ old('nama_kegiatan', $peminjaman->nama_kegiatan) }}">
                    </div>

                    <div class="mb-3" id="pesertaRapatBox">
                        <label>Peserta Rapat</label>

                        <textarea name="peserta_rapat" class="form-control" rows="4">{{ old('peserta_rapat', $peminjaman->peserta_rapat) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label>Catatan</label>

                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $peminjaman->catatan) }}</textarea>
                    </div>

                    <hr>

                    <h5 class="mb-3">
                        Detail Ruangan
                    </h5>

                    @foreach ($peminjaman->details as $i => $detail)
                        <div class="card border mb-4 room-card" data-room="{{ $i }}">

                            <div class="card-header d-flex justify-content-between">

                                <span>
                                    Ruangan #{{ $loop->iteration }}
                                </span>

                                <button type="button" class="btn btn-danger btn-sm remove-room">

                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                            <div class="card-body">

                                <input type="hidden" name="ruangan[{{ $i }}][detail_id]"
                                    value="{{ $detail->id }}">
                                <input type="hidden" name="ruangan[{{ $i }}][deleted]" value="0"
                                    class="deleted-flag">

                                {{-- GEDUNG --}}
                                <div class="mb-3">

                                    <label>Gedung</label>

                                    <select name="ruangan[{{ $i }}][id_gedung]" class="form-select">

                                        @foreach ($gedung as $g)
                                            <option value="{{ $g->id_gedung }}"
                                                {{ $detail->id_gedung == $g->id_gedung ? 'selected' : '' }}>

                                                {{ $g->nama_gedung }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- RUANGAN --}}
                                <div class="mb-3">

                                    <label>Ruangan</label>

                                    <select class="form-select" name="ruangan[{{ $i }}][id_ruangan]">

                                        @foreach ($ruangan as $r)
                                            <option value="{{ $r->id_ruangan }}"
                                                {{ $detail->id_ruangan == $r->id_ruangan ? 'selected' : '' }}>

                                                {{ $r->nama_ruangan }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- TANGGAL --}}
                                <div class="row">

                                    <div class="col-md-6">

                                        <label>Tanggal Mulai</label>

                                        <input type="date" class="form-control"
                                            name="ruangan[{{ $i }}][tanggal_mulai]"
                                            value="{{ $detail->tanggal_mulai }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label>Tanggal Selesai</label>

                                        <input type="date" class="form-control"
                                            name="ruangan[{{ $i }}][tanggal_selesai]"
                                            value="{{ $detail->tanggal_selesai }}">

                                    </div>

                                </div>

                                {{-- JAM --}}
                                <div class="row mt-3">

                                    <div class="col-md-6">

                                        <label>Jam Mulai</label>

                                        <input type="time" class="form-control"
                                            name="ruangan[{{ $i }}][jam_mulai]"
                                            value="{{ substr($detail->jam_mulai, 0, 5) }}">

                                    </div>

                                    <div class="col-md-6">

                                        <label>Jam Selesai</label>

                                        <input type="time" class="form-control"
                                            name="ruangan[{{ $i }}][jam_selesai]"
                                            value="{{ substr($detail->jam_selesai, 0, 5) }}">

                                    </div>

                                </div>

                                {{-- CATATAN --}}
                                <div class="mt-3">

                                    <label>Catatan Detail</label>

                                    <textarea class="form-control" rows="2" name="ruangan[{{ $i }}][catatan]">{{ $detail->catatan }}</textarea>

                                </div>


                                {{-- ASET --}}
                                <hr>

                                <h6>Aset yang Dipinjam</h6>

                                <div class="aset-container">

                                    @foreach ($detail->aset as $j => $aset)
                                        <div class="row mb-2 aset-row">

                                            <div class="col-md-6">

                                                <select class="form-select"
                                                    name="ruangan[{{ $i }}][aset][{{ $j }}][id_aset]">

                                                    @foreach ($asetList as $a)
                                                        <option value="{{ $a->id_aset }}"
                                                            {{ $aset->id_aset == $a->id_aset ? 'selected' : '' }}>

                                                            {{ $a->nama_aset }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="col-md-4">

                                                <input type="number" class="form-control" min="1"
                                                    value="{{ $aset->jumlah }}"
                                                    name="ruangan[{{ $i }}][aset][{{ $j }}][jumlah]">

                                            </div>

                                            <div class="col-md-2">

                                                <button type="button" class="btn btn-danger remove-aset">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </div>

                                        </div>
                                    @endforeach

                                </div>

                                <button type="button" class="btn btn-success btn-sm add-aset">

                                    <i class="fas fa-plus"></i>
                                    Tambah Aset

                                </button>

                            </div>

                        </div>
                    @endforeach
                    <div class="mb-4">
                        <button type="button" class="btn btn-success" id="btnTambahRuangan">

                            <i class="fas fa-plus"></i>
                            Tambah Ruangan
                        </button>
                    </div>

                    <div id="ruanganContainer">
                    </div>
                    <hr>

                    <h5 class="mb-3">
                        Konsumsi
                    </h5>

                    @foreach ($peminjaman->konsumsi as $i => $k)
                        <div class="border rounded p-3 mb-3 konsumsi-item">

                            <input type="hidden" name="konsumsi[{{ $i }}][id]" value="{{ $k->id }}">
                            <input type="hidden" name="konsumsi[{{ $i }}][deleted]" value="0"
                                class="konsumsi-deleted">

                            <div class="mb-3">

                                <label>Jenis Konsumsi</label>

                                <select class="form-select" name="konsumsi[{{ $i }}][jenis_konsumsi]">

                                    <option value="air_mineral"
                                        {{ $k->jenis_konsumsi == 'air_mineral' ? 'selected' : '' }}>
                                        Air Mineral
                                    </option>

                                    <option value="makanan_ringan"
                                        {{ $k->jenis_konsumsi == 'makanan_ringan' ? 'selected' : '' }}>
                                        Makanan Ringan
                                    </option>

                                    <option value="makanan_berat"
                                        {{ $k->jenis_konsumsi == 'makanan_berat' ? 'selected' : '' }}>
                                        Makanan Berat
                                    </option>

                                </select>

                            </div>

                            <div class="mb-3">

                                <label>Jumlah</label>

                                <input type="number" class="form-control" name="konsumsi[{{ $i }}][jumlah]"
                                    value="{{ $k->jumlah }}">

                            </div>

                            <div>

                                <label>Catatan</label>

                                <textarea class="form-control" name="konsumsi[{{ $i }}][catatan]" rows="2">{{ $k->catatan }}</textarea>

                            </div>

                            <div class="text-end mt-2">

                                <button type="button" class="btn btn-danger btn-sm remove-konsumsi">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </div>

                        </div>
                    @endforeach

                    <div id="konsumsiContainer"></div>

                    <div class="mb-4">

                        <button type="button" class="btn btn-success" id="btnTambahKonsumsi">

                            <i class="fas fa-plus"></i>
                            Tambah Konsumsi

                        </button>

                    </div>

                    <div class="text-end">

                        <a href="{{ route('peminjaman-ruangan.index') }}" class="btn btn-secondary">

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
        let konsumsiIndex = {{ $peminjaman->konsumsi->count() }};

        function getUsedKonsumsi() {

            let used = [];

            document.querySelectorAll(
                'select[name*="[jenis_konsumsi]"]'
            ).forEach(select => {

                let card = select.closest('.konsumsi-item');

                // abaikan yang dihapus
                let deleted = card.querySelector('.konsumsi-deleted');

                if (!deleted || deleted.value == '0') {
                    used.push(select.value);
                }

            });

            return used;
        }

        function updateTambahKonsumsiButton() {

            let totalAktif = 0;

            document.querySelectorAll('.konsumsi-item')
                .forEach(item => {

                    let deleted = item.querySelector('.konsumsi-deleted');

                    if (!deleted || deleted.value == '0') {
                        totalAktif++;
                    }

                });

            document.getElementById('btnTambahKonsumsi')
                .style.display = totalAktif >= 3 ? 'none' : 'inline-block';
        }

        document.getElementById('btnTambahKonsumsi')
            .addEventListener('click', function() {

                let semuaJenis = [
                    'air_mineral',
                    'makanan_ringan',
                    'makanan_berat'
                ];

                let used = getUsedKonsumsi();

                let available = semuaJenis.filter(
                    x => !used.includes(x)
                );

                if (available.length === 0) {

                    alert('Semua jenis konsumsi sudah dipilih.');

                    return;
                }

                let options = '';

                available.forEach(jenis => {

                    let label = '';

                    if (jenis === 'air_mineral')
                        label = 'Air Mineral';

                    if (jenis === 'makanan_ringan')
                        label = 'Makanan Ringan';

                    if (jenis === 'makanan_berat')
                        label = 'Makanan Berat';

                    options += `
                    <option value="${jenis}">
                        ${label}
                    </option>
                `;
                });

                let html = `

            <div class="border rounded p-3 mb-3 konsumsi-item">

                <div class="mb-3">

                    <label>Jenis Konsumsi</label>

                    <select class="form-select"
                        name="konsumsi_baru[${konsumsiIndex}][jenis_konsumsi]">

                        ${options}

                    </select>

                </div>

                <div class="mb-3">

                    <label>Jumlah</label>

                    <input type="number"
                        class="form-control"
                        min="1"
                        value="1"
                        name="konsumsi_baru[${konsumsiIndex}][jumlah]">

                </div>

                <div class="mb-3">

                    <label>Catatan</label>

                    <textarea class="form-control"
                        rows="2"
                        name="konsumsi_baru[${konsumsiIndex}][catatan]"></textarea>

                </div>

                <div class="text-end">

                    <button type="button"
                        class="btn btn-danger btn-sm remove-konsumsi">

                        <i class="fas fa-trash"></i>

                    </button>

                </div>

            </div>
            `;

                document.getElementById('konsumsiContainer')
                    .insertAdjacentHTML('beforeend', html);

                konsumsiIndex++;

                updateTambahKonsumsiButton();
            });



        // Tambah aset
        document.addEventListener('click', function(e) {

            if (e.target.closest('.remove-konsumsi')) {

                let item = e.target.closest('.konsumsi-item');

                let deleted = item.querySelector('.konsumsi-deleted');

                if (deleted) {

                    deleted.value = 1;

                    item.style.display = 'none';

                } else {

                    item.remove();

                }

                updateTambahKonsumsiButton();
            }

            if (e.target.closest('.add-aset')) {

                let roomCard = e.target.closest('.room-card');
                let roomIndex = roomCard.dataset.room;
                let container = roomCard.querySelector('.aset-container');
                let asetIndex = container.querySelectorAll('.aset-row').length;

                container.insertAdjacentHTML('beforeend', `
                <div class="row mb-2 aset-row">

                    <div class="col-md-6">
                        <select class="form-select"
                            name="ruangan[${roomIndex}][aset_baru][${asetIndex}][id_aset]">

                            @foreach ($asetList as $a)
                                <option value="{{ $a->id_aset }}">
                                    {{ $a->nama_aset }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="number"
                            min="1"
                            value="1"
                            class="form-control"
                            name="ruangan[${roomIndex}][aset_baru][${asetIndex}][jumlah]">
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-aset">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                </div>
            `);
            }

            // Hapus aset
            if (e.target.closest('.remove-aset')) {
                e.target.closest('.aset-row').remove();
            }

            // Hapus ruangan
            if (e.target.closest('.remove-room')) {

                let card = e.target.closest('.room-card');
                let deleted = card.querySelector('.deleted-flag');

                if (deleted) {
                    deleted.value = 1;
                    card.style.display = 'none';
                } else {
                    card.remove();
                }
            }
        });

        // Tambah ruangan
        let roomIndex = {{ $peminjaman->details->count() }};

        document.getElementById('btnTambahRuangan')
            .addEventListener('click', function() {

                let html = `
            <div class="card border mb-4 room-card" data-room="${roomIndex}">

                <div class="card-header d-flex justify-content-between">
                    <span>Ruangan Baru</span>

                    <button type="button"
                        class="btn btn-danger btn-sm remove-room">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label>Gedung</label>

                        <select class="form-select"
                            name="ruangan[${roomIndex}][id_gedung]">

                            @foreach ($gedung as $g)
                                <option value="{{ $g->id_gedung }}">
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Ruangan</label>

                        <select class="form-select"
                            name="ruangan[${roomIndex}][id_ruangan]">

                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id_ruangan }}">
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="date"
                                class="form-control"
                                name="ruangan[${roomIndex}][tanggal_mulai]">
                        </div>

                        <div class="col-md-6">
                            <label>Tanggal Selesai</label>
                            <input type="date"
                                class="form-control"
                                name="ruangan[${roomIndex}][tanggal_selesai]">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Jam Mulai</label>
                            <input type="time"
                                class="form-control"
                                name="ruangan[${roomIndex}][jam_mulai]">
                        </div>

                        <div class="col-md-6">
                            <label>Jam Selesai</label>
                            <input type="time"
                                class="form-control"
                                name="ruangan[${roomIndex}][jam_selesai]">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label>Catatan</label>

                        <textarea class="form-control"
                            name="ruangan[${roomIndex}][catatan]"></textarea>
                    </div>

                    <hr>

                    <h6>Aset</h6>

                    <div class="aset-container"></div>

                    <button type="button"
                        class="btn btn-success btn-sm add-aset">

                        <i class="fas fa-plus"></i>
                        Tambah Aset

                    </button>

                </div>
            </div>
            `;

                document.getElementById('ruanganContainer')
                    .insertAdjacentHTML('beforeend', html);

                roomIndex++;
            });

        // Toggle jenis kegiatan
        function toggleKegiatan() {

            let jenis = document.getElementById('jenis_kegiatan').value;

            document.getElementById('pesertaRapatBox').style.display =
                jenis === 'rapat' ? 'block' : 'none';

            document.getElementById('namaKegiatanBox').style.display =
                jenis === 'rapat' ? 'none' : 'block';
        }

        document.getElementById('jenis_kegiatan')
            .addEventListener('change', toggleKegiatan);

        toggleKegiatan();
    </script>

@endsection
