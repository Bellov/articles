<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
class Article extends Authenticatable
{
    protected $table = 'articles';

    protected $fillable = [
        'response_json',
    ];
}
