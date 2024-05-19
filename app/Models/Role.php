<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    
    protected $fillable = [ 
        'name',
        'description',
        'cipher',
    ];


    protected $casts = [
        'deleted_at' => 'datetime',
    ];


    /**
     * Определяет отношение многие-ко-многим между моделями Role и User
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Role::class, 'users_and_roles', 'role_id', 'user_id');
    }

    
    /**
     * Определяет отношение многие-ко-многим между моделями Role и Permission
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_and_permissions','role_id', 'permission_id');
    }

}
