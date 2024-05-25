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


   /*Так же проба пера, было интересно "как это можно сделать ещё". Вопрос в том, где можно использовать 
   подобные конструкции (если можно вообще, хоть и забавная штука, но выглядит как что-то ненадежное). 
   Есть вроде что-то более правильное, если я верно понимаю похожее по смыслу - ReflectionClass, но как я дошел 
   до этого все свинство написанное выше уже было +- закончено и писать третью уже по времени поджимало. */
   public function restoreEntityFromLog($id)
   {
      /*Extra Controller Piece
      $currentMethod = request()->route()->getActionMethod();
            if ($currentMethod == 'restoreEntityFromLog'){
                $role->id = $request->id;
            }
      */


      $changeLog = ChangeLog::find($id);
      $changeLogEntityType = $changeLog['entity_type'];
      $changeLogEntityId = $changeLog['entity_id'];
      $changeLogRecordBefore = json_decode($changeLog['record_before'], true);

      $entityModel = $changeLogEntityType::withTrashed()->find($changeLogEntityId);

      $controllerClass = 'App\Http\Controllers\\' .  basename( $changeLogEntityType . "Controller" );
      $controllerInstance = new $controllerClass();

   

      if ($changeLogRecordBefore == null) {
         if ($entityModel != null) {
            $controllerInstance->forceDelete($changeLogEntityId);
            return response()->json('Сущность успешно восстановлена в состояние record_before');
         }
         return response()->json('Попытка удалить уже удаленную сущность.');
      }

      if ($changeLogRecordBefore != null) {
         if ($entityModel != null) {
            $requestClass = 'App\Http\Requests\Update' .  basename( $changeLogEntityType . 'Request');
            $requestInstance = new $requestClass();
            $requestInstance->merge($changeLogRecordBefore);
            
            $controllerInstance->update($requestInstance,$changeLogEntityId);
            return response()->json('Сущность успешно восстановлена в состояние record_before');
         }

         $requestClass = 'App\Http\Requests\Create' .  basename( $changeLogEntityType . 'Request');
         $requestInstance = new $requestClass();
         $requestInstance->merge($changeLogRecordBefore);      
         $controllerInstance->create($requestInstance);
            return response()->json('Сущность успешно восстановлена в состояние record_before');
      }
   }

   /*
      Вопрос который стоит открытым - нужны ли changeLogs на действия произведенные при откате сущности до состояния N.
      Так же вопрос по fillable, casts etc. Что там все таки должно находиться, а чему бы там находиться не стоит.
      Есть вопросы по тому, как проверять работоспособность транзакций? Специально через какой-нибудь try/catch прокидывать
      исключения или есть иные способы?
      Данное свинство уберу как вопросы будут закрыты, пока полежат чтобы не забыть.
   */
   public function restoreEntityFromLogExtraVariable($id)
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
