@extends('layouts.admin')

@section('title', 'Detalle de usuario | Admin')

@section('page_header', 'Detalle de usuario')
@section('page_subtitle', 'Información del usuario, roles y permisos')

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-3">
                    <h2 class="h6 text-uppercase text-muted mb-2" style="letter-spacing: .5px;">
                        Información básica
                    </h2>

                    <dl class="row mb-0" style="font-size: .83rem; line-height: 1.35;">
                        <dt class="col-sm-4 text-muted">ID</dt>
                        <dd class="col-sm-8">{{ $user->id }}</dd>

                        <dt class="col-sm-4 text-muted">Nombre</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>

                        <dt class="col-sm-4 text-muted">Email</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4 text-muted">Creado</dt>
                        <dd class="col-sm-8">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4 text-muted">Actualizado</dt>
                        <dd class="col-sm-8">{{ $user->updated_at?->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-2">
                <div class="card-body py-3 px-3">
                    <h2 class="h6 text-uppercase text-muted mb-2" style="letter-spacing: .5px;">
                        Roles
                    </h2>
                    @forelse($user->roles as $role)
                        <span class="badge bg-light text-dark border small fw-medium rounded-3 me-1 mb-1 px-2 py-1">
                            {{ $role->name }}
                        </span>
                    @empty
                        <p class="text-muted small mb-0">Sin roles asignados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-2">
                <div class="card-body py-3 px-3">
                    <h2 class="h6 text-uppercase text-muted mb-1" style="letter-spacing: .5px;">
                        Permisos adicionales directos
                    </h2>
                    <p class="text-muted small mb-2">
                        Permisos asignados específicamente a este usuario, además de los que obtiene por sus roles.
                    </p>
                    @forelse($user->permissions as $permission)
                        <span class="badge bg-light text-dark border small fw-medium rounded-3 me-1 mb-1 px-2 py-1">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <p class="text-muted small mb-0">Sin permisos adicionales directos.</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 text-uppercase text-muted mb-0" style="letter-spacing: .5px;">
                            Permisos efectivos
                        </h2>
                        @if(!empty($effectivePermissions))
                            <span class="badge bg-light text-dark border small fw-medium rounded-3 px-2 py-1">
                                {{ count($effectivePermissions) }} permisos
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-2">
                        Resultado final de los permisos del usuario, combinando roles y permisos adicionales directos.
                    </p>

                    @if(!empty($effectivePermissions))
                        <div class="border rounded small overflow-hidden">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th scope="col">Permiso</th>
                                        <th scope="col" class="text-nowrap">Vía roles</th>
                                        <th scope="col" class="text-center text-nowrap">Directo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($effectivePermissions as $item)
                                        <tr>
                                            <td class="small">
                                                {{ $item['permission']->name }}
                                            </td>
                                            <td class="small">
                                                @if(count($item['via_roles']))
                                                    @foreach($item['via_roles'] as $role)
                                                        <span class="badge bg-light text-dark border small fw-medium rounded-3 me-1 mb-1 px-2 py-1">
                                                            {{ $role->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item['is_direct'])
                                                    <span class="badge bg-dark text-light border-0 small rounded-pill px-2">Sí</span>
                                                @else
                                                    <span class="text-muted small">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-0">Este usuario no tiene permisos asignados todavía.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-2 d-flex justify-content-between align-items-center" style="gap: .5rem;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            Volver al listado
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-dark">
                Editar
            </a>

            <form
                action="{{ route('admin.users.destroy', $user) }}"
                method="POST"
                onsubmit="return confirm('¿Seguro que querés eliminar este usuario?');"
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