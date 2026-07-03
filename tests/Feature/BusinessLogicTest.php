<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    // ─── User helpers ─────────────────────────────────────────

    private function buatUser(): User
    {
        $role = Role::factory()->create(['menu' => null]);
        return User::factory()->create(['id_role' => $role->id_role]);
    }

    private function buatSuperadmin(): User
    {
        // approve() di GudangController cek: role->nama_role === 'superadmin'
        $role = Role::factory()->create(['menu' => null, 'nama_role' => 'superadmin']);
        return User::factory()->create(['id_role' => $role->id_role]);
    }

    // ─── Gudang helpers ───────────────────────────────────────

    private function buatBarang(string $id, int $stokAkhir): void
    {
        DB::table('gudang_barang')->insert([
            'id_barang'       => $id,
            'nama_barang'     => 'Kertas A4',
            'jenis'           => 'atk',
            'satuan'          => 'lembar',
            'konversi_satuan' => 1,
            'satuan_dasar'    => 'lembar',
            'limit_stok'      => 0,
            'stok_awal'       => $stokAkhir,
            'stok_masuk'      => 0,
            'stok_keluar'     => 0,
            'stok_akhir'      => $stokAkhir,
            'stok_dipesan'    => 0,
        ]);
    }

    private function buatTransaksi(string $jenis, string $idBarang, int $jumlah): string
    {
        $id = 'TRX-' . now()->format('Ymd') . '-0001';

        DB::table('gudang_transaksi')->insert([
            'id_transaksi'    => $id,
            'tanggal'         => now()->toDateString(),
            'jenis_transaksi' => $jenis,
            'status'          => 'pending',
            'dibuat_oleh'     => 'Test User',
            'total_biaya'     => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        DB::table('gudang_transaksi_detail')->insert([
            'id_transaksi'   => $id,
            'id_barang'      => $idBarang,
            'jumlah_input'   => $jumlah,
            'satuan'         => 'lembar',
            'konversi_pakai' => 1,
            'jumlah'         => $jumlah,
            'harga_satuan'   => 0,
            'subtotal'       => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return $id;
    }

    // ─── Aset / Ketersediaan helpers ──────────────────────────

    private function buatJenisBarang(string $id = 'JB001'): string
    {
        DB::table('jenis_barang')->insert([
            'id_jenis_barang' => $id,
            'jenis'           => 'sarana',
            'kategori'        => 'it',
            'nama_barang'     => 'Laptop',
            'prefix_kode'     => 'LPT',
            'bisa_dipindah'   => 1,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        return $id;
    }

    private function buatGedungRuangan(): array
    {
        DB::table('gedung')->insert(['id_gedung' => 'GDG001', 'nama_gedung' => 'Gedung Test']);
        DB::table('ruangan')->insert([
            'id_ruangan'   => 'RNG001',
            'id_gedung'    => 'GDG001',
            'nama_ruangan' => 'Ruangan Test',
        ]);
        return ['GDG001', 'RNG001'];
    }

    private function buatAset(string $id, string $idJenisBarang): void
    {
        DB::table('aset')->insert([
            'id_aset'              => $id,
            'kode_aset'            => 'TEST' . $id,
            'nama_aset'            => 'Laptop Test',
            'id_jenis_barang'      => $idJenisBarang,
            'id_gedung'            => 'GDG001',
            'id_ruangan'           => 'RNG001',
            'kelayakan'            => 1,
            'keterangan_kelayakan' => 'Layak',
            'status'               => 'tersedia',
        ]);
    }

    private function buatPeminjamanDenganDetail(
        string $idAset,
        string $tanggalMulai,
        string $tanggalSelesai,
        string $decisionStatus = 'disetujui'
    ): void {
        DB::table('divisi')->insertOrIgnore([
            'id_divisi'   => 'DIV001',
            'nama_divisi' => 'Divisi Test',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $idPeminjaman = 'PJM-' . now()->format('Ymd') . '-' . substr($idAset, -3);

        DB::table('peminjaman_aset')->insert([
            'id_peminjaman'   => $idPeminjaman,
            'nama_pengaju'    => 'Peminjam',
            'email_pengaju'   => 'peminjam@test.com',
            'id_divisi'       => 'DIV001',
            'alasan'          => 'Test',
            'decision_status' => $decisionStatus,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        DB::table('peminjaman_aset_detail')->insert([
            'id_peminjaman'       => $idPeminjaman,
            'id_aset'             => $idAset,
            'jumlah'              => 1,
            'tanggal_pinjam'      => $tanggalMulai,
            'tanggal_jatuh_tempo' => $tanggalSelesai,
            'status_pengembalian' => 'menunggu',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    // ══════════════════════════════════════════════
    // GUDANG: Stok Deduction
    // GudangController::approve()
    // ══════════════════════════════════════════════

    public function test_approve_transaksi_masuk_menambah_stok(): void
    {
        $user = $this->buatSuperadmin();
        $this->buatBarang('AT001', 100);
        $idTransaksi = $this->buatTransaksi('masuk', 'AT001', 50);

        $this->actingAs($user)
            ->post("/gudang/transaksi/$idTransaksi/approve")
            ->assertRedirect();

        $barang = DB::table('gudang_barang')->where('id_barang', 'AT001')->first();
        $this->assertEquals(50, $barang->stok_masuk);
        $this->assertEquals(150, $barang->stok_akhir);
    }

    public function test_approve_transaksi_keluar_mengurangi_stok(): void
    {
        $user = $this->buatSuperadmin();
        $this->buatBarang('AT001', 100);
        $idTransaksi = $this->buatTransaksi('keluar', 'AT001', 30);

        $this->actingAs($user)
            ->post("/gudang/transaksi/$idTransaksi/approve")
            ->assertRedirect();

        $barang = DB::table('gudang_barang')->where('id_barang', 'AT001')->first();
        $this->assertEquals(30, $barang->stok_keluar);
        $this->assertEquals(70, $barang->stok_akhir);
    }

    public function test_approve_transaksi_keluar_gagal_jika_stok_tidak_cukup(): void
    {
        $user = $this->buatSuperadmin();
        $this->buatBarang('AT001', 20); // stok hanya 20
        $idTransaksi = $this->buatTransaksi('keluar', 'AT001', 50); // minta 50

        $this->actingAs($user)
            ->post("/gudang/transaksi/$idTransaksi/approve")
            ->assertRedirect(); // controller returns back() bukan abort

        // Stok harus tidak berubah (transaksi di-rollback)
        $barang = DB::table('gudang_barang')->where('id_barang', 'AT001')->first();
        $this->assertEquals(0, $barang->stok_keluar);
        $this->assertEquals(20, $barang->stok_akhir);
    }

    public function test_approve_hanya_bisa_dilakukan_superadmin(): void
    {
        $user = $this->buatUser(); // role biasa, bukan superadmin
        $this->buatBarang('AT001', 100);
        $idTransaksi = $this->buatTransaksi('masuk', 'AT001', 50);

        $this->actingAs($user)
            ->post("/gudang/transaksi/$idTransaksi/approve")
            ->assertStatus(403);

        // Stok tidak berubah
        $barang = DB::table('gudang_barang')->where('id_barang', 'AT001')->first();
        $this->assertEquals(100, $barang->stok_akhir);
    }

    // ══════════════════════════════════════════════
    // PEMINJAMAN ASET: Cek Ketersediaan
    // PeminjamanAsetController::cekKetersediaan()
    // POST /cek-ketersediaan (auth-protected)
    // ══════════════════════════════════════════════

    public function test_cek_ketersediaan_return_null_jika_parameter_kosong(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->post('/cek-ketersediaan', [])
            ->assertJson(['sisa' => null]);
    }

    public function test_cek_ketersediaan_menghitung_unit_tersedia_dengan_benar(): void
    {
        $user      = $this->buatUser();
        $idJenis   = $this->buatJenisBarang();
        $this->buatGedungRuangan();

        // 3 unit aset dengan nama dan jenis sama
        $this->buatAset('AST001', $idJenis);
        $this->buatAset('AST002', $idJenis);
        $this->buatAset('AST003', $idJenis);

        // 1 unit sedang dipinjam dalam range yang overlap (08 - 12 overlap dengan 10 - 15)
        $this->buatPeminjamanDenganDetail('AST001', '2026-07-08', '2026-07-12', 'disetujui');

        $this->actingAs($user)
            ->post('/cek-ketersediaan', [
                'nama_aset'          => 'Laptop Test',
                'id_jenis_barang'    => $idJenis,
                'tanggal_pinjam'     => '2026-07-10',
                'tanggal_jatuh_tempo' => '2026-07-15',
            ])
            ->assertJson([
                'total'   => 3,
                'dipakai' => 1,
                'sisa'    => 2,
            ]);
    }

    public function test_cek_ketersediaan_tidak_hitung_peminjaman_di_luar_range(): void
    {
        $user    = $this->buatUser();
        $idJenis = $this->buatJenisBarang();
        $this->buatGedungRuangan();

        $this->buatAset('AST001', $idJenis);
        $this->buatAset('AST002', $idJenis);
        $this->buatAset('AST003', $idJenis);

        // Peminjaman jauh sebelum range request (01 - 05, tidak overlap dengan 10 - 15)
        $this->buatPeminjamanDenganDetail('AST001', '2026-07-01', '2026-07-05', 'disetujui');

        $this->actingAs($user)
            ->post('/cek-ketersediaan', [
                'nama_aset'          => 'Laptop Test',
                'id_jenis_barang'    => $idJenis,
                'tanggal_pinjam'     => '2026-07-10',
                'tanggal_jatuh_tempo' => '2026-07-15',
            ])
            ->assertJson([
                'total'   => 3,
                'dipakai' => 0,
                'sisa'    => 3,
            ]);
    }

    public function test_cek_ketersediaan_tidak_hitung_peminjaman_yang_ditolak(): void
    {
        $user    = $this->buatUser();
        $idJenis = $this->buatJenisBarang();
        $this->buatGedungRuangan();

        $this->buatAset('AST001', $idJenis);
        $this->buatAset('AST002', $idJenis);

        // Peminjaman DITOLAK dalam range yang sama — tidak boleh dihitung sebagai dipakai
        $this->buatPeminjamanDenganDetail('AST001', '2026-07-10', '2026-07-15', 'ditolak');

        $this->actingAs($user)
            ->post('/cek-ketersediaan', [
                'nama_aset'          => 'Laptop Test',
                'id_jenis_barang'    => $idJenis,
                'tanggal_pinjam'     => '2026-07-10',
                'tanggal_jatuh_tempo' => '2026-07-15',
            ])
            ->assertJson([
                'total'   => 2,
                'dipakai' => 0,
                'sisa'    => 2,
            ]);
    }
}
