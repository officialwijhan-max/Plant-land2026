<?php

namespace  Modules\Quotation\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class QuotationExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
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
        $given_data = $this->data;
        $items = $given_data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'quotation_list')->first();
        }else {
            $permissions = null;
        }
        
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Date";
        $x->col_3 = "Invoice No";
        $x->col_4 = "Customer";
        $x->col_5 = "Branch";
        $x->col_6 = "Sold By";
        $x->col_7 = "Is Converted";
    
        $new_array->push($x);
        

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
    
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = "";
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->date;
                }else {
                    $x->col_2 = "";
                }
                if (str_contains($permissions->export_column, 'invoice_no')) {
                    $x->col_3 = $item->invoice_no;
                }else {
                    $x->col_3 = "";
                }
                if (str_contains($permissions->export_column, 'customer')) {
                    $x->col_4 = $item->customer->name;
                }else {
                    $x->col_4 = "";
                }
                if (str_contains($permissions->export_column, 'showroom')) {
                    $x->col_5 = $item->quotationable->name;
                }else {
                    $x->col_5 = "";
                }
                if (str_contains($permissions->export_column, 'user')) {
                    $x->col_6 = $item->user->name;
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'convert_status')) {
                    $x->col_7 = ($item->convert_status == 1) ? "Y" : "N";
                }else {
                    $x->col_7 = "";
                }
    
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->invoice_no;
                $x->col_4 = $item->customer->name;
                $x->col_5 = $item->quotationable->name;
                $x->col_6 = $item->user->name;
                $x->col_7 = ($item->convert_status == 1) ? "Y" : "N";
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
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 25,
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
