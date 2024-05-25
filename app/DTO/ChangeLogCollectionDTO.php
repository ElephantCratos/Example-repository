<?php

namespace App\DTO;


class ChangeLogCollectionDTO
{
    private  $changelogs; 


    public function __construct($changelogs)
    {
        $this->changelogs = $changelogs;
    }


    public function getOnlyChangedProperties() : array
    {
        $result = [];

        foreach ($this->changelogs as $changeLog) {
            $recordBefore = json_decode($changeLog->record_before, true);
            $recordAfter = json_decode($changeLog->record_after, true);

                $diff = getArrayDiff($recordBefore, $recordAfter);

                $result[] = [
                    'entity_type' => $changeLog->entity_type,
                    'entity_id' => $changeLog->entity_id,
                    'changes' => $diff,
                    'created_at' => $changeLog->created_at,
                    'created_by' => $changeLog->created_by,
                ];
            
        }

        return $result;
    }
}