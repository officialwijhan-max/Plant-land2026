<?php

namespace Modules\Product\Repositories;

use App\Traits\ImageStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Imports\ProductImport;
use Modules\Product\Exports\ServiceExport;
use Modules\Product\Exports\ProductExport;
use Modules\Product\Exports\ComboProductExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Product\Entities\Image;
use Modules\Inventory\Entities\WareHouse;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductHistory;
use Modules\Product\Entities\ComboProduct;
use Modules\Product\Entities\ComboProductDetail;
use Modules\Product\Entities\ProductVariations;
use Modules\Product\Entities\ModelType;
use Modules\Product\Entities\Brand;
use Modules\Inventory\Entities\StockReport;
use Modules\Account\Repositories\JournalRepository;
use Modules\Account\Entities\ChartAccount;
use Modules\Product\Entities\Variant;
use Modules\Product\Entities\VariantValues;
use App\Traits\Accounts;
use stdClass;

class ProductRepository implements ProductRepositoryInterface
{
    use ImageStore, Accounts;

    public function all()
    {
        return ProductSku::with("product", "product.category", "product.unit_type", "product.brand")->whereHas('product', function ($q) {
            return $q->where('product_type', '!=', 'Service');
        })->latest()->get();
    }

    public function allQuery($search_keyword)
    {

        if (isset($search_keyword) && $search_keyword != null) {

            $ProductSKUList = DB::table('product_sku')
                ->join('products', 'products.id', 'product_sku.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->join('models', 'models.id', 'products.model_id')
                ->orWhere('product_sku.sku', 'LIKE', "%{$search_keyword}%")
                ->orWhere('product_sku.purchase_price', 'LIKE', "%{$search_keyword}%")
                ->orWhere('product_sku.selling_price', 'LIKE', "%{$search_keyword}%")
                ->orWhere('models.name', 'LIKE', "%{$search_keyword}%")
                ->orWhere('brands.name', 'LIKE', "%{$search_keyword}%")
                ->orWhere('products.product_name', 'LIKE', "%{$search_keyword}%")
                ->orWhere('products.origin', 'LIKE', "%{$search_keyword}%")
                ->where('products.product_type', '!=', 'Service')
                ->select('product_sku.id as id')
                ->pluck('id');
            return ProductSku::with("product", "suggested", "product.brand", "product.model", "sku_products", "product_variation", "product.category", "product.unit_type", "item.itemable.supplier", "stock", "item")
                ->whereIn('id', $ProductSKUList)
                ->latest();
        } else {
            return ProductSku::with("product", "suggested", "product.brand", "product.model", "sku_products", "product_variation", "product.category", "product.unit_type", "item.itemable.supplier", "stock", "item")->whereHas('product', function ($q) {
                return $q->where('product_type', '!=', 'Service');
            })->latest();
        }
    }

    public function csvDownloadService($data)
    {
        if (file_exists(public_path("uploads/csv/service-list.xlsx"))) {
          unlink(public_path("uploads/csv/service-list.xlsx"));
        }
        return Excel::store(new ServiceExport($data), 'uploads/csv/service-list.xlsx', 'public_folder');
    }

    public function withPaginateService($row_count,$quick_search,$sort,$column, $relational_data = [], $selected_data = ['*'])
    {
        $items = ProductSku::query();
        $items = $items->with($relational_data)->whereHas('product', function ($q) {
                            $q->where('product_type', 'Service');
                        });
        if ($quick_search != null) {
            $items = $items->whereLike(['product.product_name','sku','selling_price'],$quick_search);
        }
        if ($row_count == "all") {
            $total_number = ProductSku::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            }else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            }else {
                return $items->latest()->paginate($row_count,$selected_data);
            }
        }
    }

    public function csvDownloadProduct($data)
    {
        if (file_exists(public_path("uploads/csv/product-list.xlsx"))) {
            unlink(public_path("uploads/csv/product-list.xlsx"));
        }
        return Excel::store(new ProductExport($data), 'uploads/csv/product-list.xlsx', 'public_folder');
    }

    public function csvDownloadCombo($data)
    {
        if (file_exists(public_path("uploads/csv/combo-list.xlsx"))) {
            unlink(public_path("uploads/csv/combo-list.xlsx"));
        }
        return Excel::store(new ComboProductExport($data), 'uploads/csv/combo-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $quick_search = trim($quick_search);
        
        $items = ProductSku::query();
        $items = $items->with($relational_data)->whereHas('product', function ($q) {
            $q->where('product_type','!=', 'Service');
        });

        if ($quick_search != null) {
            $items = $items->whereLike(['sku', 'purchase_price', 'selling_price', 'min_selling_price'], $quick_search)
                ->orWhereHas('product', function ($q) use ($quick_search) {
                    $q->whereLike(['product_name'], $quick_search)
                    ->orWhereHas('brand', function ($b) use ($quick_search) {
                        $b->whereLike(['name'], $quick_search);
                    })
                    ->orWhereHas('category', function ($c) use ($quick_search) {
                        $c->whereLike(['name'], $quick_search);
                    })
                    ->orWhereHas('model', function ($m) use ($quick_search) {
                        $m->whereLike(['name'], $quick_search);
                    });
                })
                ->orWhereHas('item', function ($q) use ($quick_search) {
                    $q->whereHasMorph('itemable', [\Modules\Purchase\Entities\PurchaseOrder::class], function ($q2) use ($quick_search) {
                        $q2->whereHas('supplier', function ($q3) use ($quick_search) {
                            $q3->whereLike(['name'], $quick_search);
                        });
                    });
                });
        }



        if ($name != null) {
            $items = $items->whereLike(['sku'], $name);
        }

        if ($row_count == "all") {
            $total_number = ProductSku::count();

            if ($column != null) {
                return $items->select($selected_data)->orderBy($column, $sort)->paginate($total_number);
            } else {
                return $items->select($selected_data)->latest()->paginate($total_number);
            }
        } else {
            if ($column != null) {
                return $items->select($selected_data)->orderBy($column, $sort)->paginate($row_count);
            } else {
                return $items->select($selected_data)->latest()->paginate($row_count);
            }
        }
    }


    public function withPaginateCombo($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = ComboProduct::query();
        $items = $items->with($relational_data)
                        ->whereHas('combo_products.productSku', function ($query) {
                            $query->HasStock();
                        });
        if ($quick_search != null) {
            $items = $items->whereLike(['name', 'price', 'min_selling_price', 'total_regular_price'], $quick_search);
        }
        if ($name != null) {
            $items = $items->whereLike(['name'], $name);
        }
        if ($row_count == "all") {
            $total_number = ComboProduct::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }


    public function allStockProduct()
    {
        return Product::with("category", "unit_type", "brand", "model")->whereHas('skus', function ($query) {
            $query->HasStock();
        })->where('product_type', '!=', 'Service')->latest()->get();
    }

    public function allService()
    {
        return Product::with("category", "unit_type", "brand", "model")->where('product_type', 'Service')->latest()->get();
    }

    public function allHouseProduct($id, $type)
    {
        return Product::with("category", "unit_type", "brand", "model", 'skus')->whereHas('skus', function ($query) use ($id, $type) {
            $query->StockProduct($id, $type);
        })->latest()->get();
    }

    public function houseComboProduct($id, $type)
    {
        return ComboProduct::whereHas('combo_products.productSku', function ($query) use ($id, $type) {
            $query->StockProduct($id, $type);
        })->latest()->get();
    }

    public function productForPurchase()
    {
        return Product::with("category", "unit_type", "brand", "model", 'skus')->where('product_type', '!=', 'Service')->get();
    }

    public function allComboProduct()
    {
        return ComboProduct::whereHas('combo_products.productSku', function ($query) {
            $query->HasStock();
        })->latest()->get();
    }

    public function searchCombo($search_keyword)
    {
        return ComboProduct::whereLike(['name', 'barcode_type'], $search_keyword)->get();
    }

    public function allProduct()
    {
        return ProductSku::with("product")->HasStock()->latest()->get();
    }

    public function searchBased($search_keyword)
    {
        return Product::whereLike(['product_name', 'product_type', 'skus.sku'], $search_keyword)->get();
    }

    public function searchProduct($search_keyword)
    {
        return Product::where('product_name', $search_keyword)->orWhereHas('skus', function ($query) use ($search_keyword) {
            $query->where('sku', $search_keyword);
        })->get();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        if ($data['product_type'] == "Combo") {
            $comboProduct = new ComboProduct;
            $comboProduct->name = $data['product_name'];
            $comboProduct->showroom_id = session()->get('showroom_id') ?? 1;
            $comboProduct->barcode_id = '2000-' . Str::random(12);
            $comboProduct->barcode_type = $data['barcode_type'];
            $comboProduct->price = $data['combo_selling_price'];
            $comboProduct->total_purchase_price = $data['purchase_price'];
            $comboProduct->total_regular_price = $data['selling_price'];
            $comboProduct->min_selling_price = $data['min_selling_price'];
            $comboProduct->description = $data['product_description'];
            $comboProduct->image_source = isset($data['file']) ? $this->saveImage($data['file'], 94, 94) : null;
            if ($comboProduct->save()) {
                foreach ($data['selected_product_id'] as $key => $product_id) {
                    $comboProductDetail = new ComboProductDetail;
                    $comboProductDetail->combo_product_id = $comboProduct->id;
                    $comboProductDetail->product_sku_id = $product_id;
                    $comboProductDetail->product_qty = $data['selected_product_qty'][$key];
                    $comboProductDetail->save();
                }
            }
        } else {
            $product = new Product();
            $data = Arr::add($data, 'description', $data['product_description']);
            $data['description'] = $data['product_description'];
            if (isset($data['file'])) {
                $data = Arr::add($data, 'image_source', $this->saveImage($data['file'], 94, 94));
            }
            $product->fill(Arr::except($data, ['product_description']))->save();

            if ($data['product_type'] == "Variable") {
                if (!empty($data['selected_variant'])) {
                    $selected_variant = count($data['selected_variant']);
                    $variation_type_combination = collect($data['variation_type'])->chunk($selected_variant)->toArray();
                    $variation_value_combination = collect($data['variation_value_id'])->chunk($selected_variant)->toArray();
                    $product_variations = [];

                    foreach ($variation_type_combination as $key => $combined_value) {
                        if (!empty($data['variation_sku']) && $data['variation_sku'][$key] != null) {
                            $new_sku = $data['variation_sku'][$key];
                        } else {
                            $new_sku = (strlen($data['product_name']) <= 10) ? str_replace(' ','-',$data['product_name']) : Str::limit(str_replace(' ','-',$data['product_name']), 9, '');
                        }
                        $productSku = new ProductSku;
                        $productSku->product_id = $product->id;
                        $productSku->sku = (ProductSku::where('sku', $new_sku)->first() == null) ? $new_sku : $new_sku . Str::random(6);
                        $productSku->cost_of_goods = $data['purchase_prices'][$key] ?? 0;
                        $productSku->alert_quantity = $data['alert_quantities'][$key] ?? 0;
                        $productSku->purchase_price = $data['purchase_prices'][$key] ?? 0;
                        $productSku->min_selling_price = $data['min_selling_prices'][$key] ?? 0;
                        $productSku->selling_price = $data['selling_prices'][$key] ?? 0;
                        $productSku->tax = ($data['tax']) ? $data['tax'] : 0;
                        $productSku->tax_type = 'percent';
                        $productSku->barcode_id = '1000-' . $product->id . '-' . Str::random(12);
                        $productSku->barcode_type = $data['barcode_type'];
                        $productSku->save();

                        $product_variations [] = [
                            "product_id" => $product->id,
                            "variant_id" => json_encode(array_values($combined_value)),
                            "product_sku_id" => $productSku->id,
                            "variant_value_id" => json_encode(array_values($variation_value_combination[$key])),
                            "image_source" => isset($data['variation_file'][$key]) ? $this->saveImage($data['variation_file'][$key], 94, 94) : null,
                            "created_by" => Auth::user()->id ?? null,
                            "updated_by" => Auth::user()->id ?? null,
                            "created_at" => Carbon::now(),
                        ];

                        foreach ($data['variation_value_id'] as $value) {
                            $variation_value = VariantValues::where('id', $value)->where('used', 0)->first();
                            if ($variation_value) {
                                $variation_value->used = 1;
                                $variation_value->save();
                            }
                        }
                    }
                    ProductVariations::insert($product_variations);
                }

            } else {
                if (!empty($data['product_sku'])) {
                    $new_sku = $data['product_sku'];
                } else {
                    $new_sku = (strlen($data['product_name']) <= 10) ? str_replace(' ','-',$data['product_name']) : Str::limit(str_replace(' ','-',$data['product_name']), 9, '');

                }

                if ($data['product_type'] == "Service") {
                    $data['selling_price'] = $data['hourly_rate'];
                }
                $productSku = new ProductSku;
                $productSku->product_id = $product->id;

                $productSku->cost_of_goods = array_key_exists('purchase_price', $data) ? $data['purchase_price'] : '';
                $productSku->alert_quantity = array_key_exists('alert_quantity', $data) ? $data['alert_quantity'] : '';
                $productSku->purchase_price = array_key_exists('purchase_price', $data) ? $data['purchase_price'] : '';
                $productSku->selling_price = $data['selling_price'];
                $productSku->min_selling_price = $data['min_selling_price'];
                $productSku->tax = $data['tax'];
                $productSku->tax_type = $data['tax_type'];
                $productSku->barcode_id = '1000-' . $product->id . '-' . Str::random(12);
                $productSku->barcode_type = $data['barcode_type'];
                $productSku->save();
                $productSku->fresh();
                $productSku->sku = $new_sku . '-' . $productSku->id;
                $productSku->save();
            }
        }
        DB::commit();
        return $data['product_type'] == "Combo" ? $comboProduct : $product;
    }

    public function find($id)
    {
        return Product::with("unit_type", "category", "subcategory", "model", "brand", "variations")->findOrFail($id);
    }

    public function findCombo($id)
    {
        return ComboProduct::findOrFail($id);
    }

    public function findSku($id)
    {
        return ProductSku::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        DB::beginTransaction();
        if ($data['product_type'] == "Combo") {
            $comboProduct = ComboProduct::findOrFail($id);
            $comboProduct->name = $data['product_name'];
            $comboProduct->barcode_type = $data['barcode_type'];
            $comboProduct->price = $data['combo_selling_price'];
            $comboProduct->min_selling_price = $data['min_selling_price'];
            $comboProduct->total_purchase_price = $data['purchase_price'];
            $comboProduct->total_regular_price = $data['selling_price'];
            $comboProduct->min_selling_price = $data['min_selling_price'];
            $comboProduct->description = $data['product_description'];
            if (isset($data['file'])) {
                if (File::exists($comboProduct->image_source)) {
                    File::delete($comboProduct->image_source);
                }
                $comboProduct->image_source = $this->saveImage($data['file'], 94, 94);
            }

            if ($comboProduct->save()) {
                foreach ($data['selected_product_id'] as $key => $product_id) {
                    $comboProductDetail = ComboProductDetail::where('product_sku_id', $product_id)->where('combo_product_id', $comboProduct->id)->first();
                    $comboProductDetail->product_sku_id = $product_id;
                    $comboProductDetail->product_qty = $data['selected_product_qty'][$key];
                    $comboProductDetail->save();
                }
            }
        } else {
            $product = Product::findOrFail($id);
            if (isset($data['file'])) {
                if (File::exists($product->image_source)) {
                    File::delete($product->image_source);
                }
                $data = Arr::add($data, 'image_source', $this->saveImage($data['file'], 94, 94));
            }
            $data = Arr::add($data, 'description', $data['product_description']);
            $product->fill(Arr::except($data, ['product_description']))->save();

            if ($data['product_type'] == "Variable") {

                if (!empty($data['selected_variant'])) {
                    $selected_variant = count($data['selected_variant']);
                    $variation_type_combination = collect($data['variation_type'])->chunk($selected_variant)->toArray();
                    $variation_value_combination = collect($data['variation_value_id'])->chunk($selected_variant)->toArray();
                    $product_variations = [];
                    foreach ($variation_type_combination as $key => $combined_value) {
                        if (array_key_exists('product_sku_ids', $data) && array_key_exists($key, $data['product_sku_ids'])) {
                            $id = $data['product_sku_ids'][$key];
                            $productSku = ProductSku::find($id);
                        } else
                            $productSku = new ProductSku;

                        $productSku->product_id = $product->id;
                        $productSku->sku = $data['variation_sku'][$key];
                        $productSku->cost_of_goods = $data['purchase_prices'][$key] ?? 0;
                        $productSku->alert_quantity = $data['alert_quantities'][$key] ?? 0;
                        $productSku->purchase_price = $data['purchase_prices'][$key] ?? 0;
                        $productSku->selling_price = $data['selling_prices'][$key] ?? 0;
                        $productSku->min_selling_price = $data['min_selling_prices'][$key] ?? 0;
                        $productSku->tax = $data['tax'];
                        $productSku->tax_type = 'percent';
                        $productSku->barcode_type = $data['barcode_type'];
                        $productSku->save();
                        if (isset($data['variation_id'][$key])) {
                            $product_variation = ProductVariations::where("id", $data['variation_id'][$key])->first();
                            if ($product_variation) {
                                $update_product_variation = [
                                    "product_id" => $product->id,
                                    "variant_id" => json_encode(array_values($combined_value)),
                                    "product_sku_id" => $productSku->id,
                                    "variant_value_id" => json_encode(array_values($variation_value_combination[$key])),
                                    "image_source" => isset($data['variation_file'][$key]) ? $this->saveImage($data['variation_file'][$key], 94, 94) : $data['old_image'][$key],
                                    "created_by" => Auth::user()->id ?? null,
                                    "updated_by" => Auth::user()->id ?? null,
                                    "created_at" => Carbon::now(),
                                    "id" => $data['variation_id'][$key]
                                ];

                                $product_variation->forceFill($update_product_variation)->save();
                            }

                        } else {
                            $product_variations [] = [
                                "product_id" => $product->id,
                                "variant_id" => json_encode(array_values($combined_value)),
                                "product_sku_id" => $productSku->id,
                                "variant_value_id" => json_encode(array_values($variation_value_combination[$key])),
                                "image_source" => isset($data['variation_file'][$key]) ? $this->saveImage($data['variation_file'][$key], 94, 94) : $data['old_image'][$key],
                                "created_by" => Auth::user()->id ?? null,
                                "updated_by" => Auth::user()->id ?? null,
                                "created_at" => Carbon::now()
                            ];
                        }

                        foreach ($data['variation_value_id'] as $value) {
                            $variation_value = VariantValues::where('id', $value)->where('used', 0)->first();
                            if ($variation_value) {
                                $variation_value->used = 1;
                                $variation_value->save();
                            }
                        }
                    }
                    ProductVariations::insert($product_variations);
                }
            } else {
                $productSku = ProductSku::where("product_id", $id)->first();

                if ($data['product_type'] == "Service") {
                    $data['selling_price'] = $data['hourly_rate'];
                }

                if (!empty($data['product_sku'])) {

                    $productSku->sku = $data['product_sku'];
                    $productSku->cost_of_goods = $data['purchase_price'];
                    $productSku->alert_quantity = $data['alert_quantity'];
                    $productSku->purchase_price = $data['purchase_price'];
                    $productSku->selling_price = $data['selling_price'];
                    $productSku->min_selling_price = $data['min_selling_price'] ?? 0;
                }
                $productSku->tax = $data['tax'];
                $productSku->tax_type = $data['tax_type'];
                $productSku->barcode_type = $data['barcode_type'];
                $productSku->save();
            }
        }
        DB::commit();
    }


    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $variations = ProductVariations::where("product_id", $id)->get();
        $productSkus = ProductSku::where("product_id", $id)->get();

        foreach ($productSkus as $p_sku) {
            $comboDetails = ComboProductDetail::where("product_sku_id", $p_sku->id)->first();
            if ($comboDetails != null) {
                $comboDetails->delete();
            }
            $p_sku->stocks()->delete();
            $p_sku->costOfGoodsPrices()->delete();
            $p_sku->delete();
        }

        if (File::exists($product->image_source)) {
            File::delete($product->image_source);
        }
        foreach ($variations as $variation) {
            if (File::exists($variation->image_source)) {
                File::delete($variation->image_source);
            }
        }

        $product->houses()->delete();
        $product->delete();
    }


    public function deleteCombo($id)
    {
        $comboProduct = ComboProduct::findOrFail($id);
        if (File::exists($comboProduct->image_source)) {
            File::delete($comboProduct->image_source);
        }
        foreach ($comboProduct->combo_products as $comboDetails) {
            $comboDetails->delete();
        }
        $comboProduct->delete();
    }

    public function decreaseQuantity($id, $quantity)
    {
        $product = ProductSku::find($id);
        $product->stock_quantity -= $quantity;
        $product->save();
    }

    public function increaseQuantity($id, $quantity)
    {
        $product = ProductSku::find($id);
        $product->stock_quantity += $quantity;
        $product->save();
    }


    public function getPrice($ids, $purchasePrice, $sellPrice)
    {
        $item = ProductSku::whereIn('id', $ids)->with('product')->get();
        $data['name'] = $item;
        $data['newpurchasePrice'] = $item->sum('purchase_price');
        $data['newsellPrice'] = $item->sum('selling_price') * ((($item->sum('tax') / $item->count('tax')) / 100) + 1);
        return $data;
    }

    public function checkQuantity($data)
    {
        $msg = '';

        // Check if house is provided
        if (array_key_exists('house', $data) && !$data['house']) {
            return trans('sale.Select Warehouse or Showroom');
        }

        // Determine house type (Warehouse or Showroom)
        if (array_key_exists('house', $data) && !empty($data['house'])) {
            $type = explode('-', $data['house']);
            if ($type[0] == "warehouse") {
                $house = WareHouse::find($type[1]);
            } else {
                $house = ShowRoom::find($type[1]);
            }
        } else {
            $house = ShowRoom::find(session()->get('showroom_id'));
        }

        // Check for combo products
        if (array_key_exists('type', $data) && $data['type'] == 'combo') {
            $combo_products = ComboProductDetail::where('combo_product_id', 1)->get();
            foreach ($combo_products as $product) {
                $sku = ProductSku::find($product->product_sku_id);
                if ($sku->product->product_type != "Service") {
                    $quantity = $house->stocks()->where('product_sku_id', $product->product_sku_id)->first();
                    $product_quantity = $data['quantity'] * $product->product_qty;

                    // If product quantity exceeds stock
                    if (!$quantity || $product_quantity > $quantity->stock) {
                        return trans('sale.In your stock you have only') . ' ' . ($quantity->stock ?? 0) . ' ' . trans('sales::sale.items left') . ' ' . trans('sales::sale.For any of one product in Combo');
                    }
                }
            }
        } else {
            // Check for individual product
            $product_sku = ProductSKU::find($data['id']);
            if ($product_sku->product->product_type != "Service") {
                $quantity = $house->stocks()->where('product_sku_id', $data['id'])->first();

                if (!$quantity) {
                    return trans('product.Oops,product not available');
                } else {
                    $stock = $quantity->stock;

                    // If requested quantity exceeds available stock
                    if ($data['quantity'] > $stock) {
                        return trans('product.In your stock you have only') . ' ' . $stock . ' ' . trans('product.items left');
                    }
                }
            } else {
                // For services, just return the quantity (if needed)
                $msg = '';
            }
        }

        // Return empty message (no issues) or the appropriate message
        return $msg;
    }


    public function checkNumberofQuantity($data)
    {
        $msg = '';

        if (array_key_exists('house', $data) && !empty($data['house'])) {
            $type = explode('-', $data['house']);
            if ($type[0] == "warehouse") {
                $house = WareHouse::find($type[1]);
            } else {
                $house = ShowRoom::find($type[1]);
            }
        } else {
            $house = ShowRoom::find(session()->get('showroom_id'));
        }

        if (array_key_exists('type', $data) && $data['type'] == 'combo') {
            $combo_products = ComboProductDetail::where('combo_product_id', 1)->get();
            foreach ($combo_products as $key => $product) {
                $sku = ProductSku::find($product->product_sku_id);
                if ($sku->product->product_type != "Service") {
                    $quantity = $house->stocks()->where('product_sku_id', $product->product_sku_id)->first();
                    $product_quantity = $data['quantity'] * $product->product_qty;
                    if (!$quantity) {
                        $msg = 0;
                        return $msg;
                    } else {
                        $stock = $quantity->stock;
                        if ($product_quantity > $stock)
                            return $msg = 0;
                    }
                }
            }
        } else {
            $product_sku = ProductSKU::find($data['id']);
            if ($product_sku->product->product_type != "Service") {
                $quantity = $house->stocks()->where('product_sku_id', $data['id'])->first();

                if (!$quantity) {
                    $msg = 0;
                    return $msg;
                } else {
                    $stock = $quantity->stock;
                    if ($data['quantity'] > $stock)
                        $msg = $stock;
                }
            } else {
                $msg = '';
            }
        }
        return $msg;
    }

    public function allBarcode()
    {
        return Product::BarcodeList();
    }

    public function productList($id, $type)
    {
        $productList = [];

        $products = $this->allHouseProduct($id, $type);
        $combos = $this->houseComboProduct($id, $type);

        foreach ($products as $key => $product) {
            $item = new stdClass();
            $item->product_name = $product->product_name;
            $item->product_type = $product->product_type;
            $item->image_source = $product->image_source;
            $item->origin = $product->origin;
            $item->brand_name = @$product->brand->name;
            $item->model_name = @$product->model->name;
            if ($product->product_type == "Single") {
                $sku = $product->skus->first();
                $item->product_id = $sku->id;
                $item->product_sku = $sku->sku;
            } else {
                $item->product_id = $product->id;
                $item->product_sku = '';
            }
            array_push($productList, $item);
        }
        foreach ($combos as $key => $product) {
            $item = new stdClass();
            $item->product_id = $product->id;
            $item->product_name = $product->name;
            $item->origin = $product->origin;
            $item->product_sku = '';
            $item->product_type = "Combo";
            $item->brand_name = @$product->brand->name;
            $item->model_name = @$product->model->name;
            $item->image_source = $product->image_source;
            array_push($productList, $item);
        }


        return $productList;
    }

    public function serviceList()
    {
        $productList = [];


        $services = $this->allService();

        foreach ($services as $key => $product) {
            $item = new stdClass();

            $item->product_id = $product->skus->first()->id;

            $item->product_name = $product->product_name;
            $item->product_type = $product->product_type;
            $item->brand_name = @$product->brand->name;
            $item->model_name = @$product->model->name;
            $item->image_source = $product->image_source;

            array_push($productList, $item);
        }

        return $productList;
    }

    public function stockProductList($type, $id, $house)
    {
        $ProductList = [];
        $products = $type == 'purchase' ? $this->productForPurchase() : $this->allHouseProduct($id, $house);

        foreach ($products as $key => $product) {
            $item = new stdClass();
            if ($product->product_type == "Single") {
                $item->product_id = $product->skus->first()->id;
            } else {
                $item->product_id = $product->id;
            }
            $item->product_name = $product->product_name;
            $item->product_type = $product->product_type;
            $item->brand = @$product->brand->name;
            $item->model = @$product->model->name;
            $item->origin = @$product->origin;
            array_push($ProductList, $item);
        }
        $execpt_service = ['purchase', 'transfer'];

        if (!in_array($type, $execpt_service)) {
            $services = $this->allService();

            foreach ($services as $key => $product) {
                $item = new stdClass();

                $item->product_id = $product->skus->first()->id;

                $item->product_name = $product->product_name;
                $item->product_type = $product->product_type;

                array_push($ProductList, $item);
            }
        }

        return $ProductList;
    }

    public function stockAlert($type)
    {
        if ($type == 'all')
            return ProductSku::latest()->get();
        else
            return ProductSku::latest()->take(10)->get();
    }


    public function csv_upload_single_product($data)
    {
        if (!empty($data['file'])) {
            ini_set('max_execution_time', 0);
            $fileName = time() . '_' . $data['file']->getClientOriginalName();
            request()->file('file')->storeAs('reports', $fileName, 'public');

            Excel::import(new ProductImport, request()->file('file'));
        }
    }

    public function listForSelectPurchaseProduct($search)
    {
        if ($search != ''){
            $items = Product::with("brand", "model", 'skus')
                            ->whereLike(['product_name'], $search)
                            ->where('product_type', '!=', 'Service')
                            ->paginate(20);
        }
        else{
            $items =Product::with("brand", "model", 'skus')->where('product_type', '!=', 'Service')->paginate(20);
        }
        $response = [];
        foreach ($items as $item) {
            if ($item->product_type == "Single") {
                $id = $item->skus->first()->id;
            } else {
                $id = $item->id;
            }
            if (app('general_setting')->origin == 1) {
                $origin = __('common.Part Number').' - '.$item->origin.';';
            } else {
                $origin = '';
            }
            if ($item->brand_id != 0) {
                $brand = ' > '.__('product.Brand').' - '.$item->brand->name.';';
            } else {
                $brand = '';
            }
            if ($item->model_id != 0) {
                $model = ' > '.__('product.Model').' - '.$item->model->name.';';
            } else {
                $model = '';
            }
            $response[]  = [
                'id'    => $id.'-'.$item->product_type,
                'text'  => $item->product_name . $origin . $brand . $model
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectProductSKU($search)
    {
        if ($search != ''){
            $items = ProductSku::with("product", "product.brand", "product.model")->whereHas('product', function ($q) {
                                    $q->where('product_type', '!=', 'Service');
                                })
                                ->whereLike(['product.product_name'], $search)
                                ->paginate(20);
        }
        else{
            $items = ProductSku::with("product", "product.brand", "product.model")->whereHas('product', function ($q) {
                                    $q->where('product_type', '!=', 'Service');
                                })
                                ->paginate(20);
        }
        $response = [];
        foreach ($items as $item) {
            $id = $item->id;
            if (app('general_setting')->origin == 1) {
                $origin = __('common.Part Number').' - '.$item->product->origin.';';
            } else {
                $origin = '';
            }
            if ($item->product->brand_id != 0) {
                $brand = ' > '.__('product.Brand').' - '.$item->product->brand->name.';';
            } else {
                $brand = '';
            }
            if ($item->product->model_id != 0) {
                $model = ' > '.__('product.Model').' - '.$item->product->model->name.';';
            } else {
                $model = '';
            }
            if (variantNameFromSku($item)) {
                $variantNameFromSku = '('.variantNameFromSku($item).')';
            } else {
                $variantNameFromSku = "";
            }
            $response[]  = [
                'id'    => $id.'-'.$item->product_type,
                'text'  => $item->product->product_name . $variantNameFromSku  . $origin . $brand . $model
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectService($search)
    {
        if ($search != ''){
            $items = Product::whereLike(['product_name'], $search)
                            ->where('product_type', 'Service')
                            ->paginate(20);
        }
        else{
            $items = Product::where('product_type', 'Service')->paginate(20);
        }
        $response = [];
        foreach ($items as $item) {
            $id = $item->skus->first()->id;

            $response[]  = [
                'id'    => $id.'-'.$item->product_type,
                'text'  => $item->product_name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectStockProduct($search, $house, $purpose_filter)
    {
        $split_house = explode('-',$house);
        $house_type = $split_house[0] == "showroom" ? ShowRoom::class : WareHouse::class;
        $house_id = $split_house[1];

        if ($search != ''){
            $items = Product::with("unit_type", "brand", "model", 'skus')
                            ->whereLike(['product_name'], $search)
                            ->whereHas('skus', function ($query) use ($house_id, $house_type) {
                                $query->StockProduct($house_id, $house_type);
                            })->latest()
                            ->paginate(15);
            if ($purpose_filter == "sale") {
                $combo_items = ComboProduct::whereLike(['name'], $search)
                                            ->whereHas('combo_products.productSku', function ($query) use ($house_id, $house_type) {
                                                $query->StockProduct($house_id, $house_type);
                                            })->latest()
                                            ->paginate(15);
            }
        }
        else{
            $items = Product::with("unit_type", "brand", "model", 'skus')
                            ->whereHas('skus', function ($query) use ($house_id, $house_type) {
                                $query->StockProduct($house_id, $house_type);
                            })->latest()
                            ->paginate(15);

            if ($purpose_filter == "sale") {
                $combo_items = ComboProduct::whereHas('combo_products.productSku', function ($query) use ($house_id, $house_type) {
                                                $query->StockProduct($house_id, $house_type);
                                            })->latest()
                                            ->paginate(15);
            }
        }

        $response = [];

        foreach ($items as $item) {
            if ($item->product_type == "Single") {
                $sku = $item->skus->first();
                $id = $sku->id;
            } else {
                $id = $item->id;
            }
            if (app('general_setting')->origin == 1) {
                $origin = __('common.Part Number').' - '.$item->origin.';';
            } else {
                $origin = '';
            }
            if ($item->brand_id != 0) {
                $brand = ' > '.__('product.Brand').' - '.$item->brand->name.';';
            } else {
                $brand = '';
            }
            if ($item->model_id != 0) {
                $model = ' > '.__('product.Model').' - '.$item->model->name.';';
            } else {
                $model = '';
            }

            $response[]  = [
                'id'    => $id.'-'.$item->product_type,
                'text'  => $item->product_name . $origin . $brand . $model
            ];
        }

        if ($purpose_filter == "sale") {
            foreach ($combo_items as $item) {

                $id = $item->id;

                $response[]  = [
                    'id'    => $id.'-Combo',
                    'text'  => $item->name
                ];
            }
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function lodeMoreProductList($id, $type, $skip_single, $skip_combo, $search_keyword, $category_id, $brand_id, $model_id, $totalProduct = 0)
    {
        $productListData = collect();

        //check if total grater then skip
        $totalProduct;
        if ($totalProduct == 0) {
            $totalProduct =  Product::count();
        }
        $totalCombo = ComboProduct::count();

        if ($totalProduct >= $skip_single) {

            $ProductList = Product::query();

            if ($search_keyword != null) {
                $productListSKU = ProductSku::whereLike(['sku'], $search_keyword)->first();
            }
            // start newly
            if ($search_keyword != null) {
                $ProductList->whereLike(['product_name', 'origin', 'brand.name', 'model.name'], $search_keyword)
                    ->when(isset($productListSKU['product_id']), function ($query) use ($productListSKU) {
                        return $query->orWhere('id', $productListSKU['product_id']);
                    });
            }
            if ($category_id !== '0' && $category_id !== null) {
                $ProductList->where('category_id', $category_id);
            }
            if ($brand_id !== '0' && $brand_id !== null) {
                $ProductList->where('brand_id', $brand_id);
            }
            if ($model_id !== '0' && $model_id !== null) {
                $ProductList->where('model_id', $model_id);
            }

            //
            $products = $ProductList->with(["brand", "model", 'skus'])->orderBy('product_name')->skip($skip_single)->take(25)->get();

            $product_skus = ProductSku::whereIn('product_id', $products->pluck('id'))
                ->skip($skip_single)
                ->take(25)
                ->get()
                ->pluck('id');

            $ProductStockList = StockReport::whereIn('product_sku_id', $product_skus)
                ->where('houseable_type', 'Modules\Inventory\Entities\ShowRoom')
                ->where('houseable_id', session()->get('showroom_id'))
                ->with(['productSku','productSku.product_variation', 'productSku.product', 'productSku.product.brand', 'productSku.product.model'])
                ->get();

            foreach ($ProductStockList as $stock) {
                $item = new \stdClass();
                $item->product_name = $stock->productSku->product->product_name;
                $item->product_type = $stock->productSku->product->product_type;
                if ($stock->productSku->product->product_type=='Variable') {
                    $item->image_source = $stock->productSku->product_variation->image_source;
                } else {
                    $item->image_source = $stock->productSku->product->image_source;
                }

                $item->origin = $stock->productSku->product->origin;
                $item->selling_price = $stock->productSku->selling_price;

                $item->brand_name = @$stock->productSku->product->brand->name;
                $item->model_name = @$stock->productSku->product->model->name;
                $item->product_id = $stock->product_sku_id;
                $item->product_sku = $stock->productSku->sku;
                $item->sku = $stock->productSku;
                $item->stock = $stock->stock;
                $item->is_combo = false;

                if ((int)$item->stock > 0) {
                    $productListData->push($item);
                }
            }

            $non_inventory_products = ProductSku::whereIn('product_id', $products->pluck('id'))
                ->whereHas('product', function($q){
                    return $q->where('product_type','Service');
                })
                ->skip($skip_single)
                ->take(25)
                ->get();

            foreach ($non_inventory_products as $productSku) {
                $item = new \stdClass();
                $item->product_name = $productSku->product->product_name;
                $item->product_type = $productSku->product->product_type;
                $item->image_source = $productSku->product->image_source;
                $item->origin = $productSku->product->origin;
                $item->selling_price = $productSku->selling_price;

                $item->brand_name = @$productSku->product->brand->name;
                $item->model_name = @$productSku->product->model->name;
                $item->product_id = $productSku->id;
                $item->product_sku = $productSku->sku;
                $item->sku = $productSku;
                $item->stock = "Service";
                $item->is_combo = false;

                $productListData->push($item);
            }
        }

        if ($totalCombo >= $skip_combo) {

            $ComboProductList = ComboProduct::query();
            if ($search_keyword !== null) {
                $ComboProductList->whereLike(['name'], $search_keyword);
            }
            $combos = $ComboProductList->whereHas('combo_products.productSku', function ($query) use ($id, $type) {
                $query->StockProduct($id, $type)->take(1);
            })
            ->orderBy('name')
            ->skip($skip_combo)
            ->take(10)
            ->get();

            foreach ($combos as $key => $product) {
                $item = new \stdClass();
                $item->product_id = $product->id;
                $item->product_name = $product->name;
                $item->selling_price = $product->price;
                $item->origin = "X";
                $item->product_sku = "X";
                $item->sku = "";
                $item->product_type = "Combo";
                $item->brand_name = "X";
                $item->model_name = "X";
                $item->image_source = $product->image_source;
                $item->is_combo = true;
                $item->stock = '';
                $productListData->push($item);
            }
        }

        //Take 10 item from collecotion $productList
        $newProductLIst = $productListData->sortBy('product_name')->take(10);
        //Count table wise data after collect new data
        $single_take_count = $newProductLIst->where('is_combo', false);
        $combo_take_count = $newProductLIst->where('is_combo', true);

        return [
            'ProductList'      => $newProductLIst,
            'single_skip'      => $single_take_count,
            'combo_skip'       => $combo_take_count
        ];
    }

    public function listForSelectProductOpeningStock($search, $brand_id, $model_id, $category_id)
    {
        $productListData = collect();
        $ProductSKUIdList = DB::table('product_sku')
            ->join('products', 'products.id', 'product_sku.product_id')
            ->when($brand_id, function ($query) use ($brand_id) {
                $query->where('brand_id', $brand_id);
            })->when($model_id, function ($query) use ($model_id) {
                $query->where('model_id', $model_id);
            })->when($search, function($q) use ($search) {
                $q->where('product_name','LIKE',"%{$search}%");
            })
            ->select('product_sku.id as id')
            ->pluck('id');

        $ProductList = ProductSku::with('product:id,product_name,product_type,origin,brand_id,model_id', 'product.brand:id,name', 'product.model:id,name')
            ->whereIn('id', $ProductSKUIdList)
            ->select('id', 'product_id', 'sku')
            ->get();

        $ProductVarintList = Variant::select('id', 'name')->get();
        $ProductVarintValueList = VariantValues::select('id', 'value')->get();

        $ProductListData = $ProductList->map(function ($productSKU) use ($ProductVarintList, $ProductVarintValueList) {
            $productSKU['product_name'] = ($productSKU->product->product_type != 'Single') ?
                $productSKU->product->product_name . ' (' . productVarinatDetail($ProductVarintList, $ProductVarintValueList, $productSKU) . ')' : $productSKU->product->product_name;
            return $productSKU;
        });

        $response = [];
        foreach ($ProductListData as $item) {
            if (app('general_setting')->origin == 1) {
                $response[]  = [
                    'id'    => $item->id . '-' . $item->product->product_type,
                    'text'  => $item->product_name . '; Origin : ' . $item->product->origin . '; Brand : ' . $item->product->brand->name . '; Model : ' . $item->product->model->name
                ];
            } else {
                $response[]  = [
                    'id'    => $item->id . '-' . $item->product->product_type,
                    'text'  => $item->product_name . '; Brand : ' . $item->product->brand->name . '; Model : ' . $item->product->model->name
                ];
            }
        }
        $data['results'] =  $response;
        if ($productListData->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }
}
