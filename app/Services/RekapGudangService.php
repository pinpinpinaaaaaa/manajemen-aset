<?php

namespace App\Services;

use App\Models\GudangBarang;
use App\Models\GudangRekapBulanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapGudangService
{
    /**
     * Jalankan rekap stok bulanan untuk bulan tertentu.
     *
     * - Hanya bisa untuk bulan yang sudah selesai (bukan bulan berjalan).
     * - Idempotent: jika rekap sudah ada, update nilainya (upsert), tidak error.
     *
     * @param  string  $bulan  Format "Y-m", contoh "2026-08". Default: bulan lalu.
     * @return array{bulan: string, jumlah_barang: int}
     * @throws \InvalidArgumentException
     */
    public function rekap(string $bulan): array
    {
        $bulanDipilih  = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $bulanSekarang = now()->startOfMonth();

        if ($bulanDipilih->greaterThanOrEqualTo($bulanSekarang)) {
            throw new \InvalidArgumentException(
                "Rekap hanya bisa dilakukan untuk bulan yang sudah selesai (bukan {$bulan})."
            );
        }

        $bulanInt = (int) $bulanDipilih->month;
        $tahunInt = (int) $bulanDipilih->year;

        DB::transaction(function () use ($bulanInt, $tahunInt) {
            foreach (GudangBarang::all() as $barang) {
                $idRekap = sprintf('RB-%d%02d-%s', $tahunInt, $bulanInt, $barang->id_barang);

                GudangRekapBulanan::updateOrCreate(
                    ['id_rekap' => $idRekap],
                    [
                        'id_barang'   => $barang->id_barang,
                        'bulan'       => $bulanInt,
                        'tahun'       => $tahunInt,
                        'stok_awal'   => $barang->stok_awal,
                        'stok_masuk'  => $barang->stok_masuk,
                        'stok_keluar' => $barang->stok_keluar,
                        'stok_akhir'  => $barang->stok_akhir,
                    ]
                );

                // Reset akumulasi bulanan, carry-forward stok_akhir ke stok_awal
                $barang->stok_awal   = $barang->stok_akhir;
                $barang->stok_masuk  = 0;
                $barang->stok_keluar = 0;
                $barang->save();
            }
        });

        return [
            'bulan'        => $bulan,
            'jumlah_barang'=> GudangBarang::count(),
        ];
    }
}
