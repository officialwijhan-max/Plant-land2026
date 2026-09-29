<?php

namespace Modules\ProAccount\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Entities\FinancialYear;
use Modules\ProAccount\Entities\Voucher;
use Modules\ProAccount\Repositories\JournalRepository;
use update\Modules\ProAccount\Repositories\IncomeStatementRepository;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Export\FinancialYearExport;

class FinancialYearRepository
{
    public function csvDownloadFinancialYear()
    {
        if (file_exists(public_path("uploads/csv/financial-years.xlsx"))) {
          unlink(public_path("uploads/csv/financial-years.xlsx"));
        }
        return Excel::store(new FinancialYearExport, 'uploads/csv/financial-years.xlsx', 'public_folder');
    }

    public function withPaginateFinancialYear($row_count,$quick_search,$sort,$column)
    {
        $items = FinancialYear::query();
        $items = $items;
        if ($quick_search != null) {
            $items = $items->whereLike(['start_date','end_date'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = FinancialYear::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number);
            }else {
                return $items->latest()->paginate($total_number);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count);
            }else {
                return $items->latest()->paginate($row_count);
            }
        }
    }

    public function getAll($relational_data = [], $selected_data = ['*'])
    {
        return FinancialYear::with($relational_data)->select($selected_data)->get();
    }

    public function getSpecificYears($start_id, $end_id, $relational_data = [], $selected_data = ['*'])
    {
        return FinancialYear::with($relational_data)->where('id', '>=', $start_id)->where('id', '<=', $end_id)->select($selected_data)->get();
    }

    public function findByID($id)
    {
        return FinancialYear::find($id);
    }

    public function isDateValidForClosing($date)
    {
        $last_voucher =  Voucher::latest()->first();
        return ($date >= $last_voucher->date) ? true : false ;
    }

    public function closing(array $data, $id)
    {
        $financial_year = $this->findByID($id);
        $endDate = date('Y-m-d', strtotime($data['end_date']));

        if ($this->isDateValidForClosing($endDate)) {

            $incomeStatementRepository = new IncomeStatementRepository();

            $direct_income_data = $incomeStatementRepository->directIncomeAccountsReports();
            $direct_income_ids = $direct_income_data['direct_income_leadgers'];

            $indirect_income_data = $incomeStatementRepository->indirectIncomeAccountsReports();
            $indirect_income_ids = $indirect_income_data['direct_income_leadgers'];

            $direct_expense_data = $incomeStatementRepository->directExpenseAccountsReports();
            $direct_expense_ids = $direct_expense_data['direct_income_leadgers'];
            if (($key = array_search(Settings('default_purchase_account'), $direct_expense_ids)) !== false) {
                unset($direct_expense_ids[$key]);
            }
            $indirect_expense_data = $incomeStatementRepository->indirectExpenseAccountsReports();
            $indirect_expense_ids = $indirect_expense_data['direct_income_leadgers'];

            $total_amount = $financial_year->showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, session()->get('showroom_id'));
            $total_tax = $financial_year->showTotalFinancialYearTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, session()->get('showroom_id'));

            $naration = 'Financial Year Closing Balance Net Income';
            $debit_account_id[] = Settings('income_summary_debit_leadger');
            $debit_sub_account_id[] = 0;
            $debit_account_amount[] = $total_amount - $total_tax;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] =  $naration;
            $total_debit_amount = $total_amount;

            $credit_account_id[] = Settings('retail_earning_leadger');
            $credit_sub_account_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_amount[] = $total_amount - $total_tax;
            $credit_narration[] = $naration;

            if ($total_tax > 0) {
                $debit_account_id[] = Settings('company_income_tax');
                $debit_sub_account_id[] = 0;
                $debit_account_amount[] = $total_tax;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] =  "Income Tax For Company of";

                $credit_account_id[] = Settings('company_tax_leadger');
                $credit_sub_account_id[] = 0;
                $credit_cash_flow_account_id[] = 0;
                $credit_amount[] = $total_tax;
                $credit_narration[] = "Payable Tax for last Financial Year";
            }

            $journalRepository = new JournalRepository();
            $voucher = $journalRepository->create([
                'type' => 'misc',
                'amount'=> $total_debit_amount,
                'is_cash_flow_journal' => 0,
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_sub_account_id,
                'credit_account_amount'=> $credit_amount,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> 'Financial Year Closing Balance',
                'referable_type'=> null,
                'referable_id'=> null,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_sub_account_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_account_amount,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
            ]);
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Financial Year closing journal');

            $financial_year->update([
                'end_date' => $endDate,
                'is_locked' => 1,
            ]);

            $NewFinanCialYear = FinancialYear::create(['start_date' => Carbon::parse($endDate)->addDays(1)->format('Y-m-d')]);
            $closing_ledger_account_balances = DB::table('pro_financial_year_leadger_balances')
                ->join('pro_leadgers','pro_financial_year_leadger_balances.leadger_id','pro_leadgers.id')
                ->whereIn('pro_leadgers.type',[1,2,5])
                ->where('accounting_period_id',$financial_year->id)
                ->orWhere('pro_leadgers.id',Settings('default_purchase_account'))
                ->where('accounting_period_id',$financial_year->id)->get();

                foreach($closing_ledger_account_balances as $account) {
                    $NewAccountBalances[] = [
                        'showroom_id' => session()->get('showroom_id'),
                        'leadger_id' => $account->leadger_id,
                        'accounting_period_id' => $NewFinanCialYear->id,
                        'balance' => $account->balance
                    ];
                }

            DB::table('pro_financial_year_leadger_balances')->insert($NewAccountBalances);
            \LogActivity::successLog(__('account.financial_year_has_been_closed').$financial_year->start_date.' - '.$financial_year->end_date, route('financial_years.index'), "Financial Year Closed");

            return "success";
        }
        return "failed";
    }
}
