<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureProductIn extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public function productIn(){
        return $this->belongsTo(ProductIn::class, 'product_in_id', 'id');
    }
}
