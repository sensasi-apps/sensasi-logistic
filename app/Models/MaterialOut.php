<?php

namespace App\Models;

use Helper;
use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOut extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'at'
    ];

    public function details()
    {
        return $this->hasMany(MaterialOutDetail::class);
    }
}
