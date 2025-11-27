@extends('layouts.admin')

@section('title', 'Crear permiso | Admin')

@section('page_header', 'Crear permiso')
@section('page_subtitle', 'Definí el nombre y el guard del permiso')

@section('content')
    <div class="card shadow-sm border-0 permission-create-card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label small mb-1" for="name">Nombre</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small mb-1" for="guard_name">
                        Guard name <span class="text-muted small">(opcional, por defecto "web")</span>
                    </label>
                    <input
                        type="text"
                        id="guard_name"
                        name="guard_name"
                        value="{{ old('guard_name') }}"
                        class="form-control @error('guard_name') is-invalid @enderror"
                    >
                    @error('guard_name')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex justify-content-between mt-3">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-dark">
                        Guardar permiso
                    </button>
                </div>
            </form>
        </div>
    </div>
<style>
    .permission-create-card {
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background-color: #ffffff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    }

    .permission-create-card .card-title {
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .permission-create-card .form-label {
        font-size: 0.8rem;
        color: #4b5563;
        font-weight: 500;
        margin-bottom: 0.15rem;
    }

    .permission-create-card .form-control {
        border-radius: 999px;
        border-color: rgba(148, 163, 184, 0.6);
        font-size: 0.84rem;
        padding: 0.45rem 1rem;
    }

    .permission-create-card .form-control:focus {
        border-color: #111827;
        box-shadow: 0 0 0 1px rgba(17, 24, 39, 0.12);
    }

    .permission-create-card .btn {
        border-radius: 999px;
        font-size: 0.85rem;
        padding: 0.35rem 1.1rem;
    }
</style>
@endsection