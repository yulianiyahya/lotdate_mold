<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LotDate Mold - Pantau Jatuh Tempo Penggantian Mold</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="lpx">

<header class="hero">
    <div class="wrap">
        <nav class="nav">
            <a href="{{ route('landing') }}" class="brand">
                <span class="brand-ico">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                </span>
                LotDate Mold
            </a>
            <div class="nav-links">
                <a href="#masalah">Masalah</a>
                <a href="#fitur">Fitur</a>
                <a href="#alur">Alur Kerja</a>
            </div>
            <div class="nav-right">
                <a href="{{ route('register') }}" class="txt">Daftar</a>
                <a href="{{ route('login') }}" class="btn-w">Masuk</a>
            </div>
        </nav>

        <div class="hero-grid">
            <div>
                <span class="badge">PT Kyoraku Blowmolding Indonesia</span>
                <h1>Jadwal ganti lot date mold, <span class="hl">tanpa terlewat.</span></h1>
                <p>Satu sistem untuk memantau umur lot date setiap mold, menghitung status otomatis, dan mengingatkan PE lewat email sebelum jatuh tempo.</p>
                <div class="hero-cta">
                    <a href="{{ route('dashboard') }}" class="btn-w">Buka Dashboard</a>
                    <a href="#alur" class="btn-d">Lihat alur kerja</a>
                </div>
            </div>

            <div class="mon">
                <div class="mon-head"><b>Monitoring Mold</b><span>13/10/2026</span></div>
                <div class="mon-sum">
                    <div class="sum n"><small>NORMAL</small><b>128</b></div>
                    <div class="sum w"><small>WARNING</small><b>14</b></div>
                    <div class="sum e"><small>EXPIRED</small><b>5</b></div>
                </div>
                <div class="mon-row th"><span>KODE MOLD</span><span>JATUH TEMPO</span><span>SISA UMUR</span><span>STATUS</span></div>
                <div class="mon-row"><b>MLD-017</b><span>28/09/2026</span><div class="bar"><i style="width:100%;background:#dc2626"></i></div><span class="pill e">Expired</span></div>
                <div class="mon-row"><b>MLD-042</b><span>02/11/2026</span><div class="bar"><i style="width:70%;background:#f59e0b"></i></div><span class="pill w">Warning</span></div>
                <div class="mon-row"><b>MLD-105</b><span>15/03/2027</span><div class="bar"><i style="width:28%;background:#10b981"></i></div><span class="pill n">Normal</span></div>
                <div class="mon-row"><b>MLD-088</b><span>20/06/2027</span><div class="bar"><i style="width:18%;background:#10b981"></i></div><span class="pill n">Normal</span></div>

                <div class="toast">
                    <span class="dot">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                    </span>
                    <div><b>Reminder terkirim ke PE</b><small>MLD-042 masuk lead time</small></div>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="wrap">
    <div class="legend">
        <b>Keterangan status</b>
        <div class="lg"><i style="background:#dc0f1e"></i><div><b>Expired <span>= Merah</span></b><p>Lewat jatuh tempo, segera ganti.</p></div></div>
        <div class="lg"><i style="background:#eab308"></i><div><b>Warning <span>= Kuning</span></b><p>Masuk lead time, reminder dikirim ke PE.</p></div></div>
        <div class="lg"><i style="background:#10b981"></i><div><b>Normal <span>= Hijau</span></b><p>Masih aman, belum perlu tindakan.</p></div></div>
    </div>

    <div class="stats">
        <div><b>1</b><span>tempat untuk semua data mold</span></div>
        <div><b>3</b><span>status otomatis: Normal, Warning, Expired</span></div>
        <div><b>0</b><span>hitung manual tanggal jatuh tempo</span></div>
    </div>

    <section class="sec" id="masalah">
        <div class="kicker">Masalah saat ini</div>
        <h2>Dari banyak sheet Excel per tahun, jadi satu daftar.</h2>
        <div class="cmp">
            <div class="box before">
                <small>SEBELUM</small>
                <div class="chips"><span class="chip">Sheet 2019</span><span class="chip">Sheet 2020</span><span class="chip">Sheet 2021</span></div>
                <p>PE membuka banyak sheet dan menghitung sisa umur lot date secara manual.</p>
            </div>
            <div class="box after">
                <small>SESUDAH</small>
                <div class="chips">
                    <span class="pill n">Normal</span><span class="pill w">Warning</span><span class="pill e">Expired</span>
                </div>
                <p>Satu daftar mold. Status dihitung otomatis dari jatuh tempo dan lead time.</p>
            </div>
        </div>
    </section>

    <section class="sec" id="fitur">
        <div class="kicker">Fitur utama</div>
        <h2>Dibuat untuk pekerjaan harian PE.</h2>
        <div class="feats">
            <div class="feat">
                <div class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg></div>
                <b>Data mold terpusat</b>
                <p>Satu daftar mold lengkap dengan lot date dan jatuh tempo penggantian.</p>
            </div>
            <div class="feat">
                <div class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
                <b>Status otomatis</b>
                <p>Normal, Warning, atau Expired mengikuti tanggal terbaru.</p>
            </div>
            <div class="feat">
                <div class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></div>
                <b>Reminder email ke PE</b>
                <p>Terkirim saat mold masuk lead time, tidak berulang untuk mold yang sama.</p>
            </div>
            <div class="feat">
                <div class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 3h6v3H9zM9 14l2 2 4-4"/></svg></div>
                <b>Riwayat penggantian</b>
                <p>Setiap penggantian lot date tercatat dan mudah ditelusuri.</p>
            </div>
        </div>
    </section>

    <section class="sec">
        <div class="kicker">Bukti &amp; pengingat</div>
        <h2>Setiap penggantian terdokumentasi, setiap jatuh tempo diingatkan.</h2>
        <div class="proof">
            <div class="pcard">
                <b>Riwayat berfoto</b>
                <p>Foto sebelum dan sesudah tiap penggantian.</p>
                <div class="ph"><div>Sebelum</div><div>Sesudah</div></div>
            </div>
            <div class="pcard">
                <b>Pengingat email</b>
                <p>Dikirim otomatis, tanpa pengiriman ganda.</p>
                <div class="mailbox">
                    <b>Pengingat mold</b>
                    <p>Mold A-02 akan jatuh tempo dalam 11 hari. Mohon siapkan pergantian.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="sec" id="alur">
        <div class="kicker">Alur kerja</div>
        <h2>Tiga langkah, tanpa hitung manual.</h2>
        <div class="steps">
            <div><div class="num">1</div><b>Input data mold</b><p>Data lot date dimasukkan sekali, atau diimpor dari Excel yang sudah ada.</p></div>
            <div><div class="num">2</div><b>Sistem memantau</b><p>Status tiap mold dihitung otomatis dan tampil di dashboard.</p></div>
            <div><div class="num">3</div><b>PE diingatkan</b><p>Email reminder dikirim, mold diganti, lalu penggantian dicatat ke riwayat.</p></div>
        </div>
    </section>

    <section class="cta">
        <h2>Mulai pantau mold dari satu layar.</h2>
        <p>Lihat mold mana yang Normal, mana yang Warning, dan mana yang sudah Expired.</p>
        <a href="{{ route('dashboard') }}" class="btn-w">Buka Dashboard</a>
    </section>
</main>

<footer class="wrap foot">
    <span>LotDate Mold · Sistem monitoring penggantian lot date mold</span>
    <span>PT Kyoraku Blowmolding Indonesia</span>
</footer>

</body>
</html>