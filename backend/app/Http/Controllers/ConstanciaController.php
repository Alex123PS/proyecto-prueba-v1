<?php

namespace App\Http\Controllers;

use App\Models\Constancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ConstanciaController extends Controller
{
    // obtener constancias
    public function index()
    {
        $constancias = Constancia::orderBy('created_at', 'desc')->get();
        //$constancias = Constancia::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Constancias obtenidas correctamente',
            'data'    => $constancias
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni'   => 'required|string|max:15',
            'ciclo' => 'required|string|max:100',
        ]);

        // conexión al sistema CEPRE:
        /*
        $datos = DB::connection('cepre')->select("
            SELECT d.nombres, d.parterno, d.materno,
                   SUM(a.cantidad_horas) AS total_horas,
                   GROUP_CONCAT(DISTINCT c.denominacion SEPARATOR ', ') AS cursos_dictados,
                   GROUP_CONCAT(DISTINCT ar.denominacion SEPARATOR ', ') AS areas,
                   GROUP_CONCAT(DISTINCT s.denominacion SEPARATOR ', ') AS sedes
            FROM asistencia_docentes a
            JOIN docentes d ON a.docentes_id = d.id
            ...
            WHERE d.nro_documento = ?
            GROUP BY d.nombres, d.parterno, d.materno
        ", [$request->dni]);
        */

        // ejemplo datos simulados CEPRE:
        $datosSimulados = [
            'nombres' => 'Docente Simulado',
            'ap_paterno' => 'Perez',
            'ap_materno' => 'Lopez',
            'sede' => 'Sede Central Puno',
            'area' => 'Ciencias',
            'curso' => 'Matemática Avanzada',
            'cantidad_horas' => 40
        ];

        $constancia = Constancia::create([
            'codigo' => 'CONST-' . strtoupper(Str::random(8)),
            'dni' => $request->dni,
            'nombres' => $datosSimulados['nombres'],
            'ap_paterno' => $datosSimulados['ap_paterno'],
            'ap_materno' => $datosSimulados['ap_materno'],
            'ciclo' => $request->ciclo,
            'sede' => $datosSimulados['sede'],
            'area' => $datosSimulados['area'],
            'curso' => $datosSimulados['curso'],
            'cantidad_horas' => $datosSimulados['cantidad_horas'],
            'fecha_emision' => now(),
            'estado' => 'Emitida',
            'emitido_por' => Auth::user()->name ?? 'Sistema',
            'observaciones' => 'Generada manualmente sin conexión a CEPRE.',
        ]);

        /*
        Constancia::create([
            'codigo' => 'CONST-' . now()->format('YmdHis'),
            'dni' => $dni,
            'nombres' => $datos->nombres,
            'ap_paterno' => $datos->parterno,
            'ap_materno' => $datos->materno,
            'sede' => $datos->sedes,
            'curso' => $datos->cursos_dictados,
            'area' => $datos->areas,
            'cantidad_horas' => $datos->total_horas,
            'fecha_emision' => now(),
            'emitido_por' => Auth::user()->name,
        ]);
         */

        return response()->json([
            'status'  => 'success',
            'message' => 'Constancia generada correctamente',
            'data'    => $constancia
        ], 201);
    }

    //UPDATE

    //ESTADOS
    

    // GENERAR PDF
    public function generarPDF($id)
    {
        $constancia = Constancia::findOrFail($id);

        // Cargar vista con datos
        $pdf = Pdf::loadView('pdf.constancia', compact('constancia'));

        // Ruta donde se guardará el PDF
        $path = 'constancias/' . $constancia->codigo . '.pdf';

        // Guardar en storage/app/public/constancias/
        Storage::disk('public')->put($path, $pdf->output());

        // Actualizar campo path_pdf
        $constancia->update(['path_pdf' => 'storage/' . $path]);

        return response()->json([
            'status' => 'success',
            'message' => 'PDF generado correctamente',
            'data' => asset('storage/' . $path)
        ]);
    }
    
}
