<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOutDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = ['material_in_detail_id', 'material_out_id', 'qty'];
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
