<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashFlowDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "cash_flow_details";
    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\CashFlowDetailFactory::new();
    }

    public function cash_flow_account()
    {
        return $this->belongsTo(CashFLowAccount::class, "cash_flow_account_id")->withDefault();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, "voucher_id")->withDefault();
    }

    public function transaction_data()
    {
        return $this->belongsTo(Transaction::class, "transaction_id")->withDefault();
    }
}
