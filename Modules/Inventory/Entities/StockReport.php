<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\ProductSku;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Product\Entities\ProductHistory;

class StockReport extends Model
{
    protected $fillable = ['stock','product_sku_id','stock_date','houseable_id','houseable_type', 'in', 'out'];

    public function houseable()
    {
        return $this->morphTo();
    }


    public function inProductHistory()
    {
        return $this->hasMany(ProductHistory::class, 'product_sku_id', 'product_sku_id')->whereIn('type', ['begining', 'purchase', 'sales_return']);
    }

    public function outProductHistory()
    {
        return $this->hasMany(ProductHistory::class, 'product_sku_id', 'product_sku_id')->whereIn('type', ['sales', 'purchase_return', 'stock_adjustment']);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function suggestProducts()
    {
        return $this->hasMany(ProductSku::class,'id','product_sku_id');
    }

    public function purchase()
    {
        return $this->hasOne(PurchaseOrder::class,'purchasable_type','houseable_type')->latest()->where('purchasable_id',$this->houseable_id);
    }

    public function items()
    {
        return $this->hasMany(ProductItemDetail::class,'product_sku_id','product_sku_id');
    }

    public function scopeSupplier($query,$supplier)
    {
        return $query->whereHas('items',function ($query) use($supplier){
            $query->whereHasMorph('itemable',PurchaseOrder::class,function ($query) use($supplier){
                $query->where('supplier_id',$supplier);
            });
        });
    }

    public function scopeProductSku($query,$product_sku_id)
    {
        return $query->whereHas('items',function ($query) use($product_sku_id){
            $query->where('itemable_type', PurchaseOrder::class)->where('product_sku_id', $product_sku_id);
        });
    }

    public function scopeBrandProduct($query,$brand_id)
    {
        return $query->whereHas('items',function ($query) use($brand_id){
            $query->where('itemable_type',PurchaseOrder::class)->whereHas('productSku', function ($query) use ($brand_id){
                $query->whereHas('product', function ($query) use ($brand_id){
                    $query->where('brand_id',$brand_id);
                });
            });
        });
    }
}
