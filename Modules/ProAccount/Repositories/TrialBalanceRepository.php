<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Leadger;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Export\TrialBalanceExport;
use Modules\ProAccount\Imports\TrialBalanceImport;

class TrialBalanceRepository
{
    public function getData($start_date, $end_date)
    {
        $leadger_ids = Leadger::where('is_cost_center', 0)
                                ->whereHas('transactions')
                                ->with(['transactions' => function($q){
                                    $q->select('type','id','amount','leadger_id','date','amount','is_opening','is_approve','showroom_id');
                                }])->select('id','name','code','type')->get();

        return $leadger_ids;
    }

    public function csvDownload($start_date, $end_date, $showroom_id)
    {
        $startDate = ($start_date != null) ? date('Y-m-d',strtotime($start_date)) : null;
        $endDate = ($end_date != null) ? date('Y-m-d',strtotime($end_date)) : null;
        if (file_exists(public_path("uploads/csv/trial_balance_list.xlsx"))) {
          unlink(public_path("uploads/csv/trial_balance_list.xlsx"));
        }
        return Excel::store(new TrialBalanceExport($startDate, $endDate, $showroom_id), 'uploads/csv/trial_balance_list.xlsx', 'public_folder');
    }

    public function csvUploadLeadger($data)
    {
        Excel::import(new TrialBalanceImport, $data['file']->store('temp'));
    }
}
