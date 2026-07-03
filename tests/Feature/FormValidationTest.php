<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 2: Form Validation Tests — public forms (tanpa login)
 *
 * ⚠️ Valid-submission test untuk PermintaanKendaraan tidak bisa dijalankan
 * di SQLite karena store() menggunakan CONCAT() yang MySQL-specific.
 * Hanya validation-failure tests yang di-cover di sini.
 */
class FormValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            ValidateCsrfToken::class,
            ThrottleRequests::class,
        ]);
    }

    private function buatDivisi(string $id = 'DIV001'): void
    {
        DB::table('divisi')->insert([
            'id_divisi'   => $id,
            'nama_divisi' => 'Divisi Test',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    // ──────────────────────────────────────────────
    // Helper: data valid untuk form permintaan kendaraan
    // (tidak sampai hit DB kendaraan — hanya untuk lolos validasi)
    // ──────────────────────────────────────────────
    private function dataKendaraanValid(string $idDivisi = 'DIV001'): array
    {
        return [
            'nama_pengaju'   => 'Budi Santoso',
            'email'          => 'budi@example.com',
            'id_divisi'      => $idDivisi,
            'tanggal_mulai'  => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '17:00',
            'keperluan'      => 'Perjalanan dinas ke Bandung',
            'tempat_jemput'  => 'Kantor Pusat',
            'tempat_tujuan'  => 'Bandung',
            'jumlah'         => 1,
        ];
    }

    // ══════════════════════════════════════════════
    // /form-permintaan-kendaraan
    // PermintaanKendaraanController::store()
    // ══════════════════════════════════════════════

    public function test_pk_semua_field_wajib_kosong_gagal_validasi(): void
    {
        $response = $this->post('/form-permintaan-kendaraan', []);

        $response->assertSessionHasErrors([
            'nama_pengaju',
            'id_divisi',
            'tanggal_mulai',
            'tanggal_selesai',
            'jam_mulai',
            'jam_selesai',
            'keperluan',
            'tempat_jemput',
            'tempat_tujuan',
            'jumlah',
        ]);
    }

    public function test_pk_email_format_invalid_gagal_validasi(): void
    {
        $this->buatDivisi();

        $data = $this->dataKendaraanValid();
        $data['email'] = 'ini-bukan-email';

        $this->post('/form-permintaan-kendaraan', $data)
            ->assertSessionHasErrors('email');
    }

    public function test_pk_tanggal_selesai_sebelum_mulai_gagal_validasi(): void
    {
        $this->buatDivisi();

        $data = $this->dataKendaraanValid();
        $data['tanggal_mulai']  = now()->addDays(3)->toDateString();
        $data['tanggal_selesai'] = now()->addDay()->toDateString(); // lebih awal

        $this->post('/form-permintaan-kendaraan', $data)
            ->assertSessionHasErrors('tanggal_selesai');
    }

    public function test_pk_jam_selesai_sebelum_mulai_hari_sama_gagal_validasi(): void
    {
        $this->buatDivisi();

        $data = $this->dataKendaraanValid();
        $data['tanggal_selesai'] = $data['tanggal_mulai']; // hari sama
        $data['jam_mulai']       = '14:00';
        $data['jam_selesai']     = '09:00'; // lebih awal

        $this->post('/form-permintaan-kendaraan', $data)
            ->assertSessionHasErrors('jam_selesai');
    }

    public function test_pk_jumlah_nol_gagal_validasi(): void
    {
        $this->buatDivisi();

        $data = $this->dataKendaraanValid();
        $data['jumlah'] = 0;

        $this->post('/form-permintaan-kendaraan', $data)
            ->assertSessionHasErrors('jumlah');
    }

    public function test_pk_divisi_tidak_ada_di_database_gagal_validasi(): void
    {
        // Sengaja tidak buatDivisi — id_divisi harus gagal exists check
        $data = $this->dataKendaraanValid('TIDAK-ADA');

        $this->post('/form-permintaan-kendaraan', $data)
            ->assertSessionHasErrors('id_divisi');
    }

    // ══════════════════════════════════════════════
    // /form-pengaduan-kerusakan
    // PengaduanKerusakanController::store()
    // ══════════════════════════════════════════════

    public function test_pkd_semua_field_kosong_gagal_validasi(): void
    {
        $this->post('/form-pengaduan-kerusakan', [])
            ->assertSessionHasErrors([
                'nama_pelapor',
                'id_divisi',
                'items',
            ]);
    }

    public function test_pkd_nama_pelapor_wajib_gagal_validasi(): void
    {
        $this->buatDivisi();

        $this->post('/form-pengaduan-kerusakan', [
            'id_divisi' => 'DIV001',
            'items' => [[
                'id_gedung'          => 'GDG001',
                'id_ruangan'         => 'RNG001',
                'id_aset'            => 'AST001',
                'keluhan'            => 'Rusak',
                'kategori_kerusakan' => 'ringan',
            ]],
        ])->assertSessionHasErrors('nama_pelapor');
    }

    public function test_pkd_email_pelapor_invalid_gagal_validasi(): void
    {
        $this->buatDivisi();

        $this->post('/form-pengaduan-kerusakan', [
            'nama_pelapor'  => 'User Test',
            'email_pelapor' => 'bukan-email-valid',
            'id_divisi'     => 'DIV001',
        ])->assertSessionHasErrors('email_pelapor');
    }

    public function test_pkd_items_kosong_gagal_validasi(): void
    {
        $this->buatDivisi();

        $this->post('/form-pengaduan-kerusakan', [
            'nama_pelapor' => 'User Test',
            'id_divisi'    => 'DIV001',
            // items tidak ada
        ])->assertSessionHasErrors('items');
    }

    public function test_pkd_items_melebihi_10_gagal_validasi(): void
    {
        $this->buatDivisi();

        // Kirim 11 item (max adalah 10)
        $items = [];
        for ($i = 0; $i < 11; $i++) {
            $items[] = [
                'id_gedung'          => 'GDG001',
                'id_ruangan'         => 'RNG001',
                'id_aset'            => "AST00$i",
                'keluhan'            => 'Rusak',
                'kategori_kerusakan' => 'ringan',
            ];
        }

        $this->post('/form-pengaduan-kerusakan', [
            'nama_pelapor' => 'User Test',
            'id_divisi'    => 'DIV001',
            'items'        => $items,
        ])->assertSessionHasErrors('items');
    }
}
