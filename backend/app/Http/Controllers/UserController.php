<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuarios obtenidos correctamente',
            'data'    => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'roles'    => 'array|nullable'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'estado'    => 'ACTIVO', // siempre se crea activo
        ]);

        // Asignar roles si se envían
        if (!empty($request->roles)) {
            $user->syncRoles($request->roles);
        }

        $user->load('roles', 'tenant');

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario creado correctamente',
            'data'    => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'roles'    => 'array|nullable',
        ]);

        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if (!empty($request->roles)) {
            $user->syncRoles($request->roles);
        }

        $user->load('roles', 'tenant');

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario actualizado correctamente',
            'data'    => $user
        ]);
    }

    public function activar($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->estado = 'ACTIVO';
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario activado correctamente',
            'data'    => $user
        ]);
    }

    public function desactivar($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->estado = 'INACTIVO';
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario desactivado correctamente',
            'data'    => $user
        ]);
    }
}
