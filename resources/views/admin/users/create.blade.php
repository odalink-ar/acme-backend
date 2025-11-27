@extends('layouts.admin')

@section('title', 'Crear usuario | Admin')

@section('page_header', 'Crear usuario')
@section('page_subtitle', 'Definí los datos, roles y permisos del usuario')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="d-flex flex-column gap-4">
                @include('admin.users._form', [
                    'user' => null,
                    'roles' => $roles,
                    'permissions' => $permissions,
                ])
                <div class="d-flex justify-content-between mt-4 pt-2 border-top">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-dark">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection