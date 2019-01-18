<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'nick', 'email', 'password',
    ];

    public $timestamps = false;
}
