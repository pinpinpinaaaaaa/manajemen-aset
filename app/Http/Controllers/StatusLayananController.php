<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeminjamanRuangan;
use App\Models\PeminjamanAset;
use App\Models\PermintaanKendaraan;
use App\Models\Ekspedisi;
use App\Models\PengaduanKerusakan;
use App\Models\PermintaanBarangGudang;
use App\Models\PengadaanBarangJasa;

class StatusLayananController extends Controller
{
    public function index(Request $request)
    {
        $cari  = trim($request->get('cari', ''));
        $hasil = [];

        if ($cari !== '') {
            $hasil = $this->cari($cari);
        }

        return view('status_layanan.index', compact('cari', 'hasil'));
    }

    private function cari(string $cari): array
    {
        $rows = [];

        // PeminjamanRuangan
        PeminjamanRuangan::where('id_peminjaman', $cari)
            ->orWhere('email_pengaju', $cari)
            ->get(['id_peminjaman', 'nama_pengaju', 'email_pengaju', 'decision_status', 'status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Peminjaman Ruangan',
                    'id'      => $r->id_peminjaman,
                    'nama'    => $r->nama_pengaju,
                    'email'   => $r->email_pengaju,
                    'approval'=> $r->decision_status,
                    'status'  => $r->status,
                    'tanggal' => $r->created_at,
                ];
            });

        // PeminjamanAset
        PeminjamanAset::where('id_peminjaman', $cari)
            ->orWhere('email_pengaju', $cari)
            ->get(['id_peminjaman', 'nama_pengaju', 'email_pengaju', 'decision_status', 'status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Peminjaman Aset Kantor',
                    'id'      => $r->id_peminjaman,
                    'nama'    => $r->nama_pengaju,
                    'email'   => $r->email_pengaju,
                    'approval'=> $r->decision_status,
                    'status'  => $r->status,
                    'tanggal' => $r->created_at,
                ];
            });

        // PermintaanKendaraan
        PermintaanKendaraan::where('id_permohonan', $cari)
            ->orWhere('email', $cari)
            ->get(['id_permohonan', 'nama', 'email', 'status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Permintaan Kendaraan',
                    'id'      => $r->id_permohonan,
                    'nama'    => $r->nama,
                    'email'   => $r->email,
                    'approval'=> null,
                    'status'  => $r->status,
                    'tanggal' => $r->created_at,
                ];
            });

        // Ekspedisi
        Ekspedisi::where('id_ekspedisi', $cari)
            ->orWhere('email_pengaju', $cari)
            ->get(['id_ekspedisi', 'nama_pengirim', 'email_pengaju', 'decision_status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Ekspedisi',
                    'id'      => $r->id_ekspedisi,
                    'nama'    => $r->nama_pengirim,
                    'email'   => $r->email_pengaju,
                    'approval'=> $r->decision_status,
                    'status'  => null,
                    'tanggal' => $r->created_at,
                ];
            });

        // PengaduanKerusakan
        PengaduanKerusakan::where('id_pengaduan', $cari)
            ->orWhere('email_pelapor', $cari)
            ->get(['id_pengaduan', 'nama_pelapor', 'email_pelapor', 'decision_status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Pengaduan Kerusakan',
                    'id'      => $r->id_pengaduan,
                    'nama'    => $r->nama_pelapor,
                    'email'   => $r->email_pelapor,
                    'approval'=> $r->decision_status,
                    'status'  => null,
                    'tanggal' => $r->created_at,
                ];
            });

        // PermintaanBarangGudang
        PermintaanBarangGudang::where('id_permintaan', $cari)
            ->orWhere('email_pengaju', $cari)
            ->get(['id_permintaan', 'nama_pengaju', 'email_pengaju', 'decision_status', 'status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Permintaan Barang Gudang',
                    'id'      => $r->id_permintaan,
                    'nama'    => $r->nama_pengaju,
                    'email'   => $r->email_pengaju,
                    'approval'=> $r->decision_status,
                    'status'  => $r->status,
                    'tanggal' => $r->created_at,
                ];
            });

        // PengadaanBarangJasa
        PengadaanBarangJasa::where('id_pengadaan', $cari)
            ->orWhere('email_pengaju', $cari)
            ->get(['id_pengadaan', 'nama_pengaju', 'email_pengaju', 'decision_status', 'status', 'created_at'])
            ->each(function ($r) use (&$rows) {
                $rows[] = [
                    'modul'   => 'Pengadaan Barang & Jasa',
                    'id'      => $r->id_pengadaan,
                    'nama'    => $r->nama_pengaju,
                    'email'   => $r->email_pengaju,
                    'approval'=> $r->decision_status,
                    'status'  => $r->status,
                    'tanggal' => $r->created_at,
                ];
            });

        // Urutkan dari terbaru
        usort($rows, fn($a, $b) => $b['tanggal'] <=> $a['tanggal']);

        return $rows;
    }
}
