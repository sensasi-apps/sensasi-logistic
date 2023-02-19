<?php

namespace App\Models;

use Helper;
use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIn extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    public function details(){
        return $this->hasMany(ProductInDetail::class);
    }
}
