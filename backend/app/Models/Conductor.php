<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conductor extends Model
{
    protected $connection = 'mysql'; // Base de datos principal
    protected $table = 'conductores';
    
    protected $fillable = [
        'nombres',
        'num_licencia',
        'documento',
        'estado',
        'tenant_id',
    ];

    protected $primaryKey = 'id';
    protected $guarded = 'id';
    public $timestamps = true;
 
}
