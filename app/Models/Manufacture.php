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

    protected $fillable = ['code', 'at', 'created_at', 'created_by_user_id', 'material_out_id', 'product_in_id','note'];

    protected $dates = [
        'at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $Manufacture) {
            Helper::logAction('created', $Manufacture);
        });

        static::updated(function (self $Manufacture) {
            Helper::logAction('updated', $Manufacture);
        });
    }

    public function productIn(){
        return $this->belongsTo(productIn::class, 'product_in_id', 'id');
    }

    public function materialOut(){
        return $this->belongsTo(materialOut::class, 'material_out_id', 'id');
    }
}

