<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - LotDate Mold</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lotdate.css') }}">
</head>
<body>
    <div class="guest">
        <div class="guest-card">
            <div class="guest-brand">LotDate Mold</div>
            <div class="card">
                @yield('content')
            </div>
            <div class="guest-foot">@yield('footer')</div>
        </div>
    </div>
</body>
</html>