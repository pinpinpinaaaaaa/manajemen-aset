@extends('layouts.app')

@section('title', 'Anggaran RKAT')

@section('content')
<main class="main-content">
    <div class="content-padding">

        <div class="page-header">
            <h1 class="page-title">Anggaran RKAT</h1>
            <nav class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <span class="separator">/</span>
                <span class="current">Anggaran RKAT</span>
            </nav>
        </div>

        <div style="background:#fff;border-radius:12px;padding:48px 32px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,.06);margin-top:16px;">
            <i class="fas fa-file-invoice-dollar" style="font-size:48px;color:#ebca56;margin-bottom:16px;display:block;"></i>
            <h2 style="font-size:20px;font-weight:600;color:#111;margin-bottom:8px;">Anggaran RKAT</h2>
            <p style="color:#6b7280;font-size:14px;">Fitur dalam pengembangan.</p>
        </div>

    </div>
</main>
@endsection
