<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;


class UsersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $users = User::with(['roles', 'permissions'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $roleIds = $data['roles'] ?? [];
        $selectedPermissionIds = array_map('intval', $data['permissions'] ?? []);

        // Permisos concedidos por los roles seleccionados
        $rolePermissionIds = Role::query()
            ->whereIn('id', $roleIds)
            ->with('permissions:id')
            ->get()
            ->flatMap->permissions
            ->pluck('id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();

        // Permisos directos = seleccionados - los que ya vienen por roles
        $directPermissionIds = array_values(array_diff(
            $selectedPermissionIds,
            $rolePermissionIds
        ));

        $roles = Role::whereIn('id', $roleIds)->get();
        $directPermissions = Permission::whereIn('id', $directPermissionIds)->get();

        $user->syncRoles($roles);
        $user->syncPermissions($directPermissions);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        // Cargamos roles + permisos heredados por cada rol
        // y permisos directos del usuario
        $user->load([
            'roles.permissions',
            'permissions',
        ]);

        // Permisos asignados directamente al usuario (keyBy para lookup rápido)
        $directPermissions = $user->permissions->keyBy('id');

        // Mapa temporal:
        //  [ permission_id => [ 'permission' => Permission, 'roles' => [Role,...] ] ]
        $viaRoles = [];

        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                // Si aún no está registrado, creamos el slot
                if (!isset($viaRoles[$permission->id])) {
                    $viaRoles[$permission->id] = [
                        'permission' => $permission,
                        'roles' => [],
                    ];
                }

                // Agregamos el rol que otorga ese permiso
                $viaRoles[$permission->id]['roles'][] = $role;
            }
        }

        // Lista final de permisos efectivos que mandamos a la vista
        $effectivePermissions = [];

        // 1) Permisos via roles (y directos si coincide)
        foreach ($viaRoles as $permId => $info) {
            $isDirect = $directPermissions->has($permId);

            $effectivePermissions[] = [
                'permission' => $info['permission'],
                'via_roles' => $info['roles'],   // roles que otorgan el permiso
                'is_direct' => $isDirect,        // extra directo sí/no
            ];

            // Si ya lo incluimos como via rol + directo, lo sacamos de directPermissions
            if ($isDirect) {
                $directPermissions->forget($permId);
            }
        }

        // 2) Permisos que son solo directos (ningún rol los da)
        foreach ($directPermissions as $permission) {
            $effectivePermissions[] = [
                'permission' => $permission,
                'via_roles' => [],   // sin roles
                'is_direct' => true,
            ];
        }

        // 3) Los ordenamos por nombre de permiso (UI limpia)
        usort($effectivePermissions, function ($a, $b) {
            return strcmp($a['permission']->name, $b['permission']->name);
        });

        return view('admin.users.show', [
            'user' => $user,
            'effectivePermissions' => $effectivePermissions,
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        $userRoleIds = $user->roles->pluck('id')->toArray();
        $userPermissionIds = $user->permissions->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'userRoleIds', 'userPermissionIds'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        $roleIds = $data['roles'] ?? [];
        $selectedPermissionIds = array_map('intval', $data['permissions'] ?? []);

        // Permisos concedidos por los roles seleccionados
        $rolePermissionIds = Role::query()
            ->whereIn('id', $roleIds)
            ->with('permissions:id')
            ->get()
            ->flatMap->permissions
            ->pluck('id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();

        // Permisos directos = seleccionados - los que ya vienen por roles
        $directPermissionIds = array_values(array_diff(
            $selectedPermissionIds,
            $rolePermissionIds
        ));

        $roles = Role::whereIn('id', $roleIds)->get();
        $directPermissions = Permission::whereIn('id', $directPermissionIds)->get();

        $user->syncRoles($roles);
        $user->syncPermissions($directPermissions);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
