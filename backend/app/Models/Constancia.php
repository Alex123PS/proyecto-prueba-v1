<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constancia extends Model
{
    protected $connection = 'mysql'; // Base de datos principal
    protected $table = 'constancias';
    
    protected $fillable = [
        'codigo',
        'dni',
        'nombres',
        'ap_paterno',
        'ap_materno',
        'ciclo',
        'sede',
        'area',
        'curso',
        'cantidad_horas',
        'fecha_emision',
        'nombres',
        'estado',
        'emitido_por',
        'path_pdf',
        'observaciones'
    ];

    protected $primaryKey = 'id';
    protected $guarded = 'id';
    public $timestamps = true;
}
