<?php

namespace App\Services;

use App\Models\GudangBarang;
use App\Models\GudangTransaksi;
use App\Models\GudangTransaksiDetail;
use App\Models\PermintaanBarangGudang;
use Illuminate\Support\Facades\DB;

class PermintaanBarangGudangService
{
    /**
     * Selesaikan permintaan barang gudang:
     * buat transaksi keluar, update stok, ubah status ke Selesai.
     * Harus dipanggil di dalam DB::transaction().
     */
    public function complete(string $id): void
    {
        $permintaan = PermintaanBarangGudang::with('details')
            ->findOrFail($id);

        if ($permintaan->status !== 'Tersedia') {
            abort(403, 'Barang belum tersedia.');
        }

        $items = $permintaan->details->map(fn($d) => [
            'id_barang'    => $d->id_barang,
            'jumlah'       => $d->jumlah,
            'harga_satuan' => 0,
        ])->all();

        $this->createGudangTransaksi('keluar', $permintaan->id_permintaan, $items);

        foreach ($permintaan->details as $detail) {
            $barang = GudangBarang::lockForUpdate()
                ->where('id_barang', $detail->id_barang)
                ->first();

            if ($barang) {
                $barang->stok_dipesan = max(0, $barang->stok_dipesan - $detail->jumlah);
                $barang->save();
            }
        }

        $permintaan->update(['status' => 'Selesai']);
    }

    /**
     * Buat gudang transaksi beserta detail-nya.
     * Dipanggil dari complete() dan dari controller untuk kasus lain.
     */
    public function createGudangTransaksi(string $jenis, string $referensi, array $items): void
    {
        $idTransaksi = $this->generateTransaksiId();
        $totalBiaya  = 0;

        $transaksi = GudangTransaksi::create([
            'id_transaksi'    => $idTransaksi,
            'tanggal'         => now(),
            'jenis_transaksi' => $jenis,
            'referensi'       => $referensi,
            'dibuat_oleh'     => auth()->user()?->name ?? 'system',
            'total_biaya'     => 0,
            'status'          => 'approved',
            'approved_by'     => auth()->user()?->id_user ?? null,
            'approved_at'     => now(),
        ]);

        foreach ($items as $item) {
            $barang = GudangBarang::lockForUpdate()
                ->where('id_barang', $item['id_barang'])
                ->firstOrFail();

            $jumlahInput  = $item['jumlah'];
            $satuanDipilih = $item['satuan_pilih'] ?? $barang->satuan_dasar;

            if ($satuanDipilih === $barang->satuan_dasar) {
                $konversi  = 1;
                $jumlahReal = $jumlahInput;
            } else {
                $konversi  = max((int) $barang->konversi_satuan, 1);
                $jumlahReal = $jumlahInput * $konversi;
            }

            if ($jenis === 'keluar' && $barang->stok_akhir < $jumlahReal) {
                throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi");
            }

            $harga    = $item['harga_satuan'] ?? 0;
            $subtotal = $harga * $jumlahInput;
            $totalBiaya += $subtotal;

            GudangTransaksiDetail::create([
                'id_transaksi'  => $idTransaksi,
                'id_barang'     => $barang->id_barang,
                'jumlah_input'  => $jumlahInput,
                'satuan'        => $satuanDipilih,
                'konversi_pakai'=> $konversi,
                'jumlah'        => $jumlahReal,
                'harga_satuan'  => $harga,
                'subtotal'      => $subtotal,
            ]);

            if ($jenis === 'keluar') {
                $barang->stok_keluar += $jumlahReal;
                $barang->stok_akhir  -= $jumlahReal;

                if ($barang->stok_akhir < 0) {
                    throw new \Exception("Stok tidak boleh minus");
                }

                $barang->save();
            }
        }

        $transaksi->update(['total_biaya' => $totalBiaya]);
    }

    private function generateTransaksiId(): string
    {
        $date = now()->format('Ymd');

        $last = GudangTransaksi::where('id_transaksi', 'like', "TRX-$date-%")
            ->lockForUpdate()
            ->orderByDesc('id_transaksi')
            ->first();

        $next = $last ? (int) substr($last->id_transaksi, -4) + 1 : 1;

        return "TRX-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
