<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIn extends Model
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
        return $this->hasMany(ProductInDetail::class);
    }

    public function getIdForHumanAttribute()
    {
        return $this->code ?? $this->at->format('d-m-Y') ?? null;
    }

    public function outDetails()
    {
        return $this->hasManyThrough(ProductOutDetail::class, ProductInDetail::class)->has('productOut');
    }

    public function getHasOutDetailsAttribute()
    {
        return $this->outDetails->count() > 0;
    }
}
