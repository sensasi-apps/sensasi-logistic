<?php

namespace App\Models;

use Helper;
use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacture extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'at'
    ];

    public function productIn(){
        return $this->belongsTo(productIn::class, 'product_in_id', 'id');
    }

    public function materialOut(){
        return $this->belongsTo(materialOut::class, 'material_out_id', 'id');
    }
}

