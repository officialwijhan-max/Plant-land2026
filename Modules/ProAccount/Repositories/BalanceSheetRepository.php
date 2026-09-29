<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Leadger;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Export\BalanceSheetReportExports;
use Modules\ProAccount\Export\BalanceSheetReportDateRangeExports;

class BalanceSheetRepository
{
    public function assetAccountsReports($relational_data = [], $selected_data = ['*'])
    {
        $leadgers = Leadger::with($relational_data)->where('parent_id', 0)->where('type', 1)->select($selected_data)->get();
        $data = array();
        foreach ($leadgers as $key => $leadger) {
            foreach ($leadger->childrenCategories as $key => $child) {
                array_push($data, $child->id);
                if (count($child->categories) > 0) {
                    $data = $this->getChildren($child->categories, $data);
                }
            }
        }
        $leadger_data['asset_leadgers'] = $data;
        $leadger_data['leadger'] = $leadgers;
        return $leadger_data;
    }

    public function findAccountInventory($relational_data = [], $selected_data = ['*'])
    {
        return $leadgers = Leadger::with($relational_data)->where('parent_id', 0)->where('type', 1)->select($selected_data)->get();
    }

    public function csvDownloadIncomeStatementDateRange($data)
    {
        if (file_exists(public_path("/export_csv/balance_sheet.xlsx"))) {
          unlink(public_path("/export_csv/balance_sheet.xlsx"));
        }
        return Excel::store(new BalanceSheetReportDateRangeExports($data), '/export_csv/balance_sheet.xlsx', 'public_folder');
    }

    public function liabilityAccountsReports($relational_data = [], $selected_data = ['*'])
    {
        $leadgers = Leadger::with($relational_data)->where('parent_id', 0)->where('type', 2)->select($selected_data)->get();
        $data = array();
        foreach ($leadgers as $key => $leadger) {
            foreach ($leadger->childrenCategories as $key => $child) {
                array_push($data, $child->id);
                if (count($child->categories) > 0) {
                    $data = $this->getChildren($child->categories, $data);
                }
            }
        }
        $leadger_data['liability_leadgers'] = $data;
        $leadger_data['leadger'] = $leadgers;
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

    public function csvDownloadIncomeStatement($data)
    {
        if (file_exists(public_path("/export_csv/balance_sheet.xlsx"))) {
          unlink(public_path("/export_csv/balance_sheet.xlsx"));
        }
        return Excel::store(new BalanceSheetReportExports($data), '/export_csv/balance_sheet.xlsx', 'public_folder');
    }
}
