<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialIn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['code', 'at', 'type', 'created_by_user_id', 'last_updated_by_user_id', 'note'];
    protected $dates = [
        'at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $materialIn) {
            Helper::logAction('created', $materialIn);
        });

        static::updated(function (self $materialIn) {
            Helper::logAction('updated', $materialIn);
        });
    }

    public function details()
    {
        return $this->hasMany(MaterialInDetail::class);
    }

    public function outDetails()
    {
        return $this->hasManyThrough(MaterialOutDetail::class, MaterialInDetail::class)->has('materialOut');
    }
}
