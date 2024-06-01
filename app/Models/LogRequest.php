<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRequest extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "logs_requests";
    protected $fillable = [
        'api_method',
        'http_method',
        'controller_path',
        'controller_method',
        'request_body',
        'request_headers',
        'user_ip',
        'user_id',
        'user_agent',
        'response_status',
        'response_body',
        'response_headers',
        'created_at'
    ];
}


