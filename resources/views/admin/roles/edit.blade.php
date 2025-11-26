@extends('layouts.admin')

@section('title', 'Editar rol | Admin')

@section('page_header', 'Editar rol')
@section('page_subtitle', 'Actualizá el nombre, guard y permisos')

@section('content')
<div class="card shadow-sm border-0 role-edit-card">
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="d-flex flex-column gap-4">
            @csrf
            @method('PUT')

            <div class="row g-4">
                {{-- Datos básicos del rol --}}
                <div class="col-md-5">
                    <h5 class="card-title mb-3">Datos del rol</h5>

                    <div class="mb-3">
                        <label class="form-label small mb-1" for="name">Nombre</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $role->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label class="form-label small mb-1" for="guard_name">
                            Guard name <span class="text-muted small">(por defecto "web")</span>
                        </label>
                        <input
                            type="text"
                            id="guard_name"
                            name="guard_name"
                            value="{{ old('guard_name', $role->guard_name) }}"
                            class="form-control @error('guard_name') is-invalid @enderror"
                        >
                        @error('guard_name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Permisos del rol --}}
                <div class="col-md-7" x-data="{ query: '' }">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5 class="card-title mb-1">Permisos</h5>
                            <p class="text-muted small mb-0">
                                Seleccioná uno o varios permisos para este rol.
                            </p>
                        </div>
                        <span class="badge bg-light text-muted border small">
                            {{ $permissions->count() }} total
                        </span>
                    </div>

                    <input
                        type="text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Buscar permisos..."
                        x-model="query"
                    >

                    <div class="permissions-panel border rounded small overflow-auto mt-1">
                        <div class="row row-cols-1 row-cols-md-2 g-1 p-2">
                            @foreach($permissions as $permission)
                                <div
                                    class="col"
                                    x-show="!query || '{{ strtolower($permission->name) }}'.includes(query.toLowerCase())"
                                >
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="perm_{{ $permission->id }}"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            @if(collect(old('permissions', $rolePermissionIds))->contains($permission->id)) checked @endif
                                        >
                                        <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @error('permissions')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-2 border-top">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    Volver
                </a>
                <button type="submit" class="btn btn-dark">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .role-edit-card {
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.04);
        background-color: #ffffff;
    }

    .role-edit-card .card-title {
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    .role-edit-card .form-label {
        color: #4b5563;
    }

    .permissions-panel {
        max-height: 260px;
        min-height: 260px;
        width: 100%;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
        background-color: #ffffff;
        border-color: rgba(148, 163, 184, 0.35);
    }

    .permissions-panel::-webkit-scrollbar {
        width: 6px;
    }

    .permissions-panel::-webkit-scrollbar-track {
        background: transparent;
    }

    .permissions-panel::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.18);
        border-radius: 999px;
    }
</style>
@endsection