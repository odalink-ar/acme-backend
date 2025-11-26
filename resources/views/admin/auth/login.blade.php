<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <style>
        body.auth-body {
            min-height: 100vh;
            background: radial-gradient(circle at top, #f9fafb 0, #e5e7eb 45%, #d1d5db 100%);
        }

        .auth-card {
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
            background-color: #ffffff;
        }

        .auth-brand-mark {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            background: radial-gradient(circle at 30% 20%, #ffffff, #e5e7eb);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.15);
        }

        .auth-brand-text {
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-size: 0.8rem;
            color: #111827;
        }

        .auth-title {
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .auth-subtitle {
            font-size: 0.85rem;
        }

        .auth-card .form-label {
            color: #4b5563;
        }

        .auth-card .form-control {
            border-radius: 999px;
            padding: 0.55rem 0.9rem;
            background-color: #f9fafb;
            border: 1px solid rgba(148, 163, 184, 0.6);
            font-size: 0.9rem;
        }

        .auth-card .form-control:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            border-color: #2563eb;
        }

        .auth-card .btn-primary-auth {
            border-radius: 999px;
            padding: 0.55rem 0.9rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            color: #f9fafb !important;
            background-color: #111827;
            border-color: #111827;
        }

        .auth-card .btn-primary-auth:hover {
            background-color: #020617;
            border-color: #020617;
        }

        .auth-remember .form-check-input {
            border-radius: 0.35rem;
        }

        .auth-remember label {
            font-size: 0.8rem;
            color: #6b7280;
        }

        @media (max-width: 576px) {
            .auth-card {
                border-radius: 16px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
            }
        }
    </style>
</head>
<body class="auth-body">

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="auth-card card border-0" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4 p-md-5">

            <div class="d-flex flex-column align-items-center mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="auth-brand-mark"></span>
                    <span class="auth-brand-text">ADMIN</span>
                </div>
                <h1 class="h5 mb-1 auth-title text-center">
                    Panel de administración
                </h1>
                <p class="text-muted text-center mb-0 auth-subtitle">
                    Ingresá con tus credenciales
                </p>
            </div>

            {{-- Errores generales --}}
            @if ($errors->any())
                <div class="alert alert-danger small mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" novalidate class="d-flex flex-column gap-3">
                @csrf

                <div>
                    <label for="email" class="form-label small mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required
                        autofocus
                    >
                    @error('email')
                        <div class="invalid-feedback small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label small mb-1">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center auth-remember">
                    <div class="form-check mb-0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="1"
                            id="remember"
                            name="remember"
                        >
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>
                </div>

                <div class="d-grid mt-1">
                    <button type="submit" class="btn btn-primary-auth">
                        Ingresar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>
</body>
</html>