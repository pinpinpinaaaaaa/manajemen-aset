@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Edit Role</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <a href="{{ route('roles.index') }}">Roles</a>
                <span class="separator">/</span>
                <span class="current">Edit Role</span>
            </nav>
        </div>

        <div class="card mt-4 p-4 shadow-sm rounded-lg">
            <form action="{{ route('roles.update', $role->id_role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-4">
                    <label class="form-label">Nama Role</label>
                    <input
                        type="text"
                        name="nama_role"
                        class="form-control"
                        value="{{ $role->nama_role }}"
                        required
                    >
                </div>

                <h3 class="mb-3" style="font-size:20px; font-weight:600;">
                    Akses Menu & Permissions
                </h3>

                @php
                    $currentMenus = $currentMenus ?? [];
                @endphp

                <div style="display:flex; flex-direction:column; gap:24px;">

                @foreach ($menus as $parent)
                    <div style="
                        padding:20px;
                        border:1px solid #d1d5db;
                        background:#f8fafc;
                        border-radius:12px;
                    ">

                        <h4 style="font-size:17px; font-weight:600; margin-bottom:16px;">
                            {{ $parent->name }}
                        </h4>

                        <div style="display:flex; flex-direction:column; gap:16px; padding-left:16px;">

                            @foreach ($parent->children as $menu)

                                {{-- LEVEL 2 - FILE --}}
                                @if ($menu->type === 'file')
                                    <label style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:8px;
                                        padding:10px 14px;
                                        border:1px solid #d1d5db;
                                        background:#ffffff;
                                        border-radius:8px;
                                        cursor:pointer;
                                        width:fit-content;
                                    ">
                                        <input
                                            type="checkbox"
                                            name="menus[]"
                                            value="{{ $menu->id }}"
                                            {{ in_array($menu->id, $currentMenus) ? 'checked' : '' }}
                                        >
                                        <span>{{ $menu->name }}</span>
                                    </label>

                                {{-- LEVEL 2 - FOLDER (contoh: Sarpras, Gudang) --}}
                                @else
                                    <div>
                                        <div style="font-weight:600; margin-bottom:10px;">
                                            {{ $menu->name }}
                                        </div>

                                        <div style="
                                            display:flex;
                                            flex-wrap:wrap;
                                            gap:12px;
                                            padding-left:16px;
                                        ">
                                            @foreach ($menu->children as $child)
                                                <label style="
                                                    display:flex;
                                                    align-items:center;
                                                    gap:8px;
                                                    padding:10px 14px;
                                                    border:1px solid #d1d5db;
                                                    background:#ffffff;
                                                    border-radius:8px;
                                                    cursor:pointer;
                                                ">
                                                    <input
                                                        type="checkbox"
                                                        name="menus[]"
                                                        value="{{ $child->id }}"
                                                        {{ in_array($child->id, $currentMenus) ? 'checked' : '' }}
                                                    >
                                                    <span>{{ $child->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            @endforeach

                        </div>
                    </div>
                @endforeach

                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary btn-save">
                        Update
                    </button>
                </div>

            </form>
        </div>

    </div>
</main>
@endsection
