<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use App\Models\Views\MaterialInDetailsStockView;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialInDetail extends Model
{
    use HasFactory, CUDLogTrait;
    
    protected $guarded = ['id'];

    public $timestamps = false;

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function materialIn()
    {
        return $this->belongsTo(MaterialIn::class);
    }

    public function outDetails()
    {
        return $this->hasMany(MaterialOutDetail::class);
    }

    public function getOutTotalAttribute()
    {
        return $this->outDetails->sum('qty');
    }

    public function getQtyRemainAttribute()
    {
        if (!$this->relationLoaded('stock')) {
            $this->load('stock');
        }

        return $this->stock->qty;
    }

    public function stock()
    {
        return $this->hasOne(MaterialInDetailsStockView::class);
    }

    public static function search($q)
    {
        return self::with(['material', 'materialIn', 'stock'])
            ->has('materialIn')
            ->whereRelation('stock', 'qty', '>', 0)
            ->where(
                fn ($query) => $query
                    ->whereRelation('material', 'name', 'LIKE', "%{$q}%")
                    ->orWhereRelation('materialIn', 'at', 'LIKE', "%{$q}%")
            )
            ->orderBy('material_in_id')->limit(25);
    }
}
