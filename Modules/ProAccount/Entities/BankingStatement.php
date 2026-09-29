<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;

class BankingStatement extends Model
{
    use HasFactory;

    protected $table = "pro_banking_statements";
    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\BankingStatementFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function leadger()
    {
        return $this->belongsTo(Leadger::class, "leadger_id", "id")->withDefault();
    }

    public function reconcilor()
    {
        return $this->belongsTo(User::class, 're_conciled_by', "id")->withDefault();
    }

    public function banking_statement_details()
    {
        return $this->hasMany(BankingStatementDetail::class, "banking_statement_id", "id")->with(['amounts']);
    }
}
