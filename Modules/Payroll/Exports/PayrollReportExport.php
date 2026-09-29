<?php

namespace Modules\Payroll\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PayrollReportExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
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
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'payroll_report_list')->first();
        } else {
            $permissions = null;
        }

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'STAFF';
        $x->col_3 = 'STAFF ID';
        $x->col_4 = 'ROLE';
        $x->col_5 = 'MONTH - YEAR';
        $x->col_6 = 'BASIC SALARY';
        $x->col_7 = 'GROSS SALARY';
        $x->col_8 = 'EARNINGS';
        $x->col_9 = 'DEDUCTIONS';
        $x->col_10 = 'TAX';
        $x->col_11 = 'PAID DATE';
        $x->col_12 = 'NET SALARY';

        $new_array->push($x);

        foreach ($items as $key => $payroll) {
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'employee')) {
                    $x->col_2 = $payroll->staff->user->name;
                } else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'staff_id')) {
                    $x->col_3 = $payroll->staff->employee_id;
                } else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'role')) {
                    $x->col_4 = $payroll->role->name;
                } else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'month')) {
                    $x->col_5 = $payroll->payroll_month . ' - ' . $payroll->payroll_year;
                } else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'basic_salary')) {
                    $x->col_6 = single_price($payroll->basic_salary);
                } else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'gross_salary')) {
                    $x->col_7 = single_price($payroll->gross_salary);
                } else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'earnings')) {
                    $x->col_8 = single_price($payroll->total_earning);
                } else {
                    $x->col_8 = '-';
                }
                if (str_contains($permissions->export_column, 'deductions')) {
                    $x->col_9 = single_price($payroll->total_deduction);
                } else {
                    $x->col_9 = '-';
                }
                if (str_contains($permissions->export_column, 'tax')) {
                    $x->col_10 = single_price($payroll->tax);
                } else {
                    $x->col_10 = '-';
                }
                if (str_contains($permissions->export_column, 'paid_date')) {
                    $x->col_11 = ($payroll->payment_date != null) ? showDate($payroll->payment_date) : "X";
                } else {
                    $x->col_11 = '-';
                }
                if (str_contains($permissions->export_column, 'net_salary')) {
                    $x->col_12 = single_price($payroll->net_salary);
                } else {
                    $x->col_12 = '-';
                }
                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $payroll->staff->user->name;
                $x->col_3 = $payroll->staff->employee_id;
                $x->col_4 = $payroll->role->name;
                $x->col_5 = $payroll->payroll_month . ' - ' . $payroll->payroll_year;
                $x->col_6 = single_price($payroll->basic_salary);
                $x->col_7 = single_price($payroll->gross_salary);
                $x->col_8 = single_price($payroll->total_earning);
                $x->col_9 = single_price($payroll->total_deduction);
                $x->col_10 = single_price($payroll->tax);
                $x->col_11 = ($payroll->payment_date != null) ? showDate($payroll->payment_date) : "X";
                $x->col_12 = single_price($payroll->net_salary);
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
            $item->col_10,
            $item->col_11,
            $item->col_12
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
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
