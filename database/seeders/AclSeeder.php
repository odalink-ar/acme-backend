<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AclSeeder extends Seeder
{
    /**
     * Definición centralizada de permisos.
     * Acá podés meter tranquilamente 50, 100, 200 permisos.
     */
    protected array $permissions = [
        // Módulo inicio
        'inicio.show',

        // Ejemplo módulo usuarios
        'users.view',
        'users.create',
        'users.update',
        'users.delete',

        // Ejemplo módulo roles
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',

        // Ejemplo módulo permisos
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',

        // Agregás acá todos los demás...
    ];

    /**
     * Definición centralizada de roles y sus permisos.
     * El valor '*' significa: todos los permisos.
     */
    protected array $roles = [
        'sudo' => ['*'], // super rol, tiene todo

        'admin' => [
            'inicio.show',
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',
        ],

        'manager' => [
            'inicio.show',
            'users.view', 'users.update',
            'roles.view',
            'permissions.view',
        ],

        // acá podrías seguir agregando más roles...
    ];

    /**
     * Usuarios iniciales (opcionales) y su rol principal.
     */
    protected array $users = [
        [
            'name' => 'acme',
            'email' => 'admin@acme.com',
            'password' => 'acmeinc',
            'role' => 'sudo',
        ],
        // Podés agregar más usuarios iniciales acá si querés...
    ];

    public function run(): void
    {
        // Muy importante con Spatie: limpiar cache de permisos
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | CREAR PERMISOS
        |--------------------------------------------------------------------------
        */
        $permissionModels = collect();

        foreach ($this->permissions as $permName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web']
            );

            $permissionModels[$permName] = $permission;
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR ROLES Y ASIGNAR PERMISOS
        |--------------------------------------------------------------------------
        */
        $roleModels = collect();

        foreach ($this->roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );

            // Si el rol tiene '*', le damos todos los permisos definidos
            if (in_array('*', $rolePermissions, true)) {
                $role->syncPermissions($permissionModels->values());
            } else {
                $permsToAssign = $permissionModels->only($rolePermissions)->values();
                $role->syncPermissions($permsToAssign);
            }

            $roleModels[$roleName] = $role;
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR USUARIOS INICIALES Y ASIGNAR ROLES
        |--------------------------------------------------------------------------
        */
        foreach ($this->users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            if (! empty($userData['role']) && isset($roleModels[$userData['role']])) {
                if (! $user->hasRole($userData['role'])) {
                    $user->assignRole($roleModels[$userData['role']]);
                }
            }

            // IMPORTANTE:
            // No hacemos syncPermissions al usuario, porque validás por permisos
            // y el usuario los recibe via roles. Dejá los permisos directos solo
            // para excepciones específicas.
        }

        $this->command->info('ACL (roles, permisos, usuarios iniciales) configurado correctamente.');
    }
}