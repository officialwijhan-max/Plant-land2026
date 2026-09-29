<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubLeadger extends Model
{
    use HasFactory;

    protected $table = "pro_sub_leadgers";
    protected $fillable = ["name", 'code', 'level', "description", "is_active", "leadger_id", "created_by", "updated_by", "morphable_type", "morphable_id"];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\SubLeadgerFactory::new();
    }

    public function leadger()
    {
        return $this->belongsTo(Leadger::class, "leadger_id", "id")->withDefault();
    }

    public function morphable()
    {
        return $this->morphTo()->withDefault();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, "sub_leadger_id", "id");
    }

    public function getBalanceAmountAttribute()
    {
        if ($this->leadger->type == 1 || $this->leadger->type == 3) {
            return $this->transactions->where('is_approve', 1)->where('type', 'Dr')->sum('amount') - $this->transactions->where('is_approve', 1)->where('type', 'Cr')->sum('amount');
        } else {
            return $this->transactions->where('is_approve', 1)->where('type', 'Cr')->sum('amount') - $this->transactions->where('is_approve', 1)->where('type', 'Dr')->sum('amount');
        }
    }
}
