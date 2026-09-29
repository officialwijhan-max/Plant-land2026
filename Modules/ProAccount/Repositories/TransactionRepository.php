<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\Voucher;

class TransactionRepository
{
    public function all()
    {
        return Voucher::with(['transactions', 'transactions.leadger'])->where('showroom_id', session()->get('showroom_id'))->latest()->paginate(10);
    }

    public function searchByAccountId($dateFrom, $dateTo, $account_id,$showroom_id)
    {
        $query = Voucher::query();
        $query = $query->where('showroom_id', $showroom_id);

        if (Settings('accounting_entry_system') == "single_entry") {
            if ($account_id != 0) {
                $query = $query->whereIn('type', ['pay_cash','pay_bank','rec_cash','rec_bank','pay','rec'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo))->where('leadger_id', $account_id);
                            })
                            ->orWhereIn('sale_or_purchase', ['exp','inc'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo))->where('leadger_id', $account_id);
                            });
            }else {
                $query = $query->whereIn('type', ['pay_cash','pay_bank','rec_cash','rec_bank','pay','rec'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo));
                            })
                            ->orWhereIn('sale_or_purchase', ['exp','inc'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo));
                            });
            }
        } else {
            if ($account_id != 0) {
                $query = $query->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                            $q->whereBetween('date',array($dateFrom, $dateTo))->where('leadger_id', $account_id);
                        });
            }else {
                $query = $query->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                            $q->whereBetween('date',array($dateFrom, $dateTo));
                        });
            }
        }

        return $query->with(['transactions' => function ($r) {
                        $r->select('id', 'type', 'amount','leadger_id','showroom_id');
                    }])
                    ->with(['transactions.leadger' => function ($r) {
                        $r->select('id', 'type', 'name','code');
                    }])
                    ->select('id','amount', 'date','narration','txn_id','type','is_approve','showroom_id')->latest()->paginate(10);

    }

    public function forPrint($dateFrom, $dateTo, $account_id,$showroom_id)
    {
        $query = Voucher::query();
        $query = $query->where('showroom_id', $showroom_id);

        if (Settings('accounting_entry_system') == "single_entry") {
            if ($account_id != 0) {
                $query = $query->whereIn('type', ['pay_cash','pay_bank','rec_cash','rec_bank','pay','rec'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo))->where('leadger_id', $account_id);
                            })
                            ->orWhereIn('sale_or_purchase', ['exp','inc'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo))->where('leadger_id', $account_id);
                            });
            }else {
                $query = $query->whereIn('type', ['pay_cash','pay_bank','rec_cash','rec_bank','pay','rec'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo));
                            })
                            ->orWhereIn('sale_or_purchase', ['exp','inc'])
                            ->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                                $q->whereBetween('date',array($dateFrom, $dateTo));
                            });
            }
        } else {
            if ($account_id != 0) {
                $query = $query->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                            $q->whereBetween('date',array($dateFrom, $dateTo))->where('leadger_id', $account_id);
                        });
            }else {
                $query = $query->whereHas('transactions', function($q) use($dateFrom, $dateTo, $account_id){
                            $q->whereBetween('date',array($dateFrom, $dateTo));
                        });
            }
        }

        return $query ->with(['transactions' => function ($r) {
                            $r->select('id', 'type', 'amount','leadger_id','showroom_id');
                        }])
                        ->with(['transactions.leadger' => function ($r) {
                            $r->select('id', 'type', 'name','code');
                        }])
                        ->select('id','amount', 'date','narration','txn_id','type','is_approve','showroom_id')->latest()->get();
    }

}
