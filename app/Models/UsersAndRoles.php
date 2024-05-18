<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersAndRoles extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'users_and_roles';
    
    protected $fillable = [
        'deleted_at', 
        'deleted_by',
    ];
    
    protected $casts = [
        'deleted_at' => 'datetime'
    ];
}
