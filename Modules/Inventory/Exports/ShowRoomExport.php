<?php

namespace Modules\Inventory\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShowRoomExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles
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
        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'Branch';
        $x->col_3 = 'Address';
        $x->col_4 = 'Email';
        $x->col_5 = 'Phone';
        $x->col_6 = 'Status';
        $new_array->push($x);

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'showroom_list')->first();
        }else {
            $permissions = null;
        }

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
                if (str_contains($permissions->export_column, 'address')) {
                    $x->col_3 = $item->address;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'email')) {
                    $x->col_4 = $item->email;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'phone')) {
                    $x->col_5 = $item->phone;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_6 = ($item->status == 1) ? __('inventory.Active') : __('inventory.De-Active');
                }else {
                    $x->col_6 = '-';
                }
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $item->id;
                $x->col_2 = $item->name;
                $x->col_3 = $item->address;
                $x->col_4 = $item->email;
                $x->col_5 = $item->phone;
                $x->col_6 = ($item->status == 1) ? __('inventory.Active') : __('inventory.De-Active');
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($transactions): array
    {
        return [
            $transactions->col_1,
            $transactions->col_2,
            $transactions->col_3,
            $transactions->col_4,
            $transactions->col_5,
            $transactions->col_6,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 50,
            'D' => 25,
            'E' => 20,
            'F' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 11]],
            // 2    => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
