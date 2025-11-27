@csrf

@php
    $selectedRoles = is_array(old('roles'))
        ? array_map('intval', old('roles'))
        : ($userRoleIds ?? []);

    $selectedPermissions = is_array(old('permissions'))
        ? array_map('intval', old('permissions'))
        : ($userPermissionIds ?? []);

    $permissionOptions = $permissions->map(function ($p) {
        return [
            'id'    => (int) $p->id,
            'label' => $p->name, // acá podés hacer "Grupo - Nombre" si querés
        ];
    })->values()->all();

    $rolePermissionsMap = $roles->mapWithKeys(function ($role) {
        return [
            (string) $role->id => $role->permissions
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all(),
        ];
    })->all();

    $roleMeta = $roles->map(function ($role) {
        return [
            'id' => (int) $role->id,
            'name' => $role->name,
        ];
    })->values()->all();
@endphp

<div
    x-data="userAccessForm(
        @js($permissionOptions),
        @js($selectedPermissions),
        @js($rolePermissionsMap),
        @js($selectedRoles),
        @js($roleMeta)
    )"
    class="user-access-form d-flex flex-column gap-4"
>
    {{-- DATOS BÁSICOS --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title mb-3">Datos básicos del usuario</h5>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small mb-1">Nombre</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name ?? '') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        autocomplete="name"
                        required
                    >
                    @error('name')
                    <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email ?? '') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                    <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-2 mt-1">
                <div class="col-md-6">
                    <label class="form-label small mb-1">
                        Contraseña
                        @if(isset($user))
                            <span class="text-muted small">(dejá vacía para no cambiar)</span>
                        @endif
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        @if(!isset($user)) required @endif
                        autocomplete="new-password"
                    >
                    @error('password')
                    <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small mb-1">Confirmar contraseña</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        @if(!isset($user)) required @endif
                        autocomplete="new-password"
                    >
                </div>
            </div>
        </div>
    </div>

    {{-- ROLES --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                <div>
                    <h5 class="card-title mb-1">Roles</h5>
                    <p class="text-muted small mb-0">
                        Usá los roles como agrupadores de permisos. Podés ajustar detalles desde la sección de permisos.
                    </p>
                </div>
                <div class="d-flex flex-column align-items-end">
                    <span class="badge bg-light text-muted border small mb-1">
                        <span x-text="activeRoleIds.length"></span>
                        <span>/ {{ $roles->count() }} roles</span>
                    </span>
                </div>
            </div>

            <div class="row g-2 mt-1">
                @forelse($roles as $role)
                    <div class="col-6 col-md-6 col-lg-2">
                        <label
                            class="role-option w-100"
                            :class="activeRoleIds.includes('{{ (string) $role->id }}') ? 'is-active' : ''"
                            for="role_{{ $role->id }}"
                        >
                            <input
                                class="form-check-input d-none"
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                id="role_{{ $role->id }}"
                                @checked(in_array($role->id, $selectedRoles))
                                @change="onRoleToggle({{ $role->id }}, $event.target.checked)"
                            >
                            <div class="role-option-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="role-option-name">{{ $role->name }}</span>
                                    <span class="badge bg-light text-muted border small">
                                        {{ $role->permissions->count() }} perm.
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted small mb-0">
                            No hay roles disponibles. Creá algunos primero.
                        </p>
                    </div>
                @endforelse
            </div>

            @error('roles')
            <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- PERMISOS: DOBLE LISTA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="card-title mb-1">Permisos del usuario</h5>
                    <p class="text-muted small mb-0">
                        Los roles definen los permisos base. Acá podés agregar o quitar <strong>permisos adicionales</strong> específicos.
                    </p>
                </div>
            </div>

            <div class="row g-2 mt-1 align-items-stretch">
                {{-- Disponibles --}}
                <div class="col-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small mb-0">Disponibles</label>
                        <span class="badge bg-light text-muted border small" x-text="filteredAvailable.length + ' / ' + totalDirectCandidates"></span>
                    </div>

                    <input
                        type="text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Buscar permisos..."
                        x-model="searchAvailable"
                    >

                    <div class="border rounded small overflow-auto permissions-panel">
                        <div class="list-group list-group-flush">
                            <template x-for="perm in filteredAvailable" :key="perm.id">
                                <button
                                    type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    @click="addPermission(perm.id)"
                                >
                                    <div class="d-flex flex-column text-start">
                                        <span class="small fw-medium" x-text="perm.label"></span>
                                        <span class="text-muted small" x-text="permissionViaLabel(perm.id)"></span>
                                    </div>
                                    <span class="text-success small fw-semibold">Agregar →</span>
                                </button>
                            </template>

                            <template x-if="filteredAvailable.length === 0">
                                <div class="p-2 text-muted small">
                                    No hay permisos disponibles para agregar.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Botones de mover todo --}}
                <div class="col-md-2 d-flex flex-column justify-content-center align-items-center gap-2">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary px-3"
                        @click="addAll()"
                        :disabled="available.length === 0"
                    >
                        &gt;
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary px-3"
                        @click="clearSelected()"
                        :disabled="directIds.length === 0"
                    >
                        &lt;
                    </button>
                </div>

                {{-- Asignados --}}
                <div class="col-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small mb-0">Asignados</label>
                        <span
                            class="badge bg-light text-muted border small"
                            x-text="(filteredSelectedRoleBased.length + filteredSelectedExtras.length) + ' / ' + (selectedRoleBased.length + directIds.length)"
                        ></span>
                    </div>

                    <input
                        type="text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Buscar permisos asignados..."
                        x-model="searchSelected"
                    >

                    <div class="border rounded small overflow-auto permissions-panel">
                        <div class="list-group list-group-flush">
                            {{-- Permisos via roles (solo lectura) --}}
                            <template x-for="perm in filteredSelectedRoleBased" :key="'role-perm-' + perm.id">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column text-start">
                                        <span class="small fw-medium" x-text="perm.label"></span>
                                        <span class="text-warning small" x-text="'Permiso en ' + permissionViaLabel(perm.id)"></span>
                                    </div>
                                    <span class="badge bg-light text-muted border small">Rol</span>
                                </div>
                            </template>

                            {{-- Separador visual cuando hay ambos tipos --}}
                            <template x-if="filteredSelectedRoleBased.length > 0 && filteredSelectedExtras.length > 0">
                                <div class="list-group-item py-1">
                                    <span class="text-success small">Permisos adicionales</span>
                                </div>
                            </template>

                            {{-- Permisos adicionales (directos, editables) --}}
                            <template x-for="perm in filteredSelectedExtras" :key="'extra-perm-' + perm.id">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column text-start">
                                        <span class="small fw-medium" x-text="perm.label"></span>
                                        <span class="text-muted small" x-text="permissionViaLabel(perm.id)"></span>
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm p-0 small text-danger text-decoration-none"
                                        @click="removePermission(perm.id)"
                                    >
                                        Quitar
                                    </button>
                                </div>
                            </template>

                            {{-- Estado vacío cuando no hay permisos via roles ni adicionales --}}
                            <template x-if="filteredSelectedRoleBased.length === 0 && filteredSelectedExtras.length === 0">
                                <div class="p-2 text-muted small">
                                    No hay permisos asignados.
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Inputs ocultos para enviar permisos seleccionados --}}
                    <template x-for="id in directIds" :key="'perm-input-' + id">
                        <input type="hidden" name="permissions[]" :value="id">
                    </template>
                </div>
            </div>

            @error('permissions')
            <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<script>
    function userAccessForm(allPermissions, initiallySelectedIds, rolePermissionsMap, initiallySelectedRoleIds, rolesMeta) {
        return {
            allPermissions: allPermissions || [],
            // IDs de permisos adicionales (directos), no los que vienen por roles
            directIds: (initiallySelectedIds || []).map(Number),
            rolePermissionsMap: rolePermissionsMap || {},
            activeRoleIds: (initiallySelectedRoleIds || []).map(id => String(id)),
            rolesMeta: rolesMeta || [],
            searchAvailable: '',
            searchSelected: '',

            // Permisos que vienen por los roles actualmente seleccionados
            get rolePermissionIds() {
                const ids = new Set();
                this.activeRoleIds.forEach((rid) => {
                    const perms = this.rolePermissionsMap[rid] || [];
                    perms.forEach((id) => ids.add(Number(id)));
                });
                return ids;
            },

            // Total de permisos que podrían ser adicionales (no vienen por roles)
            get totalDirectCandidates() {
                const roleSet = this.rolePermissionIds;
                return this.allPermissions.filter((p) => !roleSet.has(p.id)).length;
            },

            // Permisos disponibles para agregar como adicionales
            get available() {
                const roleSet = this.rolePermissionIds;
                const directSet = new Set(this.directIds);
                return this.allPermissions.filter((p) => !roleSet.has(p.id) && !directSet.has(p.id));
            },

            get filteredAvailable() {
                const term = this.searchAvailable.toLowerCase();
                if (!term) return this.available;
                return this.available.filter((p) => p.label.toLowerCase().includes(term));
            },

            // Permisos adicionales actualmente seleccionados
            get selectedExtras() {
                return this.allPermissions.filter((p) => this.directIds.includes(p.id));
            },

            get filteredSelectedExtras() {
                const term = this.searchSelected.toLowerCase();
                if (!term) return this.selectedExtras;
                return this.selectedExtras.filter((p) => p.label.toLowerCase().includes(term));
            },

            // Permisos que el usuario tiene via roles seleccionados (solo lectura en UI)
            get selectedRoleBased() {
                const roleSet = this.rolePermissionIds;
                return this.allPermissions.filter((p) => roleSet.has(p.id));
            },

            get filteredSelectedRoleBased() {
                const term = this.searchSelected.toLowerCase();
                if (!term) return this.selectedRoleBased;
                return this.selectedRoleBased.filter((p) => p.label.toLowerCase().includes(term));
            },

            addPermission(id) {
                id = Number(id);
                if (!this.directIds.includes(id)) {
                    this.directIds.push(id);
                }
            },
            addAll() {
                const source = this.searchAvailable && this.searchAvailable.trim().length
                    ? this.filteredAvailable
                    : this.available;

                source.forEach((perm) => {
                    if (!this.directIds.includes(perm.id)) {
                        this.directIds.push(perm.id);
                    }
                });
            },

            removePermission(id) {
                id = Number(id);
                this.directIds = this.directIds.filter((x) => x !== id);
            },

            clearSelected() {
                const hasFilter = this.searchSelected && this.searchSelected.trim().length;

                // Si no hay filtro, limpiamos todos los permisos adicionales
                if (!hasFilter) {
                    this.directIds = [];
                    return;
                }

                // Si hay filtro, quitamos solo los adicionales visibles (filtrados)
                const idsToRemove = this.filteredSelectedExtras.map((p) => p.id);

                this.directIds = this.directIds.filter((id) => !idsToRemove.includes(id));
            },
            onRoleToggle(roleId, isChecked) {
                const key = String(roleId);
                if (isChecked) {
                    if (!this.activeRoleIds.includes(key)) {
                        this.activeRoleIds.push(key);
                    }
                } else {
                    this.activeRoleIds = this.activeRoleIds.filter((rid) => rid !== key);
                }
            },

            permissionViaLabel(id) {
                id = Number(id);
                const via = [];

                this.rolesMeta.forEach((role) => {
                    const rId = String(role.id);
                    const perms = this.rolePermissionsMap[rId] || [];
                    if (perms.map(Number).includes(id)) {
                        via.push(role.name);
                    }
                });

                if (via.length > 0) {
                    return ' (' + via.join(', ') + ')';
                }

                return '(No tiene roles)';
            }
        }
    }
</script>
<style>
    .permissions-panel {
        max-height: 260px;
        min-height: 260px;
        width: 100%;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
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

    .user-access-form .card {
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background-color: #ffffff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    }

    .user-access-form .card-body {
        padding: 0.9rem 1rem;
    }

    .user-access-form .card-title {
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .user-access-form .role-option {
        display: block;
        padding: 0.55rem 0.75rem;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background-color: #f9fafb;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .user-access-form .role-option:hover {
        background-color: #f3f4f6;
        border-color: rgba(148, 163, 184, 0.7);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
    }

    .user-access-form .role-option.is-active {
        border-color: #111827;
        background-color: #111827;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.18);
    }

    .user-access-form .role-option.is-active .role-option-name,
    .user-access-form .role-option.is-active .role-option-meta {
        color: #f9fafb;
    }

    .user-access-form .role-option.is-active .badge {
        background-color: rgba(249, 250, 251, 0.1);
        color: #e5e7eb;
        border-color: rgba(249, 250, 251, 0.2);
    }

    .user-access-form .role-option-name {
        font-size: 0.85rem;
        font-weight: 500;
        color: #111827;
    }

    .user-access-form .role-option-meta {
        font-size: 0.78rem;
    }
    .user-access-form .form-control {
        border-radius: 999px;
        border-color: rgba(148, 163, 184, 0.6);
        font-size: 0.84rem;
    }

    .user-access-form .form-control:focus {
        border-color: #111827;
        box-shadow: 0 0 0 1px rgba(17, 24, 39, 0.12);
    }
    .user-access-form .form-check {
        padding: 0.15rem 0.4rem;
        border-radius: 999px;
        border: 1px solid transparent;
        background-color: transparent;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .user-access-form .form-check-input {
        width: 0.8rem;
        height: 0.8rem;
        margin-top: 0.15rem;
        margin-right: 0.35rem;
        box-shadow: none;
    }

    .user-access-form .form-check-input:checked {
        background-color: #111827;
        border-color: #111827;
    }

    .user-access-form .form-check:hover {
        background-color: #f3f4f6;
        border-color: rgba(148, 163, 184, 0.4);
    }

    .user-access-form .form-check-label.small {
        font-size: 0.8rem;
        color: #374151;
    }
    .user-access-form .btn.btn-outline-secondary {
        border-radius: 999px;
        border-color: rgba(148, 163, 184, 0.6);
        font-size: 0.78rem;
    }

    .user-access-form .btn.btn-outline-secondary:hover:not(:disabled) {
        background-color: #111827;
        color: #ffffff;
        border-color: #111827;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.18);
    }

    .user-access-form .btn.btn-outline-secondary:disabled {
        opacity: 0.45;
    }

    .user-access-form .form-label {
        color: #4b5563;
    }

    .user-access-form .list-group-item {
        border: 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        padding: 0.28rem 0.55rem;
        background-color: transparent;
    }

    .user-access-form .list-group-item:last-child {
        border-bottom: 0;
    }

    .user-access-form .list-group-item-action:hover {
        background-color: #f5f5f7;
        color: inherit;
    }

    .user-access-form .badge.bg-light.text-muted.border.small {
        border-radius: 999px;
        padding: 0.15rem 0.6rem;
        font-weight: 500;
    }

    .user-access-form .permissions-panel {
        border-color: rgba(148, 163, 184, 0.3);
        background-color: #f9fafb;
    }

    .user-access-form .form-control-sm {
        padding: 0.3rem 0.55rem;
    }

    .user-access-form .badge {
        padding: 0.15rem 0.45rem;
        font-size: 0.7rem;
    }    
   .btn {
        border-radius: 999px;
        font-size: 0.85rem;
        padding: 0.35rem 1.1rem;
    } 
</style>
