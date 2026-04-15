<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <style>
        body { font-family: Figtree, sans-serif; background: #eceff3; }
        .login-shell { max-width: 980px; }
        .brand-top { background: #214b97; }
        .hero-area { background: #f3f5f8; }
        .title-main { color: #1f2937; font-size: 30px; line-height: 1.2; font-weight: 600; }
        .title-sub { color: #214b97; font-size: 30px; line-height: 1.2; font-weight: 600; }
    </style>
</head>
<body>
    <div class="page page-center min-vh-100 py-4">
        <div class="container-tight login-shell">
            <div class="card shadow">
                <div class="card-header brand-top text-white py-2 px-3 border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white-lt text-white border-0">RPL</span>
                        <span class="small">Rekrutmen Mahasiswa RPL</span>
                    </div>                    
                </div>

                <div class="card-body hero-area p-3 p-md-4 p-lg-5">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <div class="title-main">Selamat Datang di Sistem</div>
                            <div class="title-sub">Rekrutmen Mahasiswa RPL</div>

                            <x-auth-session-status class="mt-3" :status="session('status')" />

                            @if ($errors->any())
                                <div class="alert alert-danger py-2 px-3 mt-3 mb-0" role="alert" style="font-size:12px;max-width:320px;">
                                    {{ __('Email atau password tidak valid.') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="mt-3" style="max-width:320px;">
                                @csrf

                                <div class="mb-2">
                                    <label for="username" class="form-label mb-1" style="font-size:12px;">Username</label>
                                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" class="form-control" placeholder="Masukkan username">
                                </div>

                                <div class="mb-2">
                                    <label for="password" class="form-label mb-1" style="font-size:12px;">Password</label>
                                    <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control" placeholder="Masukkan password">
                                </div>

                                <div class="mb-2">
                                    <button type="submit" class="btn w-100 text-white" style="background:#2f5cad;border-color:#2f5cad;">Login</button>
                                </div>

                                @if (Route::has('register'))
                                    <div class="text-center text-muted" style="font-size:11px;">
                                        Belum punya akun?
                                        <a href="{{ route('register') }}" class="text-decoration-none" style="color:#214b97;">Registrasi disini</a>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="col-lg-7 d-none d-lg-block">
                            <div class="p-3 rounded" style="background:#eaf0fa;">
                                <svg viewBox="0 0 700 360" class="w-100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <rect x="20" y="266" width="660" height="20" rx="10" fill="#CFDCF2"/>
                                    <circle cx="560" cy="62" r="16" fill="#D6E2F5"/>
                                    <circle cx="610" cy="92" r="9" fill="#D6E2F5"/>
                                    <rect x="70" y="150" width="170" height="96" rx="10" fill="#DCE7F8"/>
                                    <rect x="90" y="172" width="130" height="14" rx="7" fill="#AEC4E8"/>
                                    <rect x="90" y="196" width="96" height="10" rx="5" fill="#BFD1EE"/>
                                    <rect x="318" y="98" width="240" height="150" rx="14" fill="#DCE7F8"/>
                                    <rect x="344" y="126" width="188" height="84" rx="10" fill="#AEC4E8"/>
                                    <path d="M182 272H445" stroke="#8CA8D9" stroke-width="12" stroke-linecap="round"/>
                                    <circle cx="210" cy="272" r="35" fill="#214B97"/>
                                    <circle cx="420" cy="272" r="35" fill="#214B97"/>
                                    <rect x="178" y="305" width="64" height="40" rx="10" fill="#7FA0D6"/>
                                    <rect x="388" y="305" width="64" height="40" rx="10" fill="#7FA0D6"/>
                                    <circle cx="312" cy="220" r="38" fill="#6D92CD"/>
                                    <rect x="276" y="252" width="74" height="52" rx="12" fill="#4D78C2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center text-muted py-2" style="font-size:11px;">
                    &copy; {{ date('Y') }} Sistem Rekrutmen Mahasiswa RPL
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>
