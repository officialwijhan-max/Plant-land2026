<?php

namespace Modules\Purchase\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseReturnExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'purchase_return')->first();
        } else {
            $permissions = null;
        }

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "DATE";
        $x->col_3 = "SUPPLIER";
        $x->col_4 = "INVOICE NO";
        $x->col_5 = "RETURN QTY";
        $x->col_6 = "RETURN AMOUNT";
        $x->col_7 = "IS APPROVED";

        $new_array->push($x);


        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();

                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = "";
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->date;
                } else {
                    $x->col_2 = "";
                }
                if (str_contains($permissions->export_column, 'supplier_name')) {
                    $x->col_3 = $item->supplier->name;
                } else {
                    $x->col_3 = "";
                }
                if (str_contains($permissions->export_column, 'invoice_no')) {
                    $x->col_4 = $item->invoice_no;
                } else {
                    $x->col_4 = "";
                }
                if (str_contains($permissions->export_column, 'return_qty')) {
                    $x->col_5 = @$item->items->sum('return_quantity');
                } else {
                    $x->col_5 = "";
                }
                if (str_contains($permissions->export_column, 'return_amount')) {
                    $x->col_6 = number_format($item->items->sum('return_amount'), 2);
                } else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_7 = ($item->status == 1) ? "Y" : "N";
                } else {
                    $x->col_7 = "";
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->supplier->name;
                $x->col_4 = $item->invoice_no;
                $x->col_5 = @$item->items->sum('return_quantity');
                $x->col_6 = number_format($item->items->sum('return_amount'), 2);
                $x->col_7 = ($item->status == 1) ? "Y" : "N";
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
            $row->col_5,
            $row->col_6,
            $row->col_7,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 15,
            'D' => 20,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 15,
            'I' => 25,
            'J' => 15,
            'K' => 15,
            'L' => 20,
            'M' => 15,
        ];
    }
}
