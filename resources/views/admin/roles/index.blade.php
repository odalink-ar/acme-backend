@extends('layouts.admin')

@section('title', 'Roles | Admin')

@section('page_header', 'Roles')
@section('page_subtitle', 'Gestioná los roles y sus permisos')

@section('page_actions')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-outline-dark">
        Crear rol
    </a>
@endsection

@section('content')
    {{-- Filtro --}}
    <div class="card shadow-sm border-0 mb-3 user-filter-card">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.roles.index') }}" class="d-flex flex-wrap gap-3 align-items-end">

                <div class="flex-grow-1" style="max-width: 320px;">
                    <label for="search" class="form-label small text-muted mb-1">Buscar</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="form-control form-control-sm rounded-pill"
                        placeholder="Buscar rol..."
                    >
                </div>

                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                        Buscar
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            Limpiar
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- Mensaje --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="card shadow-sm border-0 roles-table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0 roles-table">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted">ID</th>
                            <th class="small text-uppercase text-muted">Nombre</th>
                            <th class="small text-uppercase text-muted">Guard</th>
                            <th class="small text-uppercase text-muted">Permisos</th>
                            <th class="small text-uppercase text-muted text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="small text-muted">{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td class="text-muted small">{{ $role->guard_name }}</td>
                                <td>
                                    @php $permCount = $role->permissions->count(); @endphp
                                    @if($permCount > 0)
                                        <span class="role-pill">
                                            {{ $permCount }} {{ $permCount === 1 ? 'permiso' : 'permisos' }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Sin permisos</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2 align-items-center role-actions">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar este rol?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No se encontraron roles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
<style>
    .roles-table-card {
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.04);
        background-color: #ffffff;
    }

    .roles-table thead th {
        font-size: 0.7rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #9ca3af;
        border-bottom: 1px solid rgba(148, 163, 184, 0.25);
    }

    .roles-table tbody td {
        border-top: 1px solid rgba(0, 0, 0, 0.045);
        vertical-align: middle;
        padding-top: 0.45rem;
        padding-bottom: 0.45rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        font-size: 0.82rem;
    }

    .roles-table tbody tr {
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .roles-table tbody tr:hover {
        background-color: #f9f9fa !important;
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.06);
    }

    .roles-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid rgba(0,0,0,0.035);
    }

    .roles-table tbody tr {
        border-radius: 6px;
    }

    .roles-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .role-pill {
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

    .role-actions .btn {
        border-radius: 999px;
        padding-inline: 0.9rem;
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
</style>
@endsection