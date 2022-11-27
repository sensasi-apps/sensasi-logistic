<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOutDetail extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = ['product_in_detail_id', 'product_out_id', 'qty', 'price'];
    public $timestamps = false;

    public function productInDetail()
    {
        return $this->belongsTo(productInDetail::class);
    }

    public function productOut()
    {
        return $this->belongsTo(ProductOut::class);
    }
}
