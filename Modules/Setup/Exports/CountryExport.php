<?php

namespace Modules\Setup\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Setup\Entities\Country;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use DB;

class CountryExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;

    public function collection()
    {
        $new_array = collect();
        $items = Country::get();
        $x = new \stdClass();
        $x->col_1 = 'Country List';
        $x->col_2 = '';
        $x->col_3 = '';
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'Name';
        $x->col_3 = 'Phone Code';
        $new_array->push($x);
        foreach ($items as $key => $item)
        {
            if (count(auth()->user()->user_col_permissions) > 0) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'country_list')->first();
            }else {
                $permissions = null;
            }
    
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $item->id;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_2 = $item->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'phonecode')) {
                    $x->col_3 = '+ '.$item->phonecode;
                }else {
                    $x->col_3 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $item->id;
                $x->col_2 = $item->name;
                $x->col_3 = '+ '.$item->phonecode;
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($row): array
    {
        return [
            $row->col_1,
            $row->col_2,
            $row->col_3,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 20,
            'D' => 10,
            'E' => 20,
            'F' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            2    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:D1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:D1');
            },
        ];
    }
}
