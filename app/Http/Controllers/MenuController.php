<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        return view('menus.index', [
            'menus' => Menu::orderBy('parent_id')
                ->orderBy('order')
                ->get()
        ]);
    }

    public function create()
    {
        return view('menus.create', [
            'parents' => Menu::where('type', 'folder')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'type'      => 'required|in:folder,file',
            'parent_id' => 'nullable|exists:menus,id',
            'order'     => 'nullable|integer',
        ]);

        Menu::create([
            'name'      => $request->name,
            'type'      => $request->type,
            'route_name'=> $request->route_name ?? null,
            'parent_id' => $request->parent_id,
            'order'     => $request->order ?? 0,
        ]);

        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil ditambahkan');
    }
}
