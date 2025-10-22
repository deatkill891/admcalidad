<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    
    // Revertimos a como estaba:
    protected $table = 'CatMateriales';
    protected $primaryKey = 'IdMaterial';

    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'IdMaterial', 'IdMaterial');
    }
}