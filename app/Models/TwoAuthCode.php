<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoAuthCode extends Model
{
    use HasFactory;

    protected $table = 'two_factor_auth_codes';

    protected $fillable = [ 
        'code',
        'expired_at',
        'isValid',
        'user_id'
    ];

    protected $dates = [
        'expired_at'
    ];
}
