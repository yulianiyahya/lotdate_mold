<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - LotDate Mold</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lotdate.css') }}">
</head>
<body>
    <div class="guest">
        <div class="guest-card">
            <div class="guest-brand">LotDate Mold</div>

            <div class="card">
                <h1 class="title" style="font-size:24px;">Daftar Akun</h1>
                <p class="subtitle">Akun baru terdaftar sebagai PIC.</p>

                @if ($errors->any())
                    <div class="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <div class="field">
                        <label for="name">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required minlength="8">
                        <div class="hint">Minimal 8 karakter.</div>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Ulangi password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                </form>
            </div>

            <div class="guest-foot">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></div>
        </div>
    </div>
</body>
</html>