<?php

namespace Modules\ProAccount\Exports;
use Maatwebsite\Excel\Concerns\FromQuery;
use Modules\ProAccount\Entities\Leadger;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use DB;

class LeadgerExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths, WithStyles, WithCustomStartCell, WithDrawings
{
    use Exportable;

    public function query()
    {
        return Leadger::query();
    }

    public function map($item): array
    {
        $parent_code = 0;
        if ($item->parent_id != 0) {
            $parent_code = Leadger::where('id', $item->parent_id)->first()->code;
        }
        return [
           $item->code,
           $item->acc_type,
           ($item->cost_center == 0) ? "no" : "yes",
           $item->name,
           $parent_code
        ];
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function headings(): array
    {
        return [
            'code',
            'acc_type',
            'cost_center',
            'name',
            'parent_code',
        ];
    }

    public function drawings()
   {
       $drawing = new Drawing();
       $drawing->setName(Settings("company_name"));
       $drawing->setPath(public_path('/frontend/img/logo.png'));
       $drawing->setHeight(70);
       $drawing->setCoordinates('A3');
       return $drawing;
   }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 10,
            'D' => 60,
            'E' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            10    => ['font' => ['bold' => true]]
        ];
    }
}
