<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTecnicoHorno extends Model
{
    use HasFactory;

    protected $table = 'CatTecnicosHornos';
    protected $primaryKey = 'IdTecnico';
    public $timestamps = false;
}