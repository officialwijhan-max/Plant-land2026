<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\FinancialYear;
use Modules\ProAccount\Entities\Leadger;
use Modules\ProAccount\Entities\Transaction;
use App\Traits\Accounts;

class CashbookRepository
{
    use Accounts;

    public function branchAccount()
    {
        return Leadger::where('morphable_id', session()->get('showroom_id'))->where('morphable_type', 'Modules\Inventory\Entities\ShowRoom')->first();
    }
    public function search_credit($start_date, $end_date, $account_id)
    {
        $conditions = array();
        $results =  Transaction::whereHas('voucher', function($query) use($account_id, $start_date, $end_date){
                        $query->where('is_approve', 1)->whereBetween('date', [$start_date, $end_date])->whereHas('transactions', function($query) use($account_id){
                            $query->where('leadger_id',$account_id)->where('type', 'Dr');
                        });
                    })->whereNotIn('leadger_id', [$account_id])
                    ->with(['voucher', 'voucher.transactions', 'leadger', 'voucher', 'voucher.referable'])
                    ->latest()->get();
        return $results;
    }

    public function search_debit($start_date, $end_date, $account_id)
    {
        $conditions = array();
        $results =  Transaction::whereHas('voucher', function($query) use($account_id, $start_date, $end_date){
                        $query->where('is_approve', 1)->whereBetween('date', [$start_date, $end_date])->whereHas('transactions', function($query) use($account_id){
                            $query->where('leadger_id',$account_id)->where('type', 'Cr');
                        });
                    })->whereNotIn('leadger_id', [$account_id])
                    ->with(['voucher', 'voucher.transactions', 'leadger', 'voucher', 'voucher.referable'])
                    ->latest()->get();
        return $results;
    }

    public function search($previous_date, $account_id)
    {
        $conditions = array();
        $start_date = FinancialYear::where('is_locked',0)->latest()->first()->start_date;
        $results =  Transaction::whereHas('voucher', function($query) use($account_id, $previous_date, $start_date){
                        $query->where('is_approve', 1)->whereBetween('date',[$start_date, $previous_date])->whereHas('transactions', function($query) use($account_id){
                            $query->where('leadger_id',$account_id);
                        });
                    })->whereNotIn('leadger_id', [$account_id])
                    ->with(['voucher', 'voucher.transactions', 'leadger', 'voucher', 'voucher.referable'])
                    ->latest()->get();
        return $results;
    }
}
