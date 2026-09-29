<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\User;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "pro_vouchers";
    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\VoucherFactory::new();
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($category) {
            $category->created_by = auth()->user()->id ?? null;
        });

        static::updating(function ($category) {
            $category->updated_by = auth()->user()->id ?? null;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updator()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by')->withDefault();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, "voucher_id", "id");
    }

    public function transactionsWithTrashed()
    {
        return $this->hasMany(Transaction::class, "voucher_id", "id")->withTrashed();
    }

    public function cash_flow_details()
    {
        return $this->hasMany(CashFlowDetail::class, "voucher_id", "id");
    }

    public function cash_flow_detailsWithTrashed()
    {
        return $this->hasMany(CashFlowDetail::class, "voucher_id", "id")->withTrashed();
    }

    public function balanced_amount_detail()
    {
        return $this->hasOne(BalancedInvoicedDetail::class, "voucher_id", "id");
    }

    public function referable()
    {
        return $this->morphTo()->withDefault();
    }

    public function GetTypeName()
    {
        if ($this->type == "misc") {
            return "JLV";
        }
        else if ($this->type == "cash") {
            return "CLV";
        }
        else if ($this->type == "rec_cash") {
            return "RVC";
        }
        else if ($this->type == "pay_cash") {
            return "PVC";
        }
        else if ($this->type == "bank") {
            return "BLV";
        }
        else if ($this->type == "rec_bank") {
            return "RVB";
        }
        else if ($this->type == "pay_bank") {
            return "PVB";
        }
        else if ($this->type == "pay") {
            return "PV";
        }
        else if ($this->type == "rec") {
            return "RV";
        }
        else {
            return strtoupper(str_replace('_',' ',$this->type));
        }
    }

    public function scopeExpense($query,$type)
    {
        if ($type == 'today')
            return $query->whereDate('date',Carbon::today());
        if ($type == 'week')
            return $query->whereBetween('date',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        if ($type == 'month')
            return $query->whereMonth('date',Carbon::now());
        if ($type == 'year')
        {
            return $query;
        }
        return $query;
    }
}
