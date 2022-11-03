<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class materials_model extends Model
{
    use HasFactory;
    protected $table = 'materials';
    protected $fillable = ['code', 'name', 'tags_json', 'unit'];
}
