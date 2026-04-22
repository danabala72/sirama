<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tom-select.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-style.css') }}">

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
                            <a href="{{ route('asesor.index') }}" class="nav-link {{ request()->routeIs('asesor.index') ? 'active' : '' }}">
                                <i class="ti ti-user-shield me-1"></i>
                                Asesor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('mahasiswa.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.index') ? 'active' : '' }}">
                                <i class="ti ti-users me-1"></i>
                                Mahasiswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('jurusan.index') }}" class="nav-link {{ request()->routeIs('jurusan.index') ? 'active' : '' }}">
                                <i class="ti ti-certificate me-1"></i>
                                Jurusan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('mk.index') }}" class="nav-link {{ request()->routeIs('mk.index') ? 'active' : '' }}">
                                <i class="ti ti-school me-1"></i>
                                Mata Kuliah
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()?->role?->role === 'Asesor')
                        <!-- Judul Kelompok Menu (Opsional) -->
                        <li class="nav-item">
                            <div class="nav-link disabled text-muted opacity-50 px-3">
                                <small class="fw-bold">ASESMEN TRANSFER SKS</small>
                            </div>
                        </li>

                        <!-- Menu Asesmen Pendidikan Formal -->
                        <li class="nav-item">
                            <a href="{{ route('asesmen.formal') }}" class="nav-link">
                                <i class="ti ti-book-2 me-1"></i>
                                Pendidikan Formal
                            </a>
                        </li>

                        <!-- Menu Asesmen Non-Formal / Informal -->
                        <li class="nav-item">
                            <a href="{{ route('asesmen.nonformal') }}" class="nav-link">
                                <i class="ti ti-briefcase me-1"></i>
                                Non-Formal & Informal
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

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/tabler.min.js') }}"></script>
    <script src="{{ asset('js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>

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
    </style>

</body>

</html>