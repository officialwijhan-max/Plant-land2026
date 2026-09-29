<?php

namespace Modules\ProAccount\Repositories;

use Modules\ProAccount\Entities\CashFlowDetail;

class CashFlowReportRepository
{
    public function balanceBeforeDate($dateFrom, $beforedateAccount)
    {
        $beforeDateTransactions = CashFlowDetail::where('cash_flow_account_id', $beforedateAccount['id'])->where('created_at', '<', $dateFrom." 23:59:59")->latest()->get();
        if ($beforedateAccount->type == 1 || $beforedateAccount->type == 4) {
            $balance = $beforeDateTransactions->where('type', 'Dr')->sum('amount') - $beforeDateTransactions->where('type', 'Cr')->sum('amount');
        }else {
            $balance = $beforeDateTransactions->where('type', 'Cr')->sum('amount') - $beforeDateTransactions->where('type', 'Dr')->sum('amount');
        }
        return $balance;
    }

    public function search($dateFrom, $dateTo, $account_id)
    {
        if ($dateFrom != null && $dateTo != null) {

            $DebitList =  CashFlowDetail::whereBetween('created_at',array($dateFrom." 00:00:00", $dateTo." 23:59:59"))
            ->whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Dr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->paginate(20);

            $CreditList =  CashFlowDetail::whereBetween('created_at',array($dateFrom." 00:00:00", $dateTo." 23:59:59"))
            ->whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Cr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->paginate(20);

        }else {

            $DebitList = CashFlowDetail::whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Dr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->paginate(20);

            $CreditList = CashFlowDetail::whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Cr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->paginate(20);
        }
        return $DebitList->merge($CreditList);
    }

    public function searchPrint($dateFrom, $dateTo, $account_id)
    {
        if ($dateFrom != null && $dateTo != null) {

            $DebitList =  CashFlowDetail::whereBetween('created_at',array($dateFrom." 00:00:00", $dateTo." 23:59:59"))
            ->whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Dr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->get();

            $CreditList =  CashFlowDetail::whereBetween('created_at',array($dateFrom." 00:00:00", $dateTo." 23:59:59"))
            ->whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Cr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->get();

        }else {

            $DebitList = CashFlowDetail::whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Dr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->get();

            $CreditList = CashFlowDetail::whereHas('voucher', function($query) use($account_id){
                $query->where('is_approve', 1);
            })
            ->where('type','Cr')
            ->where('cash_flow_account_id',$account_id)
            ->with(['cash_flow_account','voucher', 'transaction_data'])
            ->get();
        }
        return $DebitList->merge($CreditList);
    }
}
