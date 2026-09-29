<?php

namespace Modules\Account\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;
    protected $data;
    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    {
        $given_data = $this->data;
        $items = $given_data['items'];

        $new_array = collect();

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'expense_list')->first();
        }else {
            $permissions = null;
        }

        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'BRANCH NAME';
        $x->col_3 = 'DATE';
        $x->col_4 = 'TXN ID';
        $x->col_5 = 'AMOUNT';
        $x->col_6 = 'APPROVED';
        $new_array->push($x);
        foreach ($items as $key => $item)
        {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key+1;
                }else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'name')) {
                    $x->col_2 = @$item->showroom->name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_3 = @$item->voucher->date;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'reference')) {
                    $x->col_4 = $item->voucher->tx_id;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'amount')) {
                    $x->col_5 = number_format($item->voucher->amount, 2);
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_6 = ($item->voucher->is_approve == 1) ? __('common.Approved') : __('common.Pending');
                }else {
                    $x->col_6 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = @$item->showroom->name;
                $x->col_3 = @$item->voucher->date;
                $x->col_4 = $item->voucher->tx_id;
                $x->col_5 = number_format($item->voucher->amount, 2);
                $x->col_6 = ($item->voucher->is_approve == 1) ? __('common.Approved') : __('common.Pending');
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($transactions): array
    {
        return [
            $transactions->col_1,
            $transactions->col_2,
            $transactions->col_3,
            $transactions->col_4,
            $transactions->col_5,
            $transactions->col_6,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 50,
            'D' => 25,
            'E' => 20,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
