<?php

namespace Modules\Setup\Repositories;

use Modules\Setup\Entities\ApplyLoan;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Repositories\VoucherRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setup\Exports\ApplyLoanListExport;
use Modules\Setup\Exports\ApplyLoanListApprovalExport;
use Modules\Setup\Exports\LoanHistoryExport;

class ApplyLoanRepository implements  ApplyLoanRepositoryInterface
{

    public function all()
    {
        return ApplyLoan::where('user_id', Auth::id())->orWhere('created_by',Auth::id())->latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/loan_list.xlsx"))) {
            unlink(public_path("uploads/csv/loan_list.xlsx"));
        }
        return Excel::store(new ApplyLoanListExport($data), 'uploads/csv/loan_list.xlsx', 'public_folder');
    }

    public function csvDownloadApplied($data)
    {
        if (file_exists(public_path("uploads/csv/applied_loan_list.xlsx"))) {
            unlink(public_path("uploads/csv/applied_loan_list.xlsx"));
        }
        return Excel::store(new ApplyLoanListApprovalExport($data), 'uploads/csv/applied_loan_list.xlsx', 'public_folder');
    }

    public function csvDownloadHistoryLoan($data)
    {
        if (file_exists(public_path("uploads/csv/loan_history_list.xlsx"))) {
            unlink(public_path("uploads/csv/loan_history_list.xlsx"));
        }
        return Excel::store(new LoanHistoryExport($data), 'uploads/csv/loan_history_list.xlsx', 'public_folder');
    }

    public function withPaginateHistoryLoan($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = User::query();
        $items = $items->with($relational_data)->whereHas('loans');
        if ($quick_search != null) {
            $items->whereLike(['name'], $quick_search);
        }
        $items = $items;
        if ($row_count == "all") {
            $total_number = User::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = ApplyLoan::query();
        $items = $items->with($relational_data)->where('user_id', auth()->user()->id)->orWhere('created_by', auth()->user()->id);
        if ($quick_search != null) {
            $items = $items->whereHas('user', function ($q) use ($quick_search) {
                $q->whereLike(['name'], $quick_search);
            });
        }
        $items = $items;
        if ($row_count == "all") {
            $total_number = ApplyLoan::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginateApplied($row_count, $quick_search, $name, $sort, $column, $is_paid_to_employee = null, $relational_data = [], $selected_data = ['*'])
    {
        $items = ApplyLoan::query();
        $items = $items->with($relational_data);
        if ($quick_search != null) {
            $items = $items->whereHas('user', function ($q) use ($quick_search) {
                $q->whereLike(['name'], $quick_search);
            });
        }
        if ($row_count == "all") {
            $total_number = ApplyLoan::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->paginate($row_count, $selected_data);
            }
        }
    }

    public function appliedAll()
    {
        return ApplyLoan::with('user','department')->latest()->get();
    }

    public function staffLoans($id)
    {
        return ApplyLoan::where('user_id', $id)->latest()->get();
    }

    public function create(array $data)
    {
        $apply_loan = new ApplyLoan();
        $apply_loan->user_id = $data['user'];
        $apply_loan->department_id = $data['department_id'];
        $apply_loan->title = $data['title'];
        $apply_loan->loan_type = $data['loan_type'];
        $apply_loan->apply_date = Carbon::now()->toDateString();
        $apply_loan->loan_date = Carbon::parse($data['loan_date'])->format('Y-m-d');
        $apply_loan->amount = $data['amount'];
        $apply_loan->total_month = $data['total_month'];
        $apply_loan->monthly_installment = $data['monthly_installment'];
        $apply_loan->note = $data['note'];
        $apply_loan->save();
    }

    public function find($id)
    {
        return ApplyLoan::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $apply_loan = ApplyLoan::findOrFail($id);
        $apply_loan->user_id = Auth::user()->id;
        $apply_loan->department_id = $data['department_id'];
        $apply_loan->title = $data['title'];
        $apply_loan->loan_type = $data['loan_type'];
        $apply_loan->apply_date = Carbon::now()->toDateString();
        $apply_loan->loan_date = Carbon::parse($data['loan_date'])->format('Y-m-d');
        $apply_loan->amount = $data['amount'];
        $apply_loan->total_month = $data['total_month'];
        $apply_loan->monthly_installment = $data['monthly_installment'];
        $apply_loan->note = $data['note'];
        $apply_loan->save();
    }

    public function delete($id)
    {
        return ApplyLoan::findOrFail($id)->delete();
    }

    public function change_approval(array $data)
    {
        $apply_loan = ApplyLoan::findOrFail($data['id']);
        $chart_account = ChartAccount::where('type', '1')->where('code', '01-01-02')->first();
        $apply_loan->approval = $data['approval'];

        if ($data['approval'] == 1) {
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $debit_account_id[] = Settings('advance_loan_and_accured_salary_account');
                $debit_sub_account_id[] = 0;
                $debit_amount[] = $apply_loan->amount;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = 'Loan Generated for ' . $apply_loan->title . ' at ' . Carbon::now()->format('Y-m-d');

                $credit_account_id[] = Settings('leadger_account_for_employee');
                $credit_sub_account_id[] = $apply_loan->user->staff->morph->id;
                $credit_cash_flow_account_id[] = 0;
                $credit_amount[] = $apply_loan->amount;
                $credit_narration[] = 'Generated Loan for ' . $apply_loan->title . ' at ' . Carbon::now()->format('Y-m-d');

                $journalRepository = new ProJournalRepository();
                $voucher = $journalRepository->create([
                    'type' => 'misc',
                    'amount' => $apply_loan->amount,
                    'is_cash_flow_journal' => 0,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'debit_account_id' => $debit_account_id,
                    'debit_sub_account_id' => $debit_sub_account_id,
                    'debit_account_amount' => $debit_amount,
                    'debit_cash_flow_account_id' => $debit_cash_flow_account_id,
                    'debit_narration' => $debit_narration,
                    'narration_voucher' => 'Loan Approved for ' . $apply_loan->title . ' at ' . Carbon::now()->format('Y-m-d'),
                    'referable_type' => get_class($apply_loan),
                    'referable_id' => $apply_loan->id,
                    'sale_or_purchase' => "loan_gen",

                    'credit_account_id' => $credit_account_id,
                    'credit_sub_account_id' => $credit_sub_account_id,
                    'credit_cash_flow_account_id' => $credit_cash_flow_account_id,
                    'credit_account_amount' => $credit_amount,
                    'credit_narration' => $credit_narration,
                    'is_approve' => $is_approved,
                ]);
            } else {
                $debit_account_id[] = ChartAccount::where('contactable_id', $apply_loan->user_id)->where('contactable_type', User::class)->first()->id;
                $debit_account_amount[] = $apply_loan->amount;
                $narration[] = 'Staff Loan';
                $repo = new VoucherRepository();
                $repo->create([
                    'voucher_type' => 'CV' ,
                    'amount'=> $apply_loan->amount,
                    'date'=> Carbon::now()->format('Y-m-d'),
                    'payment_type' => 'voucher_payment',
                    'credit_account_id'=> $chart_account->id,  //debit side and credit side shoud be same
                    'credit_account_amount'=> $apply_loan->amount,  //debit side and credit side shoud be same
                    'credit_account_narration'=> 'Advance & Loan Accounts',  //debit side and credit side shoud be same
                    'debit_account_id'=> $debit_account_id,   //debit side and credit side shoud be same
                    'debit_account_amount'=> $debit_account_amount,
                    'debit_account_narration'=> $narration,
                    'narration' => 'Staff Loan',
                    'cheque_no' => null,
                    'cheque_date' => null,
                    'bank_name' => null,
                    'bank_branch' => null,
                    'is_approve' => (app('business_settings')->where('type', 'loan_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);
            }
        }

        $apply_loan->save();
    }

    public function loanUser()
    {
        return User::with('role','staff')->whereHas('loans')->get();
    }
}
