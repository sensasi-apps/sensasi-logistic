<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = ['user_id', 'action', 'table_id', 'table_name', 'value', 'ip', 'browser'];

    public $timestamps = false;
}
