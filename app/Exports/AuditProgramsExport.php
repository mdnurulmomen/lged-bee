<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AuditProgramsExport implements FromView, WithStyles, ShouldAutoSize
{
    private $programs;

    public function __construct($programs)
    {
        $this->programs = $programs;
    }

    public function view(): View
    {
        return view('audit-programs.export', [
            'sectorAreaPrograms' => $this->programs
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

}
