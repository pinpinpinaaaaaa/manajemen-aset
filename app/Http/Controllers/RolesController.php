<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Menu;

class RolesController extends Controller
{
    private function generateId()
    {
        $latest = Role::selectRaw("CAST(SUBSTRING(id_role, 2) AS UNSIGNED) AS num")
            ->orderByDesc('num')
            ->first();

        if (!$latest) {
            return 'R0001';
        }

        $num = $latest->num + 1;
        return 'R' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        return view('roles.index', [
            'roles' => Role::all()
        ]);
    }

    public function create()
    {
        $menus = Menu::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('order')
            ->get();

        return view('roles.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|max:50',
            'menus'     => 'nullable|array',
        ]);

        Role::create([
            'id_role'   => $this->generateId(),
            'nama_role' => $request->nama_role,
            'menu'      => $request->has('menus')
                ? array_values($request->menus) // bisa [] atau isi
                : null, // full akses
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan');
    }
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $menus = Menu::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('order')
            ->get();

        $currentMenus = is_array($role->menu)
            ? $role->menu
            : [];

        return view('roles.edit', compact(
            'role',
            'menus',
            'currentMenus'
        ));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'nama_role' => 'required|max:50|unique:roles,nama_role,' . $role->id_role . ',id_role',
            'menus'     => 'nullable|array',
        ]);

        $role->update([
            'nama_role' => $request->nama_role,
            'menu'      => $request->has('menus')
                ? array_values($request->menus)
                : null,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();

        return back()->with('success', 'Role berhasil dihapus');
    }
}
