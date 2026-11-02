<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - LotDate Mold</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lotdate.css') }}">
</head>
<body>
    <div class="guest">
        <div class="guest-card">
            <div class="guest-brand">LotDate Mold</div>

            <div class="card">
                <h1 class="title" style="font-size:24px;">Lupa Password</h1>
                <p class="subtitle">Masukkan email akunmu. Kami kirim tautan untuk membuat password baru.</p>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Kirim tautan reset</button>
                </form>
            </div>

            <div class="guest-foot"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></div>
        </div>
    </div>
</body>
</html>