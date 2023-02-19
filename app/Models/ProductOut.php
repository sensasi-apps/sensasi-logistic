<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOut extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(ProductOutDetail::class);
    }
    
}
