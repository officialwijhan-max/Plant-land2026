<?php

namespace Modules\ProAccount\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankCheckDetail extends Model
{
    use HasFactory;

    protected $table = "pro_bank_check_details";
    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\BankCheckDetailFactory::new();
    }
}
