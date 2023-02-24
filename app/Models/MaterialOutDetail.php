<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialOutDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function materialInDetail(): BelongsTo
    {
        return $this->belongsTo(MaterialInDetail::class);
    }

    public function materialOut(): BelongsTo
    {
        return $this->belongsTo(MaterialOut::class);
    }
}
