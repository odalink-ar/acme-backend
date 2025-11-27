@extends('layouts.admin')

@section('title', 'Usuarios | Admin')

@section('page_header', 'Usuarios')
@section('page_subtitle', 'Gestioná los usuarios, roles y permisos del sistema')

@section('page_actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-dark">
        Crear usuario
    </a>
@endsection

@section('content')
    {{-- Filtro de búsqueda --}}
    <div class="card shadow-sm border-0 mb-3 user-filter-card">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-wrap gap-3 align-items-end">

                <div class="flex-grow-1" style="max-width: 320px;">
                    <label for="search" class="form-label small text-muted mb-1">Buscar</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="form-control form-control-sm rounded-pill"
                        placeholder="Buscar usuario"
                    >
                </div>

                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                        Buscar
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            Limpiar
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Tabla de usuarios --}}
    <div class="card shadow-sm border-0 user-table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0 user-table">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted">ID</th>
                            <th class="small text-uppercase text-muted">Nombre</th>
                            <th class="small text-uppercase text-muted">Email</th>
                            <th class="small text-uppercase text-muted">Roles</th>
                            <th class="small text-uppercase text-muted">Permisos</th>
                            <th class="small text-uppercase text-muted text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="small text-muted" data-label="ID">{{ $user->id }}</td>
                                <td data-label="Nombre">{{ $user->name }}</td>
                                <td class="text-muted small" data-label="Email">{{ $user->email }}</td>
                                <td data-label="Roles">
                                    @php $roleCount = $user->roles->count(); @endphp
                                    @if($roleCount > 0)
                                        <span class="user-pill">
                                            {{ $roleCount }} {{ $roleCount === 1 ? 'rol' : 'roles' }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Sin roles</span>
                                    @endif
                                </td>
                                <td data-label="Permisos">
                                    @php $permCount = $user->permissions->count(); @endphp
                                    @if($permCount > 0)
                                        <span class="user-pill user-pill-soft">
                                            {{ $permCount }} {{ $permCount === 1 ? 'permiso' : 'permisos' }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Sin permisos</span>
                                    @endif
                                </td>
                                <td class="text-end" data-label="Acciones">
                                    <div class="d-flex justify-content-end gap-2 align-items-center user-actions">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                                            Editar
                                        </a>
                                        <form
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Seguro que querés eliminar este usuario?');"
                                            class="m-0"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-footer mt-2 d-flex flex-column align-items-center gap-1">
                @if($users->hasPages())
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>

<style>
    .user-table-card {
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.04);
        background-color: #ffffff;
    }

    .user-table thead th {
        font-size: 0.7rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #9ca3af;
        border-bottom: 1px solid rgba(148, 163, 184, 0.25);
    }

    .user-table tbody td {
        border-top: 1px solid rgba(0, 0, 0, 0.045);
        vertical-align: middle;
        padding-top: 0.45rem;
        padding-bottom: 0.45rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        font-size: 0.82rem;
    }

    .user-table tbody tr {
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .user-table tbody tr:hover {
        background-color: #f9f9fa !important;
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.06);
    }

    .user-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid rgba(0,0,0,0.035);
    }

    .user-table tbody tr {
        border-radius: 6px;
    }

    .user-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.15rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #111827;
        color: #f9fafb;
    }

    .user-pill.user-pill-soft {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .user-actions .btn {
        border-radius: 999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        line-height: 1.1;
    }

    .user-filter-card {
        border-radius: 16px;
        border: 1px solid rgba(15, 23, 42, 0.05);
        background-color: #ffffff;
    }

    .user-filter-card .form-control {
        background-color: #fafafa;
        border: 1px solid rgba(0,0,0,0.08);
        padding: 0.45rem 0.9rem;
    }

    .user-filter-card .form-control:focus {
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
    }

    .user-filter-card .btn {
        border-radius: 999px;
    }
  .pagination-footer {
        padding: 0.5rem 0.75rem 0.75rem;
    }

    @media (max-width: 768px) {
        .user-table thead {
            display: none;
        }

        .user-table tbody {
            display: block;
        }

        .user-table tbody tr {
            display: block;
            margin-bottom: 0.75rem;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background-color: #ffffff;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
            transform: none !important;
        }

        .user-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 0;
            border-bottom: 0;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .user-table tbody td::before {
            content: attr(data-label);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9ca3af;
            margin-right: 0.75rem;
        }

        .user-table tbody tr:hover {
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
            background-color: #ffffff !important;
        }

        .user-table tbody td:last-child {
            padding-top: 0.4rem;
        }

        .user-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.4rem;
        }

        .user-actions .btn {
            width: 100%;
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
        }
    }    
</style>
@endsection