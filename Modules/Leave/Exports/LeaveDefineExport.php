<?php

namespace Modules\Leave\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class LeaveDefineExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'leave_define_list')->first();
        }else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'ROLE';
        $x->col_3 = 'USER';
        $x->col_4 = 'LEAVE TYPE';
        $x->col_5 = 'TOTAL DAYS';
        $x->col_6 = 'MAX FORWARD BALANCE (DAYS)';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'role')) {
                    $x->col_2 = @$item->role->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'user')) {
                    $x->col_3 = $item->user->name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'leave_type')) {
                    $x->col_4 = $item->leave_type->name;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'total_days')) {
                    $x->col_5 = $item->total_days;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'forward_balance')) {
                    $x->col_6 = $item->max_forward;
                }else {
                    $x->col_6 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = @$item->role->name;
                $x->col_3 = $item->user->name;
                $x->col_4 = $item->leave_type->name;
                $x->col_5 = $item->total_days;
                $x->col_6 = $item->max_forward;

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
            'B' => 20,
            'C' => 25,
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
