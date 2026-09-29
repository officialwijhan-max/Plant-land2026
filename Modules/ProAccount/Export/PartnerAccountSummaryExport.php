<?php

namespace Modules\ProAccount\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\ProAccount\Entities\FinancialYear;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\ProAccount\Entities\Transaction;
use DB;

class PartnerAccountSummaryExport implements FromCollection, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;

    protected $account_id, $start_date, $end_date, $voucher_type;

    function __construct($account_id,$start_date, $end_date, $voucher_type) {
        $this->account_id = $account_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->voucher_type = $voucher_type;
    }

    public function collection()
    {
        $end_date_filter = $this->end_date;
        $start_date_filter = $this->start_date;
        $voucherType = $this->voucher_type;
        $accountId = $this->account_id;

        if ($voucherType == "cash") {
            $type[] = "cash";
            $type[] = "rec_cash";
            $type[] = "pay_cash";
        }
        if ($voucherType == "bank") {
            $type[] = "bank";
            $type[] = "rec_bank";
            $type[] = "pay_bank";
        }
        if ($voucherType == "misc") {
            $type[] = "misc";
        }

        if ($voucherType != "all") {
            $DebitList = Transaction::whereHas('voucher', function($query) use($accountId,$start_date_filter, $end_date_filter, $type){
                    $query->where('is_approve', 1)->whereIn('type', $type)->whereHas('transactions', function($query) use($accountId,$start_date_filter, $end_date_filter){
                        $query->where('sub_leadger_id',$accountId)->where('type','Dr')->whereBetween('date', array($start_date_filter, $end_date_filter));
                    });
                })
                ->where('type','Cr')
                ->with(['leadger' => function($query){
                            $query->select('name','code','type');
                        },'voucher' => function($query){
                            $query->select('id','date','amount','type','txn_id','narration');
                        }, 'fiscal_year' => function($query){
                            $query->select('start_date','end_date','id');
                        }])
                ->with(['voucher.transactions' => function($query) use ($accountId,$start_date_filter, $end_date_filter){
                    $query->where('sub_leadger_id',$accountId)->whereBetween('date', array($start_date_filter, $end_date_filter));
                }])
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();

            $CreditList = Transaction::whereHas('voucher', function($query) use($accountId,$start_date_filter, $end_date_filter, $type){
                    $query->where('is_approve', 1)->whereIn('type', $type)->whereHas('transactions', function($query) use($accountId,$start_date_filter, $end_date_filter){
                        $query->where('sub_leadger_id',$accountId)->where('type','Cr')->whereBetween('date', array($start_date_filter, $end_date_filter));
                    });
                })
                ->where('type','Dr')
                ->with(['leadger' => function($query){
                            $query->select('name','code','type');
                        },'voucher' => function($query){
                            $query->select('id','date','amount','type','txn_id','narration');
                        }, 'fiscal_year' => function($query){
                            $query->select('start_date','end_date','id');
                        }])
                ->with(['voucher.transactions' => function($query) use ($accountId,$start_date_filter, $end_date_filter){
                    $query->where('sub_leadger_id',$accountId)->whereBetween('date', array($start_date_filter, $end_date_filter));
                }])
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();
        }else {
            $DebitList = Transaction::whereHas('voucher', function($query) use($accountId,$start_date_filter, $end_date_filter){
                    $query->where('is_approve', 1)->whereHas('transactions', function($query) use($accountId,$start_date_filter, $end_date_filter){
                        $query->where('sub_leadger_id',$accountId)->where('type','Dr')->whereBetween('date', array($start_date_filter, $end_date_filter));
                    });
                })
                ->where('type','Cr')
                ->with(['leadger' => function($query){
                            $query->select('name','code','type');
                        },'voucher' => function($query){
                            $query->select('id','date','amount','type','txn_id','narration');
                        }, 'fiscal_year' => function($query){
                            $query->select('start_date','end_date','id');
                        }])
                ->with(['voucher.transactions' => function($query) use ($accountId,$start_date_filter, $end_date_filter){
                    $query->where('sub_leadger_id',$accountId)->whereBetween('date', array($start_date_filter, $end_date_filter));
                }])
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();

            $CreditList = Transaction::whereHas('voucher', function($query) use($accountId,$start_date_filter, $end_date_filter){
                    $query->where('is_approve', 1)->whereHas('transactions', function($query) use($accountId,$start_date_filter, $end_date_filter){
                        $query->where('sub_leadger_id',$accountId)->where('type','Cr')->whereBetween('date', array($start_date_filter, $end_date_filter));
                    });
                })
                ->where('type','Dr')
                ->with(['leadger' => function($query){
                            $query->select('name','code','type');
                        },'voucher' => function($query){
                            $query->select('id','date','amount','type','txn_id','narration');
                        }, 'fiscal_year' => function($query){
                            $query->select('start_date','end_date','id');
                        }])
                ->with(['voucher.transactions' => function($query) use ($accountId,$start_date_filter, $end_date_filter){
                    $query->where('sub_leadger_id',$accountId)->whereBetween('date', array($start_date_filter, $end_date_filter));
                }])
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();
        }
        $results = $DebitList->merge($CreditList)->sortBy('leadger_id');

        $group_account = [];
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        $fiscal_period = [];

        $fiscalYears = FinancialYear::whereIn('id', $results->pluck('accounting_period_id')->unique('accounting_period_id'))->get();

        foreach ($fiscalYears as $key => $fiscalYear) {
            array_push($fiscal_period, date('Y', strtotime($fiscalYear->start_date)));
        }
        $subleadger = SubLeadger::find($accountId);
        if ($subleadger->morphable_type == "Modules\Hr\Entities\Employee") {
            $accountName = "EMPLOYEE ACCOUNT";
        }elseif ($subleadger->morphable_type == "Modules\Customer\Entities\Customer") {
            $accountName = "CUSTOMER ACCOUNT";
        }elseif ($subleadger->morphable_type == "Modules\Purchase\Entities\Supplier") {
            $accountName = "SUPPLIER ACCOUNT";
        }else {
            $accountName = "PARTNER ACCOUNT";
        }


        $new_array = collect();

        $x = new \stdClass();
        $x->col_1 = $accountName;
        $x->col_2 = "FISCAL PERIOD";
        $x->col_3 = "Date Range";
        $x->col_4 = "Entry Type";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $x->col_8 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $x->col_8 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = $subleadger->code.' - '.$subleadger->name;
        $x->col_2 = $fiscal_period;
        $x->col_3 = $start_date_filter .' - '. $end_date_filter;
        $x->col_4 = strtoupper($voucherType);
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $x->col_8 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $x->col_8 = "";
        $new_array->push($x);

        $x = new \stdClass();
        $x->col_1 = "ACCOUNT";
        $x->col_2 = "FISCAL YEAR";
        $x->col_3 = "DATE";
        $x->col_4 = "TXN ID";
        $x->col_5 = "LABEL";
        $x->col_6 = "DEBIT";
        $x->col_7 = "CREDIT";
        $x->col_8 = "BALANCE";
        $new_array->push($x);

        foreach ($results as $key => $transaction) {
            if (!in_array($transaction->leadger_id, $group_account)) {
                array_push($group_account, $transaction->leadger_id);
                $debit_total = 0;
                $credit_total = 0;
                $x = new \stdClass();
                $x->col_1 = "";
                $x->col_2 = "";
                $x->col_3 = "";
                $x->col_4 = "";
                $x->col_5 = "";
                $x->col_6 = "";
                $x->col_7 = "";
                $x->col_8 = "";
                $new_array->push($x);
                $x = new \stdClass();
                $x->col_1 = $transaction->leadger->code . '-' . $transaction->leadger->name;
                $x->col_2 = "";
                $x->col_3 = "";
                $x->col_4 = "";
                $x->col_5 = "";
                $x->col_6 = "";
                $x->col_7 = "";
                $x->col_8 = "";
                $new_array->push($x);
            }
            $x = new \stdClass();
            $x->col_1 = "";
            $x->col_2 = date(Settings("date_format_id"), strtotime($transaction->fiscal_year->start_date));
            $x->col_3 = date(Settings("date_format_id"), strtotime($transaction->date));
            if ($transaction->voucher->type == "cash" || $transaction->voucher->type == "rec_cash" || $transaction->voucher->type == "pay_cash") {
                $x->col_4 = trans("account.Cash");
            }elseif ($transaction->voucher->type == "bank" || $transaction->voucher->type == "rec_bank" || $transaction->voucher->type == "pay_bank") {
                $x->col_4 = trans('account.bank');
            }else {
                $x->col_4 = trans('account.miscellaneous');
            }
            $x->col_5 = $transaction->voucher->narration;
            if ($transaction->type == "Dr") {
                $debit_total += $transaction->amount;
                $total_debit_amount += $transaction->amount;
                $x->col_6 = single_price($transaction->amount);
            }else {
                $x->col_6 = "";
            }
            if ($transaction->type == "Cr") {
                $credit_total += $transaction->amount;
                $total_credit_amount += $transaction->amount;
                $x->col_7 = single_price($transaction->amount);
            }else {
                $x->col_7 = "";
            }
            if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3) {
                $x->col_8 = single_price($debit_total - $credit_total);
            }else {
                $x->col_8 = single_price($credit_total - $debit_total);
            }

            $new_array->push($x);
        }
        $x = new \stdClass();
        $x->col_1 = "";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = "";
        $x->col_7 = "";
        $x->col_8 = "";
        $new_array->push($x);
        $x = new \stdClass();
        $x->col_1 = "Final Cumulated Balance";
        $x->col_2 = "";
        $x->col_3 = "";
        $x->col_4 = "";
        $x->col_5 = "";
        $x->col_6 = single_price($total_debit_amount);
        $x->col_7 = single_price($total_credit_amount);
        if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3) {
            $x->col_8 = single_price($total_debit_amount - $total_credit_amount);
        }else {
            $x->col_8 = single_price($total_credit_amount - $total_debit_amount);
        }
        $new_array->push($x);

        return $new_array;
    }

    public function map($row): array
    {
        return [
            $row->col_1,
            $row->col_2,
            $row->col_3,
            $row->col_4,
            $row->col_5,
            $row->col_6,
            $row->col_7,
            $row->col_8,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 25,
            'D' => 15,
            'E' => 40,
            'F' => 25,
            'G' => 25,
            'H' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 13]],
            5    => ['font' => ['bold' => true, 'size' => 13]],
            "A"    => ['font' => ['bold' => true, 'size' => 12]],
            "H"    => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
