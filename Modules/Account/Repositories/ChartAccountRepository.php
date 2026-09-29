<?php

namespace Modules\Account\Repositories;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Arr;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\ChartAccount;
use Modules\Contact\Entities\ContactModel;
use Modules\Account\Entities\TimePeriodAccount;
use Modules\Inventory\Entities\ShowRoom;
use Carbon\Carbon;

class ChartAccountRepository implements ChartAccountRepositoryInterface
{
    public function parentNullAccountList()
    {
      return ChartAccount::whereNull('parent_id')
            ->with('childrenCategories','transactions')
            ->with('childrenCategories.transactions')
            ->latest()
            ->get();
    }

    public function parent_category()
    {
        return ChartAccount::with("chart_accounts")
            ->where('parent_id', null)
            ->orWhere('is_group', 1)
            ->latest()
            ->get();
    }

    public function parent_all()
    {
        return ChartAccount::with("chart_accounts")
            ->where('parent_id', null)
            ->where('status', 1)
            ->latest()
            ->get();
    }

    public function all($start_date = null,$end_date = null)
    {
        if ($start_date == null) {
            return ChartAccount::latest()->get();
        }else {
            return ChartAccount::wherehas('transactions', function($query) use($start_date,$end_date) {
                $query->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"));
            })->with(['transactions' => function($query) use($start_date,$end_date){
                $query->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"));
                }])->latest()->get();
        }
    }

    public function getAllContacts()
    {
        return ChartAccount::where('contactable_type','Modules\Contact\Entities\ContactModel')->latest()->get();
    }

    public function getSuppliers()
    {
        return ChartAccount::where('contactable_type','Modules\Contact\Entities\ContactModel')->where('type', 2)->latest()->get();
    }

    public function getCustomers()
    {
        return ChartAccount::where('contactable_type','Modules\Contact\Entities\ContactModel')->where('type', 1)->latest()->get();
    }
    public function getAllCustomers($request)
    {

        if ($request->search != '') {
            $items = ChartAccount::whereLike(['name', 'code'], $request->search)
                                    ->where('contactable_type', 'Modules\Contact\Entities\ContactModel')
                                    ->where('type', 1)
                                    ->paginate(10);
        } else {
            $items = ChartAccount::where('contactable_type', 'Modules\Contact\Entities\ContactModel')
                                    ->where('type', 1)
                                    ->paginate(10);
        }


        $response = [];
        foreach ($items as $item) {
            $response[] = [
                'id' => $item->id,
                'text' => '(' . $item->code . ') ' . $item->name,
            ];
        }

        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }


    public function getFirst($type, $id)
    {
        return ChartAccount::where('contactable_type',$type)
                            ->where('contactable_id', $id)
                            ->first();
    }

    public function create(array $data)
    {
        $charAccount = new ChartAccount();

        if ( isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $parent_account = $this->find($data['parent_id']);
            $data = Arr::add($data, "level", $parent_account ? ($parent_account->level + 1) : 1 );
            $data = Arr::add($data, "parent_id", $data['parent_id']);
            $data = Arr::set($data, "type", $parent_account ? $parent_account->type : $data['type']  );
        } else {
            $data = Arr::add($data, "level",1);
            $data = Arr::set($data, "parent_id", null);
        }
        $charAccount->fill($data)->save();
        if(isset($data['as_sub_category'])){
            $parent_account = $this->find($data['parent_id']);
            $charAccount->update([
                'type' =>$parent_account ? $parent_account->type :  $data['type'],
                'code' => $parent_account ? ($parent_account->code .'-' .leadingZeroTwo($charAccount->id)) : leadingZeroTwo($charAccount->id)
            ]);
        }else{
            $charAccount->update([
                'code' => '0'.$data['type'].'-'.leadingZeroTwo($charAccount->id)
            ]);
        }
        return  $charAccount;
    }

    public function find($id)
    {
        return ChartAccount::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $charAccount = ChartAccount::findOrFail($id);
        if (isset($data['as_sub_category']) && $data['as_sub_category'] == 1) {
            $data = Arr::add($data, "parent_id", $data['parent_id']);
        } else {
            $data = Arr::set($data, "parent_id", null);
        }
        return $charAccount->update($data);
    }

    public function delete($id)
    {
        $chart_account = ChartAccount::with('chart_accounts')->findOrFail($id);

        if ($chart_account->chart_accounts->count()){
            Toastr::error(__("account.Chart Account Has Children Account. Please Delete Children Account First."));
            return false;
        }
        return $chart_account->delete();
    }

    public function incomeAccounts()
    {
        return ChartAccount::IncomeAccounts()->latest()->get();
    }

    public function getPaymentAccountList()
    {
        $accountList =  ChartAccount::where('is_group',0)->latest()->get();

        $filteredList =  $accountList->filter(function($account,$key){
           return in_array($account->configuration_group_id,[1,2]) ? FALSE : TRUE;
        });

        return $filteredList->all();


        $finalList =  $accountList->where('configuration_group_id','!=',1);
        return $finalList->merge($finalList->where('configuration_group_id','!=',2));
    }

    public function expenseAccountList($timePeriod = null)
    {
        if ($timePeriod == null) {
            ChartAccount::with('transactions')->where('type', 3)->where('is_group', 0)->latest()->get();
        }
        $accountingPeriod = TimePeriodAccount::findOrFail($timePeriod);
        if ($accountingPeriod->is_closed == 0) {
            $last_date = Carbon::now()->endOfDay()->toDateTimeString();
            return ChartAccount::where('type', 3)->wherehas('transactions', function($query) use($accountingPeriod, $last_date) {
                $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $last_date));
            })->with(['transactions' => function($query) use($accountingPeriod, $last_date){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $last_date));
                }])->get();
        }else {
            return ChartAccount::where('type', 3)->wherehas('transactions', function($query) use($accountingPeriod) {
                $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
            })->with(['transactions' => function($query) use($accountingPeriod){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
                }])->get();
        }
    }

    public function incomeAccountList($timePeriod = null)
    {
        if ($timePeriod == null) {
            ChartAccount::with('transactions')->where('type', 4)->where('is_group', 0)->get();
        }

        $accountingPeriod = TimePeriodAccount::findOrFail($timePeriod);
        if ($accountingPeriod->is_closed == 0) {
            $last_date = Carbon::now()->endOfDay()->toDateTimeString();

            return ChartAccount::where('type', 4)->wherehas('transactions', function($query) use($accountingPeriod,$last_date) {
                $query->whereBetween('created_at',[$accountingPeriod->start_date." 00:00:00",$last_date]);
            })->with(['transactions' => function($query) use($accountingPeriod, $last_date){
                    $query->whereBetween('created_at' , [$accountingPeriod->start_date." 00:00:00", $last_date]);
                }])->get();
        }else {
            return ChartAccount::where('type', 4)->wherehas('transactions', function($query) use($accountingPeriod) {
                $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
            })->with(['transactions' => function($query) use($accountingPeriod){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
                }])->get();
        }
    }



    public function incomeAccountByDate($form_date, $to_date)
    {
         return ChartAccount::where('type', 4)->wherehas('transactions', function($query) use($form_date,$to_date) {

                $query->whereBetween('created_at',[$form_date." 00:00:00",$to_date." 23:59:59"]);

            })->with(['transactions' => function($query) use($form_date, $to_date){
                   $query->whereBetween('created_at',[$form_date." 00:00:00",$to_date." 23:59:59"]);
                }])->get();
    }

    public function expenseAccountByDate($form_date, $to_date)
    {
        return ChartAccount::where('type', 3)->wherehas('transactions', function($query) use($form_date,$to_date) {

                $query->whereBetween('created_at',[$form_date." 00:00:00",$to_date." 23:59:59"]);

            })->with(['transactions' => function($query) use($form_date, $to_date){
                   $query->whereBetween('created_at',[$form_date." 00:00:00",$to_date." 23:59:59"]);
                }])->get();
    }

    public function assetAccountList($timePeriod = null)
    {
        if ($timePeriod == null) {
            ChartAccount::with('transactions')->where('type', 1)->where('is_group', 0)->get();
        }
        $accountingPeriod = TimePeriodAccount::findOrFail($timePeriod);
        if ($accountingPeriod->is_closed == 0) {
            $last_date = Carbon::now()->endOfDay()->toDateTimeString();
            return ChartAccount::whereBetween('type', 1)->wherehas('transactions', function($query) use($accountingPeriod, $last_date) {
                $query->whereBetween('created_at', '>=', $accountingPeriod->start_date." 00:00:00", $last_date);
            })->with(['transactions' => function($query) use($accountingPeriod, $last_date){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $last_date));
                }])->get();
        }else {
            return ChartAccount::where('type', 1)->wherehas('transactions', function($query) use($accountingPeriod) {
                $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
            })->with(['transactions' => function($query) use($accountingPeriod){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
                }])->get();
        }
    }

    public function liabilityAccountList($timePeriod = null)
    {
        if ($timePeriod == null) {
            ChartAccount::with('transactions')->where('type', 2)->where('is_group', 0)->get();
        }
        $accountingPeriod = TimePeriodAccount::findOrFail($timePeriod);
        if ($accountingPeriod->is_closed == 0) {
            $last_date = Carbon::now()->endOfDay()->toDateTimeString();
            return ChartAccount::where('type', 2)->wherehas('transactions', function($query) use($accountingPeriod) {
                $query->where('created_at', '>=', $accountingPeriod->start_date." 00:00:00");
            })->with(['transactions' => function($query) use($accountingPeriod, $last_date){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $last_date));
                }])->get();
        }else {
            return ChartAccount::where('type', 2)->wherehas('transactions', function($query) use($accountingPeriod) {
                $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
            })->with(['transactions' => function($query) use($accountingPeriod){
                    $query->whereBetween('created_at' , array($accountingPeriod->start_date." 00:00:00", $accountingPeriod->end_date." 23:59:59"));
                }])->get();
        }
    }

    public function dailyExpense($date)
    {
        return ChartAccount::where('type', 3)->wherehas('transactions', function($query) use($date) {
            $query->whereDate('created_at' ,$date);
            })->get();
    }
    public function dailyIncome($date)
    {
        return ChartAccount::with(['transactions' => function($q) use($date){

            $q->whereDate('created_at' , $date);

        }])->where('type', 4)->wherehas('transactions')->get();


    }

    public function rename_account(array $data)
    {
        $charAccount = ChartAccount::findOrFail($data['account_id']);
        return $charAccount->update($data);
    }

    public function listForSelectAccount($request)
    {
        // No explicit order previously meant a newly created account (the
        // highest id) landed on whatever the last page happened to be
        // rather than showing up near the top of the picker - order
        // newest-first so it's immediately visible.
        if ($request->search != '') {
            $items = ChartAccount::whereLike(['name', 'code'], $request->search)
                                    ->latest('id')
                                    ->paginate(10);
        } else {
            $items = ChartAccount::latest('id')->paginate(10);
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

    public function listForSelectContactAccounts($request)
    {
        // Cost Report's account filter should only offer customer/supplier
        // ledger accounts (contactable_type = ContactModel, created via
        // ContactRepository::create_chart_account()) - not treasury, bank,
        // expense, income, or any other chart account. Filtering by `type`
        // alone can't do this: customer accounts use type=1 (Asset), the
        // same type as Cash/Bank accounts.
        $query = ChartAccount::where('contactable_type', ContactModel::class);

        if ($request->search != '') {
            $items = $query->whereLike(['name', 'code'], $request->search)
                                    ->latest('id')
                                    ->paginate(10);
        } else {
            $items = $query->latest('id')->paginate(10);
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

    public function listForSelectAccountCashBank($request)
    {
        // Scope to the current branch: its own Cash-in-Hand account, plus
        // whichever Bank accounts are assigned to it (or unassigned,
        // meaning shared with every branch - see the bank_account_showroom
        // migration). Previously this returned every cash/bank account
        // system-wide, so any branch could post against any other
        // branch's cash vault.
        $showroomId = session()->get('showroom_id', 1);

        $cashAccountIds = ChartAccount::where('configuration_group_id', 1)
            ->where('contactable_id', $showroomId)
            ->where('contactable_type', ShowRoom::class)
            ->pluck('id');

        $bankAccountIds = BankAccount::whereDoesntHave('showRooms')
            ->orWhereHas('showRooms', function ($query) use ($showroomId) {
                $query->where('show_rooms.id', $showroomId);
            })
            ->pluck('chart_account_id');

        $query = ChartAccount::whereIn('id', $cashAccountIds->merge($bankAccountIds));

        if ($request->search != '') {
            $items = $query->whereLike(['name', 'code'], $request->search)->paginate(10);
        } else {
            $items = $query->paginate(10);
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

    public function listForSelectAccountBasedOnType($request)
    {
        // Same fix as listForSelectAccount() above - no order meant a
        // freshly created category (e.g. via the quick-add button on
        // Add Expense/Add Revenue) could land on any page of the
        // paginated dropdown instead of being immediately visible.
        if ($request->search != '') {
            $items = ChartAccount::where('type', $request->type)
                                ->whereLike(['name', 'code'], $request->search)
                                ->latest('id')
                                ->paginate(10);
        } else {
            $items = ChartAccount::where('type', $request->type)->latest('id')->paginate(10);
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
    public function listForCustomers($request)
    {
        if ($request->search != '') {
            $items = ContactModel::where('is_active', 1)
                                ->whereLike(['name', 'business_name'], $request->search)
                                ->paginate(10);
        } else {
            $items = ContactModel::where('is_active', 1)->paginate(10);
        }

        $response = [];
        foreach ($items as $item) {
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectAccountBasedOnConfigurationGroup($request)
    {
        // Callers (Add Expense/Revenue's Payment From/To Account,
        // Treasury/Showroom Transfer, Payment Request) pass
        // configuration_group_id=2 for "Account for Bank" and =1 for
        // "Account for Cash" (see public/modules/core/expense_accounts.js).
        // This used to filter every group by is_actual_bank = 0 - the
        // exact opposite of what BankAccountRepository::create() sets on
        // a genuine bank account (is_actual_bank = 1). Real bank accounts
        // never appeared in the bank picker at all, only the generic
        // "Bank Account" placeholder chart account seeded alongside them -
        // silently routing every bank-sourced expense/revenue/transfer
        // onto an untracked account instead of the one actually shown on
        // the Bank Accounts page. Cash accounts are never flagged
        // is_actual_bank (that column only means something for the bank
        // group), so only constrain by it when group 2 (bank) is requested.
        $query = ChartAccount::where('configuration_group_id', $request->configuration_group_id);
        if ($request->configuration_group_id == 2) {
            $query->where('is_actual_bank', 1);
        }
        if ($request->search != '') {
            $items = $query->whereLike(['name', 'code'], $request->search)
                                ->paginate(10);
        } else {
            $items = $query->paginate(10);
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

}
