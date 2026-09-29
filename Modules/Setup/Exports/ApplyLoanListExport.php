<?php

namespace Modules\Setup\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplyLoanListExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
{
    use Exportable;
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $items = $this->data['items'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'loan_list')->first();
        } else {
            $permissions = null;
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Date';
        $x->col_3 = 'User';
        $x->col_4 = 'Department';
        $x->col_5 = 'Type';
        $x->col_6 = 'Amount';
        $x->col_7 = 'Monthly Installment';
        $x->col_8 = 'Due';
        $x->col_9 = 'Status';
        $new_array->push($x);

        foreach ($items as $key => $item) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'date')) {
                    $x->col_2 = $item->loan_date;
                } else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'user')) {
                    $x->col_3 = $item->user->name;
                } else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'department')) {
                    $x->col_4 = $item->department->name;
                } else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'type')) {
                    $x->col_5 = $item->loan_type;
                } else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'amount')) {
                    $x->col_6 = single_price($item->amount);
                } else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'monthly_installment')) {
                    $x->col_7 = single_price($item->paid_loan_amount);
                } else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'due')) {
                    $x->col_8 = single_price($item->amount - $item->paid_loan_amount);
                } else {
                    $x->col_8 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    if ($item->approval == 0) {
                        $x->col_9 = __('common.Pending');
                    } elseif ($item->approval == 1) {
                        $x->col_9 = __('common.Approved');
                    } else {
                        $x->col_9 = __('common.Cancelled');
                    }
                } else {
                    $x->col_9 = '-';
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $item->loan_date;
                $x->col_3 = $item->user->name;
                $x->col_4 = $item->department->name;
                $x->col_5 = $item->loan_type;
                $x->col_6 = single_price($item->amount);
                $x->col_7 = single_price($item->paid_loan_amount);
                $x->col_8 = single_price($item->amount - $item->paid_loan_amount);
                if ($item->approval == 0) {
                    $x->col_9 = __('common.Pending');
                } elseif ($item->approval == 1) {
                    $x->col_9 = __('common.Approved');
                } else {
                    $x->col_9 = __('common.Cancelled');
                }
                $new_array->push($x);
            }
        }
        return $new_array;
    }

    public function map($item): array
    {
        return [
            $item->col_1,
            $item->col_2,
            $item->col_3,
            $item->col_4,
            $item->col_5,
            $item->col_6,
            $item->col_7,
            $item->col_8,
            $item->col_9,
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 25,
            'D' => 15,
            'E' => 15,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
