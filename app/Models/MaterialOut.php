<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialOut extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $guarded = ['id'];
    
    protected $dates = [
        'at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $materialOut) {
            Helper::logAction('created', $materialOut);
        });

        static::updated(function (self $materialOut) {
            Helper::logAction('updated', $materialOut);
        });
    }

    public function details()
    {
        return $this->hasMany(MaterialOutDetail::class);
    }
}
