<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\{
    GedungController,
    GudangController,
    AparController,
    AsetController,
    KendaraanController,
    MaintenanceController,
    RolesController,
    RuanganController,
    UserController,
    LaporanPemusnahanController,
    LaporanTahunanController,
    JenisBarangController,
    DashboardController,
    DivisiController,
    PermintaanBarangGudangController,
    PengadaanBarangJasaController,
    PeminjamanRuanganController,
    PeminjamanAsetController,
    MenuController,
    AuditLogController,
    PemindahanAsetController,
    VendorController,
    PengaduanKerusakanController,
    PermintaanKendaraanController,
    EkspedisiController
};

Route::get('/', fn() => view('welcome'));

Route::get('/menu-form', function () {
    return view('menu_form'); // halaman yang berisi list form publik
})->name('menu.form');

// === FORM PUBLIK PERMINTAAN KENDARAAN ===
Route::get('/form-permintaan-kendaraan', [PermintaanKendaraanController::class, 'create'])->name('form-permintaan-kendaraan.create');
Route::post('/form-permintaan-kendaraan', [PermintaanKendaraanController::class, 'store'])->name('form-permintaan-kendaraan.store')->middleware('throttle:20,1');

// === FORM PUBLIK EKSPEDISI ===
Route::get('/form-ekspedisi', [EkspedisiController::class, 'create'])->name('form-ekspedisi.create');
Route::post('/form-ekspedisi', [EkspedisiController::class, 'store'])->name('form-ekspedisi.store')->middleware('throttle:20,1');

// === FORM PUBLIK PENGADUAN KERUSAKAN ===
Route::get('/form-pengaduan-kerusakan', [PengaduanKerusakanController::class, 'create'])->name('form-pengaduan-kerusakan.create');
Route::post('/form-pengaduan-kerusakan', [PengaduanKerusakanController::class, 'store'])->name('form-pengaduan-kerusakan.store')->middleware('throttle:20,1');

// === FORM PUBLIK PENGADAAN Barang & Jasa ===
Route::get('/form-pengadaan-barang', [PengadaanBarangJasaController::class, 'create'])->name('form-pengadaan-barang.create');
Route::post('/form-pengadaan-barang', [PengadaanBarangJasaController::class, 'store'])->name('form-pengadaan-barang.store')->middleware('throttle:20,1');

// === FORM PUBLIK PERMINTAAN BARANG ===
Route::get('/form-permintaan-barang', [PermintaanBarangGudangController::class, 'create'])->name('form-permintaan-barang.create');
Route::post('/form-permintaan-barang', [PermintaanBarangGudangController::class, 'store'])->name('form-permintaan-barang.store')->middleware('throttle:20,1');

// === FORM PUBLIK PEMINJAMAN RUANGAN ===
Route::get('/form-peminjaman-ruangan', [PeminjamanRuanganController::class, 'create'])->name(name: 'form-peminjaman-ruangan.create');
Route::post('/form-peminjaman-ruangan', [PeminjamanRuanganController::class, 'store'])->name('form-peminjaman-ruangan.store')->middleware('throttle:20,1');

// === FORM PUBLIK PEMINJAMAN ASET ===
Route::get('/form_peminjaman_aset', [PeminjamanAsetController::class, 'create'])->name('form_peminjaman_aset.create');
Route::post('/form_peminjaman_aset', [PeminjamanAsetController::class, 'store'])->name('form_peminjaman_aset.store')->middleware('throttle:20,1');

// === FORM PUBLIK TAMBAH VENDOR ===
Route::get('/vendor/create', [VendorController::class, 'create'])->name('vendor.create');
Route::post('/vendor', [VendorController::class, 'store'])->name('vendor.store')->middleware('throttle:20,1');


// =======================================
// ENDPOINT AJAX PUBLIK (dipakai form-form publik tanpa login)
// Harus di sini — sebelum auth group — agar AJAX dari form publik bisa jalan
// =======================================
Route::get('/get-ruangan/{id}', [\App\Http\Controllers\LaporanPemusnahanController::class, 'getRuangan'])->name('public.get-ruangan');
Route::get('/get-aset/{id}',    [\App\Http\Controllers\LaporanPemusnahanController::class, 'getAset'])->name('public.get-aset');

// =======================================
// AUTH DEFAULT LARAVEL
// =======================================
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// =======================================
// ROUTE YANG WAJIB LOGIN
// =======================================
Route::middleware(['auth', 'menu.access'])->group(function () {

    Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::get('/vendor/{vendor}', [VendorController::class, 'show'])->name('vendor.show');
    Route::get('/vendor/{vendor}/edit', [VendorController::class, 'edit'])->name('vendor.edit');
    Route::match(['put', 'patch'], '/vendor/{vendor}', [VendorController::class, 'update'])->name('vendor.update');
    Route::delete('/vendor/{vendor}', [VendorController::class, 'destroy'])->name('vendor.destroy');

    // === AUDIT LOG
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit.logs.show');

    // === HALAMAN UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === DIVISI
    Route::post('/divisi', [DivisiController::class, 'store'])->name('divisi.store');

    // === PEMINDAHAN ASET
    Route::get('/get-aset-pindah/{id_ruangan}', [PemindahanAsetController::class, 'getAsetPindah']);
    Route::get('/pemindahan_aset/laporan',[PemindahanAsetController::class,'laporan'])->name('pemindahan_aset.laporan');
    Route::get('/pemindahan_aset/create', [PemindahanAsetController::class, 'create'])->name('pemindahan_aset.create');
    Route::post('/pemindahan_aset',[PemindahanAsetController::class, 'store'])->name('pemindahan_aset.store');
    Route::get('/pemindahan_aset',[PemindahanAsetController::class, 'index'])->name('pemindahan_aset.index');
    Route::get('/pemindahan_aset/{id}',[PemindahanAsetController::class,'show'])->name('pemindahan_aset.show');
    Route::post('/pemindahan_aset/{id}/approve',[PemindahanAsetController::class, 'approve'])->name('pemindahan_aset.approve');
    Route::post('/pemindahan_aset/{id}/reject',[PemindahanAsetController::class, 'reject'])->name('pemindahan_aset.reject');
    Route::post('/pemindahan_aset/detail/{id}/pindahkan',[PemindahanAsetController::class, 'pindahkanDetail'])->name('pemindahan_aset.pindahkan_detail');
    Route::post('/pemindahan_aset/{id}/pindahkan',[PemindahanAsetController::class, 'pindahkan'])->name('pemindahan_aset.pindahkan');
    Route::delete('/pemindahan_aset/{id}',[PemindahanAsetController::class, 'destroy'])->name('pemindahan_aset.destroy');
    Route::put('/pemindahan_aset/{id}/status',[PemindahanAsetController::class, 'updateStatus'])->name('pemindahan_aset.updateStatus');
    Route::get('/pemindahan_aset/{id}/edit',[PemindahanAsetController::class,'edit']) ->name('pemindahan_aset.edit');
    Route::put('/pemindahan_aset/{id}',[PemindahanAsetController::class,'update'])->name('pemindahan_aset.update');
    Route::get('/pemindahan/laporan/export/excel',[PemindahanAsetController::class,'exportExcel'])->name('pemindahan_aset.export.excel');
    Route::get('/pemindahan/laporan/export/pdf',[PemindahanAsetController::class,'exportPdf'])->name('pemindahan_aset.export.pdf');

    // === PERMINTAAN BARANG GUDANG
    Route::get('/permintaan-barang/riwayat',[PermintaanBarangGudangController::class, 'riwayat'])->name('permintaan-barang.riwayat');
    Route::get('/permintaan-barang',[PermintaanBarangGudangController::class, 'index'])->name('permintaan-barang.index');
    Route::get('/permintaan-barang/{id}',[PermintaanBarangGudangController::class, 'show'])->name('permintaan-barang.show');
    Route::get('/permintaan-barang/{id}/edit',[PermintaanBarangGudangController::class, 'edit'])->name('permintaan-barang.edit');
    Route::put('/permintaan-barang/{id}',[PermintaanBarangGudangController::class, 'update'])->name('permintaan-barang.update');
    Route::delete('/permintaan-barang/{id}',[PermintaanBarangGudangController::class, 'destroy'] )->name('permintaan-barang.destroy');
    Route::post('/permintaan-barang/{id}/approve',[PermintaanBarangGudangController::class, 'approve'])->name('permintaan-barang.approve');
    Route::post('/permintaan-barang/{id}/reject',[PermintaanBarangGudangController::class, 'reject'])->name('permintaan-barang.reject');
    Route::post('/permintaan-barang/{id}/process',[PermintaanBarangGudangController::class, 'process'])->name('permintaan-barang.process');
    Route::post('/permintaan-barang/{id}/markAvailable',[PermintaanBarangGudangController::class, 'markAvailable'])->name('permintaan-barang.markAvailable');
    Route::post('/permintaan-barang/{id}/complete',[PermintaanBarangGudangController::class, 'complete'])->name('permintaan-barang.complete');
    Route::get('/permintaan-barang/riwayat/export-pdf',[PermintaanBarangGudangController::class, 'exportPdfRiwayat'])->name('permintaan-barang.riwayat.pdf');
    Route::get('/permintaan-barang/riwayat/export-excel',[PermintaanBarangGudangController::class, 'exportExcelRiwayat'])->name('permintaan-barang.riwayat.excel');

    // === PENGADAAN Barang & Jasa
    Route::get('/pengadaan-barang/riwayat',[PengadaanBarangJasaController::class, 'riwayat'])->name('pengadaan-barang.riwayat');
    Route::get('/pengadaan-barang',[PengadaanBarangJasaController::class, 'index'])->name('pengadaan-barang.index');
    Route::get('/pengadaan-barang/{id}',[PengadaanBarangJasaController::class, 'show'])->name('pengadaan-barang.show');
    Route::get('/pengadaan-barang/{id}/edit',[PengadaanBarangJasaController::class, 'edit'])->name('pengadaan-barang.edit');
    Route::put('/pengadaan-barang/{id}',[PengadaanBarangJasaController::class, 'update'])->name('pengadaan-barang.update');
    Route::delete('/pengadaan-barang/{id}',[PengadaanBarangJasaController::class, 'destroy'] )->name('pengadaan-barang.destroy');
    Route::post('/pengadaan-barang/{id}/approve',[PengadaanBarangJasaController::class, 'approve'])->name('pengadaan-barang.approve');
    Route::post('/pengadaan-barang/{id}/reject',[PengadaanBarangJasaController::class, 'reject'])->name('pengadaan-barang.reject');
    Route::post('/pengadaan-barang/{id}/process',[PengadaanBarangJasaController::class, 'process'])->name('pengadaan-barang.process');
    Route::post('/pengadaan-barang/{id}/complete',[PengadaanBarangJasaController::class, 'complete'])->name('pengadaan-barang.complete');
    Route::get('/pengadaan-barang/riwayat/export-pdf',[PengadaanBarangJasaController::class, 'exportPdfRiwayat'])->name('pengadaan-barang.riwayat.pdf');
    Route::get('/pengadaan-barang/riwayat/export-excel',[PengadaanBarangJasaController::class, 'exportExcelRiwayat'])->name('pengadaan-barang.riwayat.excel');

    // === PEMINJAMAN RUANGAN
    Route::get('/get-ruangan-available',[PeminjamanRuanganController::class, 'getRuanganAvailable']);
    Route::post('/cek-ketersediaan-ruangan',[PeminjamanRuanganController::class, 'cekKetersediaanRuangan']);
    Route::get('/peminjaman-ruangan/riwayat',[PeminjamanRuanganController::class, 'riwayat'])->name('peminjaman-ruangan.riwayat');
    Route::get('/get-ruangan/{id}', [PeminjamanRuanganController::class, 'getRuanganByGedung']);
    Route::get('/get-tanggal-available/{id}', [PeminjamanRuanganController::class, 'getTanggalAvailable']);
    Route::get('/get-aset-tersedia', [PeminjamanRuanganController::class, 'getAsetTersedia']);
    Route::get('/peminjaman-ruangan',[PeminjamanRuanganController::class, 'index'])->name('peminjaman-ruangan.index');
    Route::get('/peminjaman-ruangan/{id}',[PeminjamanRuanganController::class, 'show'])->name('peminjaman-ruangan.show');
    Route::get('/peminjaman-ruangan/{id}/edit',[PeminjamanRuanganController::class, 'edit'])->name('peminjaman-ruangan.edit');
    Route::put('/peminjaman-ruangan/{id}',[PeminjamanRuanganController::class, 'update'])->name('peminjaman-ruangan.update');
    Route::delete('/peminjaman-ruangan/{id}',[PeminjamanRuanganController::class, 'destroy'] )->name('peminjaman-ruangan.destroy');
    Route::post('/peminjaman-ruangan/{id}/approve',[PeminjamanRuanganController::class, 'approve'])->name('peminjaman-ruangan.approve');
    Route::post('/peminjaman-ruangan/{id}/reject',[PeminjamanRuanganController::class, 'reject'])->name('peminjaman-ruangan.reject');
    Route::post('/peminjaman-ruangan/{id}/process',[PeminjamanRuanganController::class, 'process'])->name('peminjaman-ruangan.process');
    Route::post('/peminjaman-ruangan/{id}/tersedia',[PeminjamanRuanganController::class, 'tersedia'])->name('peminjaman-ruangan.tersedia');
    Route::post('/peminjaman-ruangan/{id}/complete',[PeminjamanRuanganController::class, 'complete'])->name('peminjaman-ruangan.complete');
    Route::get('/peminjaman-ruangan/riwayat/export-pdf',[PeminjamanRuanganController::class, 'exportPdfRiwayat'])->name('peminjaman-ruangan.riwayat.pdf');
    Route::get('/peminjaman-ruangan/riwayat/export-excel',[PeminjamanRuanganController::class, 'exportExcelRiwayat'])->name('peminjaman-ruangan.riwayat.excel');

    // === PEMINJAMAN ASET
    Route::post('/cek-ketersediaan', [PeminjamanAsetController::class, 'cekKetersediaan']);
    Route::get('/peminjaman_aset/riwayat', [PeminjamanAsetController::class, 'riwayat'])->name('peminjaman_aset.riwayat');
    Route::get('/peminjaman_aset', [PeminjamanAsetController::class, 'index'])->name('peminjaman_aset.index');
    Route::get('/peminjaman_aset/{id}', [PeminjamanAsetController::class, 'show'])->name('peminjaman_aset.show');
    Route::get('/peminjaman_aset/{id}/edit',[PeminjamanAsetController::class, 'edit'])->name('peminjaman_aset.edit');
    Route::put('/peminjaman_aset/{id}',[PeminjamanAsetController::class, 'update'])->name('peminjaman_aset.update');
    Route::delete('/peminjaman_aset/{id}', [PeminjamanAsetController::class, 'destroy'])->name('peminjaman_aset.destroy');
    Route::post('/peminjaman_aset/{id}/approve', [PeminjamanAsetController::class, 'approve'])->name('peminjaman_aset.approve');
    Route::post('/peminjaman_aset/{id}/reject', [PeminjamanAsetController::class, 'reject'])->name('peminjaman_aset.reject');
    Route::post('/peminjaman_aset/{id}/serahkan', [PeminjamanAsetController::class, 'serahkanItem'])->name('peminjaman_aset.serahkanItem');
    Route::post('/peminjaman_aset/detail/{id_detail}/kembalikan',[PeminjamanAsetController::class, 'kembalikan'])->name('peminjaman_aset.kembalikan');
    Route::post('/peminjaman_aset/{id}/complete', [PeminjamanAsetController::class, 'complete'])->name('peminjaman_aset.complete');
    Route::get('/peminjaman_aset/riwayat/export-pdf', [PeminjamanAsetController::class, 'exportPdfRiwayat'])->name('peminjaman_aset.riwayat.pdf');
    Route::get('/peminjaman_aset/riwayat/export-excel', [PeminjamanAsetController::class, 'exportExcelRiwayat'])->name('peminjaman_aset.riwayat.excel');

    // === PENGADUAN KERUSAKAN
    Route::get('/pengaduan-kerusakan/riwayat',[PengaduanKerusakanController::class, 'riwayat'])->name('pengaduan-kerusakan.riwayat');
    Route::get('/pengaduan-kerusakan',[PengaduanKerusakanController::class, 'index'])->name('pengaduan-kerusakan.index');
    Route::get('/pengaduan-kerusakan/{id}',[PengaduanKerusakanController::class, 'show'])->name('pengaduan-kerusakan.show');
    Route::delete('/pengaduan-kerusakan/{id}',[PengaduanKerusakanController::class, 'destroy'] )->name('pengaduan-kerusakan.destroy');
    Route::post('/pengaduan-kerusakan/{id}/approve',[PengaduanKerusakanController::class, 'approve'])->name('pengaduan-kerusakan.approve');
    Route::post('/pengaduan-kerusakan/{id}/reject',[PengaduanKerusakanController::class, 'reject'])->name('pengaduan-kerusakan.reject');
    Route::get('/pengaduan-kerusakan/riwayat/export-pdf',[PengaduanKerusakanController::class, 'exportPdfRiwayat'])->name('pengaduan-kerusakan.riwayat.pdf');
    Route::get('/pengaduan-kerusakan/riwayat/export-excel',[PengaduanKerusakanController::class, 'exportExcelRiwayat'])->name('pengaduan-kerusakan.riwayat.excel');

    // === PERMINTAAN KENDARAAN
    Route::get('/cek-kendaraan', [PermintaanKendaraanController::class, 'cekKetersediaan']);
    Route::get('/permintaan-kendaraan/riwayat',[PermintaanKendaraanController::class, 'riwayat'])->name('permintaan-kendaraan.riwayat');
    Route::get('/permintaan-kendaraan',[PermintaanKendaraanController::class, 'index'])->name('permintaan-kendaraan.index');
    Route::get('/permintaan-kendaraan/{id}',[PermintaanKendaraanController::class, 'show'])->name('permintaan-kendaraan.show');
    Route::get('/permintaan-kendaraan/{id}/edit',[PermintaanKendaraanController::class, 'edit'])->name('permintaan-kendaraan.edit');
    Route::put('/permintaan-kendaraan/{id}',[PermintaanKendaraanController::class, 'update'])->name('permintaan-kendaraan.update');
    Route::delete('/permintaan-kendaraan/{id}',[PermintaanKendaraanController::class, 'destroy'] )->name('permintaan-kendaraan.destroy');
    Route::post('/permintaan-kendaraan/{id}/approve',[PermintaanKendaraanController::class, 'approve'])->name('permintaan-kendaraan.approve');
    Route::post('/permintaan-kendaraan/{id}/reject',[PermintaanKendaraanController::class, 'reject'])->name('permintaan-kendaraan.reject');
    Route::post('/permintaan-kendaraan/{id}/process',[PermintaanKendaraanController::class, 'process'])->name('permintaan-kendaraan.process');
    Route::post('/permintaan-kendaraan/{id}/complete',[PermintaanKendaraanController::class, 'complete'])->name('permintaan-kendaraan.complete');
    Route::get('/permintaan-kendaraan/riwayat/export-pdf',[PermintaanKendaraanController::class, 'exportPdfRiwayat'])->name('permintaan-kendaraan.riwayat.pdf');
    Route::get('/permintaan-kendaraan/riwayat/export-excel',[PermintaanKendaraanController::class, 'exportExcelRiwayat'])->name('permintaan-kendaraan.riwayat.excel');

    // === EKSPEDISI
    Route::get('/ekspedisi/riwayat',[EkspedisiController::class, 'riwayat'])->name('ekspedisi.riwayat');
    Route::get('/ekspedisi',[EkspedisiController::class, 'index'])->name('ekspedisi.index');
    Route::get('/ekspedisi/{id}',[EkspedisiController::class, 'show'])->name('ekspedisi.show');
    Route::get('/ekspedisi/{id}/edit',[EkspedisiController::class, 'edit'])->name('ekspedisi.edit');
    Route::put('/ekspedisi/{id}',[EkspedisiController::class, 'update'])->name('ekspedisi.update');
    Route::delete('/ekspedisi/{id}',[EkspedisiController::class, 'destroy'] )->name('ekspedisi.destroy');
    Route::post('/ekspedisi/{id}/approve',[EkspedisiController::class, 'approve'])->name('ekspedisi.approve');
    Route::post('/ekspedisi/{id}/reject',[EkspedisiController::class, 'reject'])->name('ekspedisi.reject');
    Route::post('/ekspedisi/{id}/process',[EkspedisiController::class, 'process'])->name('ekspedisi.process');
    Route::post('/ekspedisi/{id}/received',[EkspedisiController::class, 'received'])->name('ekspedisi.received');
    Route::post('/ekspedisi/{id}/complete',[EkspedisiController::class, 'complete'])->name('ekspedisi.complete');
    Route::get('/ekspedisi/{id}/label',[EkspedisiController::class, 'printLabel'])->name('ekspedisi.label');
    Route::get('/ekspedisi/{id}/tanda-terima',[EkspedisiController::class, 'printTandaTerima'])->name('ekspedisi.tanda-terima');
    Route::get('/ekspedisi/riwayat/export-pdf',[EkspedisiController::class, 'exportPdfRiwayat'])->name('ekspedisi.riwayat.pdf');
    Route::get('/ekspedisi/riwayat/export-excel',[EkspedisiController::class, 'exportExcelRiwayat'])->name('ekspedisi.riwayat.excel');

    // ===================================
    // RESOURCE ADMIN
    // ===================================

    // === GEDUNG
    Route::get('/gedung/{id}/dashboard', [GedungController::class, 'dashboard'])->name('gedung.dashboard');
    Route::resource('gedung', GedungController::class);

    // === RUANGAN
    Route::get('/ruangan/create', [RuanganController::class, 'create'])->name('ruangan.create'); // Form create
    Route::post('/ruangan', [RuanganController::class, 'store'])->name('ruangan.store'); // Store
    Route::get('/ruangan/show/{id_ruangan}', [RuanganController::class, 'show'])->name('ruangan.show'); // Show ruangan
    Route::get('/ruangan/{id_ruangan}/edit', [RuanganController::class, 'edit'])->name('ruangan.edit'); // Edit form
    Route::put('/ruangan/{id_ruangan}', [RuanganController::class, 'update'])->name('ruangan.update'); // Update process
    Route::delete('/ruangan/{id_ruangan}', [RuanganController::class, 'destroy'])->name('ruangan.destroy'); // Delete
    Route::get('/ruangan/{id}/dashboard', [RuanganController::class, 'dashboard'])->name('ruangan.dashboard'); // Dashboard per ruangan
    Route::get('/ruangan/{id_gedung?}', [RuanganController::class, 'index'])->name('ruangan.index'); // Filter ruangan berdasarkan gedung → tetap ke index
    Route::get('/api/ruangan-by-gedung/{id}', [RuanganController::class, 'getByGedung'])->name('api.ruangan.byGedung'); // API dependent dropdown
    
    // === ASET
    Route::get('/aset/generate-kode', [JenisBarangController::class, 'generateKode'])->name('aset.generateKode');
    Route::get('/aset/export', [AsetController::class, 'export'])->name('aset.export');
    Route::resource('aset', AsetController::class);
    Route::get('/api/jenis-barang/{kategori}', [AsetController::class, 'getJenisBarang']);
    Route::get('/jenis-barang/{kategori}', [JenisBarangController::class, 'getByKategori']);
    Route::post('aset/check/{id}', [AsetController::class, 'check'])->name('aset.check');

    // === JENIS BARANG
    Route::resource('jenis_barang', JenisBarangController::class)->except(['show']);

    
    // === GUDANG
    // TRANSAKSI BARANG
    Route::post('gudang/transaksi',[GudangController::class, 'transaksiStore'])->name('gudang.transaksi.store');
    Route::get('gudang/transaksi/index',[GudangController::class, 'transaksiIndex'])->name('gudang.transaksi.index');
    Route::get('gudang/transaksi/create',[GudangController::class, 'transaksiForm'])->name('gudang.transaksi.create');
    Route::get('gudang/transaksi/{id}',[GudangController::class, 'transaksiDetail'])->name('gudang.transaksi.detail');
    Route::get('gudang/transaksi/{id}/edit',[GudangController::class, 'transaksiEdit'])->name('gudang.transaksi.edit');
    Route::post('/gudang/transaksi/{id}/approve', [GudangController::class, 'approve'])->name('gudang.transaksi.approve');
    Route::post('/gudang/transaksi/{id}/reject', [GudangController::class, 'reject'])->name('gudang.transaksi.reject');
    Route::post('gudang/rekap-bulanan',[GudangController::class, 'rekapBulanan'])->name('gudang.rekapBulanan'); // REKAP BULANAN
    Route::get('gudang/stok_opname',[GudangController::class, 'stokOpnameIndex'])->name('gudang.stok_opname.index'); // HALAMAN STOK OPNAME (INDEX)
    Route::post('/gudang/stok_opname/mulai', [GudangController::class, 'opnameMulai'])->name('gudang.stok_opname.mulai');
    Route::post('/gudang/stok_opname/update/{id}', [GudangController::class, 'opnameUpdate'])->name('gudang.stok_opname.update');
    Route::post('/gudang/stok_opname/{id}/selesai', [GudangController::class, 'opnameSelesai'])->name('gudang.stok_opname.selesai');
    Route::get('/gudang/stok_opname/{id}', [GudangController::class, 'opnameDetail'])->name('gudang.stok_opname.detail');
    Route::resource('gudang', GudangController::class); // DASHBOARD
    Route::get('/gudang/stok_opname/{id}/export/pdf', [GudangController::class, 'exportOpnamePdf'])->name('gudang.stok_opname.export.pdf');
    Route::get('/gudang/stok_opname/{id}/export/excel', [GudangController::class, 'exportOpnameExcel'])->name('gudang.stok_opname.export.excel');

    // === APAR
    Route::post('apar/check/{id}', [AparController::class, 'check'])->name('apar.check');
    Route::post('apar/use/{id}', [AparController::class, 'use'])->name('apar.use');
    Route::get('/api/apar/ruangan-by-gedung/{id}', [AparController::class, 'getRuanganByGedung']);
    Route::resource('apar', AparController::class);

    // === KENDARAAN
    Route::resource('kendaraan', KendaraanController::class);

    // === MAINTENANCE
    Route::get('/ruangan/by-gedung/{id}', [RuanganController::class,'getByGedung']);
    Route::get('/aset/by-ruangan/{id}', [AsetController::class,'getByRuangan']);
    Route::get('/maintenance/laporan', [MaintenanceController::class,'laporan'])->name('maintenance.laporan');
    Route::get('/maintenance/export-pdf', [MaintenanceController::class,'exportPdf'])->name('maintenance.exportPdf');
    Route::get('/maintenance/export-excel', [MaintenanceController::class,'exportExcel'])->name('maintenance.exportExcel');
    Route::post('/maintenance/{id}/approve', [MaintenanceController::class,'approve'])->name('maintenance.approve');
    Route::post('/maintenance/{id}/reject', [MaintenanceController::class,'reject'])->name('maintenance.reject');
    Route::post('/maintenance/{id}/mulai',[MaintenanceController::class,'mulai'])->name('maintenance.mulai');
    Route::post('/maintenance/detail/{id}/mulai',[MaintenanceController::class, 'mulaiDetail'])->name('maintenance.mulaiDetail');
    Route::post('/maintenance/{id}/selesai', [MaintenanceController::class,'selesai'])->name('maintenance.selesai');
    Route::post('/maintenance/detail/{id}/selesai',[MaintenanceController::class, 'selesaiDetail'])->name('maintenance.selesaiDetail');
    Route::resource('maintenance', MaintenanceController::class);

    
    // === ROLES
    Route::prefix('roles')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('roles.index');
        Route::get('/create', [RolesController::class, 'create'])->name('roles.create');
        Route::post('/store', [RolesController::class, 'store'])->name('roles.store');
        Route::get('/edit/{id}', [RolesController::class, 'edit'])->name('roles.edit');
        Route::put('/update/{id}', [RolesController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');
    });

    // === USERS
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // === PEMUSNAHAN
    Route::resource('laporan_pemusnahan', LaporanPemusnahanController::class)->except(['create']);
    Route::get('/laporan-pemusnahan/create',[LaporanPemusnahanController::class, 'create'])->name('laporan_pemusnahan.create');
    Route::get('/laporan-pemusnahan/create/aset/{id_aset}',[LaporanPemusnahanController::class, 'createWithAset'])->name('laporan_pemusnahan.create.aset');
    Route::get('/laporan-pemusnahan/laporan',[LaporanPemusnahanController::class,'laporan'])->name('laporan_pemusnahan.laporan');
    Route::get('/laporan-pemusnahan/export-pdf',[LaporanPemusnahanController::class, 'exportPdf'])->name('laporan_pemusnahan.exportPdf');
    Route::get('/laporan-pemusnahan/export-excel',[LaporanPemusnahanController::class, 'exportExcel'])->name('laporan_pemusnahan.exportExcel');
    Route::get('/get-ruangan/{id}', [LaporanPemusnahanController::class, 'getRuangan']);
    Route::get('/get-aset/{id}', [LaporanPemusnahanController::class, 'getAset']);
    Route::post('/laporan_pemusnahan/{id}/approve',[LaporanPemusnahanController::class,'approve'])->name('laporan_pemusnahan.approve');
    Route::post('/laporan_pemusnahan/{id}/tolak',[LaporanPemusnahanController::class,'tolak'])->name('laporan_pemusnahan.tolak');
    Route::post('/laporan_pemusnahan/{id}/proses',[LaporanPemusnahanController::class,'proses'])->name('laporan_pemusnahan.proses');
    Route::post('/laporan_pemusnahan/{id}/selesai',[LaporanPemusnahanController::class,'selesai'])->name('laporan_pemusnahan.selesai');
    
    // === LAPORAN TAHUNAN
    Route::prefix('laporan_tahunan')->group(function () {
        Route::get('/', [LaporanTahunanController::class, 'index'])->name('laporan_tahunan.index');
        Route::post('/generate', [LaporanTahunanController::class, 'generate'])->name('laporan_tahunan.generate');
        Route::get('/{id}', [LaporanTahunanController::class, 'show'])->name('laporan_tahunan.show');
        Route::get('/{id}/export', [LaporanTahunanController::class, 'exportPDF'])->name('laporan_tahunan.export');
        Route::delete('/{id}', [LaporanTahunanController::class, 'destroy'])->name('laporan_tahunan.destroy');
    });

    Route::get('/laporan-tahunan/{id}/export-pdf',[LaporanTahunanController::class, 'exportPdf'])->name('laporan_tahunan.export_pdf');    
});
