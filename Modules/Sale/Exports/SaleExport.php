<?php

namespace  Modules\Sale\Exports;
use Modules\Sale\Entities\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class SaleExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;

    protected $type, $is_draft, $is_approved, $data;

    function __construct($type, $is_draft, $is_approved, $data) {
        $this->type = $type;
        $this->is_draft = $is_draft;
        $this->is_approved = $is_approved;
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
        $sale_type = $this->type;
        $draft_or_not = $this->is_draft;
        $approved = $this->is_approved;
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            if ($sale_type == 2) {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'pos_sale_list')->first();
            }
            if ($sale_type == 1) {
                if ($approved == "not_approved") {
                    $permissions = auth()->user()->user_col_permissions->where('table_name', 'regular_sale_list')->first();
                } elseif ($approved == "approved") {
                    $permissions = auth()->user()->user_col_permissions->where('table_name', 'make_sale_rtn_list')->first();
                } else {
                    $permissions = auth()->user()->user_col_permissions->where('table_name', 'regular_sale_list')->first();
                }
            }
        }else {
            $permissions = null;
        }

        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Date";
        $x->col_3 = "Invoice No";
        $x->col_4 = "Sold By";
        $x->col_5 = "Customer";
        $x->col_6 = "Total Amount";
        $x->col_7 = "Paid Amount";
        $x->col_8 = "Due";
        $x->col_9 = "Is Approved";

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
                if (str_contains($permissions->export_column, 'user')) {
                    $x->col_4 = $item->user->name;
                }else {
                    $x->col_4 = "";
                }
                if (str_contains($permissions->export_column, 'customer_name')) {
                    $x->col_5 = ($item->customer_id != null) ? $item->customer->name : $item->agentuser->name;
                }else {
                    $x->col_5 = "";
                }
                if (str_contains($permissions->export_column, 'total_amount')) {
                    $x->col_6 = number_format($item->payable_amount, 2);
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'paid')) {
                    $x->col_7 = number_format($item->payments()->where('payment_type','pay')->sum('amount'), 2);
                }else {
                    $x->col_7 = "";
                }
                if (str_contains($permissions->export_column, 'due')) {
                    $x->col_8 = ($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount') > 0) ? number_format($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount'), 2) : number_format(0, 2);
                }else {
                    $x->col_8 = "";
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_9 = ($item->is_approved == 1) ? "Y" : "N";
                }else {
                    $x->col_9 = "";
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->date;
                $x->col_3 = $item->invoice_no;
                $x->col_4 = $item->user->name;
                $x->col_5 = ($item->customer_id != null) ? $item->customer->name : $item->agentuser->name;
                $x->col_6 = number_format($item->payable_amount, 2);
                $x->col_7 = number_format($item->payments()->where('payment_type','pay')->sum('amount'), 2);
                $x->col_8 = ($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount') > 0) ? number_format($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount'), 2) : number_format(0, 2);
                $x->col_9 = ($item->is_approved == 1) ? "Y" : "N";
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
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 15,
            'K' => 15,
            'L' => 20,
            'M' => 15,
        ];
    }
}
