<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function getServerInfo()
    {
        $phpVersion = phpversion();
        return response()->json(['php_version' => $phpVersion]);
    }

    public function getClientInfo(Request $request)
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        return response()->json(['ip' => $ip, 'user_agent' => $userAgent]);
    }
    public function getDatabaseInfo()
    {
        $databaseName = DB::connection()->getDatabaseName();

        return response()->json(['database_name' => $databaseName]);
    }
}
