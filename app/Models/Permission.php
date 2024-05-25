<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;


    public $timestamps = false;
    
    protected $fillable = [ 
        'name',
        'description',
        'cipher',
        'deleted_at',
        'deleted_by',
    ];


    protected $casts = [
        'deleted_at' => 'datetime',
    ];
   
     /**
     * Определяет отношение многие-ко-многим между моделями User и Permission
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Permission::class, 'roles_and_permissions', 'permission_id', 'role_id');
    }
}

