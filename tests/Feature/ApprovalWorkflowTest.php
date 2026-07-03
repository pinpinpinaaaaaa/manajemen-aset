<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // POST requests dalam test tidak punya session CSRF — bypass tanpa disable middleware lain
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    // ─── Fixtures ─────────────────────────────────────────────

    private function buatUser(): User
    {
        $role = Role::factory()->create(['menu' => null]);
        return User::factory()->create(['id_role' => $role->id_role]);
    }

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

    private function buatPeminjaman(string $idDivisi, string $status = 'menunggu_persetujuan'): string
    {
        $id = 'PJM-' . now()->format('Ymd') . '-0001';
        DB::table('peminjaman_aset')->insert([
            'id_peminjaman'   => $id,
            'nama_pengaju'    => 'Test User',
            'email_pengaju'   => 'test@test.com',
            'id_divisi'       => $idDivisi,
            'alasan'          => 'Test alasan',
            'decision_status' => $status,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        return $id;
    }

    private function buatAset(string $idAset = 'AST001'): string
    {
        DB::table('gedung')->insert(['id_gedung' => 'GDG001', 'nama_gedung' => 'Gedung Test']);
        DB::table('ruangan')->insert([
            'id_ruangan'   => 'RNG001',
            'id_gedung'    => 'GDG001',
            'nama_ruangan' => 'Ruangan Test',
        ]);
        DB::table('aset')->insert([
            'id_aset'               => $idAset,
            'kode_aset'             => 'TEST001',
            'nama_aset'             => 'Aset Test',
            'id_gedung'             => 'GDG001',
            'id_ruangan'            => 'RNG001',
            'kelayakan'             => 1,
            'keterangan_kelayakan'  => 'Layak',
            'status'                => 'tersedia',
        ]);
        return $idAset;
    }

    private function buatDetail(string $idPeminjaman, string $idAset, string $statusPengembalian = 'menunggu'): int
    {
        return DB::table('peminjaman_aset_detail')->insertGetId([
            'id_peminjaman'      => $idPeminjaman,
            'id_aset'            => $idAset,
            'jumlah'             => 1,
            'tanggal_pinjam'     => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status_pengembalian' => $statusPengembalian,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    // ══════════════════════════════════════════════
    // APPROVE
    // ══════════════════════════════════════════════

    public function test_approve_mengubah_decision_status_menjadi_disetujui(): void
    {
        $user = $this->buatUser();
        $id   = $this->buatPeminjaman($this->buatDivisi());

        $this->actingAs($user)
            ->post("/peminjaman_aset/$id/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman_aset', [
            'id_peminjaman'  => $id,
            'decision_status' => 'disetujui',
        ]);
    }

    public function test_approve_menyimpan_approver_dan_waktu(): void
    {
        $user = $this->buatUser();
        $id   = $this->buatPeminjaman($this->buatDivisi());

        $this->actingAs($user)->post("/peminjaman_aset/$id/approve");

        $record = DB::table('peminjaman_aset')->where('id_peminjaman', $id)->first();
        $this->assertSame($user->id_user, $record->decided_by);
        $this->assertNotNull($record->decided_at);
    }

    // ══════════════════════════════════════════════
    // REJECT
    // ══════════════════════════════════════════════

    public function test_reject_mengubah_decision_status_menjadi_ditolak(): void
    {
        $user = $this->buatUser();
        $id   = $this->buatPeminjaman($this->buatDivisi());

        $this->actingAs($user)
            ->post("/peminjaman_aset/$id/reject", ['catatan' => 'Tidak memenuhi syarat'])
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman_aset', [
            'id_peminjaman'  => $id,
            'decision_status' => 'ditolak',
            'catatan'         => 'Tidak memenuhi syarat',
        ]);
    }

    public function test_reject_tanpa_catatan_gagal_validasi(): void
    {
        $user = $this->buatUser();
        $id   = $this->buatPeminjaman($this->buatDivisi());

        $this->actingAs($user)
            ->post("/peminjaman_aset/$id/reject", [])
            ->assertSessionHasErrors('catatan');

        // Status harus tetap tidak berubah
        $this->assertDatabaseHas('peminjaman_aset', [
            'id_peminjaman'  => $id,
            'decision_status' => 'menunggu_persetujuan',
        ]);
    }

    // ══════════════════════════════════════════════
    // SERAHKAN ITEM
    // ══════════════════════════════════════════════

    public function test_serahkan_mengubah_status_detail_menjadi_dipinjam(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'disetujui');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset);

        $this->actingAs($user)
            ->post("/peminjaman_aset/$detailId/serahkan")
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman_aset_detail', [
            'id'                  => $detailId,
            'status_pengembalian' => 'dipinjam',
        ]);
    }

    public function test_serahkan_mengubah_status_aset_menjadi_terpakai(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'disetujui');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset);

        $this->actingAs($user)->post("/peminjaman_aset/$detailId/serahkan");

        $this->assertDatabaseHas('aset', ['id_aset' => $idAset, 'status' => 'terpakai']);
    }

    public function test_serahkan_gagal_403_jika_peminjaman_belum_disetujui(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'menunggu_persetujuan');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset);

        $this->actingAs($user)
            ->post("/peminjaman_aset/$detailId/serahkan")
            ->assertStatus(403);
    }

    // ══════════════════════════════════════════════
    // KEMBALIKAN
    // ══════════════════════════════════════════════

    public function test_kembalikan_mengubah_status_detail_menjadi_dikembalikan(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'disetujui');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset, 'dipinjam');
        DB::table('aset')->where('id_aset', $idAset)->update(['status' => 'terpakai']);

        $this->actingAs($user)
            ->post("/peminjaman_aset/detail/$detailId/kembalikan", ['kondisi_kembali' => 'baik'])
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman_aset_detail', [
            'id'                  => $detailId,
            'status_pengembalian' => 'dikembalikan',
            'kondisi_kembali'     => 'baik',
        ]);
    }

    public function test_kembalikan_mengembalikan_status_aset_ke_tersedia(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'disetujui');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset, 'dipinjam');
        DB::table('aset')->where('id_aset', $idAset)->update(['status' => 'terpakai']);

        $this->actingAs($user)
            ->post("/peminjaman_aset/detail/$detailId/kembalikan", ['kondisi_kembali' => 'baik']);

        $this->assertDatabaseHas('aset', ['id_aset' => $idAset, 'status' => 'tersedia']);
    }

    public function test_kembalikan_dua_kali_gagal_403(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'disetujui');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset, 'dikembalikan'); // sudah kembali

        $this->actingAs($user)
            ->post("/peminjaman_aset/detail/$detailId/kembalikan", ['kondisi_kembali' => 'baik'])
            ->assertStatus(403);
    }

    /**
     * BUG REPORT: kolom 'status' tidak ada di tabel peminjaman_aset (migration)
     * dan tidak ada di $fillable model. Sehingga $parent->update(['status' => 'Selesai'])
     * di kembalikan() tidak berpengaruh — status tidak pernah tersimpan.
     *
     * Test ini akan GAGAL sampai bug diperbaiki:
     * 1. Tambah $table->string('status')->nullable() ke migrasi peminjaman_aset
     * 2. Tambah 'status' ke $fillable PeminjamanAset model
     */
    public function test_kembalikan_semua_item_mengupdate_status_parent_menjadi_selesai(): void
    {
        $user        = $this->buatUser();
        $idPeminjaman = $this->buatPeminjaman($this->buatDivisi(), 'disetujui');
        $idAset      = $this->buatAset();
        $detailId    = $this->buatDetail($idPeminjaman, $idAset, 'dipinjam');
        DB::table('aset')->where('id_aset', $idAset)->update(['status' => 'terpakai']);

        $this->actingAs($user)
            ->post("/peminjaman_aset/detail/$detailId/kembalikan", ['kondisi_kembali' => 'baik']);

        // Semua item sudah dikembalikan → parent.status harus 'Selesai'
        // Pakai raw query (bukan assertDatabaseHas) agar tidak crash saat kolom belum ada di schema
        $record = DB::table('peminjaman_aset')->where('id_peminjaman', $idPeminjaman)->first();
        $this->assertSame(
            'Selesai',
            $record->status ?? null,
            'BUG: kolom status tidak ada di schema/fillable peminjaman_aset — ' .
            'tambah kolom ke migrasi dan fillable model'
        );
    }
}
