<?php

namespace App\DTO;

class ServerLogDTO
{
    private $api_method;

    private $http_method;

    private $controller_path;

    private $controller_method;

    private $request_body;

    private $request_headers;

    private $user_id;

    private $user_ip;

    private $response_body;

    private $response_headers;

    private $response_status;

    private $created_at;

    public function __construct($api_method, $http_method, $controller_path, $controller_method, $request_body, $request_headers,
     $user_id, $user_ip, $response_body, $response_headers, $response_status, $created_at)
    {
        $this->api_method = $api_method;
        $this->http_method = $http_method;
        $this->controller_path = $controller_path;
        $this->controller_method = $controller_method;
        $this->request_body = $request_body;
        $this->request_headers = $request_headers;
        $this->user_id = $user_id;
        $this->user_ip = $user_ip;
        $this->response_body = $response_body;
        $this->response_headers = $response_headers;
        $this->response_status = $response_status;
        $this->created_at = $created_at;
    }

    public static function fromLogRequest($LogRequest)
    {
        return new self 
        (
            $LogRequest->api_method,
            $LogRequest->http_method,
            $LogRequest->controller_path,
            $LogRequest->controller_method,
            $LogRequest->request_body,
            $LogRequest->request_headers,
            $LogRequest->user_id,
            $LogRequest->user_ip,
            $LogRequest->response_body,
            $LogRequest->response_headers,
            $LogRequest->response_status,
            $LogRequest->created_at
        );
    }
    public function toArray() : array
    {
        return[
            'api_method' => $this -> api_method,
            'http_method' => $this->http_method,
            'controller_path' => $this -> controller_path,
            'controller_method' => $this->controller_method,
            'request_body' => $this->request_body,
            'request_headers' => $this->request_headers,
            'user_id' => $this->user_id,
            'user_ip' => $this->user_ip,
            'response_body' => $this->response_body,
            'response_headers' => $this->response_headers,
            'response_status' => $this -> response_status,
            'created_at' => $this->created_at,
        ];
    }
}
