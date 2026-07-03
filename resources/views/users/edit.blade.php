@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <main class="main-content">
        <div class="content-padding">

            <div class="page-header">
                <h1 class="page-title">Edit User</h1>

                <nav class="breadcrumb">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>

                    <span class="separator">/</span>

                    <a href="{{ route('users.index') }}">
                        User
                    </a>

                    <span class="separator">/</span>

                    <span class="current">
                        Edit User
                    </span>
                </nav>
            </div>

            <div class="card mt-4 p-4 shadow-sm rounded-lg">

                <form action="{{ route('users.update', $user->id_user) }}" method="POST">

                    @csrf
                    @method('PUT')

                    {{-- NAMA --}}
                    <div class="form-group mb-3">
                        <label class="form-label">
                            Nama User
                        </label>

                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            required>

                        @error('name')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    {{-- EMAIL --}}
                    <div class="form-group mb-3">
                        <label class="form-label">
                            Email
                        </label>

                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            required>

                        @error('email')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div class="form-group mb-3">
                        <label class="form-label">
                            Password Baru
                        </label>

                        <input type="password" name="password" class="form-control"
                            placeholder="Kosongkan jika tidak ingin mengganti password">

                        <small class="text-muted">
                            Kosongkan jika password tidak ingin diubah.
                        </small>

                        @error('password')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    {{-- KONFIRMASI PASSWORD --}}
                    <div class="form-group mb-4">
                        <label class="form-label">
                            Konfirmasi Password
                        </label>

                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Konfirmasi password baru">
                    </div>

                    {{-- ROLE --}}
                    <div class="form-group mb-4">
                        <label class="form-label">
                            Role
                        </label>

                        <select name="id_role" class="form-select">

                            <option value="">
                                -- Pilih Role --
                            </option>

                            @foreach ($roles as $role)
                                <option value="{{ $role->id_role }}"
                                    {{ old('id_role', $user->id_role) == $role->id_role ? 'selected' : '' }}>
                                    {{ $role->nama_role }}
                                </option>
                            @endforeach

                        </select>

                        @error('id_role')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    {{-- MENU ACCESS --}}
                    <div class="form-group mb-4">

                        <label class="form-label d-block mb-2">
                            Hak Akses Menu
                        </label>

                        <div class="alert-info">
                            Jika kosong → mengikuti akses dari role.
                        </div>

                        <div class="menu-grid">

                            @php
                                $selectedMenus = old('menu', is_array($user->menu) ? $user->menu : []);
                            @endphp

                            @foreach ($menus as $menu)
                                <label class="menu-item">

                                    <input type="checkbox" name="menu[]" value="{{ $menu->id }}"
                                        {{ in_array($menu->id, $selectedMenus) ? 'checked' : '' }}>

                                    <span>
                                        {{ $menu->name }}
                                    </span>

                                </label>
                            @endforeach

                        </div>

                        @error('menu')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    {{-- BUTTON --}}
                    <div class="text-end">

                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </main>

    <style>
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 10px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: all .2s ease;
        }

        .menu-item:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .menu-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .alert-info {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 14px;
        }
    </style>
@endsection
