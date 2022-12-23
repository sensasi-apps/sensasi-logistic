<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = ['code', 'name', 'tags', 'default_price','unit'];
    protected $appends = ['tags', 'qty'];

    public static function boot()
    {
        parent::boot();

        static::created(function (self $product) {
            Helper::logAction('created', $product);
        });

        static::updated(function (self $product) {
            Helper::logAction('updated', $product);
        });
    }

    public function setTagsAttribute(Array $tags)
    {
        $this->tags_json = json_encode($tags);
    }

    public function getTagsAttribute()
    {
        return json_decode($this->tags_json);
    }

    public function monthlyMovements()
    {
        return $this->hasMany(ProductMonthlyMovement::class)->orderByDesc('year')->orderByDesc('month');
    }

    public function getQtyAttribute()
    {
        $qty = 0;
        $this->load('monthlyMovements');

        foreach ($this->monthlyMovements as $monthlyMovement) {
            $qty += $monthlyMovement->in - $monthlyMovement->out;
        }

        return $qty;
    }
}
