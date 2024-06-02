<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromArray, WithHeadings
{
    protected $reportData;
    protected $headings;

    public function __construct(array $reportData, array $headings)
    {
        $this->reportData = $reportData;
        $this->headings = $headings;
    }

    public function array(): array
    {
        return $this->reportData;
    }

    public function headings(): array
    {
        return $this->headings;
    }

}
