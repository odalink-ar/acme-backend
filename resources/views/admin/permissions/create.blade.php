@extends('layouts.admin')

@section('title', 'Crear permiso | Admin')

@section('page_header', 'Crear permiso')
@section('page_subtitle', 'Definí el nombre y el guard del permiso')

@section('content')
    <div class="card shadow-sm border-0">
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
@endsection