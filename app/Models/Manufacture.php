<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manufacture extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'at'
    ];

    public function productIn(): BelongsTo
    {
        return $this->belongsTo(ProductIn::class);
    }

    public function materialOut(): BelongsTo
    {
        return $this->belongsTo(MaterialOut::class);
    }
}
