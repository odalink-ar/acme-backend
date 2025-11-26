<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string("search")->toString();

        $permissions = Permission::query()
            ->when($search, function ($query) use ($search) {
                $query
                    ->where("name", "like", "%{$search}%")
                    ->orWhere("guard_name", "like", "%{$search}%");
            })
            ->orderBy("name")
            ->paginate(10)
            ->withQueryString(); // mantiene ?search en la paginación

        return view("admin.permissions.index", [
            'permissions' => $permissions,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view("admin.permissions.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => [
                "required",
                "string",
                "max:255",
                "unique:permissions,name",
            ],
            "guard_name" => ["nullable", "string", "max:255"],
        ]);

        if (empty($data["guard_name"])) {
            $data["guard_name"] = config("auth.defaults.guard", "web");
        }

        Permission::create($data);

        return redirect()
            ->route("admin.permissions.index")
            ->with("success", "Permiso creado correctamente.");
    }

    public function show(Permission $permission)
    {
        return view("admin.permissions.show", compact("permission"));
    }

    public function edit(Permission $permission)
    {
        return view("admin.permissions.edit", compact("permission"));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            "name" => [
                "required",
                "string",
                "max:255",
                "unique:permissions,name," . $permission->id,
            ],
            "guard_name" => ["nullable", "string", "max:255"],
        ]);

        if (empty($data["guard_name"])) {
            $data["guard_name"] = config("auth.defaults.guard", "web");
        }

        $permission->update($data);

        return redirect()
            ->route("admin.permissions.index")
            ->with("success", "Permiso actualizado correctamente.");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route("admin.permissions.index")
            ->with("success", "Permiso eliminado correctamente.");
    }
}
