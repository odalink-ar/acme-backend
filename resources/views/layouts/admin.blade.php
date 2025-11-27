<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel de administración')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link
        href="{{ asset('assets/acme/css/bootstrap.min.css') }}"
        rel="stylesheet"
    >

    {{-- Alpine.js --}}
    <script src="{{ asset('assets/acme/js/alpine.js') }}" defer></script>
<style>
    .page-link {
        color: #111827 !important;
    }
    .active>.page-link {
        color: #fff !important;
        border-color:  #111827 !important;
        background-color: #111827 !important;
    }
    .admin-navbar {
        background-color: #f9fafb;
        border-bottom: 1px solid rgba(148, 163, 184, 0.35);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }

    .admin-navbar .navbar-brand {
        font-size: 0.9rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #111827;
    }

    .admin-brand-mark {
        width: 18px;
        height: 18px;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        background: radial-gradient(circle at 30% 20%, #ffffff, #e5e7eb);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12);
    }

    .admin-brand-text {
        letter-spacing: 0.16em;
        text-transform: uppercase;
        font-size: 0.8rem;
    }

    .admin-navbar .navbar-toggler-icon {
        filter: invert(0.4);
    }

    .admin-nav-link {
        border-radius: 999px;
        padding: 0.3rem 0.9rem;
        font-size: 0.86rem;
        color: #4b5563;
        transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
    }

    .admin-nav-link:hover {
        background-color: #e5e7eb;
        color: #111827;
    }

    .admin-nav-link.active {
        background-color: #111827;
        color: #f9fafb !important;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        padding: 6px 14px;
    }

    .admin-nav-user .btn-outline-secondary {
        border-color: rgba(148, 163, 184, 0.7);
        color: #4b5563;
        font-size: 0.8rem;
    }

    .admin-nav-user .btn-outline-secondary:hover {
        background-color: #111827;
        color: #f9fafb;
        border-color: #111827;
    }
</style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg admin-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ route('admin.dashboard') }}">
            <span class="admin-brand-mark"></span>
            <span class="admin-brand-text">Admin</span>
        </a>

        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a
                        class="nav-link admin-nav-link @if(request()->routeIs('admin.users.*')) active @endif"
                        href="{{ route('admin.users.index') }}"
                    >
                        Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link admin-nav-link @if(request()->routeIs('admin.roles.*')) active @endif"
                        href="{{ route('admin.roles.index') }}"
                    >
                        Roles
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link admin-nav-link @if(request()->routeIs('admin.permissions.*')) active @endif"
                        href="{{ route('admin.permissions.index') }}"
                    >
                        Permisos
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 admin-nav-user">
                @auth
                    <span class="small text-muted">
                        {{ auth()->user()->email }}
                    </span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="submit">
                            Cerrar sesión
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        @hasSection('page_header')
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 mb-0">@yield('page_header')</h1>
                    @hasSection('page_subtitle')
                        <p class="text-muted small mb-0">
                            @yield('page_subtitle')
                        </p>
                    @endif
                </div>
                @yield('page_actions')
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script
    src="{{ asset('assets/acme/js/bootstrap.bundle.min.js') }}">
</script>
</body>
</html>