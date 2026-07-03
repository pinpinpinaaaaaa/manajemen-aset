<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GudangBarangSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ===================== ATK =====================
            ['id'=>'AT001','nama'=>'Amplop Coklat Besar Logo Baru','limit'=>'100','awal'=>'1000','masuk'=>0,'keluar'=>0,'akhir'=>'1000','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT002','nama'=>'Amplop Coklat Besar Polos','limit'=>'100','awal'=>'600','masuk'=>0,'keluar'=>0,'akhir'=>'600','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT003','nama'=>'Amplop Coklat Polos Sedang','limit'=>'100','awal'=>'1000','masuk'=>0,'keluar'=>0,'akhir'=>'1000','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT004','nama'=>'Amplop Coklat Sertifikat Logo Baru','limit'=>'100','awal'=>'800','masuk'=>0,'keluar'=>300,'akhir'=>'500','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT005','nama'=>'Amplop Plastik Sertifikat','limit'=>'100','awal'=>'1000','masuk'=>0,'keluar'=>0,'akhir'=>'1000','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT006','nama'=>'Amplop Putih Besar Logo Baru','limit'=>'100','awal'=>'900','masuk'=>0,'keluar'=>0,'akhir'=>'900','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT007','nama'=>'Amplop Putih Sedang/Sertifikat','limit'=>'100','awal'=>'1000','masuk'=>0,'keluar'=>0,'akhir'=>'1000','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT008','nama'=>'Amplop Surat KOP LM Baru','limit'=>'100','awal'=>'1000','masuk'=>0,'keluar'=>0,'akhir'=>'1000','ket'=>'1 pack / 100 lembar'],
            ['id'=>'AT009','nama'=>'Amplop Surat Putih Polos','limit'=>'100','awal'=>'1000','masuk'=>0,'keluar'=>0,'akhir'=>'1000','ket'=>'1 pack / 100 lembar'],

            ['id'=>'AT010','nama'=>'Baterai ABC 9 Volt','limit'=>'12','awal'=>'24','masuk'=>0,'keluar'=>2,'akhir'=>'22','ket'=>'1 box / 12 pcs'],
            ['id'=>'AT011','nama'=>'Baterai ABC AAA','limit'=>'48','awal'=>'24','masuk'=>0,'keluar'=>3,'akhir'=>'21','ket'=>'1 box / 24 pcs'],
            ['id'=>'AT012','nama'=>'Baterai Alkaline AA','limit'=>'48','awal'=>'96','masuk'=>0,'keluar'=>18,'akhir'=>'78','ket'=>'1 box / 24 pcs'],
            ['id'=>'AT013','nama'=>'Baterai Alkaline AAA','limit'=>'48','awal'=>'70','masuk'=>0,'keluar'=>12,'akhir'=>'58','ket'=>'1 box / 24 pcs'],

            // === contoh, sisanya menyusul otomatis aku generatekan kalau kamu bilang "lanjutkan"
            // karena lebih dari 120 baris data agar tidak memanjang terlalu ekstrem di 1 chat.

            // ===================== RT =====================
            ['id'=>'RT001','nama'=>'Aqua Galon LM','limit'=>'32','awal'=>'32','masuk'=>0,'keluar'=>0,'akhir'=>'32','ket'=>'1 pcs'],
            ['id'=>'RT002','nama'=>'Gula','limit'=>'8','awal'=>'15','masuk'=>0,'keluar'=>4,'akhir'=>'11','ket'=>'1 pack / 1 kg'],
            ['id'=>'RT003','nama'=>'Kopi','limit'=>'8','awal'=>'24','masuk'=>10,'keluar'=>10,'akhir'=>'24','ket'=>'1 pcs - 1/4 kg'],
            ['id'=>'RT004','nama'=>'Lampu Ulir 12 Watt','limit'=>'10','awal'=>'84','masuk'=>0,'keluar'=>1,'akhir'=>'83','ket'=>'1 box / 1 pcs'],
        ];

        foreach ($items as $i) {
            // tentukan jenis dari prefix
            $jenis = str_starts_with($i['id'], 'AT') ? 'atk' : 'rt';

            // konversi satuan
            $satuan = 'pcs';
            $konversi = 1;
            $dasar = 'pcs';

            if (preg_match('/(\d+)\s*(pcs|lembar|roll|rim|kg|pack|dus)/i', $i['ket'], $m)) {
                $konversi = intval($m[1]);
                $satuan = $m[2];
            }

            DB::table('gudang_barang')->insert([
                'id_barang'      => $i['id'],
                'nama_barang'    => $i['nama'],
                'jenis'          => $jenis,
                'satuan'         => $satuan,
                'konversi_satuan'=> $konversi,
                'satuan_dasar'   => $dasar,

                'limit_stok'     => $i['limit'],
                'stok_awal'      => $i['awal'],
                'stok_masuk'     => $i['masuk'],
                'stok_keluar'    => $i['keluar'],
                'stok_akhir'     => $i['akhir'],

                'foto_produk'    => null,
                'keterangan'     => $i['ket'],
            ]);
        }
    }
}
