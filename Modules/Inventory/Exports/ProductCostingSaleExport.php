<?php

namespace Modules\Inventory\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductCostingSaleExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'product_costing_sales')->first();
        } else {
            $permissions = null;
        }

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Invoice No";
        $x->col_3 = "Address";
        $x->col_4 = "Product Name";
        $x->col_5 = "Previous Stock";
        $x->col_6 = "Newly Added Stock";
        $x->col_7 = "Last Costing Price (Unit)";
        $x->col_8 = "New Costing Price (Unit)";

        $new_array->push($x);


        foreach ($items as $key => $cost_of_goods) {
            if ($permissions) {
                $x = new \stdClass();

                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = "-";
                }
                if (str_contains($permissions->export_column, 'invoice_no')) {
                    $x->col_2 = ($cost_of_goods->costable->invoice_no) ? $cost_of_goods->costable->invoice_no : "Begining";
                } else {
                    $x->col_2 = "-";
                }
                if (str_contains($permissions->export_column, 'address')) {
                    $x->col_3 = @$cost_of_goods->storeable->name;
                } else {
                    $x->col_3 = "-";
                }
                if (str_contains($permissions->export_column, 'product_name')) {
                    $x->col_4 = @$cost_of_goods->productSku->product->product_name;
                } else {
                    $x->col_4 = "-";
                }
                if (str_contains($permissions->export_column, 'previous_stock')) {
                    $x->col_5 = $cost_of_goods->previous_remaining_stock;
                } else {
                    $x->col_5 = "-";
                }
                if (str_contains($permissions->export_column, 'newly_added_stock')) {
                    $x->col_6 = $cost_of_goods->newly_stock;
                } else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'last_costing_price_')) {
                    $x->col_7 = single_price($cost_of_goods->previous_cost_of_goods_sold) . ' /' . @$cost_of_goods->productSku->product->unit_type->name;
                } else {
                    $x->col_7 = "-";
                }
                if (str_contains($permissions->export_column, 'new_costing_price_')) {
                    $x->col_8 = single_price($cost_of_goods->new_cost_of_goods_sold) . ' /' . @$cost_of_goods->productSku->product->unit_type->name;
                } else {
                    $x->col_8 = "-";
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = ($cost_of_goods->costable->invoice_no) ? $cost_of_goods->costable->invoice_no : "Begining";
                $x->col_3 = @$cost_of_goods->storeable->name;
                $x->col_4 = @$cost_of_goods->productSku->product->product_name;
                $x->col_5 = $cost_of_goods->previous_remaining_stock;
                $x->col_6 = $cost_of_goods->newly_stock;
                $x->col_7 = single_price($cost_of_goods->previous_cost_of_goods_sold) . ' /' . @$cost_of_goods->productSku->product->unit_type->name;
                $x->col_8 = single_price($cost_of_goods->new_cost_of_goods_sold) . ' /' . @$cost_of_goods->productSku->product->unit_type->name;
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
