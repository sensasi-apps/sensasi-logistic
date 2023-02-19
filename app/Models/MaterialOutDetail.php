<?php

namespace App\Models;

use App\Models\Traits\CUDLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOutDetail extends Model
{
    use HasFactory, CUDLogTrait;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function materialInDetail()
    {
        return $this->belongsTo(MaterialInDetail::class);
    }

    public function materialOut()
    {
        return $this->belongsTo(MaterialOut::class);
    }
}
