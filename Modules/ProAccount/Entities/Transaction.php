<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "pro_transactions";
    protected $guarded = [];
    public $timestamps = false;

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\TransactionFactory::new();
    }

    public function leadger()
    {
        return $this->belongsTo(Leadger::class, "leadger_id", "id")->withDefault();
    }

    public function fiscal_year()
    {
        return $this->belongsTo(FinancialYear::class, "accounting_period_id", "id")->withDefault();
    }

    public function sub_leadger()
    {
        return $this->belongsTo(SubLeadger::class, "sub_leadger_id", "id")->withDefault(['name' => 'X']);
    }

    public function cash_flow_account()
    {
        return $this->belongsTo(CashFLowAccount::class, "cash_flow_account_id", "id")->withDefault(['name' => 'X']);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, "voucher_id", "id")->with(['transactions','transactions.leadger'])->withDefault();
    }

    public function cash_flow_detail()
    {
        return $this->hasOne(CashFlowDetail::class, "transaction_id", "id")->withDefault();
    }

    public function GetOppositeSideAccount()
    {
        return $this->voucher->transactions->whereNotIn('type', $this->type);
    }

    public function scopeBalanceAmount($query,$type)
    {
        if ($type == 'today'){
            return $query->whereDate('date',Carbon::today());
        } elseif ($type == 'week'){
            return $query->whereBetween('date',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($type == 'month'){
            return $query->whereMonth('date',Carbon::now());
        } elseif ($type == 'year') {
            return $query->where('accounting_period_id',app('financial_year')->id);
        }
        return $query;
    }
}
