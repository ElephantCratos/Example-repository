<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTO\ChangeLogDTO;
use App\DTO\ChangeLogCollectionDTO;
use App\Models\ChangeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ChangeLogController extends Controller
{

    /**
     * Показывает все логи изменений связанные с Roles под запрошенным id.
     * Возвращает ответ в формате JSON.
     * 
     * @return JsonResponse
     * @param int $id
     */
   public function showRoleLogs($id) : JsonResponse
   {
      $changeLogs = ChangeLog::where('entity_type', '=', 'App\\Models\\Role')
      ->where('entity_id', '=' , $id)
      ->get();

      $changeLogCollectionDTO = new ChangeLogCollectionDTO($changeLogs);
      $changeLogOnlyChangedData = $changeLogCollectionDTO->getOnlyChangedProperties();
      
      return response()->json(['All change logs for role' => $changeLogOnlyChangedData]);
      
   }


   /**
     * Показывает все логи изменений связанные с Permission под запрошенным id
     * Возвращает ответ в формате JSON.
     * 
     * @return JsonResponse
     * @param int $id
     */
   public function showPermissionLogs($id) : JsonResponse
   {
      $changeLogs = ChangeLog::
        where('entity_type', '=', 'App\\Models\\Permission')
      ->where('entity_id', '=' , $id)
      ->get();

      $changeLogCollectionDTO = new ChangeLogCollectionDTO($changeLogs);
      $changeLogOnlyChangedData = $changeLogCollectionDTO->getOnlyChangedProperties();

      return response()->json(['All change logs for permission' =>$changeLogOnlyChangedData]);
      
   }

     /**
     * Создает лог на основе данных ChangeLogDTO
     * 
     * @return JsonResponse
     * @param ChangeLogDTO $changeLogDTO
     */
   public static function createLog(ChangeLogDTO $changeLogDTO) 
   {
        $changeLogDTO = $changeLogDTO->toArray();
       
        $changeLog = ChangeLog::create($changeLogDTO);
   }


   

   /**
     * Приводит модель указанную в логе в состояние record_before
     * Возвращает ответ в формате JSON.
     * 
     * @return JsonResponse
     * @param int $id
     */
   public function restoreEntityFromLog($id) : JsonResponse
   {
      return DB::transaction(function () use ($id) {
         $changeLog = ChangeLog::find($id);
         $changeLogEntityType = $changeLog['entity_type'];
         $changeLogEntityId = $changeLog['entity_id'];
         $changeLogRecordBefore = json_decode($changeLog['record_before'], true);

         $entityModel = $changeLogEntityType::withTrashed()->find($changeLogEntityId);
         $oldRecordEntityModel = json_encode($entityModel);

         
      

         if ($changeLogRecordBefore == null) {
            if ($entityModel != null) {

               $entityModel->forceDelete();
               $changeLogNew = new ChangeLogDTO($changeLogEntityType, $changeLogEntityId, $oldRecordEntityModel, json_encode(null), Carbon::now(), Auth::user()->id);
               ChangeLogController::createLog($changeLogNew);
               return response()->json(['Сущность успешно приведена в состояние record_before', '$entityModel' => $entityModel, 'changeLogBefore' => $changeLogRecordBefore], 200);
            }
            return response()->json(['Попытка привести уже удаленную сущность к удаленному состоянию']);
         }


         if ($changeLogRecordBefore != null) {
            if ($entityModel != null) {
               
               $entityModel->update($changeLogRecordBefore);
               $entityModel->save();
               $changeLogNew = new ChangeLogDTO($changeLogEntityType, $changeLogEntityId, $oldRecordEntityModel, $entityModel, Carbon::now(), Auth::user()->id);
               ChangeLogController::createLog($changeLogNew);
               return response()->json(['Сущность успешно приведена в состояние record_before', '$entityModel' => $entityModel, 'changeLogBefore' => $changeLogRecordBefore], 200);
            }
            
            $restoredEntity = $changeLogEntityType::create($changeLogRecordBefore);
            $changeLogNew = new ChangeLogDTO($changeLogEntityType, $changeLogEntityId,  json_encode(null) , $restoredEntity, Carbon::now(), Auth::user()->id);
            ChangeLogController::createLog($changeLogNew);
            return response()->json(['Сущность успешно приведена в состояние record_before', '$entityModel' => $entityModel, 'changeLogBefore' => $restoredEntity], 200);
         }

      },5);
   }

}
