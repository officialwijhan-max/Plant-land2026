<?php

namespace  Modules\Sale\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class SaleReturnExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
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
            1    => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'return_sale_list')->first();
        }else {
            $permissions = null;
        }
        
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Invoice";
        $x->col_3 = "Branch";
        $x->col_4 = "Biller";
        $x->col_5 = "Customer";
        $x->col_6 = "Quantity";
        $x->col_7 = "Total Amount";
        $x->col_8 = "Return Amount";
        $x->col_9 = "Status";
    
        $new_array->push($x);
        

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
    
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = "";
                }
                if (str_contains($permissions->export_column, 'invoice')) {
                    $x->col_2 = @$item->invoice_no;
                }else {
                    $x->col_2 = "";
                }
                if (str_contains($permissions->export_column, 'branch')) {
                    $x->col_3 = @$item->saleable->name;
                }else {
                    $x->col_3 = "";
                }
                if (str_contains($permissions->export_column, 'biller')) {
                    $x->col_4 = @$item->user->name;
                }else {
                    $x->col_4 = "";
                }
                if (str_contains($permissions->export_column, 'customer_name')) {
                    $x->col_5 = @$item->customer->name;
                }else {
                    $x->col_5 = "";
                }
                if (str_contains($permissions->export_column, 'qty')) {
                    $x->col_6 = @$item->items->sum('return_quantity');
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'total_amount')) {
                    $x->col_7 = number_format($item->payable_amount,2);
                }else {
                    $x->col_7 = "";
                }
                if (str_contains($permissions->export_column, 'rtn_amount')) {
                    $x->col_8 = number_format(@$item->items->sum('return_amount'),2);
                }else {
                    $x->col_8 = "";
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_9 = ($item->status == 1) ? "P" : "A";
                }else {
                    $x->col_9 = "";
                }
    
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = @$item->invoice_no;
                $x->col_3 = @$item->saleable->name;
                $x->col_4 = @$item->user->name;
                $x->col_5 = @$item->customer->name;
                $x->col_6 = @$item->items->sum('return_quantity');
                $x->col_7 = number_format($item->payable_amount,2);
                $x->col_8 = number_format(@$item->items->sum('return_amount'),2);
                $x->col_9 = ($item->status == 1) ? "P" : "A";
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
         ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 15,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 40,
            'H' => 20,
            'I' => 25,
            'J' => 15,
            'K' => 15,
            'L' => 20,
            'M' => 15,
        ];
    }
}
