<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOutDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function productInDetail(): BelongsTo
    {
        return $this->belongsTo(ProductInDetail::class);
    }

    public function productOut(): BelongsTo
    {
        return $this->belongsTo(ProductOut::class);
    }
}
