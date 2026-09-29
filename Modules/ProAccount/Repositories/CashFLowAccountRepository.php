<?php

namespace Modules\ProAccount\Repositories;

use Illuminate\Support\Arr;
use Modules\ProAccount\Entities\CashFLowAccount;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Imports\CashFLowAccountImport;
use Modules\ProAccount\Exports\CashFLowAccountExport;

class CashFLowAccountRepository
{
    public function getAllQuery($filter_date)
    {
        return CashFLowAccount::get();
    }

    public function withPaginate($row_count,$quick_search,$name,$sort,$column)
    {
        $items = CashFLowAccount::query();
        
        if ($quick_search != null) {
            $items = $items->whereLike(['name','code'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = CashFLowAccount::count();

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

    public function getAll()
    {
        return CashFLowAccount::get();
    }

    public function cashFlowAccountForSelect($search)
    {
        if ($search != '') {
            $items = CashFLowAccount::whereLike(['name', 'code'], $search)->paginate(10);
        } else {
            $items = CashFLowAccount::paginate(10);
        }


        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => '('.$item->code.') '.$item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function cashFlowAccountExpenseForSelect($search)
    {
        if ($search != '') {
            $items = CashFLowAccount::where('type', 3)->whereLike(['name', 'code'], $search);
        }else {
            $items = CashFLowAccount::where('type', 3)->paginate(10);
        }

        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => '('.$item->code.') '.$item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function create(array $data)
    {
        $cashFlowAccount = new CashFLowAccount();
        $cashFlowAccount->fill($data)->save();
        return  $cashFlowAccount;
    }

    public function find($id)
    {
        return CashFLowAccount::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        return CashFLowAccount::findOrFail($id)->update($data);
    }

    public function rename_account(array $data)
    {
        $cashFlowAccount = $this->find($data['account_id']);
        return $cashFlowAccount->update($data);
    }

    public function delete($id)
    {
        $cashFlowAccount = $this->find($id);
        if ($cashFlowAccount->is_blocked) {
            \LogActivity::successLog('Failed to Delete - '.$cashFlowAccount->name, route('activity_log'), "CashFlow Account Deleted");
            return "failed";
        }else {
            \LogActivity::successLog(trans('common.Successfully Deleted').' - '.$cashFlowAccount->name, route('activity_log'), "CashFlow Account Deleted");
            $cashFlowAccount->delete();
            return "done";
        }
    }

    public function csvUpload($data)
    {
        Excel::import(new CashFLowAccountImport, $data['file']->store('temp'));
    }

    public function csvDownload()
    {
        if (file_exists(public_path("uploads/csv/cashflow_accountlist.xlsx"))) {
          unlink(public_path("uploads/csv/cashflow_accountlist.xlsx"));
        }
        return Excel::store(new CashFLowAccountExport, 'uploads/csv/cashflow_accountlist.xlsx', 'public_folder');
    }

}
