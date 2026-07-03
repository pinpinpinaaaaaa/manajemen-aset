<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GudangTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $transaksi = [

            [
                'id' => 'TRX-' . $now->copy()->subDays(10)->format('Ymd') . '-0001',
                'tanggal' => $now->copy()->subDays(10),
                'jenis' => 'masuk',
                'ref' => 'Pengadaan ATK & RT',
                'items' => [
                    [
                        'id_barang' => 'AT001',
                        'jumlah' => 5,
                        'harga' => 10000 // 1 pack Rp10.000
                    ],
                    [
                        'id_barang' => 'AT002',
                        'jumlah' => 3,
                        'harga' => 15000
                    ],
                    [
                        'id_barang' => 'RT002',
                        'jumlah' => 5,
                        'harga' => 25000
                    ],
                ],
            ],

            [
                'id'     => 'TRX-' . $now->copy()->subDays(6)->format('Ymd') . '-0002',
                'tanggal'=> $now->copy()->subDays(6),
                'jenis'  => 'keluar',
                'ref'    => 'Operasional kantor',
                'items'  => [
                    ['id_barang' => 'AT001', 'jumlah' => 1],
                    ['id_barang' => 'AT004', 'jumlah' => 1],
                    ['id_barang' => 'RT001', 'jumlah' => 2],
                ],
            ],

            [
                'id' => 'TRX-' . $now->copy()->subDays(3)->format('Ymd') . '-0003',
                'tanggal' => $now->copy()->subDays(3),
                'jenis' => 'masuk',
                'ref' => 'Restock pantry',
                'items' => [
                    [
                        'id_barang' => 'RT001',
                        'jumlah' => 10,
                        'harga' => 5000
                    ],
                    [
                        'id_barang' => 'RT002',
                        'jumlah' => 3,
                        'harga' => 20000
                    ],
                    [
                        'id_barang' => 'RT003',
                        'jumlah' => 5,
                        'harga' => 12000
                    ],
                ],
            ],
        ];

        foreach ($transaksi as $trx) {

            $totalBiaya = 0;

            // ======================
            // INSERT HEADER
            // ======================
            DB::table('gudang_transaksi')->insert([
                'id_transaksi'    => $trx['id'],
                'tanggal'         => $trx['tanggal'],
                'jenis_transaksi' => $trx['jenis'],
                'referensi'       => $trx['ref'],
                'dibuat_oleh'     => 'seeder',
                'total_biaya'     => 0, // update setelah detail
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // ======================
            // INSERT DETAIL
            // ======================
            foreach ($trx['items'] as $item) {

                $barang = DB::table('gudang_barang')
                    ->where('id_barang', $item['id_barang'])
                    ->first();

                if (!$barang) continue;

                $konversi = $barang->konversi_satuan;
                $jumlahPcs = $item['jumlah'] * $konversi;

                $harga = $trx['jenis'] === 'masuk'
                    ? ($item['harga'] ?? 0)
                    : 0;

                // harga mengikuti satuan input (pack, dus, dll)
                $subtotal = $harga * $item['jumlah'];

                $totalBiaya += $subtotal;

                DB::table('gudang_transaksi_detail')->insert([
                    'id_transaksi'   => $trx['id'],
                    'id_barang'      => $item['id_barang'],

                    'jumlah_input'   => $item['jumlah'],
                    'satuan'         => $barang->satuan,
                    'konversi_pakai' => $konversi,
                    'jumlah'         => $jumlahPcs,

                    'harga_satuan'   => $harga,
                    'subtotal'       => $subtotal,

                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // ======================
            // UPDATE TOTAL BIAYA
            // ======================
            DB::table('gudang_transaksi')
                ->where('id_transaksi', $trx['id'])
                ->update(['total_biaya' => $totalBiaya]);
        }
    }
}
