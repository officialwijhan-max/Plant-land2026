<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialYear extends Model
{
    use HasFactory;

    protected $table = "pro_financial_years";
    protected $fillable = ['start_date','end_date','is_locked'];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\FinancialYearFactory::new();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, "accounting_period_id", "id");
    }

    public function accounts()
    {
        return $this->belongsToMany(Leadger::class, 'transactions', 'accounting_period_id', 'leadger_id');
    }

    public function financial_year_leadger_amounts()
    {
        return $this->hasMany(FinancialYearLeadgerBalance::class, "accounting_period_id", "id");
    }

    public function showTotalBalance($direct_income_ids, $showroom_id = null)
    {
        if ($showroom_id != null) {
            return $this->financial_year_leadger_amounts->where('showroom_id', $showroom_id)->whereIn('leadger_id', $direct_income_ids)->sum('balance');
        } else {
            return $this->financial_year_leadger_amounts->whereIn('leadger_id', $direct_income_ids)->sum('balance');
        }
    }

    public function showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)
    {
        $total_direct_income = $this->showTotalBalance($direct_income_ids, $showroom_id);
        $total_indirect_income = $this->showTotalBalance($indirect_income_ids, $showroom_id);
        $total_direct_expense = $this->showTotalBalance($direct_expense_ids, $showroom_id);
        $total_indirect_expense = $this->showTotalBalance($indirect_expense_ids, $showroom_id);

        $balance = $total_direct_income + $total_indirect_income - $total_direct_expense - $total_indirect_expense;
        return $balance;
    }

    public function showTotalFinancialYearTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)
    {
        $total_balance_before_tax = $this->showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id);

        $total_tax = $total_balance_before_tax * Settings('company_tax') / 100;
        return $total_tax;
    }

    public function showNetTotalProfitLossFinancialYear($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)
    {
        $total_balance_before_tax = $this->showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id);

        $total_tax = $this->showTotalFinancialYearTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id);
        return $total_balance_before_tax - $total_tax;
    }

    public function showLastFinancialYearTax($showroom_id)
    {
        $last_year_tax = 0;
        if ($showroom_id != null) {
            $datas = $this->financial_year_leadger_amounts->where('showroom_id', $showroom_id)->where('leadger_id', Settings('company_tax_leadger'));
        } else {
            $datas = $this->financial_year_leadger_amounts->where('leadger_id', Settings('company_tax_leadger'));
        }
        foreach ($datas as $key => $company_tax_leadger) {
            $last_year_tax += $company_tax_leadger->balance;
        }
        return $last_year_tax;
    }

    public function showLastFinancialYearNetProfitLoss($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id)
    {
        $last_year_tax = $this->showLastFinancialYearTax($showroom_id);
        $before_tax_total = $this->showTotalProfitLossBeforeTax($direct_income_ids,$direct_expense_ids,$indirect_income_ids,$indirect_expense_ids, $showroom_id);
        return $before_tax_total - $last_year_tax;
    }
}
