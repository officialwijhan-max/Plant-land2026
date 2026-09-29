<?php

namespace Modules\Payroll\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PayrollExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles, WithCustomStartCell
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
        $m = $this->data['m'];
        $y = $this->data['y'];

        if (count(auth()->user()->user_col_permissions) > 0) {
            $permissions = auth()->user()->user_col_permissions->where('table_name', 'payroll_list')->first();
        } else {
            $permissions = null;
        }

        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = 'ID';
        $x->col_2 = 'Staff';
        $x->col_3 = 'Staff ID';
        $x->col_4 = 'Department';
        $x->col_5 = 'Role';
        $x->col_6 = 'Phone';
        $x->col_7 = 'Basic Salary';
        $x->col_8 = 'Total Loan';
        $x->col_9 = 'Paid Loan Amount';
        $x->col_10 = 'Due Loan Amount';
        $x->col_11 = 'Status';

        $new_array->push($x);

        foreach ($items as $key => $user) {
            if ($user->staff) {
                $getPayrollDetails = $user->staff->payrolls->where('staff_id', $user->staff->id)->where('payroll_month', $m)->where('payroll_year', $y)->first();
            }
            if ($permissions) {
                $x = new \stdClass();
                if (str_contains($permissions->export_column, 'id')) {
                    $x->col_1 = $key + 1;
                } else {
                    $x->col_1 = '-';
                }
                if (str_contains($permissions->export_column, 'employee')) {
                    $x->col_2 = $user->name;
                } else {
                    $x->col_2 = '-';
                }
                if (str_contains($permissions->export_column, 'staff_id')) {
                    $x->col_3 = @$user->staff->employee_id;
                } else {
                    $x->col_3 = '-';
                }
                if (str_contains($permissions->export_column, 'department')) {
                    $x->col_4 = @$user->staff->department->name;
                } else {
                    $x->col_4 = '-';
                }
                if (str_contains($permissions->export_column, 'role')) {
                    $x->col_5 = @$user->role->name;
                } else {
                    $x->col_5 = '-';
                }
                if (str_contains($permissions->export_column, 'phone')) {
                    $x->col_6 = @$user->staff->phone;
                } else {
                    $x->col_6 = '-';
                }
                if (str_contains($permissions->export_column, 'basic_salary')) {
                    $x->col_7 = number_format(@$user->staff->basic_salary, 2);
                } else {
                    $x->col_7 = '-';
                }
                if (str_contains($permissions->export_column, 'total_loan')) {
                    $x->col_8 = number_format($user->LoanInfo['total_loan'], 2);
                } else {
                    $x->col_8 = '-';
                }
                if (str_contains($permissions->export_column, 'paid_loan_amount')) {
                    $x->col_9 = number_format($user->LoanInfo['total_paid'], 2);
                } else {
                    $x->col_9 = '-';
                }
                if (str_contains($permissions->export_column, 'due_loan_amount')) {
                    $x->col_10 = number_format($user->LoanInfo['total_due'], 2);
                } else {
                    $x->col_10 = '-';
                }
                if (str_contains($permissions->export_column, 'status')) {
                    if (!empty($getPayrollDetails)) {
                        if ($getPayrollDetails->payroll_status == 'G') {
                            $x->col_11 = __('payroll.Generated');
                        }
                        if ($getPayrollDetails->payroll_status == 'P') {
                            $x->col_11 = __('payroll.Paid');
                        }
                    } else {
                        $x->col_11 = __('payroll.Not generated');
                    }
                } else {
                    $x->col_11 = '-';
                }

                $new_array->push($x);
            } else {
                $x = new \stdClass();
                $x->col_1 = $key + 1;
                $x->col_2 = $user->name;
                $x->col_3 = @$user->staff->employee_id;
                $x->col_4 = @$user->staff->department->name;
                $x->col_5 = @$user->role->name;
                $x->col_6 = @$user->staff->phone;
                $x->col_7 = number_format(@$user->staff->basic_salary, 2);
                $x->col_8 = number_format($user->LoanInfo['total_loan'], 2);
                $x->col_9 = number_format($user->LoanInfo['total_paid'], 2);
                $x->col_10 = number_format($user->LoanInfo['total_due'], 2);
                if (!empty($getPayrollDetails)) {
                    if ($getPayrollDetails->payroll_status == 'G') {
                        $x->col_11 = __('payroll.Generated');
                    }
                    if ($getPayrollDetails->payroll_status == 'P') {
                        $x->col_11 = __('payroll.Paid');
                    }
                } else {
                    $x->col_11 = __('payroll.Not generated');
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
            $item->col_10,
            $item->col_11,
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
            'L' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2    => ['font' => ['bold' => true]]
        ];
    }
}
