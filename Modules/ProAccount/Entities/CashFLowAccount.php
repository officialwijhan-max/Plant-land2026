<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashFLowAccount extends Model
{
    use HasFactory;

    protected $table = "cash_flow_accounts";
    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\CashFLowAccountFactory::new();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, "cash_flow_account_id", "id");
    }

    public function cash_flow_details()
    {
        return $this->hasMany(CashFlowDetail::class, "cash_flow_account_id", "id");
    }
}
