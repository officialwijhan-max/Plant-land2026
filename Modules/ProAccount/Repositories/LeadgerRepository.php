<?php

namespace Modules\ProAccount\Repositories;

use Illuminate\Support\Arr;
use Modules\ProAccount\Entities\Leadger;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Imports\LeadgerImport;
use Modules\ProAccount\Exports\LeadgerExport;
use Modules\ProAccount\Repositories\JournalRepository;

class LeadgerRepository
{
    public function getAll()
    {
        return Leadger::with('transactions')->get(['name','id', 'parent_id']);
    }

    public function getActiveAll()
    {
        return Leadger::with('transactions')->where('is_active', 1)->get(['name','id', 'code', 'parent_id']);
    }

    public function getActiveExpenseAll()
    {
        return Leadger::with('transactions')->where('is_active', 1)->where('type', 3)->get(['name','id', 'code', 'parent_id']);
    }

    public function cashBankAccounts()
    {
        return Leadger::with('transactions')->where('is_active', 1)
                    ->whereIn('acc_type', ['bank','cash'])
                    ->where('is_cost_center', 0)
                    ->get();
    }

    public function bankAccounts()
    {
        return Leadger::with('transactions')->where('is_active', 1)
                    ->where('acc_type', 'bank')
                    ->where('is_cost_center', 0)
                    ->get(['name','id', 'code', 'parent_id']);
    }

    public function leadgerForSelect($search)
    {
        if ($search != '') {
            $items = Leadger::whereLike(['name', 'code'], $search)->paginate(10);
        } else {
            $items = Leadger::paginate(10);
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
        } else {
            if ((auth()->user()->role->type == "system_user" || permissionCheck('leadger.store'))) {
                $response[]  =[
                    'id'    => "create_new",
                    'text'  => '<span class="add_new_icon_for_select"><i class="fa fa-plus"></i></span> Add New <span class="add_new_font_for_select"> -> '.$search.'</span>'
                ];
                $data['results'] =  $response;
            }
        }
        return $data;
    }

    public function getActiveExpenseLeadgerByAjax($search)
    {
        $leadger = Leadger::query();

        if ($search != '') {
            $leadger->whereLike(['name', 'code'], $search);
        }
        $items = $leadger->where('is_cost_center',0)->where('type', 3)->paginate(10);
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

    public function getActiveIncomeLeadgerByAjax($search)
    {
        $leadger = Leadger::query();

        if ($search != '') {
            $leadger->whereLike(['name', 'code'], $search);
        }
        $items = $leadger->where('is_cost_center',0)
                        ->where('type', 4)
                        
                        ->paginate(10);
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

    public function getLeadgerByAjax($search, $type)
    {
        $leadger = Leadger::query();
        if ($type != null) {
            if (strtolower($type) == "cash") {
                $leadger->whereNotIn('acc_type',['bank']);
            }
            if (strtolower($type) == "bank") {
                $leadger->whereNotIn('acc_type',['cash']);
            }
        }
        if ($search != '') {
            $leadger->whereLike(['name', 'code'], $search);
        }
        $items = $leadger
                        ->where('is_cost_center',0)
                        ->paginate(10);

        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => $item->code.' ('.$item->name.')'
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        } else {
            if ((auth()->user()->role->type == "system_user" || permissionCheck('leadger.store'))) {
                $response[]  =[
                    'id'    => "create_new",
                    'text'  => '<span class="add_new_icon_for_select"><i class="fa fa-plus"></i></span> Add New <span class="add_new_font_for_select"> -> '.$search.'</span>'
                ];
                $data['results'] =  $response;
            }
        }
        return $data;
    }

    public function parentNullAccountList($relational_data = [], $selected_data = ['*'])
    {
      return Leadger::with($relational_data)
                    ->where('parent_id',0)
                    ->orderBy('type','asc')
                    ->latest()
                    ->paginate(20, $selected_data);
    }

    public function parent_category()
    {
        return Leadger::with("chart_accounts", 'subLedgers','transactions')
                        ->where('parent_id', 0)
                        ->latest()
                        ->get();
    }

    public function cost_center($relational_data = [], $selected_data = ['*'])
    {
        return Leadger::with($relational_data)
                        ->where('is_cost_center', 1)                        
                        ->latest()
                        ->select($selected_data)
                        ->get();
    }

    public function transactional_accounts($relational_data = [], $selected_data = ['*'])
    {
        return Leadger::with($relational_data)
                        ->where('is_cost_center', 0)                        
                        ->latest()
                        ->select($selected_data)
                        ->get();
    }

    public function create(array $data)
    {
        $charAccount = new Leadger();
        if ( isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $parent_account = $this->find($data['parent_id']);
            $data = Arr::add($data, "level", $parent_account ? ($parent_account->level + 1) : 1);
            $data = Arr::add($data, "parent_id", $data['parent_id']);
            $data = Arr::set($data, "type", $parent_account ? $parent_account->type : $data['type']);
        } else {
            $data = Arr::add($data, "level", 1);
            $data = Arr::set($data, "parent_id", 0);
        }
        $charAccount->fill($data)->save();
        if(isset($data['as_sub_category'])){
            $parent_account = $this->find($data['parent_id']);
            $charAccount->update([
                'type' =>$parent_account ? $parent_account->type : $data['type'],
            ]);
        }else{
            $charAccount->update([
                'type' => $data['type'] == 5 ? 2 : $data['type']
            ]);
        }
        if ($data['type'] == 5) {
            $jsonString = file_get_contents(base_path('Modules/ProAccount/Resources/assets/config_files/equity.json'));
            $data = json_decode($jsonString, true);
            // Update Keydd
            array_push($data['equity_account'],$charAccount->id);
            // Write File
            $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
            file_put_contents(base_path('Modules/ProAccount/Resources/assets/config_files/equity.json'), stripslashes($newJsonString));
        }
        if ($data['opening_balance'] > 0) {
            $credit_amounts[] = $data['opening_balance'];
            $credit_account_id[] = Settings('opening_balance_equity');
            $credit_partner_id[] = 0;
            $credit_cash_flow_account_id[] = 0;
            $credit_narration[] = "Opening Balance At the time of Creation of: ".$charAccount->name;

            $debit_amounts[] = $data['opening_balance'];
            $debit_account_id[] = $charAccount->id;
            $debit_partner_id[] = 0;
            $debit_cash_flow_account_id[] = 0;
            $debit_narration[] = "Opening Balance At the time of Creation of: ".$charAccount->name;

            $journalRecieveRepository = new JournalRepository();
            $voucher = $journalRecieveRepository->create([
                'type' => "misc",
                'is_cash_flow_journal' => 0,
                'amount'=> $data['opening_balance'],
                'date'=> now(),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> "Opening Balance At the time of Creation",
                'referable_type'=> get_class($charAccount),
                'referable_id'=> $charAccount->id,
                'is_invoiced'=> 0,
                'is_advanced'=> 0,
                'is_manual_entry'=> 1,

                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
                'sale_or_purchase' => "opening",
                'ref_no' => null,
                'is_sale_purchase' => 0,
            ]);
            \LogActivity::successLog(trans("common.Successfully Added").' - '.$voucher->GetTypeName().'-'.$voucher->txn_id,route('journal.audit_history',$voucher->id),'Journal entry');
        }
        return  $charAccount;
    }

    public function find($id)
    {
        return Leadger::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $leadger = Leadger::findOrFail($id);

        $parent_account = Leadger::find($data['parent_id']);
        if (isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $parent_account = $this->find($data['parent_id']);
            $data = Arr::add($data, "level", $parent_account ? ($parent_account->level + 1) : 1 );
            $data = Arr::add($data, "parent_id", $data['parent_id']);
            $data = Arr::set($data, "type", $parent_account ? $parent_account->type : $data['type']  );
        }
        return $leadger->update($data);
    }

    public function rename_account(array $data)
    {
        $leadger = Leadger::find($data['account_id']);
        if (isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $parent_account = $this->find($data['parent_id']);
            if ($leadger->BalanceAmount > 0 && $leadger->type != $parent_account->type) {
                return "can not update";
            }
            $data = Arr::add($data, "level", $parent_account ? ($parent_account->level + 1) : 1 );
            $data = Arr::add($data, "parent_id", $data['parent_id']);
            if(!empty($data['type']))
                $data = Arr::set($data, "type", $parent_account ? $parent_account->type : $data['type']  );
        } else {
            $data = Arr::add($data, "level", 1);
            $data = Arr::set($data, "parent_id", 0);
            if(!empty($data['type']))
                $data = Arr::set($data, "type", $data['type']);
        }

        \LogActivity::successLog('Ledger Updated - '.$leadger->name, route('activity_log'), "Ledger Updated");
        return $leadger->update($data);
    }

    public function delete($id)
    {
        $leadger = $this->find($id);
        if ($leadger->is_blocked) {
            \LogActivity::successLog('Ledger Deletion Failed - '.$leadger->name, route('activity_log'), "Ledger Delete Fails");
            return "failed";
        }else {
            \LogActivity::successLog('Ledger Deleted - '.$leadger->name, route('activity_log'), "Ledger Deleted");
            $leadger->delete();
            return "done";
        }
    }
    public function getActiveAllByAjax($search)
    {

        if($search != ''){
            $ledgers = Leadger::where('is_active',1)
                                ->whereLike(['name', 'code'], $search)
                                ->select('id','name','code')
                                ->paginate(10);
        }else{
            $ledgers = Leadger::where('is_active',1)
                                ->select('id','name','code')
                                ->paginate(10);
        }
        $response = [];
        foreach($ledgers as $ledger){
            $response[]  =[
                'id'    =>$ledger->id,
                'text'  =>$ledger->name.' ('.$ledger->code.')'
            ];
        }
        return  $response;
    }

    public function listForSelectAccountCashBank($request)
    {
        if ($request->search != '') {
            $items = Leadger::whereIn('acc_type', ["cash","bank"])
                                    ->whereLike(['name', 'code'], $request->search)
                                    ->paginate(10);
        } else {
            $items = Leadger::whereIn('acc_type', ["cash","bank"])
                                    ->paginate(10);
        }

        $response = [];
        foreach ($items as $item) {
            $response[]  = [
                'id'    => $item->id,
                'text'  => '('.$item->code.') '.$item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function csvUploadLeadger($data)
    {
        Excel::import(new LeadgerImport, $data['file']->store('temp'));
    }

    public function csvDownloadLeadgers()
    {
        if (file_exists(public_path("uploads/csv/leadger_accountlist.xlsx"))) {
          unlink(public_path("uploads/csv/leadger_accountlist.xlsx"));
        }
        return Excel::store(new LeadgerExport, 'uploads/csv/leadger_accountlist.xlsx', 'public_folder');
    }

}
