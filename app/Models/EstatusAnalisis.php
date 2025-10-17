<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatusAnalisis extends Model
{
    use HasFactory;
    protected $table = 'CatEstatusAnalisis';
    protected $primaryKey = 'IdEstatusAnalisis';
    public $timestamps = false;
}