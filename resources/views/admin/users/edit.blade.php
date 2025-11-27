@extends('layouts.admin')

@section('title', 'Editar usuario | Admin')

@section('page_header', 'Editar usuario')
@section('page_subtitle', 'Actualizá los datos, roles y permisos del usuario')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="d-flex flex-column gap-4">
                @csrf
                @method('PUT')

                @include('admin.users._form', [
                    'user' => $user,
                    'roles' => $roles,
                    'permissions' => $permissions,
                    'userRoleIds' => $userRoleIds ?? [],
                    'userPermissionIds' => $userPermissionIds ?? [],
                ])

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        Volver
                    </a>
                    <button type="submit" class="btn btn-dark">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection