@extends('layouts.admin')

@section('title', 'Detalle de rol | Admin')

@section('page_header', 'Detalle de rol')
@section('page_subtitle', 'Información del rol y sus permisos')

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">
                        Información básica
                    </h2>

                    <dl class="row mb-0 small">
                        <dt class="col-sm-4 text-muted">ID</dt>
                        <dd class="col-sm-8">{{ $role->id }}</dd>

                        <dt class="col-sm-4 text-muted">Nombre</dt>
                        <dd class="col-sm-8">{{ $role->name }}</dd>

                        <dt class="col-sm-4 text-muted">Guard</dt>
                        <dd class="col-sm-8">{{ $role->guard_name }}</dd>

                        <dt class="col-sm-4 text-muted">Creado</dt>
                        <dd class="col-sm-8">{{ $role->created_at?->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4 text-muted">Actualizado</dt>
                        <dd class="col-sm-8">{{ $role->updated_at?->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">
                        Permisos
                    </h2>
                    @forelse($role->permissions as $permission)
                        <span class="badge bg-light text-muted border me-1 mb-1">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <p class="text-muted small mb-0">Este rol no tiene permisos asignados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            Volver al listado
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-dark">
                Editar
            </a>

            <form
                action="{{ route('admin.roles.destroy', $role) }}"
                method="POST"
                onsubmit="return confirm('¿Seguro que querés eliminar este rol?');"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
@endsection