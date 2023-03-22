<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialOut extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $appends = [
        'id_for_human'
    ];

    protected $casts = [
        'at' => 'date:Y-m-d',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(MaterialOutDetail::class);
    }

    public function productManufacture(): HasOne
    {
        return $this->hasOne(ProductManufacture::class);
    }

    public function materialManufacture(): HasOne
    {
        return $this->hasOne(MaterialManufacture::class);
    }

    public function getIdForHumanAttribute(): string|null
    {
        return $this->code ?? $this->at->format('d-m-Y') ?? null;
    }
}
