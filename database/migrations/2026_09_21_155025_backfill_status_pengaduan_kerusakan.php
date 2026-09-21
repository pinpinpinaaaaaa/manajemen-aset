<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Record yang di-approve sebelum kolom `status` ditambahkan
     * mendapat nilai default 'Belum Diproses', padahal seharusnya 'Sedang Diproses'.
     * Migration ini membenarkan data tersebut.
     */
    public function up(): void
    {
        // disetujui + masih 'Belum Diproses' → sebenarnya sedang diproses (maintenance sudah dibuat)
        DB::table('pengaduan_kerusakan')
            ->where('decision_status', 'disetujui')
            ->where('status', 'Belum Diproses')
            ->update(['status' => 'Sedang Diproses']);

        // ditolak yang belum punya status tepat → biarkan 'Belum Diproses' (tidak ada aksi lanjut)
    }

    public function down(): void
    {
        // Kembalikan disetujui+Sedang Diproses ke Belum Diproses
        // (hanya yang memang diubah oleh up() — tidak bisa dibedakan dari yang benar, jadi no-op)
    }
};
