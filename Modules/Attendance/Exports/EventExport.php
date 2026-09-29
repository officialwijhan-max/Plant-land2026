<?php

namespace Modules\Attendance\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class EventExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'event_list')->first();
        } else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Title';
        $x->col_3 = 'For Whom';
        $x->col_4 = 'Start Date';
        $x->col_5 = 'End Date';
        $x->col_6 = 'Location';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                if (str_contains($permissions->export_column, 'title')) {
                    $x->col_2 = @$item->title;
                } else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'for_whom')) {
                    $x->col_3 = @$item->for_whom;
                } else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'start_date')) {
                    $x->col_4 = $item->from_date;
                } else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'end_date')) {
                    $x->col_5 = $item->to_date;
                } else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'location')) {
                    $x->col_6 = $item->location;
                } else {
                    $x->col_6 = '-';
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $item->title;
                $x->col_3 = $item->for_whom;
                $x->col_4 = $item->from_date;
                $x->col_5 = $item->to_date;
                $x->col_6 = $item->location;
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($item): array
    {
        return [
            $item->col_1,
            $item->col_2,
            $item->col_3,
            $item->col_4,
            $item->col_5,
            $item->col_6,
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 40,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
