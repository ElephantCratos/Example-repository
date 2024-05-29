<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SecretKeyGitRequest;
use Illuminate\Support\Facades\Process;
use App\Models\GitLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Process\Exceptions\ProcessFailedException;

use Carbon\Carbon;

class ServerUpdateController extends Controller
{
   public function updateServerToMainBranch(SecretKeyGitRequest $request)
   {
      if ($this->isUpdateInProgress()) {
         return response()->json(['message' => 'Обновление проекта в данный момент выполняется. Пожалуйста, попробуйте позже.'], 503);
      }

      $ipAddress = $request->ip();
      $currentDateTime = Carbon::now()->toDateTimeString();

      GitLog::create(['information' => 'Запрос на обновление был получен с ip-адреса: ' . $ipAddress . '   Время: ' . $currentDateTime]);

      try {
         $this->lockUpdate();
         $this->runGitCommand('checkout origin main');
         $this->runGitCommand('reset --hard HEAD');
         $this->runGitCommand('clean -fd');
         $this->runGitCommand('pull');

         $logMessage = 'Проект успешно обновлен из Git';
         GitLog::create(['information' => 'Проект успешно обновлен из Git']);
         $this->unlockUpdate();

         return response()->json(['message' => 'Проект успешно обновлен из Git']);
      }

      catch (ProcessFailedException $e) {
         GitLog::create(['information' => 'Ошибка обновления проекта из Git: ' . $e->getMessage()]);
         $this->unlockUpdate();
         return response()->json(['message' => 'Ошибка обновления проекта из Git: ' . $e->getMessage()], 500);
      }
   }

   private function runGitCommand($command)
   {
      GitLog::create(['information' => 'Выполнение команды git ' . $command]);
      echo "Выполнение команды git" . $command . "\n";

      Process::path(base_path())
         ->run("git $command")
         ->throw();
   }

   private function isUpdateInProgress(): bool
   {
      return Cache::has('project_update_in_progress');
   }

   private function lockUpdate()
   {
      Cache::put('project_update_in_progress', true);
   }

   private function unlockUpdate()
   {
      Cache::forget('project_update_in_progress');
   }

   


}
