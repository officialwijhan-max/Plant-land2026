<?php

namespace Modules\Setup\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class DepartmentExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;
    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'department_list')->first();
        }else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Name';
        $x->col_3 = 'Details';
        $x->col_4 = 'Is Active';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_2 = @$item->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'details')) {
                    $x->col_3 = @$item->details;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_4 = showStatus($item->status);
                }else {
                    $x->col_4 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->name;
                $x->col_3 = $item->details;
                $x->col_4 = showStatus($item->status);
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
            'C' => 50,
            'D' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
