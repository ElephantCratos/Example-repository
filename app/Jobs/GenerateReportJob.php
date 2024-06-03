<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JobLog;
use App\Http\Controllers\ReportController;
use Exception;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries;

    public $timeout;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->tries = env('REPEAT_COUNT', 3);
        $this->timeout = env('MAX_EXECUTION_TIME_MINUTES', 30) * 60;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        JobLog::create(['job' => 'GenerateReportJob', 'status' => 'started', 'message' => 'Работа формирования отчета начата']);

        $reportController = new ReportController;
        $reportController->generateReport();

        JobLog::create(['job' => 'GenerateReportJob', 'status' => 'finished', 'message' => 'Работа формирования отчета закончена']);
    }

    public function failed(Exception $exception): void
    {
        JobLog::create(['job' => 'GenerateReportJob', 'status' => 'failed', 'message' => $exception->getMessage()]);

        if ($this->attempts() < env('REPEAT_COUNT', 3)) {
            $this->release(env('REPEAT_TIMEOUT_MINUTES', 10) * 60); 
        }
    }


}
