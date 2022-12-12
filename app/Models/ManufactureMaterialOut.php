<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureMaterialOut extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public function materialOut(){
        return $this->belongsTo(MaterialOut::class, 'material_out_id', 'id');
    }
}
