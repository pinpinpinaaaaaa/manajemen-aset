<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function menuForm()
    {
        return view('menu_form');
    }

    public function sukses(Request $request)
    {
        $id    = $request->query('id', '-');
        $modul = $request->query('modul', 'Permohonan');
        return view('form_sukses', compact('id', 'modul'));
    }
}
