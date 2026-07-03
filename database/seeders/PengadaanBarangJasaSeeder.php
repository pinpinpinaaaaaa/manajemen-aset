<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengadaanBarangJasaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ================= HEADER PENGADAAN =================
        DB::table('pengadaan_barang_jasa')->insert([
            [
                'id_pengadaan' => 'PGD001',
                'nama_pengaju' => 'Budi Santoso',
                'email_pengaju' => 'budi@perusahaan.com',
                'id_divisi' => 'DIV001',
                'alasan' => 'Kebutuhan komputer baru untuk staf IT.',
                'tanggal_kebutuhan' => $now->copy()->addDays(7),

                'total_biaya' => 18000000,
                'decision_status' => 'disetujui',
                'status' => 'Selesai',

                'decided_by' => 'U001',
                'decided_at' => $now->copy()->subDays(5),
                'catatan' => 'Pengadaan disetujui dan barang telah diterima.',

                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_pengadaan' => 'PGD002',
                'nama_pengaju' => 'Siti Rahma',
                'email_pengaju' => 'siti@perusahaan.com',
                'id_divisi' => 'DIV002',
                'alasan' => 'Kebutuhan jasa servis AC untuk seluruh ruangan gedung.',
                'tanggal_kebutuhan' => $now->copy()->addDays(3),

                'total_biaya' => 1500000,
                'decision_status' => 'disetujui',
                'status' => 'Selesai',

                'decided_by' => 'U001',
                'decided_at' => $now->copy()->subDays(2),
                'catatan' => 'Servis AC telah dilakukan oleh teknisi.',

                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_pengadaan' => 'PGD003',
                'nama_pengaju' => 'Ahmad Rizki',
                'email_pengaju' => 'ahmad@perusahaan.com',
                'id_divisi' => 'DIV003',
                'alasan' => 'Persediaan ATK untuk operasional kantor.',
                'tanggal_kebutuhan' => $now->copy()->addDays(14),

                'total_biaya' => 500000,
                'decision_status' => 'menunggu_persetujuan',
                'status' => 'Belum Diproses',

                'decided_by' => null,
                'decided_at' => null,
                'catatan' => 'Menunggu persetujuan kepala bagian.',

                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        // ================= DETAIL PENGADAAN =================
        DB::table('pengadaan_barang_jasa_detail')->insert([
            // PGD001 - Barang
            [
                'id_pengadaan' => 'PGD001',
                'jenis' => 'barang',

                'nama_barang' => 'Komputer Desktop',
                'merk' => 'Dell',
                'tipe_model' => 'Optiplex 7010',
                'spesifikasi' => 'Intel Core i7, RAM 16GB, SSD 512GB',
                'jumlah' => 2,

                'kategori_jasa' => null,

                'harga_satuan' => 9000000,
                'subtotal' => 18000000,
                'catatan' => 'Untuk kebutuhan staf IT.',

                'created_at' => $now,
                'updated_at' => $now,
            ],

            // PGD002 - Jasa
            [
                'id_pengadaan' => 'PGD002',
                'jenis' => 'jasa',

                'nama_barang' => null,
                'merk' => null,
                'tipe_model' => null,
                'spesifikasi' => null,
                'jumlah' => null,

                'kategori_jasa' => 'Servis AC',

                'harga_satuan' => 1500000,
                'subtotal' => 1500000,
                'catatan' => 'Servis 10 unit AC gedung.',

                'created_at' => $now,
                'updated_at' => $now,
            ],

            // PGD003 - Barang
            [
                'id_pengadaan' => 'PGD003',
                'jenis' => 'barang',

                'nama_barang' => 'Kertas HVS A4',
                'merk' => 'PaperOne',
                'tipe_model' => '80 GSM',
                'spesifikasi' => '1 rim isi 500 lembar',
                'jumlah' => 20,

                'kategori_jasa' => null,

                'harga_satuan' => 25000,
                'subtotal' => 500000,
                'catatan' => 'Stok untuk satu bulan.',

                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        // ================= FILE LAMPIRAN =================
        DB::table('pengadaan_barang_jasa_files')->insert([
            [
                'id_detail' => 1,
                'file_path' => 'pengadaan/PGD001_penawaran.pdf',
                'file_name' => 'Surat Penawaran Komputer.pdf',
                'file_type' => 'application/pdf',

                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_detail' => 2,
                'file_path' => 'pengadaan/PGD002_invoice.pdf',
                'file_name' => 'Invoice Servis AC.pdf',
                'file_type' => 'application/pdf',

                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}