<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class CarryForwardExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;

    function __construct() {
        // 
    }

    public function collection()
    {
        $items = User::with('leaves','leaveDefines')->whereNotIn('role_id',[1,4,5])->latest()->get();

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'carry_forward_list')->first();
        }else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Name';
        $x->col_3 = 'UserName';
        $x->col_4 = 'Email';
        $x->col_5 = 'Carry Forward';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'staff')) {
                    $x->col_2 = $item->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'username')) {
                    $x->col_3 = $item->username;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'email')) {
                    $x->col_4 = $item->email;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'carry_forward')) {
                    $x->col_5 = $item->CarryForward;
                }else {
                    $x->col_5 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->name;
                $x->col_3 = $item->username;
                $x->col_4 = $item->email;
                $x->col_5 = $item->CarryForward;
                
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
            'D' => 35,
            'E' => 15,
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
