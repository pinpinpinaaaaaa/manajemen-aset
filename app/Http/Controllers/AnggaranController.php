<?php

namespace App\Http\Controllers;

class AnggaranController extends Controller
{
    public function rkat()
    {
        return view('anggaran.rkat');
    }

    public function realisasi()
    {
        return view('anggaran.realisasi');
    }
}
