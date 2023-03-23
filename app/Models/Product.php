<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, CUDLogTrait;

    protected $fillable = ['code', 'name', 'tags', 'unit', 'low_qty', 'brand', 'default_price'];
    protected $appends = ['tags', 'qty', 'id_for_human'];
    protected $with = ['monthlyMovements'];

    public function setTagsAttribute(array $tags): void
    {
        $this->tags_json = json_encode($tags);
    }

    public function getTagsAttribute(): array
    {
        return json_decode($this->tags_json) ?? [];
    }

    public function monthlyMovements(): HasMany
    {
        return $this->hasMany(ProductMonthlyMovement::class)->orderByDesc('year')->orderByDesc('month');
    }

    public function getQtyAttribute(): int
    {
        $qty = 0;

        foreach ($this->monthlyMovements as $monthlyMovement) {
            $qty += $monthlyMovement->in - $monthlyMovement->out;
        }

        return $qty;
    }

    public function getIdForHumanAttribute(): string
    {
        $codePrinted = $this->code ? "{$this->code} - " : null;
        $brandPrinted = $this->brand ? " ({$this->brand})" : null;

        return "{$codePrinted}{$this->name}{$brandPrinted}";
    }

    public function getHasChildrenAttribute(): bool
    {
        return $this->monthlyMovements->count() > 0;
    }
}
