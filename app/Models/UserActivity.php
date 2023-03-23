<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'value' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
