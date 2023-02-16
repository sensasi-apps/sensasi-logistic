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

    protected $appends = [
        'id_for_human'
    ];

    public function details()
    {
        return $this->hasMany(MaterialInDetail::class);
    }

    public function outDetails()
    {
        return $this->hasManyThrough(MaterialOutDetail::class, MaterialInDetail::class)->has('materialOut');
    }

    public function getIdForHumanAttribute()
    {
        return $this->code ?? $this->at->format('d-m-Y') ?? null;
    }
}
