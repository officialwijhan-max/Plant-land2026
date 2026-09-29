<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankingStatementDetail extends Model
{
    use HasFactory;

    protected $table = "pro_banking_statement_details";
    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\BankingStatementDetailFactory::new();
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by')->withDefault();
    }

    public function banking_statement()
    {
        return $this->belongsTo(BankingStatement::class)->withDefault()->with(['leadger']);
    }

    public function amounts()
    {
        return $this->hasMany(Transaction::class, "amount", "amount")->where('is_reconciled', 0)->with(['leadger', 'voucher']);
    }

    public function reconciled_amounts()
    {
        return $this->hasMany(Transaction::class, "banking_statement_detail_id", "id")->where('is_reconciled', 1)->with(['leadger', 'voucher']);
    }

    public function getMatcheAmountAttribute()
    {
        if ($this->sign == 1 && ($this->banking_statement->leadger->type == 1 || $this->banking_statement->leadger->type == 3)) {
            return $this->amounts->where('date', $this->date)->where('type', 'Cr');
        }
        if ($this->sign == 0 && ($this->banking_statement->leadger->type == 1 || $this->banking_statement->leadger->type == 3)) {
            return $this->amounts->where('date', $this->date)->where('type', 'Dr');
        }
    }
}
