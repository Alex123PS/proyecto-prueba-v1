<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use Illuminate\Http\Request;

class ConductorController extends Controller
{
    //conductor controller ejm master
    public function index()
    {
        $conductores = Conductor::all();
        //$conductores = Conductor::where('estado', 'activo')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Conductores obtenidos correctamente',
            'data'    => $conductores
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'       => 'required|string|max:100',
            'documento'     => 'required|string|max:20|unique:conductores,documento',
            'num_licencia'  => 'nullable|string|max:50',
        ]);
 
        $conductor = Conductor::create([
            'nombres'      => $request->nombres,
            'documento'    => $request->documento,
            'num_licencia' => $request->num_licencia,
            'estado'       => 'ACTIVO', // siempre se crea activo
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Conductor creado correctamente',
            'data'    => $conductor
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Buscar el conductor por ID
        $conductor = Conductor::where('id', $id)->firstOrFail();

        // Validar campos
        $request->validate([
            'nombres'       => 'required|string|max:100',
            'documento'     => 'required|string|max:20|unique:conductores,documento,' . $id,
            'num_licencia'  => 'nullable|string|max:50',
        ]);

        // Actualizar cada campo de manera explícita
        $conductor->update([
            'nombres'      => $request->nombres,
            'documento'    => $request->documento,
            'num_licencia' => $request->num_licencia,
            //'estado'       => 'ACTIVO', // solo el estado activar y des lo usaran
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Conductor actualizado correctamente',
            'data'    => $conductor
        ]);
    }

    public function activar($id)
    {
        $conductor = Conductor::where('id', $id)->firstOrFail();
        $conductor->estado = 'ACTIVO';
        $conductor->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Conductor activado correctamente',
            'data'    => $conductor
        ]);
    }

    public function desactivar($id)
    {
        $conductor = Conductor::where('id', $id)->firstOrFail();
        $conductor->estado = 'INACTIVO';
        $conductor->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Conductor desactivado correctamente',
            'data'    => $conductor
        ]);
    }
}
