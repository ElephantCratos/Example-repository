<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChangeLog;
use App\Models\LogRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    private $hours;
    private $startTime;
    private $endTime;

    public function __construct()
    {
        $this->hours = env('REPORT_INTERVAL_HOURS', 1);
        $this->startTime = Carbon::now()->subHours($this->hours);
        $this->endTime = Carbon::now();
    }

    public function generateReport()
    {
        $reportData = [
            'methodsRating' => $this->getMethodsRating(),
            'entitiesRating' => $this->getEntitiesRating(),
            'usersRating' => $this->getUsersRating(),
        ];

        $headings = [
            'methodsRating' => ['Метод', 'Количество вызовов', 'Последний вызов'],
            'entitiesRating' => ['Сущность', 'Количество вызовов', 'Последний вызов'],
            'usersRating' => ['Пользователь', 'Количество запросов',                 'Последняя операция']
        ];

        $fileName = 'report_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $filePath = 'reports/' . $fileName;

        $this->generateExcelDoc($reportData, $headings, $filePath);

        $fullPath = storage_path('app/' . $filePath);
        $this->sendReportToTelegram($fullPath);

        return response()->json(['message' => 'Отчет успешно создан и отправлен администраторам.']);
    }

    protected function generateExcelDoc($reportData, $headings, $filePath)
    {
        Storage::makeDirectory('reports');

        $excelData = [];

        foreach ($reportData as $key => $data) {
            $excelData[] = $headings[$key];
            foreach ($data as $item) {
                $excelData[] = $item;
            }
            $excelData[] = ['']; 
        }

        Excel::store(new ReportExport($excelData, []), $filePath, 'local');
    }

    protected function sendReportToTelegram($filePath)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $client = new Client();
        $response = $client->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
            'multipart' => [
                [
                    'name'     => 'chat_id',
                    'contents' => $chatId
                ],

                [
                    'name'     => 'document',
                    'contents' => fopen($filePath, 'r')
                ],

                [
                    'name'     => 'caption',
                    'contents' => 'Данные отчета актуальны на ' . $this->endTime 
                ]
            ]
        ]);
    }

    protected function getMethodsRating()
    {
        $methods = LogRequest::whereBetween('created_at', [$this->startTime, $this->endTime])
            ->select('api_method')
            ->groupBy('api_method')
            ->selectRaw('count(*) as count')
            ->selectRaw('MAX(created_at) as last_called_at')
            ->orderBy('count', 'desc')
            ->get();

        $formattedMethods = $methods->map(function ($method) {
            return [
                'Метод' => $method->api_method,
                'Количество вызовов' => $method->count,
                'Последний вызов' => $method->last_called_at,
            ];
        });

        return $formattedMethods->toArray();
    }

    protected function getEntitiesRating()
    {
        $entities = ChangeLog::whereBetween('created_at', [$this->startTime, $this->endTime])
            ->select('entity_type')
            ->groupBy('entity_type')
            ->selectRaw('count(*) as count')
            ->selectRaw('MAX(created_at) as last_called_at')
            ->orderBy('count', 'desc')
            ->get();

        $formattedEntities = $entities->map(function ($entity) {
            return [
                'Сущность' => $entity->entity_type,
                'Количество вызовов' => $entity->count,
                'Последний вызов' => $entity->last_called_at,
            ];
        });

        return $formattedEntities->toArray();
    }

    protected function getUsersRating()
    {
        $startTime = $this->startTime;
        $endTime = $this->endTime;

        $users = User::withCount([
            'logRequests' => function ($query) use ($startTime, $endTime) {
                $query->whereBetween('created_at', [$startTime, $endTime]);
            }
        ])->get();

        $formattedUsers = $users->map(function ($user) use ($startTime, $endTime) {
            $lastRequest = LogRequest::where('user_id', $user->id)
                ->whereBetween('created_at', [$startTime, $endTime])
                ->orderBy('created_at', 'desc')
                ->first();

            return [
                'Пользователь' => $user->username,
                'Количество запросов' => $user->log_requests_count,
                'Последняя операция' => $lastRequest?->created_at,
            ];
        });

        return $formattedUsers->toArray();
    }
}
