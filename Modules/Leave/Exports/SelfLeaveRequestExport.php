<?php

namespace Modules\Leave\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Employee\Entities\Leave\ApplyLeave;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class SelfLeaveRequestExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'self_leave_list')->first();
        }else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Type';
        $x->col_3 = 'From';
        $x->col_4 = 'To';
        $x->col_5 = 'Apply Date';
        $x->col_6 = 'Status';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'type')) {
                    $x->col_2 = $item->leave_type->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'from')) {
                    $x->col_3 = showDate($item->start_date);
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'to')) {
                    $x->col_4 = $item->end_date != '0000-00-00' ? showDate($item->end_date) : '';
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'apply_date')) {
                    $x->col_5 = showDate($item->apply_date);
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    if ($item->status == 0){
                        $x->col_6 = __('common.Pending');
                    }
                    elseif ($item->status == 1){
                        $x->col_6 = __('common.Approved');
                    }
                    else{
                        $x->col_6 = __('common.Cancelled');
                    }
                }else {
                    $x->col_6 = '-';
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->leave_type->name;
                $x->col_3 = showDate($item->start_date);
                $x->col_4 = $item->end_date != '0000-00-00' ? showDate($item->end_date) : '';
                $x->col_5 = showDate($item->apply_date);
                if ($item->status == 0){
                    $x->col_6 = __('common.Pending');
                }
                elseif ($item->status == 1){
                    $x->col_6 = __('common.Approved');
                }
                else{
                    $x->col_6 = __('common.Cancelled');
                }
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
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 15,
            'H' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
