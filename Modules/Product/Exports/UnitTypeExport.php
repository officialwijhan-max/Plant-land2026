<?php

namespace Modules\Product\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class UnitTypeExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;
    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;
        $items = $given_data['items'];

        $new_array = collect();

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'unit_list')->first();
        }else {
            $permissions = null;
        }
        
        $x = new \stdClass();
        $x->col_1 = "UNIT LIST";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $new_array->push($x);


        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Model';
        $x->col_3 = 'Description';
        $x->col_4 = 'Status';
        $new_array->push($x);
        foreach ($items as $key => $item)
        {
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
                if (str_contains($permissions->export_column, 'description')) {
                    $x->col_3 = $item->description;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_4 = ($item->status == 1) ? __('common.Active') : __('common.DeActive');
                }else {
                    $x->col_4 = '-';
                }
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $item->id;
                $x->col_2 = $item->name;
                $x->col_3 = $item->description;
                $x->col_4 = ($item->status == 1) ? __('common.Active') : __('common.DeActive');
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
            $row->col_4,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 50,
            'D' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 11]],
            2    => ['font' => ['bold' => true, 'size' => 11]],
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
