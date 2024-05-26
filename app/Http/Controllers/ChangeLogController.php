<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTO\ChangeLogDTO;
use App\DTO\ChangeLogCollectionDTO;
use App\Models\ChangeLog;
use Carbon\Carbon;

class ChangeLogController extends Controller
{
   public function showRoleLogs($id)
   {
      $changeLogs = ChangeLog::where('entity_type', '=', 'App\\Models\\Role')
      ->where('entity_id', '=' , $id)
      ->get();

      $changeLogCollectionDTO = new ChangeLogCollectionDTO($changeLogs);
      $changeLogOnlyChangedData = $changeLogCollectionDTO->getOnlyChangedProperties();
      
      return response()->json(['All change logs for role' =>$changeLogOnlyChangedData]);
      
   }

   public function showPermissionLogs($id)
   {
      $changeLogs = ChangeLog::
        where('entity_type', '=', 'App\\Models\\Permission')
      ->where('entity_id', '=' , $id)
      ->get();

      $changeLogCollectionDTO = new ChangeLogCollectionDTO($changeLogs);
      $changeLogOnlyChangedData = $changeLogCollectionDTO->getOnlyChangedProperties();

      return response()->json(['All change logs for permission' =>$changeLogOnlyChangedData]);
      
   }


   public static function createLog(ChangeLogDTO $changeLogDTO)
   {
        $changeLogDTO = $changeLogDTO->toArray();
       
        $changeLog = ChangeLog::create($changeLogDTO);
   }


   

   /*
      Вопрос который стоит открытым - нужны ли changeLogs на действия произведенные при откате сущности до состояния N.
      Так же вопрос по fillable. Что там все таки должно находиться, а чему бы там находиться не стоит.
      Есть вопросы по тому, как проверять работоспособность транзакций? Специально через какой-нибудь try/catch прокидывать
      исключения или есть иные способы?
      Данное свинство уберу как вопросы будут закрыты, пока полежат чтобы не забыть.
   */
   public function restoreEntityFromLog($id)
   {
      $changeLog = ChangeLog::find($id);
      $changeLogEntityType = $changeLog['entity_type'];
      $changeLogEntityId = $changeLog['entity_id'];
      $changeLogRecordBefore = json_decode($changeLog['record_before'], true);

      $entityModel = $changeLogEntityType::withTrashed()->find($changeLogEntityId);


      
      if ($changeLogRecordBefore == null) {
         if ($entityModel != null) {
            $entityModel->forceDelete();
         }
         return response()->json('Попытка удалить уже удаленную сущность.');
      }

      if ($changeLogRecordBefore != null) {
         if ($entityModel != null) {
            $entityModel->update($changeLogRecordBefore);
            $entityModel->save();
            return response()->json(['Сущность успешно восстановлена в состояние метод UPDATE', '$entityModel' => $entityModel, 'changeLogBefore' => $changeLogRecordBefore]);
         }
         $restoredEntity = $changeLogEntityType::create($changeLogRecordBefore);
         return response()->json('Сущность успешно восстановлена в состояние record_before case2');
      }

   }
}
