<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialIn extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    protected $dates = [
        'at'
    ];

    public function details()
    {
        return $this->hasMany(MaterialInDetail::class);
    }

    public function outDetails()
    {
        return $this->hasManyThrough(MaterialOutDetail::class, MaterialInDetail::class)->has('materialOut');
    }
}
