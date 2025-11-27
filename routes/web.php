<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    AuthController,
    PermissionsController,
    RolesController,
    UsersController
};

Route::redirect('/', '/admin');
Route::get('/admin', function () {
    return auth()->check()
        ? redirect('/admin/dashboard')
        : redirect('/admin/login');
});

Route::middleware('guest')->group(function () {
        Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('admin/login', [AuthController::class, 'loginWeb'])->name('admin.login.post');
});

// Rutas protegidas /admin
Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {

         Route::get('dashboard', function () {
            return view('admin.dashboard', [
                'usersCount' => \App\Models\User::count(),
                'rolesCount' => \Spatie\Permission\Models\Role::count(),
                'permissionsCount' => \Spatie\Permission\Models\Permission::count(),
            ]);
        })->name('dashboard');


        Route::get("user", [AuthController::class, "me"])->name('user');
        Route::resource('permissions', PermissionsController::class)->names('permissions');
        Route::resource('roles', RolesController::class)->names('roles');
        Route::resource("/users", UsersController::class)->names("users");
        Route::post("logout", [AuthController::class, "logoutSpa"])->name('logout'); // admin.logout

        Route::get('/hola', function (Request $request) {
            return $request->user();
        });        
     
});