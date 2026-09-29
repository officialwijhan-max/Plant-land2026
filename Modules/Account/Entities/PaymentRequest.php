<?php

namespace Modules\Account\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $table = 'payment_requests';
    protected $fillable = [
        'bank_id',
        'accountant_id',
        'staff_id',
        'narration',
        'region',
        'amount',
        'status',
        'rejection_reason'
    ];
    public function bank()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }

    
    public function accountant()
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
   
}
