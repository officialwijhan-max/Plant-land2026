<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountDeposit extends Model
{
    protected $fillable = ['chart_account_id', 'amount', 'source', 'date', 'voucher_id', 'created_by'];

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class, 'chart_account_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
