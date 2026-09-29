<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;
        $items = $given_data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'staff_list')->first();
        } else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Name';
        $x->col_3 = 'Email';
        $x->col_4 = 'Phone';
        $x->col_5 = 'Role';
        $x->col_6 = 'Status';
        $x->col_7 = 'Department';
        $x->col_8 = 'Showroom';
        $x->col_9 = 'Registered Date';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_2 = @$item->user->name;
                } else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'email')) {
                    $x->col_3 = @$item->user->email;
                } else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'phone')) {
                    $x->col_4 = @$item->phone;
                } else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'role')) {
                    $x->col_5 = @$item->user->role->name;
                } else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_6 = $item->user->is_active == 1 ? 'Active' : 'De-Active';
                } else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'department')) {
                    $x->col_7 = @$item->department->name;
                } else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'showroom')) {
                    $x->col_8 = @$item->showroom->name;
                } else {
                    $x->col_8 = '-';
                }
                if (str_contains($permissions->export_column, 'registered_date')) {
                    $x->col_9 = $item->created_at;
                } else {
                    $x->col_9 = '-';
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = @$item->user->name;
                $x->col_3 = @$item->user->email;
                $x->col_4 = @$item->phone;
                $x->col_5 = @$item->user->role->name;
                $x->col_6 = $item->user->is_active == 1 ? 'Active' : 'De-Active';
                $x->col_7 = @$item->department->name;
                $x->col_8 = @$item->showroom->name;
                $x->col_9 = $item->created_at;
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
            $item->col_9,
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
            'C' => 20,
            'D' => 30,
            'E' => 20,
            'F' => 20,
            'G' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 10,
            'K' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
