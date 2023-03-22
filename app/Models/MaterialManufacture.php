<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialManufacture extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'at' => 'date:Y-m-d',
    ];

    protected $appends = [
        'id_for_human'
    ];

    public function materialIn(): BelongsTo
    {
        return $this->belongsTo(MaterialIn::class);
    }

    public function materialOut(): BelongsTo
    {
        return $this->belongsTo(MaterialOut::class);
    }

    public function getIdForHumanAttribute(): string
    {
        return $this->code ?? $this->at->format('d-m-Y') ?? null;
    }
}
