<?php

namespace Modules\Inventory\Exports;
use Modules\Core\Entities\Product\ProductHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class ProductMovementExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
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
            1    => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'movement_list_product')->first();
        }else {
            $permissions = null;
        }
        
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Showroom or WareHouse";
        $x->col_3 = "Purpose";
        $x->col_4 = "Product Name";
        $x->col_5 = "Quantity";
        $x->col_6 = "Date";
        $x->col_7 = "Created User";
    
        $new_array->push($x);
        

        foreach ($items as $key => $item) {
            if ($item->type == "transferred" || $item->type == "Recieve Transfer Item") {
                $itemable_name = "( ".$item->itemable->name." )";
            }else {
                $itemable_name = "";
            }
            if ($permissions) {
                $x = new \stdClass();
    
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = "-";
                }
                if (str_contains($permissions->export_column, 'showroom_or_wareHouse')) {
                    $x->col_2 = (@$item->houseable->saleable->name) ? @$item->houseable->saleable->name : @$item->itemable->name;
                }else {
                    $x->col_2 = "-";
                }
                if (str_contains($permissions->export_column, 'purpose')) {
                    $x->col_3 = strtoupper(str_replace('_', ' ', $item->type)).' '.$itemable_name;
                }else {
                    $x->col_3 = "-";
                }
                if (str_contains($permissions->export_column, 'product_name')) {
                    $x->col_4 = @$item->productSku->product->product_name;
                }else {
                    $x->col_4 = "-";
                }
                if (str_contains($permissions->export_column, 'quantity')) {
                    $x->col_5 = $item->in_out;
                }else {
                    $x->col_5 = "-";
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_6 = $item->date;
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'created_user')) {
                    $x->col_7 = userName($item->created_by);
                }else {
                    $x->col_7 = "-";
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = (@$item->houseable->saleable->name) ? @$item->houseable->saleable->name : @$item->itemable->name;
                $x->col_3 = strtoupper(str_replace('_', ' ', $item->type)).' '.$itemable_name;
                $x->col_4 = @$item->productSku->product->product_name;
                $x->col_5 = $item->in_out;
                $x->col_6 = $item->date;
                $x->col_7 = userName($item->created_by);
                
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
            'B' => 25,
            'C' => 17,
            'D' => 25,
            'E' => 13,
            'F' => 13,
            'G' => 15,
            'H' => 15,
            'I' => 25,
            'J' => 15,
            'K' => 15,
            'L' => 20,
            'M' => 15,
        ];
    }
}
