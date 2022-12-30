<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacture extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'mysql';

    protected $guarded = ['id'];

    protected $dates = [
        'at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $manufacture) {
            Helper::logAction('created', $manufacture);
        });

        static::updated(function (self $manufacture) {
            Helper::logAction('updated', $manufacture);
        });
    }

    public function productIn(){
        return $this->belongsTo(productIn::class, 'product_in_id', 'id');
    }

    public function materialOut(){
        return $this->belongsTo(materialOut::class, 'material_out_id', 'id');
    }
}

