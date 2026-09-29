<?php

namespace Modules\Inventory\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockListExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;
    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true, 'size' => 11]],
            // 3    => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'stock_list')->first();
        }else {
            $permissions = null;
        }

        $new_array = collect();


        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Name";
        $x->col_3 = "SKU";
        $x->col_4 = "Brand";
        $x->col_5 = "Model";
        $x->col_6 = "Branch or WareHouse";
        $x->col_7 = "In Stock";
        $x->col_8 = "Stock Alert";
        $x->col_9 = "Purchase Price";
        $x->col_10 = "Selling Price";

        $new_array->push($x);


        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();

                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = "-";
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_2 = @$item->productSku->product->product_name;
                }else {
                    $x->col_2 = "-";
                }
                if (str_contains($permissions->export_column, 'sku')) {
                    $x->col_3 = @$item->productSku->sku;
                }else {
                    $x->col_3 = "-";
                }
                if (str_contains($permissions->export_column, 'brand')) {
                    $x->col_4 = @$item->productSku->product->brand->name;
                }else {
                    $x->col_4 = "-";
                }
                if (str_contains($permissions->export_column, 'model')) {
                    $x->col_5 = @$item->productSku->product->model->name;
                }else {
                    $x->col_5 = "-";
                }
                if (str_contains($permissions->export_column, 'showroom_or_wareHouse')) {
                    $x->col_6 = @$item->houseable->name;
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'in_stock')) {
                    $x->col_7 = @$item->stock;

                }else {
                    $x->col_7 = "-";
                }
                if (str_contains($permissions->export_column, 'stock_alert')) {
                    $x->col_8 = @$item->productSku->alert_quantity;
                }else {
                    $x->col_8 = "-";
                }
                if (str_contains($permissions->export_column, 'purchase_price')) {
                    $x->col_9 = single_price(@$item->productSku->purchase_price);
                }else {
                    $x->col_9 = "-";
                }
                if (str_contains($permissions->export_column, 'selling_price')) {
                    $x->col_10 = single_price(@$item->productSku->selling_price);
                }else {
                    $x->col_10 = "-";
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = @$item->productSku->product->product_name;
                $x->col_3 = @$item->productSku->sku;
                $x->col_4 = @$item->productSku->product->brand->name;
                $x->col_5 = @$item->productSku->product->model->name;
                $x->col_6 = @$item->houseable->name;
                $x->col_7 = @$item->stock;
                $x->col_8 = @$item->productSku->alert_quantity;
                $x->col_9 = single_price(@$item->productSku->purchase_price);
                $x->col_10 = single_price(@$item->productSku->selling_price);
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
            $row->col_9,
            $row->col_10,
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
            'I' => 15,
            'J' => 30,
            'K' => 30,
        ];
    }
}
