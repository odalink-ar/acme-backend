@extends('layouts.admin')

@section('title', 'Dashboard | Admin')

@section('page_header', 'Dashboard')
@section('page_subtitle', 'Resumen general del panel de administración')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-muted text-uppercase mb-2">
                        Usuarios
                    </h2>
                    <p class="display-6 mb-0">
                        {{-- Ejemplo, reemplazá por una variable --}}
                        {{ $usersCount ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-muted text-uppercase mb-2">
                        Roles
                    </h2>
                    <p class="display-6 mb-0">
                        {{ $rolesCount ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-muted text-uppercase mb-2">
                        Permisos
                    </h2>
                    <p class="display-6 mb-0">
                        {{ $permissionsCount ?? '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection