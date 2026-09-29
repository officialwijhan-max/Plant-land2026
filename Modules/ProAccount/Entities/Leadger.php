<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leadger extends Model
{
    use HasFactory;

    protected $table = "pro_leadgers";
    protected $fillable = ["name", 'code', 'level', "type", "acc_type", "description", "is_blocked", 'is_cost_center', "configuration_group_id", "is_active", "parent_id", "created_by", "updated_by", "morphable_type", "morphable_id"];


    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\LeadgerFactory::new();
    }

    public function categories()
    {
        return $this->hasMany(Leadger::class, "parent_id", "id")->with('categories:id,name,code,is_cost_center,parent_id,is_active,type,is_blocked','transactions:id,leadger_id,date,type,amount,showroom_id,accounting_period_id');
    }

    public function morphable()
    {
        return $this->morphTo()->withDefault();
    }

    public function parent()
    {
        return $this->belongsTo(Leadger::class, "parent_id")->withDefault();
    }

    public function childrenCategories()
    {
        return $this->hasMany(Leadger::class, "parent_id", "id")->with('categories:id,name,code,is_cost_center,parent_id,is_active,type,is_blocked','transactions:id,leadger_id,date,type,amount,showroom_id,accounting_period_id');
    }

    public function subLedgers()
    {
        return $this->hasMany(SubLeadger::class, "leadger_id", "id");
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, "leadger_id", "id")->wherehas('voucher',function($q) {
                                $q->where('is_approve',1);
                            });
    }

    public function financial_year_leadger_amounts()
    {
        return $this->hasMany(FinancialYearLeadgerBalance::class, "leadger_id", "id");
    }

    public function chart_accounts()
    {
        return $this->hasMany(Leadger::class, "parent_id", "id");
    }

    public function getTypeNameAttribute()
    {
        if ($this->type == 1) {
            return "Asset";
        }elseif ($this->type == 2) {
            return "Liability";
        }elseif ($this->type == 3) {
            return "Expense";
        }elseif ($this->type == 4) {
            return "Income";
        }elseif ($this->type == 5) {
            return "Equity";
        }else {
            return "X";
        }
    }
    
    public function getBalanceAmountByDate($type)
    {
        if (($this->type == 1 || $this->type == 3)) {
            return $this->transactions()->BalanceAmount($type)->where('type', 'Dr')->sum('amount') - $this->transactions()->BalanceAmount($type)->where('type', 'Cr')->sum('amount');
        } else {
            return $this->transactions()->BalanceAmount($type)->where('type', 'Cr')->sum('amount') - $this->transactions()->BalanceAmount($type)->where('type', 'Dr')->sum('amount');
        }
    }

    public function getBalanceAmountAttribute()
    {
        if ($this->type == 1 || $this->type == 3) {
            return $this->transactions->where('type', 'Dr')->where('showroom_id', session()->get('showroom_id'))->where('accounting_period_id', app('financial_year')->id)->sum('amount') - $this->transactions->where('type', 'Cr')->where('showroom_id', session()->get('showroom_id'))->where('accounting_period_id', app('financial_year')->id)->sum('amount');
        } else {
            return $this->transactions->where('type', 'Cr')->where('showroom_id', session()->get('showroom_id'))->where('accounting_period_id', app('financial_year')->id)->sum('amount') - $this->transactions->where('type', 'Dr')->where('showroom_id', session()->get('showroom_id'))->where('accounting_period_id', app('financial_year')->id)->sum('amount');
        }
    }

    public function GetBalanceAmount($accounting_period_id, $showroom_id)
    {
        if ($this->type == 1 || $this->type == 3) {
            return $this->transactions->where('showroom_id', $showroom_id)->where('accounting_period_id',$accounting_period_id)->where('type', 'Dr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('accounting_period_id',$accounting_period_id)->where('type', 'Cr')->sum('amount');
        } else {
            return $this->transactions->where('showroom_id', $showroom_id)->where('accounting_period_id',$accounting_period_id)->where('type', 'Cr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('accounting_period_id',$accounting_period_id)->where('type', 'Dr')->sum('amount');
        }
    }

    public function BalanceAmountBetweenDate($fromDate, $toDate, $showroom_id)
    {
        if ($this->type == 1 || $this->type == 3) {
            if ($showroom_id != 0) {
                return ($this->transactions->where('showroom_id', $showroom_id)->whereBetween('date', array($fromDate, $toDate))->where('type', 'Dr')->sum('amount') -  $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount')) - ($this->transactions->where('showroom_id', $showroom_id)->whereBetween('date', array($fromDate, $toDate))->where('type', 'Cr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount'));
            } else {
                return ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('type', 'Dr')->sum('amount') -  $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount')) - ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('type', 'Cr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount'));
            }
        } else {
            if ($showroom_id != 0) {
                return ($this->transactions->where('showroom_id', $showroom_id)->whereBetween('date', array($fromDate, $toDate))->where('type', 'Cr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount')) - ($this->transactions->where('showroom_id', $showroom_id)->whereBetween('date', array($fromDate, $toDate))->where('type', 'Dr')->sum('amount') -  $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount'));
            } else {
                return ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('type', 'Cr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount')) - ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('type', 'Dr')->sum('amount') -  $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount'));
            }
        }
    }

    public function BalanceAmountTillDate($fromDate, $showroom_id)
    {
        if ($this->type == 1 || $this->type == 3) {
            if ($showroom_id != 0) {
                return $this->transactions->where('showroom_id', $showroom_id)->where('date', '<' ,$fromDate)->where('type', 'Dr')->sum('amount') + $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('date', '<' ,$fromDate)->where('type', 'Cr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount');
            } else {
                return $this->transactions->where('date', '<' ,$fromDate)->where('type', 'Dr')->sum('amount') + $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount') - $this->transactions->where('date', '<' ,$fromDate)->where('type', 'Cr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount');
            }
        } else {
            if ($showroom_id != 0) {
                return $this->transactions->where('showroom_id', $showroom_id)->where('date', '<' ,$fromDate)->where('type', 'Cr')->sum('amount') + $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('date', '<' ,$fromDate)->where('type', 'Dr')->sum('amount') - $this->transactions->where('showroom_id', $showroom_id)->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount');
            } else {
                return $this->transactions->where('date', '<' ,$fromDate)->where('type', 'Cr')->sum('amount') + $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount') - $this->transactions->where('date', '<' ,$fromDate)->where('type', 'Dr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount');
            }
        }
    }

    public function DebitBalanceAmountTillDate($fromDate)
    {
        return $this->transactions->where('date', '<' ,$fromDate)->where('type', 'Dr')->sum('amount') + $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount');
    }

    public function CreditBalanceAmountTillDate($fromDate)
    {
        return $this->transactions->where('date', '<' ,$fromDate)->where('type', 'Cr')->sum('amount') + $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount');
    }

    public function DebitBalanceAmountBetweenDate($fromDate, $toDate, $showroom_id)
    {
        if ($showroom_id != 0) {
            return ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('showroom_id', $showroom_id)->where('type', 'Dr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('showroom_id', $showroom_id)->where('is_opening', 1)->where('type', 'Dr')->sum('amount'));
        } else {
            return ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('type', 'Dr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Dr')->sum('amount'));
        }
    }

    public function CreditBalanceAmountBetweenDate($fromDate, $toDate, $showroom_id)
    {
        if ($showroom_id != 0) {
            return ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('showroom_id', $showroom_id)->where('type', 'Cr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('showroom_id', $showroom_id)->where('is_opening', 1)->where('type', 'Cr')->sum('amount'));
        } else {
            return ($this->transactions->whereBetween('date', array($fromDate, $toDate))->where('type', 'Cr')->sum('amount') - $this->transactions->where('date', $fromDate)->where('is_opening', 1)->where('type', 'Cr')->sum('amount'));
        }
    }

    public function FinancialYearBalance($accounting_period_id, $showroom_id)
    {
        if ($showroom_id != null) {
            $data = $this->financial_year_leadger_amounts->where('showroom_id', $showroom_id)->where('accounting_period_id',$accounting_period_id)->first();
            if ($data) {
                return $data->balance;
            }
            return 0;
        } else {
            $data = $this->financial_year_leadger_amounts->where('accounting_period_id',$accounting_period_id)->sum('balance');
        }
    }

    public function transactionsForFinancialYear()
    {
        return $this->financial_year_leadger_amounts;
    }
}
