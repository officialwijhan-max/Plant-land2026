<?php

namespace Modules\Inventory\Exports;
use Modules\Purchases\Entities\StockTransfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockTransferedExport implements FromCollection, WithMapping, WithColumnWidths, WithCustomStartCell, WithStyles
{
    use Exportable;
    protected $type;
    protected $data;

    function __construct($type,$data) {
        $this->type = $type;
        $this->data = $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 13]],
            2    => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function collection()
    {
        $type_check = $this->type;

        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            if ($type_check == "rcv") {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'product_transfer_rcv_list')->first();
            } else {
                $permissions = auth()->user()->user_col_permissions->where('table_name', 'product_sent_list')->first();
            }
        }else {
            $permissions = null;
        }


        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = ($type_check == "sent") ? "Sent Transferred Products" : "Recieve Transfer Product";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";

        $new_array->push($x);


        $x = new \stdClass();
        $x->col_1 = "SL";
        $x->col_2 = "Date";
        $x->col_3 = "From";
        $x->col_4 = "To";
        $x->col_5 = "QTY";
        $x->col_6 = "Total Amount";
        $x->col_7 = "Status";

        $new_array->push($x);


        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();

                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = "-";
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->date;
                }else {
                    $x->col_2 = "-";
                }
                if (str_contains($permissions->export_column, 'from')) {
                    $x->col_3 = @$item->sendable->name;
                }else {
                    $x->col_3 = "-";
                }
                if (str_contains($permissions->export_column, 'to')) {
                    $x->col_4 = @$item->receivable->name;
                }else {
                    $x->col_4 = "-";
                }
                if (str_contains($permissions->export_column, 'qty')) {
                    $x->col_5 = @$item->items->sum('quantity');
                }else {
                    $x->col_5 = "-";
                }
                if (str_contains($permissions->export_column, 'total_amount')) {
                    $x->col_6 = single_price(@$item->items->sum('sub_total'));
                }else {
                    $x->col_6 = "";
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_7 = ($item->status == 1) ? "Approved" : "Pending";
                }else {
                    $x->col_7 = "-";
                }

                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->date;
                $x->col_3 = @$item->sendable->name;
                $x->col_4 = @$item->receivable->name;
                $x->col_5 = @$item->items->sum('quantity');
                $x->col_6 = single_price(@$item->items->sum('sub_total'));
                $x->col_7 = ($item->status == 1) ? "Approved" : "Pending";
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
        ];
    }
}
