<?php

namespace Modules\Inventory\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockAdjustmentExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
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
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'stock_adjustments')->first();
        } else {
            $permissions = null;
        }

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Date";
        $x->col_3 = "Showroom or WareHouse";
        $x->col_4 = "Reference No";
        $x->col_5 = "Recovery Amount";
        $x->col_6 = "Created User";
        $x->col_7 = "Updated BY";
        $x->col_8 = "Status";

        $new_array->push($x);


        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();

                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = "-";
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->date;
                } else {
                    $x->col_2 = "-";
                }
                if (str_contains($permissions->export_column, 'showroom_or_wareHouse')) {
                    $x->col_3 = $item->adjustable->name;
                } else {
                    $x->col_3 = "-";
                }
                if (str_contains($permissions->export_column, 'reference_no')) {
                    $x->col_4 = $item->ref_no;
                } else {
                    $x->col_4 = "-";
                }
                if (str_contains($permissions->export_column, 'recovery_amount')) {
                    $x->col_5 = single_price($item->recovery_amount);
                } else {
                    $x->col_5 = "-";
                }
                if (str_contains($permissions->export_column, 'created_user')) {
                    $x->col_6 = $item->createdByUser->name;
                } else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'updated_by')) {
                    $x->col_7 = $item->updatedByUser->name;
                } else {
                    $x->col_7 = "-";
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_8 = ($item->status != 1) ? "P" : "A";
                } else {
                    $x->col_8 = "-";
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->adjustable->name;
                $x->col_4 = $item->ref_no;
                $x->col_5 = single_price($item->recovery_amount);
                $x->col_6 = $item->createdByUser->name;
                $x->col_7 = $item->updatedByUser->name;
                $x->col_8 = ($item->status != 1) ? "P" : "A";

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
            $row->col_8,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 35,
            'D' => 20,
            'E' => 30,
            'F' => 15,
            'G' => 15,
            'H' => 15,
        ];
    }
}
