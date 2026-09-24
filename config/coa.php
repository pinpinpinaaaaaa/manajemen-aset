<?php

/**
 * Daftar Chart of Accounts (COA) bertingkat untuk Anggaran RKAT.
 * Kunci array = pos utama, nilai = array sub-pos.
 * Jika sub-pos berubah, update di sini — controller & view ambil dari sini.
 */
return [
    'Maintenance' => [
        'Maintenance AC',
        'Maintenance Gedung',
        'Maintenance Kendaraan',
        'Maintenance Peralatan',
    ],
    'Pengadaan' => [
        'Pengadaan Barang',
        'Pengadaan Jasa',
        'Beban Jasa Provider',
        'Beban Jasa Sub-Kontraktor',
    ],
    'Gudang' => [
        'Pembelian Barang Gudang',
        'Perlengkapan Gudang',
    ],
    'Aset' => [
        'Pengadaan Mebel dan Furnitur',
        'Pengadaan Kendaraan',
        'Pengadaan Peralatan Elektronik',
    ],
    'Umum' => [
        'Beban ATK',
        'Beban Operasional Kantor',
        'Beban Perjalanan Dinas',
        'Beban Pemeliharaan/Perbaikan',
    ],
];
