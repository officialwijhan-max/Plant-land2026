<?php

namespace Modules\ProAccount\Repositories;

use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Export\CashFlowBothExport;
use Modules\ProAccount\Entities\CashFlowDetail;

class CashFlowRepository
{
    public function cashRecieveListIncome($start_Date, $end_date)
    {
        if ($start_Date != null and $end_date != null) {
            $result = CashFlowDetail::whereBetween('created_at',array($start_Date." 00:00:00", $end_date." 23:59:59"))
            ->whereHas('voucher', function($q){
                $q->where('is_approve', 1);
            })->whereHas('cash_flow_account', function($r){
                $r->where('type', 4);
            })->with(['cash_flow_account' => function($qr){
                $qr->select('id','name','code');
            }])->get()->groupby('cash_flow_account_id');
        }
        else {
            $result = CashFlowDetail::whereHas('voucher', function($q){
                $q->where('is_approve', 1);
            })->whereHas('cash_flow_account', function($r){
                $r->where('type', 4);
            })->with(['cash_flow_account' => function($qr){
                $qr->select('id','name','code');
            }])->get()->groupby('cash_flow_account_id');
        }
        return $result;
    }

    public function cashPaymentListExpense($start_Date, $end_date)
    {
        if ($start_Date != null and $end_date != null) {
            $result = CashFlowDetail::whereBetween('created_at',array($start_Date." 00:00:00", $end_date." 23:59:59"))
            ->whereHas('voucher', function($q){
                $q->where('is_approve', 1);
            })->whereHas('cash_flow_account', function($r){
                $r->where('type', 3);
            })->with(['cash_flow_account' => function($qr){
                $qr->select('id','name','code');
            }])->get()->groupby('cash_flow_account_id');
        }
        else {
            $result = CashFlowDetail::whereHas('voucher', function($q){
                $q->where('is_approve', 1);
            })->whereHas('cash_flow_account', function($r){
                $r->where('type', 3);
            })->with(['cash_flow_account' => function($qr){
                $qr->select('id','name','code');
            }])->get()->groupby('cash_flow_account_id');
        }
        return $result;
    }

    public function csvDownloadCashflow($cash_in, $cash_out, $start_date, $end_date)
    {
        if (file_exists(public_path("uploads/csv/cash_flow_list.xlsx"))) {
          unlink(public_path("uploads/csv/cash_flow_list.xlsx"));
        }
        return Excel::store(new CashFlowBothExport($cash_in, $cash_out, $start_date, $end_date), 'uploads/csv/cash_flow_list.xlsx', 'public_folder');
    }

    public function pdfDownloadCashflow($cash_in, $cash_out, $start_date, $end_date)
    {
        $total_cash_in = 0;
        $total_cash_out = 0;
        $transactions_in = array();
        $transactions_out = array();
        if ($cash_in == "cash-in") {
            $transactions_in = CashFlowDetail::whereBetween('created_at',array($start_date." 00:00:00", $end_date." 23:59:59"))
                                                ->whereHas('voucher', function($q){
                                                    $q->where('is_approve', 1);
                                                })->whereHas('cash_flow_account', function($r){
                                                    $r->where('type', 4);
                                                })->with(['cash_flow_account' => function($qr){
                                                    $qr->select('id','name','code');
                                                },
                                                'voucher' => function($que){
                                                    $que->select('id', 'date');
                                                }])
                                                ->get()->groupby('cash_flow_account_id');
        }
        if ($cash_out == "cash-out") {
            $transactions_out = CashFlowDetail::whereBetween('created_at',array($start_date." 00:00:00", $end_date." 23:59:59"))
                                                ->whereHas('voucher', function($q){
                                                    $q->where('is_approve', 1);
                                                })->whereHas('cash_flow_account', function($r){
                                                    $r->where('type', 3);
                                                })->with(['cash_flow_account' => function($qr){
                                                    $qr->select('id','name','code');
                                                },
                                                'voucher' => function($que){
                                                    $que->select('id', 'date');
                                                }])
                                                ->get()->groupby('cash_flow_account_id');
        }
        $new_array = collect();
        $x = new \stdClass();
        $x->date = "Income";
        $x->leadger_name = "";
        $x->code = "";
        $x->amount = "";
        $x->_date = "Expense";
        $x->_leadger_name = "";
        $x->_code = "";
        $x->_amount = "";
        $new_array->push($x);
        $x = new \stdClass();
        $x->date = "From";
        $x->leadger_name = $start_date;
        $x->code = "To";
        $x->amount = $end_date;
        $x->_date = "From";
        $x->_leadger_name = $start_date;
        $x->_code = "To";
        $x->_amount = $end_date;
        $new_array->push($x);
        $x = new \stdClass();
        $x->date = "Date";
        $x->leadger_name = "Name";
        $x->code = "Code";
        $x->amount = "Amount";
        $x->_date = "Date";
        $x->_leadger_name = "Name";
        $x->_code = "Code";
        $x->_amount = "Amount";
        $new_array->push($x);
            foreach ($transactions_in as $key => $transaction) {
                $x = new \stdClass();
                $x->date = $transaction->first()->voucher->date;
                $x->leadger_name = $transaction->first()->cash_flow_account->name;
                $x->code = $transaction->first()->cash_flow_account->code;
                $x->amount = single_price($transaction->sum('amount'));
                $total_cash_in += $transaction->sum('amount');
                $x->_date = '';
                $x->_leadger_name = '';
                $x->_code = '';
                $x->_amount = '';
                $new_array->push($x);
            }
            foreach ($transactions_out as $key => $transaction_o) {
                $x = new \stdClass();
                $x->_date = $transaction_o->first()->voucher->date;
                $x->_leadger_name = $transaction_o->first()->cash_flow_account->name;
                $x->_code = $transaction_o->first()->cash_flow_account->code;
                $x->_amount = single_price($transaction_o->sum('amount'));
                $total_cash_out += $transaction_o->sum('amount');
                $x->date = '';
                $x->leadger_name = '';
                $x->code = '';
                $x->amount = '';
                $new_array->push($x);
            }
        $x = new \stdClass();
        $x->date = "Total In";
        $x->leadger_name = "";
        $x->code = "";
        $x->amount = single_price($total_cash_in);
        $x->_date = "Total Out";
        $x->_leadger_name = "";
        $x->_code = "";
        $x->_amount = single_price($total_cash_out);
        $new_array->push($x);
        return $new_array;
    }
}
