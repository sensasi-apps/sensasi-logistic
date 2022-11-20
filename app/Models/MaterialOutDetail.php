<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOutDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = ['mat_in_detail_id', 'material_out_id', 'qty'];

    public function detail_ins(){
        return $this->belongsTo(MaterialInDetail::class, 'mat_in_detail_id', 'id');
    }
}
