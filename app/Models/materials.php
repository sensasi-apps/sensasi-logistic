<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class materials extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['code', 'name', 'tags_json', 'unit'];
}
