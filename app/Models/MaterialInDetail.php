<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use App\Models\Views\MaterialInDetailsStockView;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaterialInDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'expired_at',
        'manufactured_at'
    ];

    public $timestamps = false;

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function materialIn(): BelongsTo
    {
        return $this->belongsTo(MaterialIn::class);
    }

    public function outDetails(): HasMany
    {
        return $this->hasMany(MaterialOutDetail::class);
    }

    public function getOutTotalAttribute(): int
    {
        return $this->outDetails->sum('qty') ?? 0;
    }

    public function getQtyRemainAttribute(): int
    {
        return $this->stock->qty;
    }

    public function stock(): HasOne
    {
        return $this->hasOne(MaterialInDetailsStockView::class);
    }

    public static function search($q): array
    {
        return self::with(['material', 'materialIn', 'stock'])
            ->has('materialIn')
            ->whereRelation('stock', 'qty', '>', 0)
            ->where(
                fn ($query) => $query
                    ->whereRelation('material', 'name', 'LIKE', "%{$q}%")
                    ->orWhereRelation('material', 'brand', 'LIKE', "%{$q}%")
                    ->orWhereRelation('material', 'code', 'LIKE', "%{$q}%")
                    ->orWhereRelation('materialIn', 'at', 'LIKE', "%{$q}%")
            )
            ->limit(25)
            ->get()
            ->sortBy(['expired_at', 'materialIn.at'])
            ->values()
            ->all();
    }
}
