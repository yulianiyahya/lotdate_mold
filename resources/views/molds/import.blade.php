@extends('layouts.app')

@section('title', 'Impor Mold dari Excel')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="title">Impor Mold dari Excel</h1>
            <p class="subtitle">Unggah file Excel untuk menambahkan banyak mold sekaligus.</p>
        </div>
        <div class="head-actions">
            <a href="{{ route('molds.index') }}" class="btn btn-outline">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert">
            @foreach ($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    @if (session('import_errors'))
        <div class="alert">
            <b>Baris yang dilewati:</b>
            <ul style="margin:8px 0 0 18px;">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <p style="margin-top:0;">
            1. Unduh template, isi datanya, lalu unggah di bawah.<br>
            2. Jangan ubah nama kolom (baris pertama).<br>
            3. Format tanggal: <b>dd/mm/yyyy</b>.<br>
            4. Satuan (frekuensi_satuan dan lead_time_satuan) diisi <b>Hari</b>, <b>Bulan</b>, atau <b>Tahun</b>.<br>
            5. Kolom wajib: <b>part_name</b>, <b>frekuensi_nilai</b>, <b>frekuensi_satuan</b>, <b>lead_time_nilai</b>, <b>lead_time_satuan</b>, dan <b>penggantian_terakhir</b>. Kategori diisi <b>Blow</b> atau <b>Inject</b>.
        </p>

        <a href="{{ route('molds.import.template') }}" class="btn btn-outline">Unduh template</a>

        <form method="POST" action="{{ route('molds.import.store') }}" enctype="multipart/form-data" style="margin-top:20px;">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
            <div style="margin-top:16px;">
                <button type="submit" class="btn btn-primary">Impor sekarang</button>
            </div>
        </form>
    </div>
@endsection