<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOut extends Model
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
        return $this->hasMany(ProductOutDetail::class);
    }

    public function getIdForHumanAttribute(): string
    {
        return $this->code ?? $this->at->format('d-m-Y') ?? null;
    }
}
