<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOutDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function productInDetail()
    {
        return $this->belongsTo(productInDetail::class);
    }

    public function productOut()
    {
        return $this->belongsTo(ProductOut::class);
    }
}
