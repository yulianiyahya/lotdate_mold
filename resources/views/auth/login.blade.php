@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h1 class="title" style="font-size:24px;">Masuk</h1>
    <p class="subtitle">Gunakan username atau email akunmu.</p>

    @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->has('login'))
        <div class="alert">{{ $errors->first('login') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="login">Username atau email</label>
            <input type="text" id="login" name="login" value="{{ old('login') }}" autofocus required>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div style="text-align:right; margin: -6px 0 14px;">
            <a href="{{ route('password.request') }}" style="font-size:13px; color:#e8001c; font-weight:600; text-decoration:none;">Lupa password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>
@endsection

@section('footer')
    Belum punya akun PIC? <a href="{{ route('register') }}">Daftar</a>
@endsection