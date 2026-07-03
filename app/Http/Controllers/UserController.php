<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        return view('users.index', [
            'users' => User::with('role')->paginate($perPage),
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => Role::all(),
            'menus' => Menu::orderBy('order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|max:100',
            'email'   => 'required|email|unique:users',
            'password'=> 'required|min:6',
            'id_role' => 'required|exists:roles,id_role',
            'menu'    => 'nullable|array',
        ]);

        $last = DB::table('users')->orderBy('id_user', 'desc')->first();
        $num = $last ? ((int) substr($last->id_user, 2)) + 1 : 1;
        $id_user = 'US' . str_pad($num, 3, '0', STR_PAD_LEFT);

        User::create([
            'id_user' => $id_user,
            'name'    => $request->name,
            'email'   => $request->email,
            'password'=> Hash::make($request->password),
            'id_role' => $request->id_role,
            // null = ikut role
            'menu'    => $request->menu ?? null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('users.edit', [
            'user'  => User::findOrFail($id),
            'roles' => Role::all(),
            'menus' => Menu::orderBy('order')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|max:100',
            'email'   => 'required|email|unique:users,email,' . $id . ',id_user',
            'id_role' => 'required|exists:roles,id_role',
            'password'=> 'nullable|min:6',
            'menu'    => 'nullable|array',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'id_role' => $request->id_role,
            'menu'    => $request->menu ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}
