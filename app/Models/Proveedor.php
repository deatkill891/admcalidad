<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    
    // Revertimos a como estaba:
    protected $table = 'CatProveedores'; 
    public $timestamps = false;

    public function material()
    {
        return $this->belongsTo(Material::class, 'IdMaterial', 'IdMaterial');
    }
}