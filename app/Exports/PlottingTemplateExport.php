<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlottingTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            ['Senin', 'Seminar 1 Lantai 3', '07:20', '10:10'],
            ['Senin', 'Seminar 1 Lantai 3', '12:20', '15:10'],
            ['Senin', 'Seminar 2 Lantai 3', '07:20', '10:10'],
            ['Selasa', 'Lab Psikologi 15 Lantai 4', '07:20', '10:10'],
            ['Selasa', 'Lab Psikologi 15 Lantai 4', '12:20', '15:10'],
        ];
    }

    public function headings(): array
    {
        return [
            'HARI',
            'NAMA RUANGAN',
            'JAM MULAI',
            'JAM SELESAI'
        ];
    }

    public function title(): string
    {
        return 'Template Plotting';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}