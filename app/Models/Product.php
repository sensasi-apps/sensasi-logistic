<?php

namespace App\Models;

use Helper;
use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, CUDLogTrait;

    protected $fillable = ['code', 'name', 'tags', 'default_price', 'low_qty', 'unit'];
    protected $appends = ['tags', 'qty'];
    protected $with = ['monthlyMovements'];

    public function setTagsAttribute(array $tags)
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

        foreach ($this->monthlyMovements as $monthlyMovement) {
            $qty += $monthlyMovement->in - $monthlyMovement->out;
        }

        return $qty;
    }
}
