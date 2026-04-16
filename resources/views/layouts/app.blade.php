<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.1/dist/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.min.css" crossorigin="anonymous">

    <style>
        body {
            font-family: Figtree, sans-serif;
            background: #eef2f6;
            margin: 0;
        }

        .rpl-header {
            background: #edf1f6;
            padding: 10px 10px 0;
        }

        .rpl-header-inner {
            height: 46px;
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 0;
            border-radius: 4px 4px 0 0;
            overflow: hidden;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .06);
        }

        .rpl-brand-panel {
            background: #285aae;
            display: flex;
            align-items: center;
            padding: 0 12px;
            color: #fff;
        }

        .rpl-brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            min-width: 0;
        }

        .rpl-brand-link:hover,
        .rpl-brand-link:focus,
        .rpl-brand-link:active {
            text-decoration: none;
            color: inherit;
        }

        .rpl-brand-logo {
            width: 30px;
            height: 30px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .55);
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, .45), rgba(255, 255, 255, .1));
            flex: 0 0 auto;
            font-size: 16px;
        }

        .rpl-brand-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.05;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rpl-brand-sub {
            font-size: 10px;
            line-height: 1.15;
            color: rgba(255, 255, 255, .82);
        }

        .rpl-action-panel {
            position: relative;
            background: linear-gradient(90deg, #1d4e9a 0%, #194487 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 12px 0 24px;
            color: #fff;
        }

        .rpl-action-panel::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 28px;
            height: 100%;
            background: #285aae;
            clip-path: polygon(0 0, 100% 0, 0 100%);
            opacity: .95;
        }

        .rpl-action-panel::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 1px;
            background: rgba(255, 255, 255, .22);
            pointer-events: none;
        }

        .rpl-tools {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
            margin-left: 4px;
        }

        .rpl-tool-btn {
            width: 16px;
            height: 16px;
            border: 0;
            background: transparent;
            color: rgba(255, 255, 255, .92);
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            line-height: 1;
        }

        .rpl-tool-btn:hover {
            color: #fff;
        }

        .rpl-top-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
            white-space: nowrap;
        }

        .rpl-welcome {
            font-size: 11px;
            color: rgba(255, 255, 255, .92);
        }

        .rpl-top-actions .btn {
            font-size: 10px;
            line-height: 1;
            padding: .3rem .55rem;
            border-radius: 3px;
        }

        .main-shell {
            min-height: calc(100vh - 56px);
        }

        #layout-shell {
            display: flex;
            min-height: calc(100vh - 56px);
            align-items: stretch;
        }

        .sidebar-rpl {
            background: #f7f9fc;
            border-right: 1px solid #dbe3ef;
            flex: 0 0 260px;
            width: 260px;
            max-width: 260px;
            transition: transform .25s ease, opacity .25s ease, margin-left .25s ease;
            overflow-y: auto;
        }

        #main-content {
            flex: 1 1 auto;
            min-width: 0;
            transition: width .25s ease, max-width .25s ease, flex-basis .25s ease;
        }

        #layout-shell.sidebar-hidden #sidebar-menu {
            margin-left: -260px;
            transform: translateX(-100%);
            opacity: 0;
            pointer-events: none;
        }

        #layout-shell.sidebar-hidden #main-content {
            flex-basis: 100%;
            max-width: 100%;
        }

        @media (max-width: 991.98px) {
            .rpl-header-inner {
                grid-template-columns: 1fr;
                height: auto;
                overflow: visible;
                border-radius: 4px;
            }

            .rpl-brand-panel {
                border-right: none;
                border-bottom: none;
                border-radius: 4px 4px 0 0;
                min-height: 44px;
            }

            .rpl-action-panel {
                border-radius: 0 0 4px 4px;
                min-height: 40px;
            }

            #sidebar-menu {
                position: fixed;
                top: 56px;
                left: 0;
                z-index: 1040;
                width: 260px;
                max-width: 80vw;
                height: calc(100vh - 56px);
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform .25s ease;
                box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
                display: block !important;
                margin-left: 0;
            }

            #sidebar-menu.show {
                transform: translateX(0);
                opacity: 1;
                pointer-events: auto;
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 56px 0 0 0;
                background: rgba(0, 0, 0, .25);
                z-index: 1035;
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            #main-content {
                width: 100%;
                max-width: 100%;
                flex-basis: 100%;
            }
        }

        @media (max-width: 576px) {
            .rpl-header {
                padding: 8px 8px 0;
            }

            .rpl-action-panel::before {
                display: none;
            }

            .rpl-brand-title {
                font-size: 13px;
            }

            .rpl-brand-sub,
            .rpl-welcome {
                display: none;
            }
        }
    </style>
</head>

<body>

    <header class="rpl-header">
        <div class="rpl-header-inner">
            <div class="rpl-brand-panel">
                <a href="{{ route('dashboard') }}" class="rpl-brand-link">
                    <span class="rpl-brand-logo">
                        <i class="ti ti-shield-checkered"></i>
                    </span>
                    <span>
                        <span class="rpl-brand-title">Sistem Rekrutmen Mahasiswa RPL</span>
                        <span class="rpl-brand-sub">Politeknik Negeri Bali</span>
                    </span>
                </a>
            </div>

            <div class="rpl-action-panel">
                <div class="rpl-tools">
                    <a href="{{ route('dashboard') }}" class="rpl-tool-btn" aria-label="Dashboard">
                        <i class="ti ti-home"></i>
                    </a>
                    <button id="sidebar-toggle-btn" class="rpl-tool-btn" type="button" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle Sidebar">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </div>

                <div class="rpl-top-actions">
                    @auth
                    <span class="rpl-welcome">Selamat datang, <strong>{{ auth()->user()->username }}</strong></span>
                    <span class="text-muted">|</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm bg-white text-primary border-0">Log Out</button>
                    </form>
                    @else
                    @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-sm bg-white text-primary border-0">Log In</a>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-0 main-shell">
        <div id="layout-shell">
            <aside class="sidebar-rpl" id="sidebar-menu">
                <div class="p-3">
                    <ul class="nav nav-pills nav-vertical gap-1">
                        @if(auth()->user()?->role?->role === 'Admin')
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="ti ti-home-2 me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user') ? 'active' : '' }}">
                                <i class="ti ti-users me-1"></i>
                                User
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()?->role?->role === 'Mahasiswa')
                        <li class="nav-item">
                            <a href="{{ route('form.step', 'step=1') }}" class="nav-link {{ ($step ?? null) == 1 ? 'active' : '' }}">
                                Formulir 1
                            </a>
                        </li>
                        @foreach(range(2, 6) as $i)
                        <li class="nav-item">
                            @php
                            $hasMahasiswa = auth()->user()->mahasiswa()->exists();
                            @endphp

                            <a href="{{ $hasMahasiswa ? route('form.step', 'step=' . $i) : '#' }}"
                                class="nav-link {{ ($step ?? null) == $i ? 'active' : '' }} {{ !$hasMahasiswa ? 'disabled' : '' }}"
                                @if(!$hasMahasiswa) style="pointer-events: none;" @endif>
                                Formulir {{ $i }}
                            </a>
                        </li>

                        @endforeach
                        @endif
                    </ul>
                </div>
            </aside>


            <div id="sidebar-backdrop" class="sidebar-backdrop"></div>

            <main id="main-content" class="p-3 p-md-4">

                @isset($header)
                <div class="card mb-2">
                    <div class="card-body py-3">
                        <h2 class="m-0 h3">{{ $header }}</h2>
                    </div>
                </div>
                @endisset

                <div class="card">
                    <div class="card-body">
                        {{ $slot }}
                    </div>
                </div>

            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.7.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>

    <script>
        (() => {
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const shell = document.getElementById('layout-shell');
            const sidebar = document.getElementById('sidebar-menu');
            const backdrop = document.getElementById('sidebar-backdrop');
            const desktopQuery = window.matchMedia('(min-width: 992px)');

            if (!toggleBtn || !shell || !sidebar || !backdrop) return;

            const closeMobile = () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                toggleBtn.setAttribute('aria-expanded', 'false');
            };

            const toggleSidebar = () => {
                if (desktopQuery.matches) {
                    shell.classList.toggle('sidebar-hidden');
                    toggleBtn.setAttribute('aria-expanded', shell.classList.contains('sidebar-hidden') ? 'false' : 'true');
                    return;
                }

                const open = sidebar.classList.toggle('show');
                backdrop.classList.toggle('show', open);
                toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            toggleBtn.addEventListener('click', toggleSidebar);
            backdrop.addEventListener('click', closeMobile);
            window.addEventListener('resize', () => {
                if (desktopQuery.matches) closeMobile();
            });

            $('.dt-search input').addClass('form-control form-control-sm');
            $('.dt-length select').addClass('form-select form-select-sm');
        })();
    </script>
    @stack('scripts')
    <style>
        .dt-search {
            display: flex !important;
            align-items: center !important;
            padding-right: 2rem !important;
        }

        .dt-search input {
            margin-left: 0.75rem !important;
            border: 1px solid #dadcde !important;
            border-radius: 4px !important;
            padding: 0.4rem 0.75rem !important;
            outline: none !important;
            width: 200px !important;
            /* Lebar kotak search */
            background-color: #ffffff !important;
        }

        .dt-search input:focus {
            border-color: #206bc4 !important;
            /* Warna biru Tabler */
            box-shadow: 0 0 0 0.25rem rgba(32, 107, 196, 0.25) !important;
        }

        .dt-info {
            padding-left: 1rem !important;
        }

        /* Atur container header */
        th.dt-orderable-asc,
        th.dt-orderable-desc {
            cursor: pointer !important;
            position: relative;
            padding-right: 30px !important;
            vertical-align: middle !important;
            /* Pastikan teks di tengah */
        }

        /* Base style untuk icon sort */
        th.dt-orderable-asc::after,
        th.dt-orderable-desc::after {
            position: absolute !important;
            top: 50% !important;
            /* Letakkan di 50% tinggi header */
            transform: translateY(-50%);
            /* Geser ke atas setengah ukuran icon agar presisi di tengah */
            right: 10px;
            content: "↕";
            opacity: .3;
            font-size: .9rem;
            line-height: 1;
            /* Menghindari tinggi baris tambahan */
        }

        /* Saat aktif (Ascending) */
        th.dt-ordering-asc::after {
            content: "↑" !important;
            opacity: 1 !important;
            color: #206bc4;
        }

        /* Saat aktif (Descending) */
        th.dt-ordering-desc::after {
            content: "↓" !important;
            opacity: 1 !important;
            color: #206bc4;
        }
    </style>

</body>

</html>