<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolesAndPermissions extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'roles_and_permissions';
    
    protected $fillable = [
        'deleted_at', 
        'deleted_by',
    ];
    
    protected $casts = [
        'deleted_at' => 'datetime'
    ];
}
