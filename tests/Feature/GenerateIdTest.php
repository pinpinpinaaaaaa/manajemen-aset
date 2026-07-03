<?php

namespace Tests\Feature;

use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PeminjamanAsetController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Test correctness logika generateId() di controller.
 *
 * ⚠️  Catatan penting: test ini berjalan satu proses berurutan di SQLite
 * in-memory. lockForUpdate() tidak efektif di SQLite — test ini TIDAK
 * membuktikan keamanan concurrency. Yang dibuktikan: format ID benar,
 * penomoran sequential, tidak ada duplikat saat dipakai berturut-turut.
 */
class GenerateIdTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Helper: panggil private generateId() via Reflection
    // ──────────────────────────────────────────────

    private function callGenerateId(string $controllerClass): string
    {
        $controller = app($controllerClass);
        $method = new ReflectionMethod($controllerClass, 'generateId');
        $method->setAccessible(true);
        return $method->invoke($controller);
    }

    // Helper: insert record kendaraan minimal ke DB langsung
    private function insertKendaraan(string $id, int $urutan): void
    {
        DB::table('kendaraan')->insert([
            'id_kendaraan'    => $id,
            'jenis_kendaraan' => 'roda 4',
            'tipe'            => 'mobil',
            'plat_nomor'      => 'B' . str_pad($urutan, 4, '0', STR_PAD_LEFT) . 'TEST',
            'tahun_pembelian' => 2020,
            'umur_ekonomis'   => 10,
            'merk'            => 'Toyota',
            'model'           => 'Avanza',
            'status_kondisi'  => 'aktif',
            'status_penggunaan' => 'tersedia',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    // Helper: buat divisi dummy (dibutuhkan FK peminjaman_aset)
    private function buatDivisi(string $id = 'DIV001'): string
    {
        DB::table('divisi')->insert([
            'id_divisi'   => $id,
            'nama_divisi' => 'Divisi Test',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return $id;
    }

    // Helper: insert record peminjaman_aset langsung ke DB
    private function insertPeminjaman(int $n, string $idDivisi): void
    {
        $date = now()->format('Ymd');
        for ($i = 1; $i <= $n; $i++) {
            DB::table('peminjaman_aset')->insert([
                'id_peminjaman'   => 'PJM-' . $date . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_pengaju'    => 'Test User',
                'email_pengaju'   => 'test@test.com',
                'id_divisi'       => $idDivisi,
                'alasan'          => 'Test',
                'decision_status' => 'menunggu_persetujuan',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    // ══════════════════════════════════════════════
    // KendaraanController::generateId()
    // Format: KND001, KND002, ...
    // ══════════════════════════════════════════════

    public function test_kendaraan_id_pertama_adalah_knd001_saat_tabel_kosong(): void
    {
        $id = $this->callGenerateId(KendaraanController::class);

        $this->assertSame('KND001', $id);
    }

    public function test_kendaraan_id_berlanjut_dari_record_terakhir(): void
    {
        // Seed 5 record langsung ke DB
        for ($i = 1; $i <= 5; $i++) {
            $this->insertKendaraan('KND' . str_pad($i, 3, '0', STR_PAD_LEFT), $i);
        }

        $id = $this->callGenerateId(KendaraanController::class);

        $this->assertSame('KND006', $id);
    }

    public function test_kendaraan_id_format_tiga_digit_dengan_padding(): void
    {
        // Seed 9 record
        for ($i = 1; $i <= 9; $i++) {
            $this->insertKendaraan('KND' . str_pad($i, 3, '0', STR_PAD_LEFT), $i);
        }

        $id = $this->callGenerateId(KendaraanController::class);

        $this->assertMatchesRegularExpression('/^KND\d{3}$/', $id);
        $this->assertSame('KND010', $id);
    }

    public function test_kendaraan_sepuluh_id_berturut_unik_dan_sequential(): void
    {
        $ids = [];
        for ($i = 1; $i <= 10; $i++) {
            $id = $this->callGenerateId(KendaraanController::class);
            $ids[] = $id;
            $this->insertKendaraan($id, $i); // masukkan ke DB agar generateId berikutnya lanjut
        }

        // Semua unik
        $this->assertCount(10, array_unique($ids), 'Harus ada 10 ID unik');

        // Sequential dari KND001 ke KND010
        $this->assertSame('KND001', $ids[0]);
        $this->assertSame('KND010', $ids[9]);
    }

    // ══════════════════════════════════════════════
    // PeminjamanAsetController::generateId()
    // Format: PJM-YYYYMMDD-NNNN
    // ══════════════════════════════════════════════

    public function test_peminjaman_aset_id_format_benar(): void
    {
        $id = $this->callGenerateId(PeminjamanAsetController::class);
        $date = now()->format('Ymd');

        $this->assertMatchesRegularExpression(
            '/^PJM-' . $date . '-\d{4}$/',
            $id,
            'Format harus PJM-YYYYMMDD-NNNN'
        );
    }

    public function test_peminjaman_aset_id_pertama_adalah_0001_saat_kosong(): void
    {
        $id = $this->callGenerateId(PeminjamanAsetController::class);
        $date = now()->format('Ymd');

        $this->assertSame("PJM-$date-0001", $id);
    }

    public function test_peminjaman_aset_id_berlanjut_dari_record_terakhir(): void
    {
        $idDivisi = $this->buatDivisi();
        $this->insertPeminjaman(3, $idDivisi); // seed PJM-YYYYMMDD-0001 s/d 0003

        $id = $this->callGenerateId(PeminjamanAsetController::class);
        $date = now()->format('Ymd');

        $this->assertSame("PJM-$date-0004", $id);
    }

    public function test_peminjaman_aset_lima_id_berturut_unik_dan_sequential(): void
    {
        $idDivisi = $this->buatDivisi();
        $date = now()->format('Ymd');
        $ids = [];

        for ($i = 1; $i <= 5; $i++) {
            $id = $this->callGenerateId(PeminjamanAsetController::class);
            $ids[] = $id;
            // Masukkan ke DB agar counter lanjut pada iterasi berikutnya
            DB::table('peminjaman_aset')->insert([
                'id_peminjaman'   => $id,
                'nama_pengaju'    => 'User ' . $i,
                'email_pengaju'   => "u$i@test.com",
                'id_divisi'       => $idDivisi,
                'alasan'          => 'Test',
                'decision_status' => 'menunggu_persetujuan',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        $this->assertCount(5, array_unique($ids), 'Harus ada 5 ID unik');
        $this->assertSame("PJM-$date-0001", $ids[0]);
        $this->assertSame("PJM-$date-0005", $ids[4]);
    }
}
