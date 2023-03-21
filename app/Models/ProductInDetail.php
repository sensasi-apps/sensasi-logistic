<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use App\Models\Views\ProductInDetailsStockView;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductInDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'expired_at',
        'manufactured_at'
    ];

    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productIn(): BelongsTo
    {
        return $this->belongsTo(ProductIn::class);
    }

    public function outDetails(): HasMany
    {
        return $this->hasMany(ProductOutDetail::class);
    }

    public function getQtyRemainAttribute(): int
    {
        return $this->stock->qty;
    }

    public function stock(): HasOne
    {
        return $this->hasOne(ProductInDetailsStockView::class);
    }

    public static function search($q): array
    {
        return self::with(['product', 'productIn', 'stock'])
            ->has('productIn')
            ->whereRelation('stock', 'qty', '>', 0)
            ->where(
                fn ($query) => $query
                    ->whereRelation('product', 'name', 'LIKE', "%{$q}%")
                    ->orWhereRelation('productIn', 'at', 'LIKE', "%{$q}%")
            )
            ->limit(25)
            ->get()
            ->sortBy('productIn.at')
            ->values()
            ->all();
    }
}
