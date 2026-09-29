<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $table = "products";
    protected $primaryKey = "id";

    protected $fillable = [
        "product_name",
        "product_type",
        "model_id",
        "unit_type_id",
        "brand_id",
        "category_id",
        "sub_category_id",
        "origin",
        "description",
        "image_source",
        "created_by",
        "updated_by",
        'price_of_other_currency',
        'hsn', 'length', 'height', 'zip_length', 'flap_length', 'stitches', 'fabric', 'front_sheet', 'wall', 'zipper'
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by = Auth::user()->id ?? null;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id ?? null;
        });
    }

    public function model()
    {
        return $this->belongsTo(ModelType::class, "model_id")->withDefault();
    }

    public function unit_type()
    {
        return $this->belongsTo(UnitType::class)->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id")->withDefault();
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, "sub_category_id")->withDefault();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, "brand_id")->withDefault();
    }

    public function variations()
    {
        return $this->hasMany(ProductVariations::class);
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function houses()
    {
        return $this->morphMany(ProductHistory::class, 'houseable');
    }

    public function scopeBarcodeList($query)
    {
        $array1 = array("C39", "C39+", "C39E", "C39E+", "C93", "POSTNET", "EAN2", "EAN5", "PHARMA2T");
        $array2 = array("I25", "I25+", "PHARMA", "MSI", "MSI+", "RMS4CC", "CODE11", "UPCA", "C128A");
        foreach ($array1 as $key => $value) {
            $data[] = [
                'value' => $value,
                'support' => "Support for BarCode (Letter / Number)"
            ];
        }
        foreach ($array2 as $k => $value) {
            $data[] = [
                'value' => $value,
                'support' => "Support for BarCode (Number)"
            ];
        }
        return $data;
    }

}
