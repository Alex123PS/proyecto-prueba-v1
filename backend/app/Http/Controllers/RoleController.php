<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Roles obtenidos correctamente',
            'data'    => $roles
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles'
        ]);

        $role = Role::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rol creado correctamente',
            'data'    => $role
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id
        ]);

        $role->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rol actualizado correctamente',
            'data'    => $role
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Rol eliminado correctamente'
        ]);
    }
}
