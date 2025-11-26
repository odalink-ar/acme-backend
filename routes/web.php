<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;

Route::get('/', function () {
    return auth()->check()
        ? redirect('/admin/dashboard')
        : redirect('/admin/login');
});

Route::get('/admin', function () {
    return auth()->check()
        ? redirect('/admin/dashboard')
        : redirect('/admin/login');
});

// Login web (formulario + POST), sin auth
Route::middleware('guest')->group(function () {
        Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('admin/login', [AuthController::class, 'loginWeb'])->name('admin.login.post');

        //Route::post("admin/login", [AuthController::class, "loginSpa"]);
});

// Rutas protegidas /admin
Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {

         Route::get('dashboard', function () {
            // Podés pasar estadísticas reales acá
            return view('admin.dashboard', [
                'usersCount' => \App\Models\User::count(),
                'rolesCount' => \Spatie\Permission\Models\Role::count(), // si usás Spatie
                'permissionsCount' => \Spatie\Permission\Models\Permission::count(),
            ]);
        })->name('dashboard');

        Route::post("logout", [AuthController::class, "logoutSpa"])
            ->name('logout'); // admin.logout

        Route::get("user", [AuthController::class, "me"])
            ->name('user');

        Route::resource('permissions', PermissionsController::class)
            ->names('permissions');

        Route::resource('roles', RolesController::class)
            ->names('roles');

        Route::resource("/users", UsersController::class)->names("users");            

        Route::get('/test-unauthorized', function () {
            return response()->json([
                'success' => false,
                'message' => 'Sesión expirada.'
            ], 401);
});        
    });