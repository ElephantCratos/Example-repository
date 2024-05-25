<?php

namespace App\DTO;


class ChangeLogDTO
{
    private string $entity_type;

    private int $entity_id;

    private string $record_before;

    private string $record_after;

    private string $created_at;

    private int $created_by;



    public function __construct(string $entity_type, int $entity_id, string $record_before, string $record_after, string $created_at, int $created_by)
    {
        $this->entity_type = $entity_type;
        $this->entity_id = $entity_id;
        $this->record_before = $record_before;
        $this->record_after = $record_after;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
    }


    public function toArray() : array
    {
        return [
            'entity_type'        => $this->entity_type,
            'entity_id'          => $this->entity_id,
            'record_before'      => $this->record_before,
            'record_after'       => $this->record_after,
            'created_at'         => $this->created_at,
            'created_by'         => $this->created_by
        ];
    }
}