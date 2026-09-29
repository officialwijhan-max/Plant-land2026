<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Leadger;
use Modules\ProAccount\Entities\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Export\IncomeStatementReportExports;
use Modules\ProAccount\Export\IncomeStatementReportDateRangeExports;

class IncomeStatementRepository
{
    public function directIncomeAccountsReports()
    {
        $leadger = Leadger::with(['childrenCategories'])->find(Settings('direct_income_leadger'));

        $data = array();
        foreach ($leadger->childrenCategories as $key => $child) {
            array_push($data, $child->id);
            if (count($child->categories) > 0) {
                $data = $this->getChildren($child->categories, $data);
            }
        }
        $leadger_data['direct_income_leadgers'] = $data;
        $leadger_data['leadger'] = $leadger;
        return $leadger_data;
    }

    public function getChildren($childrens, $data)
    {
        foreach ($childrens as $key => $child) {
            array_push($data, $child->id);
            if (count($child->categories) > 0) {
                $data = $this->getChildren2($child->categories, $data);
            }
        }
        return $data;
    }

    public function getChildren2($childrens, $data)
    {
        foreach ($childrens as $key => $child) {
            array_push($data, $child->id);
            if (count($child->categories) > 0) {
                $data = $this->getChildren3($child->categories, $data);
            }
        }
        return $data;
    }

    public function getChildren3($childrens, $data)
    {
        foreach ($childrens as $key => $child) {
            array_push($data, $child->id);
        }
        return $data;
    }

    public function indirectIncomeAccountsReports()
    {
        $leadger = Leadger::with(['childrenCategories'])->find(Settings('in_direct_income_leadger'));

        $data = array();
        foreach ($leadger->childrenCategories as $key => $child) {
            array_push($data, $child->id);
            if (count($child->categories) > 0) {
                $data = $this->getChildren($child->categories, $data);
            }
        }
        $leadger_data['direct_income_leadgers'] = $data;
        $leadger_data['leadger'] = $leadger;
        return $leadger_data;
    }

    public function directExpenseAccountsReports()
    {
        $leadger = Leadger::with(['childrenCategories'])->find(Settings('direct_expense_leadger'));
        $data = array();
        foreach ($leadger->childrenCategories as $key => $child) {
            array_push($data, $child->id);
            if (count($child->categories) > 0) {
                $data = $this->getChildren($child->categories, $data);
            }
        }
        $leadger_data['direct_income_leadgers'] = $data;
        $leadger_data['leadger'] = $leadger;
        return $leadger_data;
    }

    public function indirectExpenseAccountsReports()
    {
        $leadger = Leadger::with(['childrenCategories'])->find(Settings('in_direct_expense_leadger'));
        $data = array();
        foreach ($leadger->childrenCategories as $key => $child) {
            array_push($data, $child->id);
            if (count($child->categories) > 0) {
                $data = $this->getChildren($child->categories, $data);
            }
        }
        $leadger_data['direct_income_leadgers'] = $data;
        $leadger_data['leadger'] = $leadger;
        return $leadger_data;
    }

    public function csvDownloadIncomeStatement($data)
    {
        if (file_exists(public_path("/export_csv/income_statement_report.xlsx"))) {
          unlink(public_path("/export_csv/income_statement_report.xlsx"));
        }
        return Excel::store(new IncomeStatementReportExports($data), '/export_csv/income_statement_report.xlsx', 'public_folder');
    }

    public function csvDownloadIncomeStatementDateRange($data)
    {
        if (file_exists(public_path("/export_csv/income_statement_report.xlsx"))) {
          unlink(public_path("/export_csv/income_statement_report.xlsx"));
        }
        return Excel::store(new IncomeStatementReportDateRangeExports($data), '/export_csv/income_statement_report.xlsx', 'public_folder');
    }

    public function directIncomeCashAccounts()
    {
        $leadger = Leadger::query();
        $leadger = $leadger->with(['transactions:id,leadger_id,type,amount,accounting_period_id,showroom_id'])->where('acc_type','cash');

        return $leadger = $leadger->get(['id'])->pluck('id');
    }

    public function directIncomeBankAccounts()
    {
        $leadger = Leadger::query();
        $leadger = $leadger->where('acc_type','bank');

        return $leadger = $leadger->get(['id'])->pluck('id');
    }

    public function getTransactions($cash_leadger_ids, $bank_leadger_ids, $showroom_id, $start_date, $end_date)
    {
        $cash_transactions = Transaction::with(['leadger:id,name,code'])->whereIn('leadger_id', $cash_leadger_ids)->where('showroom_id', $showroom_id)->whereBetween('date', [$start_date, $end_date])->where('is_approve', 1)->where('deleted_at', null)->get(['id','leadger_id','date','amount','type'])->groupBy(['date','leadger.name']);
        $bank_transactions = Transaction::with(['leadger:id,name,code'])->whereIn('leadger_id', $bank_leadger_ids)->where('showroom_id', $showroom_id)->whereBetween('date', [$start_date, $end_date])->where('is_approve', 1)->where('deleted_at', null)->get(['id','leadger_id','date','amount','type'])->groupBy(['date','leadger.name']);
        $result_cash = [];
        $result_bank = [];
        foreach ($cash_transactions as $key => $first_array) {
            foreach ($first_array as $ke => $tr_types) {
                $result_cash[] = [
                    'date' => $key,
                    'leadger_name' => $ke,
                    'dr_amount' => $tr_types->where('type','Dr')->sum('amount'),
                    'cr_amount' => $tr_types->where('type','Cr')->sum('amount'),
                    'details' => $tr_types
                ];
            }
        }
        foreach ($bank_transactions as $i => $bank_array) {
            foreach ($bank_array as $j => $bank_tr_types) {
                $result_bank[] = [
                    'date' => $i,
                    'leadger_name' => $j,
                    'dr_amount' => $bank_tr_types->where('type','Dr')->sum('amount'),
                    'cr_amount' => $bank_tr_types->where('type','Cr')->sum('amount'),
                    'details' => $bank_tr_types
                ];
            }
        }
        $result['cash'] = $result_cash;
        $result['bank'] = $result_bank;
        return $result;
    }
}
