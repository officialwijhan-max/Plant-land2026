<?php

namespace Modules\Payroll\Repositories;
use Modules\Account\Repositories\JournalRepository;
use Modules\Leave\Entities\ApplyLeave;
use Modules\Attendance\Entities\Attendance;
use Modules\Payroll\Entities\PayrollEarnDeduce;
use Modules\Payroll\Entities\Payroll;
use Carbon\Carbon;
use Modules\Setup\Entities\ApplyLoan;
use App\User;
use App\Staff;
use DateTime;
use Auth;
use DB;
use Modules\Account\Entities\ChartAccount;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\Account\Repositories\VoucherRepository;
use Modules\Account\Repositories\JournalRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\Payroll\Exports\PayrollExport;
use update\Modules\Payroll\Exports\PayrollReportExport;

class PayrollRepository implements PayrollRepositoryInterface
{
    protected $journalRepository, $voucherRepository;

    public function __construct(JournalRepositoryInterface $journalRepository, VoucherRepository $voucherRepository)
    {
        $this->journalRepository = $journalRepository;
        $this->voucherRepository = $voucherRepository;
    }

    public function csvUserPaginateDownload($data)
    {
        if (file_exists(public_path("uploads/csv/payrolls.xlsx"))) {
            unlink(public_path("uploads/csv/payrolls.xlsx"));
        }
        return Excel::store(new PayrollExport($data), 'uploads/csv/payrolls.xlsx', 'public_folder');
    }

    public function csvPayrollReportsDownload($data)
    {
        if (file_exists(public_path("uploads/csv/payroll_reports.xlsx"))) {
            unlink(public_path("uploads/csv/payroll_reports.xlsx"));
        }
        return Excel::store(new PayrollReportExport($data), 'uploads/csv/payroll_reports.xlsx', 'public_folder');
    }

    public function userPaginate($row_count,$quick_search,$name,$sort,$column,array $data, $relational_data = [], $selected_data = ['*'])
    {
        $items = User::query();
        $items = $items->with($relational_data)->where('role_id', $data['role_id']);

        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        $items = $items;
        if ($row_count == "all") {
            $total_number = User::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPayrollReportsPaginate($row_count,$quick_search,$name,$sort,$column,array $data, $relational_data = [], $selected_data = ['*'])
    {
        $items = Payroll::query();
        $items = $items->with($relational_data)->where('payroll_month', $data['month'])->where('payroll_year', $data['year'])->where('role_id', $data['role_id']);

        if ($quick_search != null) {
            $items = $items->whereLike(['basic_salary','total_earning','gross_salary','net_salary','total_deduction'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Payroll::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->paginate($row_count, $selected_data);
            }
        }
    }

    public function create(array $data)
    {
        $payrollGenerate = new Payroll();
        $payrollGenerate->staff_id = $data['staff_id'];
        $payrollGenerate->role_id = $data['role_id'];
        $payrollGenerate->payroll_month = $data['payroll_month'];
        $payrollGenerate->payroll_year = $data['payroll_year'];
        $payrollGenerate->basic_salary = $data['basic_salary'];
        $payrollGenerate->total_earning = $data['total_earnings'];
        $payrollGenerate->total_deduction = $data['total_deduction'];
        $payrollGenerate->gross_salary = $data['final_gross_salary'];
        $payrollGenerate->tax = $data['tax'];
        $payrollGenerate->net_salary = $data['net_salary'];
        $payrollGenerate->payroll_status = 'G';
        $result = $payrollGenerate->save();
        $payrollGenerate->toArray();
        if (!empty($data['loan_id']) && !empty($data['loanStatus'])) {
            foreach ($data['loan_id'] as $key => $loan_id) {
                $loan = ApplyLoan::findOrFail($loan_id);
                if ($loan->amount > $loan->paid_loan_amount) {

                    $loan->paid_loan_amount += $data['deductionsValue'][$key];
                    if ($loan->amount == $loan->paid_loan_amount) {
                        $loan->paid = 1;
                        $loan->save();
                    }else {
                        $loan->save();
                    }
                }
            }

        }
        if ($result) {
            $earnings = count($data['earningsType']);
            for ($i = 0; $i < $earnings; $i++) {
                if (!empty($data['earningsType'][$i]) && !empty($data['earningsValue'][$i])) {
                    $payroll_earn_deducs = new PayrollEarnDeduce;
                    $payroll_earn_deducs->payroll_id = $payrollGenerate->id;
                    $payroll_earn_deducs->type_name = $data['earningsType'][$i];
                    $payroll_earn_deducs->amount = $data['earningsValue'][$i];
                    $payroll_earn_deducs->earn_dedc_type = 'E';
                    $result = $payroll_earn_deducs->save();
                }
            }
            if ($data['deductionsValue'][0] != null) {
                $deductions = count($data['deductionstype']);
                for ($i = 0; $i < $deductions; $i++) {
                    if (!empty($data['deductionstype'][$i]) && !empty($data['deductionsValue'][$i])) {
                        $payroll_earn_deducs = new PayrollEarnDeduce;
                        $payroll_earn_deducs->payroll_id = $payrollGenerate->id;
                        $payroll_earn_deducs->type_name = $data['deductionstype'][$i];
                        $payroll_earn_deducs->amount = $data['deductionsValue'][$i];
                        $payroll_earn_deducs->earn_dedc_type = 'D';
                        if (!empty($data['loanStatus']) && array_key_exists($i , $data['loanStatus'])) {
                            $payroll_earn_deducs->loan_status = 1;
                        }
                        $result = $payroll_earn_deducs->save();
                    }
                }
            }
        }
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $this->generateJournalForPayrollGenerate($payrollGenerate);
        }
    }

    public function find($id)
    {
        return Payroll::find($id);
    }

    public function userFind($id)
    {
        return User::find($id);
    }

    public function attendance($id, $payroll_month, $payroll_year)
    {
        return Attendance::where('user_id', $id)->where('month', $payroll_month)->where('year', $payroll_year)->get();
    }

    public function leaveApprove($id)
    {
        return ApplyLeave::where('status', 1)->where('user_id', $id)->get();
    }

    public function user(array $data)
    {
        return User::with('staff.department','loans')->where('role_id', $data['role_id'])->get();
    }

    public function savePayrollPaymentData(array $data)
    {
        $payments = Payroll::find($data['payroll_generate_id']);
        $payments->payment_date = Carbon::parse($data['payment_date'])->format('Y-m-d');
        $payments->payment_mode = $data['payment_mode'];
        $payments->note = $data['note'];

        if (array_key_exists('bank_name', $data)) {
            $payments->bank_name = $data['bank_name'];
            $payments->bank_branch_name = $data['bank_branch_name'];
            $payments->account_no = $data['account_no'];
        }

        if (array_key_exists('cheque_no', $data)) {
            $payments->cheque_no = $data['cheque_no'];
        }

        $payments->payroll_status = 'P';
        $result = $payments->update();
        $user = Staff::findOrFail($payments->staff_id)->user;

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            $main_amount = $payments->net_salary;
            $credit_account_id_2[] = $data['payment_mode'];
            $credit_sub_account_id_2[] = 0;
            $credit_account_amount_2[] = $main_amount;
            $credit_cash_flow_account_id_2[] = Settings('default_salary_cash_flow_account');
            $credit_narration_2[] = 'Pay Salary for - '. $payments->payroll_month.', '.$payments->payroll_year;

            $debit_account_id_2[] = Settings('leadger_account_for_employee');
            $debit_sub_account_id_2[] = $payments->staff->morph->id;
            $debit_cash_flow_account_id_2[] = 0;
            $debit_amount_2[] = $main_amount;
            $debit_narration_2[] = 'Pay Salary for - '. $payments->payroll_month.', '.$payments->payroll_year;

            $journalRepository = new ProJournalRepository();
            $voucher = $journalRepository->create([
                'type' => 'pay',
                'amount'=> $main_amount,
                'is_cash_flow_journal' => 0,
                'date'=> Carbon::parse($data['payment_date'])->format('Y-m-d'),
                'debit_account_id'=> $debit_account_id_2,
                'debit_sub_account_id'=> $debit_sub_account_id_2,
                'debit_account_amount'=> $debit_amount_2,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id_2,
                'debit_narration'=> $debit_narration_2,
                'narration_voucher'=> ($data['note']) ? $data['note'] : 'Pay Salary for - '. $payments->payroll_month.', '.$payments->payroll_year,
                'referable_type'=> get_class($payments),
                'referable_id'=> $payments->id,

                'credit_account_id'=> $credit_account_id_2,
                'credit_sub_account_id'=> $credit_sub_account_id_2,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id_2,
                'credit_account_amount'=> $credit_account_amount_2,
                'credit_narration'=> $credit_narration_2,
                'sale_or_purchase' => "payroll_done",
                'is_approve' => 1,
            ]);
        } else {
            $main_amount = $payments->basic_salary;
            $repo = new JournalRepository();

            if (count($payments->payroll_earn_deducs) > 0) {
                $debit_account_id[] = ChartAccount::where('code', '01-01-02')->first()->id; //Cash Account
                $debit_account_amount[] = $payments->net_salary;
                $narration[] = 'Salary Pay';

                foreach ($payments->payroll_earn_deducs as $earn_deduc) {
                    if ($earn_deduc->loan_status == 1 && $earn_deduc->earn_dedc_type == "D") {
                        $debit_account_id[] = ChartAccount::where('contactable_id', $user->id)->where('contactable_type', get_class(new User))->first()->id;
                        $debit_account_amount[] = $earn_deduc->amount;
                        $narration[] = $earn_deduc->name;
                    }elseif ($earn_deduc->earn_dedc_type == "D") {
                        $main_amount -= $earn_deduc->amount;
                    }elseif ($earn_deduc->earn_dedc_type == "E") {
                        $main_amount += $earn_deduc->amount;
                    }
                }
                $repo->create([
                    'voucher_type' => 'JV',
                    'amount'=> $main_amount,
                    'date'=> Carbon::now()->format('Y-m-d'),
                    'account_type'=> 'debit',
                    'payment_type' => 'journal_voucher',
                    'account_id'=> ChartAccount::where('code', '03-18')->first()->id,  //Salary & Allowance
                    'main_amount'=> $main_amount,  //debit side and credit side shoud be same
                    'narration'=> 'Staff Salary',  //debit side and credit side shoud be same

                    'sub_account_id'=> $debit_account_id,   //debit side and credit side shoud be same
                    'sub_amount'=> $debit_account_amount,
                    'sub_narration'=> $narration,
                    'is_approve' => (app('business_settings')->where('type', 'payroll_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);
            }
            else {

                $debit_account_id[] = ChartAccount::where('code', "03-18")->first()->id; //Salary & Allowance
                $debit_account_amount[] = $payments->net_salary;
                $narration[] = 'Salary Pay';
                $chart_account = ChartAccount::where('code', '01-01-02')->first()->id; //Cash Account

                $repo->create([
                    'voucher_type' => 'JV',
                    'amount'=> $payments->basic_salary,
                    'date'=> Carbon::now()->format('Y-m-d'),
                    'account_type'=> 'credit',
                    'payment_type' => 'journal_voucher',
                    'account_id'=> $chart_account,  //here will be changed
                    'main_amount'=> $payments->basic_salary,  //debit side and credit side shoud be same
                    'narration'=> 'Staff Salary',  //debit side and credit side shoud be same

                    'sub_account_id'=> $debit_account_id,   //debit side and credit side shoud be same
                    'sub_amount'=> $debit_account_amount,
                    'sub_narration'=> $narration,
                    'is_approve' => (app('business_settings')->where('type', 'payroll_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);

            }
        }

        return $payments;
    }

    private function generateJournalForPayrollGenerate($payments)
    {
        $loan_amount = $payments->payroll_earn_deducs->where('loan_status', 1)->sum('amount');
        $main_total_amount = $payments->net_salary + $loan_amount + $payments->tax;
        // Generate Payroll Voucher
        $debit_account_id[] = $payments->staff->morph->leadger_id;
        $debit_sub_account_id[] =$payments->staff->morph->id;
        $debit_account_amount[] = $payments->net_salary;
        $debit_cash_flow_account_id[] = 0;
        $debit_narration[] = 'Monthly Payroll - '. $payments->payroll_month.', '.$payments->payroll_year;

        if (count($payments->payroll_earn_deducs->where('loan_status', 1)) > 0) {
            $debit_account_id[] = Settings('advance_loan_and_accured_salary_account');
            $debit_sub_account_id[] = 0;
            $debit_account_amount[] = $loan_amount;
            $debit_cash_flow_account_id[] = Settings('default_loan_cash_flow_account');
            $debit_narration[] = 'Loan Deducted Purpose';
        }
        if ($payments->tax > 0) {
            $debit_account_id[] = Settings('employee_tax_ledger');
            $debit_sub_account_id[] = 0;
            $debit_account_amount[] = $payments->tax;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = 'Employee Payroll Tax Amount';
        }

        $credit_account_id[] = Settings('default_salary_expense_account');
        $credit_sub_account_id[] = 0;
        $credit_cash_flow_account_id[] = 0;
        $credit_amount[] = $main_total_amount;
        $credit_narration[] = 'Monthly Payroll - '. $payments->payroll_month.', '.$payments->payroll_year;

        $journalRepository = new ProJournalRepository();
        $voucher = $journalRepository->create([
            'type' => 'misc',
            'amount'=> $main_total_amount,
            'is_cash_flow_journal' => 0,
            'date'=> Carbon::now()->format('Y-m-d'),
            'credit_account_id'=> $debit_account_id,
            'credit_sub_account_id'=> $debit_sub_account_id,
            'credit_account_amount'=> $debit_account_amount,
            'credit_cash_flow_account_id'=> $debit_cash_flow_account_id,
            'credit_narration'=> $debit_narration,
            'narration_voucher'=> 'Monthly Payroll Generated - '. $payments->payroll_month.', '.$payments->payroll_year,
            'referable_type'=> get_class($payments),
            'referable_id'=> $payments->id,

            'debit_account_id'=> $credit_account_id,
            'debit_sub_account_id'=> $credit_sub_account_id,
            'debit_cash_flow_account_id'=> $credit_cash_flow_account_id,
            'debit_account_amount'=> $credit_amount,
            'debit_narration'=> $credit_narration,
            'sale_or_purchase' => "payroll_gen",
            'is_approve' => 1,
        ]);
    }


    public function payrollEarnDetails($id)
    {
        return PayrollEarnDeduce::where('active_status', '=', '1')->where('payroll_id', '=', $id)->where('earn_dedc_type', '=', 'E')->get();
    }

    public function payrollDedcDetails($id)
    {
        return PayrollEarnDeduce::where('active_status', '=', '1')->where('payroll_id', '=', $id)->where('earn_dedc_type', '=', 'D')->get();
    }

    public function payrollReports($role, $month, $year)
    {
        return Payroll::where('payroll_month', $month)->where('payroll_year', $year)->where('role_id', $role)->latest()->get();
    }

    public function userPayrollDetails($id)
    {
        return Payroll::where('staff_id', $id)->latest()->get();
    }

}
