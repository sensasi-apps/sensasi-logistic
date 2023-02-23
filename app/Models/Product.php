<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    protected $appends = ['tags', 'qty', 'id_for_human'];
    protected $with = ['monthlyMovements'];

    public function setTagsAttribute(array $tags)
    {
        $this->tags_json = json_encode($tags);
    }

    public function getTagsAttribute()
    {
        return json_decode($this->tags_json) ?? [];
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

    public function getIdForHumanAttribute()
    {
        $codePrinted = $this->code ? "{$this->code} - " : null;

        return "{$codePrinted}{$this->name}";
    }

    public function getHasChildrenAttribute()
    {
        return $this->monthlyMovements->count() > 0;
    }
}
