<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LotDate Mold')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lotdate.css') }}">

    <script>
        try {
            if (localStorage.getItem('sidebarCollapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>

    <style>
        .sidebar-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; }

        .sidebar-collapse-btn {
            border: 0;
            background: rgba(255, 255, 255, .18);
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
            flex-shrink: 0;
        }
        .sidebar-collapse-btn:hover { background: rgba(255, 255, 255, .3); }

        .sidebar-open-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 50;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 10px;
            background: #e60012;
            color: #fff;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
        }

        html.sidebar-collapsed .sidebar { display: none !important; }
        html.sidebar-collapsed .shell { grid-template-columns: 1fr !important; }
        html.sidebar-collapsed .main { margin-left: 0 !important; width: 100% !important; padding-top: 72px; }
        html.sidebar-collapsed .sidebar-open-btn { display: flex; }

        /* Modal konfirmasi keluar */
        .logout-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, .5);
            padding: 16px;
        }
        .logout-modal.show { display: flex; }

        .logout-modal-box {
            background: #fff;
            color: #111;
            width: 100%;
            max-width: 360px;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        }
        .logout-modal-box h3 { margin: 0 0 8px; font-size: 18px; font-weight: 700; }
        .logout-modal-box p { margin: 0 0 20px; font-size: 14px; color: #555; line-height: 1.5; }

        .logout-modal-actions { display: flex; gap: 10px; justify-content: center; }
        .logout-modal-actions form { margin: 0; }

        .logout-btn-cancel,
        .logout-btn-confirm {
            padding: 9px 20px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .logout-btn-cancel { border: 1px solid #ccc; background: #fff; color: #333; }
        .logout-btn-cancel:hover { background: #f3f3f3; }
        .logout-btn-confirm { border: 0; background: #e60012; color: #fff; }
        .logout-btn-confirm:hover { background: #c4000f; }
    </style>
</head>
<body>
@php
    $menu = [
        ['Dashboard',            'dashboard',       'dashboard',    ['PIC', 'Atasan']],
        ['Daftar Mold',          'molds.index',     'molds.*',      ['PIC', 'Atasan']],
        ['Persetujuan',          'approvals.index', 'approvals.*',  ['Atasan']],
        ['Riwayat Penggantian',  'history.index',   'history.*',    ['PIC', 'Atasan']],
        ['Reminder Email',       'reminders.index', 'reminders.*',  ['Atasan']],
        ['Kelola Atasan',        'atasan.index',    'atasan.*',     ['Atasan']],
    ];
@endphp

<button type="button" class="sidebar-open-btn" id="sidebarOpen" title="Tampilkan menu" aria-label="Tampilkan menu">☰</button>

<div class="shell">
    <aside class="sidebar">
        <div class="sidebar-head">
            <div class="brand">LotDate Mold</div>
            <button type="button" class="sidebar-collapse-btn" id="sidebarClose" title="Sembunyikan menu" aria-label="Sembunyikan menu">«</button>
        </div>

        <nav>
            @foreach ($menu as [$label, $routeName, $pattern, $roles])
                @if (in_array(auth()->user()->role, $roles))
                    @if (Route::has($routeName))
                        <a href="{{ route($routeName) }}"
                           class="nav-item {{ request()->routeIs($pattern) ? 'active' : '' }}">{{ $label }}</a>
                    @else
                        <span class="nav-item disabled">{{ $label }}</span>
                    @endif
                @endif
            @endforeach
        </nav>

        <div class="sidebar-user">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->role }}</small>
            <button type="button" class="link-btn" id="logoutOpen">Keluar</button>
        </div>
    </aside>

    <main class="main">
        @yield('content')
    </main>
</div>

{{-- Modal konfirmasi keluar --}}
<div class="logout-modal" id="logoutModal" role="dialog" aria-modal="true" aria-labelledby="logoutTitle">
    <div class="logout-modal-box">
        <h3 id="logoutTitle">Keluar dari akun?</h3>
        <p>Kamu perlu login lagi untuk mengakses LotDate Mold.</p>

        <div class="logout-modal-actions">
            <button type="button" class="logout-btn-cancel" id="logoutCancel">Batal</button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn-confirm">Ya, keluar</button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const root = document.documentElement;

        function setCollapsed(state) {
            root.classList.toggle('sidebar-collapsed', state);
            try { localStorage.setItem('sidebarCollapsed', state ? '1' : '0'); } catch (e) {}
        }

        document.getElementById('sidebarClose').addEventListener('click', () => setCollapsed(true));
        document.getElementById('sidebarOpen').addEventListener('click', () => setCollapsed(false));

        // Konfirmasi keluar
        const modal = document.getElementById('logoutModal');
        const openBtn = document.getElementById('logoutOpen');
        const cancelBtn = document.getElementById('logoutCancel');

        function openModal() { modal.classList.add('show'); }
        function closeModal() { modal.classList.remove('show'); }

        openBtn.addEventListener('click', openModal);
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    })();
</script>
</body>
</html>