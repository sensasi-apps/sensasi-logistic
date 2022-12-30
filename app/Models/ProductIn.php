<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductIn extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = "mysql";

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $productIn) {
            Helper::logAction('created', $productIn);
        });

        static::updated(function (self $productIn) {
            Helper::logAction('updated', $productIn);
        });
    }

    public function details(){
        return $this->hasMany(ProductInDetail::class);
    }
}
