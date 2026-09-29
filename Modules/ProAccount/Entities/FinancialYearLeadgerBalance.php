<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialYearLeadgerBalance extends Model
{
    use HasFactory;
    protected $table = "pro_financial_year_leadger_balances";
    protected $fillable = ['showroom_id', 'leadger_id', 'accounting_period_id', 'balance'];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\FinancialYearLeadgerBalanceFactory::new();
    }

    public function leadger()
    {
        return $this->belongsTo(Leadger::class, "leadger_id", "id")->withDefault();
    }

    public function financial_year()
    {
        return $this->belongsTo(FinancialYear::class, "accounting_period_id", "id")->withDefault();
    }
}
