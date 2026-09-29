<?php

namespace  Modules\Contact\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class ContactExports implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles, WithEvents
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
            2    => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function collection()
    {
        $given_data = $this->data;
        
        $items = $given_data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            if ($given_data['type'] != "supplier") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'customer_list')->first();
            } else {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'supplier_list')->first();
            }
            
        }else {
            $permissions = null;
        }

        $new_array = collect();
        
        $x = new \stdClass();
        $x->col_1 = $given_data['type'] == "supplier" ? "SUPPLIER LIST" : "CUSTOMER LIST";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $new_array->push($x);
        
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "CONTACT ID";
        $x->col_3 = "NAME";
        $x->col_4 = "EMAIL";
        $x->col_5 = "MOBILE";
        $x->col_6 = "PAY TERM";
        $x->col_7 = "TAX NUMBER";
        $new_array->push($x);

        foreach ($items as $key => $item) {
            $payterm_1 = $item->pay_term ? $item->pay_term : 0;
            $payterm_2 = $item->pay_term_condition ? $item->pay_term_condition : '';
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                }else {
                    $x->col_1 = "";
                }
                if (str_contains($permissions->export_column, 'contact_id')) {
                    $x->col_2 = $item->contact_id;
                }else {
                    $x->col_2 = "";
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_3 = $item->name;
                }else {
                    $x->col_3 = "";
                }
                if (str_contains($permissions->export_column, 'email')) {
                    $x->col_4 = $item->email;
                }else {
                    $x->col_4 = "";
                }
                if (str_contains($permissions->export_column, 'mobile')) {
                    $x->col_5 = $item->mobile;
                }else {
                    $x->col_5 = "";
                }
                if (str_contains($permissions->export_column, 'pay_term_condition')) {
                    $x->col_6 = $payterm_1.' '.$payterm_2;
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'tax_number')) {
                    $x->col_7 = $item->tax_number;
                }else {
                    $x->col_7 = "";
                }
                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $item->contact_id;
                $x->col_3 = $item->name;
                $x->col_4 = $item->email;
                $x->col_5 = $item->mobile;
                $x->col_6 = $payterm_1.' '.$payterm_2;
                $x->col_7 = $item->tax_number;
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
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->mergeCells('A1:G1');
            },
        ];
    }
}
