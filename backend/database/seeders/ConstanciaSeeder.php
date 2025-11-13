<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConstanciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('constancias')->insert([
            [
                'codigo' => 'CONST-' . Str::upper(Str::random(8)),
                'dni' => '72845693',
                'nombres' => 'Carlos Alberto',
                'ap_paterno' => 'Mamani',
                'ap_materno' => 'Quispe',
                'ciclo' => 'Ciclo Verano 2025',
                'sede' => 'Sede Central Puno',
                'area' => 'Ciencias',
                'curso' => 'Matemática Básica',
                'cantidad_horas' => 40,
                'fecha_emision' => '2025-02-15',
                'estado' => 'Emitida',
                'emitido_por' => 'Admin Sistema',
                'path_pdf' => 'storage/constancias/CONST-001.pdf',
                'observaciones' => 'Constancia generada automáticamente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'CONST-' . Str::upper(Str::random(8)),
                'dni' => '71563482',
                'nombres' => 'María Elena',
                'ap_paterno' => 'Condori',
                'ap_materno' => 'Flores',
                'ciclo' => 'Ciclo Intensivo 2025',
                'sede' => 'Sede Juliaca',
                'area' => 'Letras',
                'curso' => 'Comunicación',
                'cantidad_horas' => 32,
                'fecha_emision' => '2025-03-01',
                'estado' => 'Emitida',
                'emitido_por' => 'Coordinador CEPRE',
                'path_pdf' => 'storage/constancias/CONST-002.pdf',
                'observaciones' => 'Entrega de constancia física pendiente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'CONST-' . Str::upper(Str::random(8)),
                'dni' => '74589631',
                'nombres' => 'Juan Pablo',
                'ap_paterno' => 'Choque',
                'ap_materno' => 'Apaza',
                'ciclo' => 'Ciclo Regular 2025',
                'sede' => 'Sede Azángaro',
                'area' => 'Ciencias',
                'curso' => 'Física',
                'cantidad_horas' => 45,
                'fecha_emision' => '2025-04-10',
                'estado' => 'Anulada',
                'emitido_por' => 'Admin Sistema',
                'path_pdf' => null,
                'observaciones' => 'Constancia anulada por duplicidad.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
