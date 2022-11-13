<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material_in_details extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = ['material_in_id', 'material_id', 'qty', 'price'];

    public function material(){
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }
}
