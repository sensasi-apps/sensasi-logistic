<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use App\Models\Views\ProductInDetailsStockView;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class);
    }

    public function outDetails()
    {
        return $this->hasMany(ProductOutDetail::class);
    }

    public function getQtyRemainAttribute()
    {
        return $this->stock->qty;
    }

    public function stock()
    {
        return $this->hasOne(ProductInDetailsStockView::class);
    }

    public static function search($q)
    {
        return self::with(['product', 'productIn', 'stock'])
            ->has('productIn')
            ->whereRelation('stock', 'qty', '>', 0)
            ->whereRelation('product', 'name', 'LIKE', "%{$q}%")
            ->orWhereRelation('productIn', 'at', 'LIKE', "%{$q}%")
            ->orderBy('product_in_id')->limit(25);;
    }
}
