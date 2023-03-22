<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductIn extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'at' => 'date:Y-m-d',
    ];

    protected $appends = [
        'id_for_human'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ProductInDetail::class);
    }

    public function outDetails(): HasManyThrough
    {
        return $this->hasManyThrough(ProductOutDetail::class, ProductInDetail::class)->has('productOut');
    }

    public function manufacture(): HasOne
    {
        return $this->hasOne(ProductManufacture::class);
    }

    public function getIdForHumanAttribute(): string|null
    {
        return $this->code ?? $this->at->format('d-m-Y') ?? null;
    }

    public function getHasOutDetailsAttribute(): int
    {
        return $this->outDetails->count() > 0;
    }
}
