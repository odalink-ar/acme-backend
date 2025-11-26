@extends('layouts.admin')

@section('title', 'Detalle de permiso | Admin')

@section('page_header', 'Detalle de permiso')
@section('page_subtitle', 'Información del permiso')

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
                        <dd class="col-sm-8">{{ $permission->id }}</dd>

                        <dt class="col-sm-4 text-muted">Nombre</dt>
                        <dd class="col-sm-8">{{ $permission->name }}</dd>

                        <dt class="col-sm-4 text-muted">Guard</dt>
                        <dd class="col-sm-8">{{ $permission->guard_name }}</dd>

                        <dt class="col-sm-4 text-muted">Creado</dt>
                        <dd class="col-sm-8">{{ $permission->created_at?->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4 text-muted">Actualizado</dt>
                        <dd class="col-sm-8">{{ $permission->updated_at?->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
            Volver al listado
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-dark">
                Editar
            </a>

            <form
                action="{{ route('admin.permissions.destroy', $permission) }}"
                method="POST"
                onsubmit="return confirm('¿Seguro que querés eliminar este permiso?');"
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