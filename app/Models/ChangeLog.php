<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    use HasFactory;

    protected $table =  "change_logs";
    public $timestamps = false;

    protected $fillable = [
    'entity_type',
    'entity_id',
    'record_before',
    'record_after',
    'created_at',
    'created_by'
    ];
}
