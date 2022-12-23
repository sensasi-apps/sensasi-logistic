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

    protected $connection = 'mysql';
    protected $fillable = ['code', 'at', 'type', 'created_by_user_id', 'last_updated_by_user_id', 'note', 'history_json'];

    protected $dates = [
        'at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $manufactureOut) {
            Helper::logAction('created', $manufactureOut);
        });

        static::updated(function (self $manufactureOut) {
            Helper::logAction('updated', $manufactureOut);
        });
    }

    public function details()
    {
        return $this->hasMany(MaterialOutDetail::class);
    }
}
