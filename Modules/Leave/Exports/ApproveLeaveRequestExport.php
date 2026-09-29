<?php

namespace Modules\Leave\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Leave\Entities\Leave\ApplyLeave;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ApproveLeaveRequestExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;

    protected $approved_type, $data;

    function __construct($approved_type, $data) {
        $this->approved_type = $approved_type;
        $this->data = $data;
    }

    public function collection()
    {
        $approved = $this->approved_type;
        $items = $this->data['items'];

        if ($approved == "approved") {
            if (count(auth()->user()->user_col_permissions) > 0) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'approve_leave_requests_list')->first();
            }else {
                $permissions = null;
            }
        }
        if ($approved == "pending") {
            if (count(auth()->user()->user_col_permissions) > 0) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'pending_leave_requests')->first();
            }else {
                $permissions = null;
            }
        }

        
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Type';
        $x->col_3 = 'Staff';
        $x->col_4 = 'Email';
        $x->col_5 = 'From';
        $x->col_6 = 'To';
        $x->col_7 = 'Apply Date';
        $x->col_8 = 'Status';
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
                if (str_contains($permissions->export_column, 'staff')) {
                    $x->col_3 = $item->user->name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'email')) {
                    $x->col_4 = $item->user->email;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'from')) {
                    $x->col_5 = showDate($item->start_date);
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'to')) {
                    $x->col_6 = $item->end_date != '0000-00-00' ? showDate($item->end_date) : '';
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'apply_date')) {
                    $x->col_7 = showDate($item->apply_date);
                }else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    if ($item->status == 0){
                        $x->col_8 = __('common.Pending');
                    }
                    elseif ($item->status == 1){
                        $x->col_8 = __('common.Approved');
                    }
                    else{
                        $x->col_8 = __('common.Cancelled');
                    }
                }else {
                    $x->col_8 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->leave_type->name;
                $x->col_3 = $item->user->name;
                $x->col_4 = $item->user->email;
                $x->col_5 = showDate($item->start_date);
                $x->col_6 = $item->end_date != '0000-00-00' ? showDate($item->end_date) : '';
                $x->col_7 = showDate($item->apply_date);
                if ($item->status == 0){
                    $x->col_8 = __('common.Pending');
                }
                elseif ($item->status == 1){
                    $x->col_8 = __('common.Approved');
                }
                else{
                    $x->col_8 = __('common.Cancelled');
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
            $item->col_7,
            $item->col_8,
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
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
