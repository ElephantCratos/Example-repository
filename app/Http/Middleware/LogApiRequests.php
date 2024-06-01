<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LogRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        
        $logData = [
            'api_method' => $request->path(),
            'http_method' => $request->method(),
            'controller_path' => $request->route()->getActionName(),
            'controller_method' => $request->route()->getActionMethod(),
            'request_body' => json_encode($this->hideRequestData($request->all())),
            'request_headers' => json_encode($this->hideRequestData($request->headers->all())),
            'user_ip' => $request->ip(),
            'user_id' => Auth::user()?->id,
            'user_agent' => $request->userAgent(),
            'created_at' => Carbon::now()
        ];

        $this->deleteOldLogs();

        $response = $next($request);

        $logData['response_status'] = $response->getStatusCode();

        $responseBody = $response->getContent();
        $decodedResponseBody = json_decode($responseBody, true);
        $responseBodyUpdated = json_encode($this->hideRequestData($decodedResponseBody));
        
        
        $logData['response_body'] = $responseBodyUpdated;
        $logData['response_headers'] = json_encode($this->hideRequestData($response->headers->all()));
        if($responseBodyUpdated != null){ 
            LogRequest::create($logData);
        }

        return $response;
    }

    /**
     * Удаляет старые записи из лога, старше 73 часов.
     */
    private function deleteOldLogs()
    {
        LogRequest::where('created_at', '<', Carbon::now()->subHours(73))->delete();
    }

    private function hideRequestData($data)
    {
        if ($data == null || !is_array($data)) { 
            return;
        }

        $keysWithHiddenValue = [
            'username',
            'password', 
            'token',
            'authorization'
            ];

        foreach ($data as $key => &$value) {
            if (in_array($key, $keysWithHiddenValue)) {
                $value = '********';
            } 
            elseif (is_array($value)) {
                    $value = $this->hideRequestData($value);
                }
            }
            return $data;
        
    }
}
