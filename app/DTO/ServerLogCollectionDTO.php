<?php

namespace App\DTO;

class ServerLogCollectionDTO
{
    private  $serverRequestLogs ;

    public function __construct($serverRequestLogs)
    {
        $this->serverRequestLogs = $serverRequestLogs;
    }

    public function getUserViewServerLogCollection()
    {
        $result = [];

        foreach ($this->serverRequestLogs as $logRequest) {

                $result[] = [
                    'entity_type' => $logRequest->api_method,
                    'entity_id'   => $logRequest->controller_path,
                    'created_at'  => $logRequest->response_status,
                    'created_by'  => $logRequest->created_at,
                ];
        }

        return $result;
    }

}
