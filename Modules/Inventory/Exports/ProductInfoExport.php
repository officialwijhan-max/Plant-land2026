<?php

namespace Modules\Inventory\Exports;
use Modules\Purchases\Entities\StockReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class ProductInfoExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;

    protected $data;

    function __construct($data) {
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'product_info_list')->first();
        }else {
            $permissions = null;
        }
        
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "NAME";
        $x->col_3 = "SKU";
        $x->col_4 = "MODEL";
        $x->col_5 = "BRAND";
        $x->col_6 = "IN STOCK";
        $x->col_7 = "PURCHASE PRICE";
        $x->col_8 = "SELL PRICE";
    
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
                if (str_contains($permissions->export_column, 'SKU')) {
                    $x->col_3 = @$item->productSku->sku;
                }else {
                    $x->col_3 = "-";
                }
                if (str_contains($permissions->export_column, 'model')) {
                    $x->col_4 = @$item->productSku->product->model->name;
                }else {
                    $x->col_4 = "-";
                }
                if (str_contains($permissions->export_column, 'brand')) {
                    $x->col_5 = @$item->productSku->product->brand->name;
                }else {
                    $x->col_5 = "-";
                }
                if (str_contains($permissions->export_column, 'in_stock')) {
                    $x->col_6 = @$item->stock.' '.@$item->productSku->product->unit_type->name;
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'purchase_price')) {
                    $x->col_7 = single_price(@$item->productSku->cost_of_goods * $item->stock);
                }else {
                    $x->col_7 = "-";
                }
                if (str_contains($permissions->export_column, 'sell_price')) {
                    $x->col_8 = single_price(@$item->productSku->selling_price * $item->stock);
                }else {
                    $x->col_8 = "-";
                }
    
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = @$item->productSku->product->product_name;
                $x->col_3 = @$item->productSku->sku;
                $x->col_4 = @$item->productSku->product->model->name;
                $x->col_5 = @$item->productSku->product->brand->name;
                $x->col_6 = @$item->stock.' '.@$item->productSku->product->unit_type->name;
                $x->col_7 = single_price(@$item->productSku->cost_of_goods * $item->stock);
                $x->col_8 = single_price(@$item->productSku->selling_price * $item->stock);
                
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
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 25,
            'H' => 25,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
        ];
    }
}
