<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AuditProgramsExport implements FromView, WithStyles, ShouldAutoSize
{
    private $programs, $sectorName, $auditAreaName;

    public function __construct($programs, $sectorName, $auditAreaName)
    {
        $this->programs = $programs;
        $this->sectorName = $sectorName;
        $this->auditAreaName = $auditAreaName;
    }

    public function view(): View
    {
        return view('audit-programs.export', [
            'sectorAreaPrograms' => $this->programs,
            'sectorName' => $this->sectorName,
            'auditAreaName' => $this->auditAreaName
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            2    => ['font' => ['bold' => true]],
        ];
    }

}
