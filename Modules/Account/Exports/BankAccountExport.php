<?php

namespace Modules\Account\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankAccountExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'bank_account_list')->first();
        }else {
            $permissions = null;
        }

        $x = new \stdClass();
        $x->col_1 = 'SL';
        $x->col_2 = 'BANK NAME';
        $x->col_3 = 'BRANCH NAME';
        $x->col_4 = 'ACCOUNT NAME';
        $x->col_5 = 'ACCOUNT NUMBER';
        $x->col_6 = 'BALANCE';
        $x->col_7 = 'STATUS';
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
                    $x->col_2 = $item->bank_name;
                }else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'bank_branch_name')) {
                    $x->col_3 = $item->branch_name;
                }else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'account_name')) {
                    $x->col_4 = $item->account_name;
                }else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'account_num')) {
                    $x->col_5 = $item->account_no;
                }else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'balance')) {
                    $x->col_6 = number_format($item->BalanceAmount, 2);
                }else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    $x->col_7 = ($item->chartAccount->status == 1) ? __('common.Active') : __('common.DeActive');
                }else {
                    $x->col_7 = '-';
                }
                
                $new_array->push($x);
            }else {
                $x = new \stdClass();
                $x->col_1 = $key+1;
                $x->col_2 = $item->bank_name;
                $x->col_3 = $item->branch_name;
                $x->col_4 = $item->account_name;
                $x->col_5 = $item->account_no;
                $x->col_6 = number_format($item->BalanceAmount, 2);
                $x->col_7 = ($item->chartAccount->status == 1) ? __('common.Active') : __('common.DeActive');
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
            $transactions->col_7,
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
            'G' => 20,
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
