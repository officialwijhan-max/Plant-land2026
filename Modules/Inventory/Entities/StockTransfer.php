<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Account\Entities\Voucher;
use Modules\ProAccount\Entities\Voucher as ProVoucher;

class StockTransfer extends Model
{
    protected $fillable = ['date','notes','documents','receivable_id','receivable_type'];

    protected $casts = ['documents' => 'array'];

    public function showroomFrom()
    {
        return $this->belongsTo(WareHouse::class,'showroom_from');
    }

    public function refers()
    {
        return $this->morphMany(Voucher::class, 'referable');
    }

    public function proRefers()
    {
        return $this->morphMany(ProVoucher::class, 'referable');
    }

    public function showroomTo()
    {
        return $this->belongsTo(WareHouse::class,'showroom_to');
    }

    public function items()
    {
        return $this->morphMany(ProductItemDetail::class, 'itemable');
    }

    public function sendable()
    {
        return $this->morphTo();
    }

    public function receivable()
    {
        return $this->morphTo();
    }

}
