<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\GetListLogsRequest;
use App\Models\LogRequest;
use App\DTO\ServerLogDTO;
use App\DTO\ServerLogCollectionDTO;

class ServerRequestLogController extends Controller
{
    public function showLogCollection(GetListLogsRequest $request)
    {
        $allowedSortBy = [
            'api_method',
            'http_method',
            'controller_path',
            'controller_method',
            'request_body', 
            'request_headers', 
            'user_id', 
            'user_ip', 
            'user_agent', 
            'response_status', 
            'response_body', 
            'response_headers', 
            'created_at'
        ];

        $allowedFilters = [
            'user_id',
            'response_status',
            'user_ip',
            'user_agent',
            'controller_path',
        ];

        $allowedOrder = [
            'asc',
            'desc',
        ];
        
        $query = LogRequest::query();

        if ($request->has('sortBy')) {
            $sortBy = $request->input('sortBy');
            
            if (in_array($sortBy[0]['key'],$allowedFilters) && in_array($sortBy[0]['order'],$allowedOrder)){
                $query->orderBy($sortBy[0]['key'], $sortBy[0]['order']);
            }
            else {
                return response()->json(["error" => "Некорректный параметр sortBy"], 400);
            }    
        }

        if ($request->has('filter')) {
            $filter = $request->input('filter');

            if (in_array($filter[0]['key'],$allowedFilters)){
                $query->where($filter[0]['key'], $filter[0]['value']);
            }
            else{
                return response()->json(["error" => "Некорректный параметр filter"], 400);
            }
        }
        
        $page  =  (int)$request->input('page', 1);
        $count =  (int)$request->input('count', 10);
        
        $serverRequestLogCollection = $query->paginate($count, ['*'], 'page', $page);
        $serverRequestLogCollectionDTO = new ServerLogCollectionDTO($serverRequestLogCollection); 
        $onlyRequiredData = $serverRequestLogCollectionDTO->getUserViewServerLogCollection();
        
        return response()->json(['Here all avalaible ApiLogs' => $onlyRequiredData], 200);
    }

    public function showServerRequestLog($id)
    {
        $user = Auth::user();
        $requiredPermission = 'get-specific-log';

        if(isPermissionExistForUser($user, $requiredPermission))
        {

            $logRequest = LogRequest::find($id);
            $logRequestDTO = ServerLogDTO::fromLogRequest($logRequest);
            
            return response()->json(['logRequest' => $logRequestDTO->toArray()],200);
        }
        abort(403, $requiredPermission . ' permission required  ');
    }

    public function deleteServerRequestLog($id)
    {
        $user = Auth::user();
        $requiredPermission = 'delete-specific-log';

        if(isPermissionExistForUser($user, $requiredPermission))
        {
            $logRequest = LogRequest::find($id);
            $logRequest->delete();
            return response()->json('RequestLog был успешно удален.');
        }
        abort(403, $requiredPermission . ' permission required  ');

    } 

    
}
