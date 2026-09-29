<?php

namespace Modules\Setup\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class LoanHistoryExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;

    function __construct() {
        // 
    }

    public function collection()
    {
        $items = User::with('role','staff')->whereHas('loans')->get();
        
        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'loan_history_list')->first();
        }else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Type';
        $x->col_3 = 'Name';
        $x->col_4 = 'Email';
        $x->col_5 = 'Phone';
        $x->col_6 = 'Role';
        $x->col_7 = 'Joining Date';
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
                    $x->col_2 = str_replace('_', ' ', @$item->role->type);
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_3 = $item->name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'email')) {
                    $x->col_4 = $item->email;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'phone')) {
                    $x->col_5 = @$item->staff->phone;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'role')) {
                    $x->col_6 = @$item->role->name;
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'registered_date')) {
                    $x->col_7 = showDate($item->created_at);
                }else {
                    $x->col_7 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = str_replace('_', ' ', @$item->role->type);
                $x->col_3 = $item->name;
                $x->col_4 = $item->email;
                $x->col_5 = @$item->staff->phone;
                $x->col_6 = @$item->role->name;
                $x->col_7 = showDate($item->created_at);

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
            'D' => 30,
            'E' => 15,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
