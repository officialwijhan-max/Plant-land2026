<?php

namespace Modules\Product\Repositories;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\VariantExport;
use Modules\Product\Entities\Variant;
use Modules\Product\Entities\VariantValues;

class VariantRepository implements VariantRepositoryInterface
{
    public function all()
    {
        return Variant::with("values")->orderBy("id", "DESC")->get();
    }

    public function serachBased($search_keyword)
    {
        return Variant::whereLike(['name', 'description'], $search_keyword)->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/variant-list.xlsx"))) {
            unlink(public_path("uploads/csv/variant-list.xlsx"));
        }
        return Excel::store(new VariantExport($data), 'uploads/csv/variant-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = Variant::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Variant::count();

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

    public function create(array $data)
    {

        DB::beginTransaction();
        $variant = new Variant();
        $variant->fill($data)->save();
        $variant_values = [];
        if ($data['variant_values']) {
            foreach ($data['variant_values'] as $value) {
                if ($value) {
                    $variant_values [] = [
                        "value" => $value,
                        "variant_id" => $variant->id,
                        "created_at" => Carbon::now()
                    ];
                }
            }
            VariantValues::insert($variant_values);
        }
        DB::commit();
    }

    public function find($id)
    {
        return Variant::with('values')->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        $variant = Variant::findOrFail($id);
        $variant->update($data);

        $edited_row = [];
        if (isset($data['edit_variant_values'])) {
            foreach ($data['edit_variant_values'] as $key => $value) {
                if ($value) {
                    $variant_values = VariantValues::where('id', $key);
                    if ($variant_values){
                        $variant_values->update([
                            "value" => $value,
                            "updated_at" => Carbon::now()
                        ]);
                        array_push($edited_row, $key);
                    }
                }
            }
            VariantValues::whereNotIn('id', $edited_row)->where('variant_id', $variant->id)->where('used', 0)->delete();
        }

        if (isset($data['add_variant_values'])) {
            foreach ($data['add_variant_values'] as $value) {
                if ($value) {
                    $new_variant_values [] = [
                        "value" => $value,
                        "variant_id" => $variant->id,
                        "created_at" => Carbon::now()
                    ];
                }
            }
            VariantValues::insert($new_variant_values);
        }
        DB::commit();
    }

    public function delete($id)
    {
        $variant = Variant::with('values')->findOrFail($id);
        $used_value = $variant->values()->where('used', 1)->first();
        if ($used_value){
            return false;
        }
        $variant->delete();
        return true;
    }

    public function variantValues($variant)
    {
        return VariantValues::where("variant_id", $variant)->get();
    }

    public function variantWithValues($variant)
    {
        return Variant::where("id", $variant)->with("values")->first();
    }

    public function variantName($productSku)
    {
        $variantName = null;


        if ($productSku->product_variation) {
            $v_name = [];
            $v_value = [];
            foreach (json_decode($productSku->product_variation->variant_id) as $key => $value) {
                array_push($v_name , $this->find($value)->name);
            }
            foreach (json_decode($productSku->product_variation->variant_value_id) as $key => $value) {
                array_push($v_value , VariantValues::find($value)->value);
            }

            for ($i=0; $i < count($v_name); $i++) {
                $variantName .= $v_name[$i] . ' : ' . $v_value[$i];
            }
        }
        return $variantName;
    }

    public function comboVariant($productCombo)
    {
        $variantName = null;

        if ($productCombo->combo_products) {
            $p_name = [];
            $p_qty = [];
            $v_name = [];
            $v_value = [];
            foreach ($productCombo->combo_products as $key => $c_product_detail) {
                array_push($p_name, $c_product_detail->productSku->product->product_name);
                if ($c_product_detail->productSku->product_variation) {
                    foreach (json_decode($c_product_detail->productSku->product_variation->variant_id) as $ke => $value) {
                        array_push($v_name, $this->find($value)->name);
                    }

                    foreach (json_decode($c_product_detail->productSku->product_variation->variant_value_id) as $k => $value) {
                        array_push($v_value, VariantValues::find($value)->value);
                    }
                }
            }

            foreach ($productCombo->combo_products as $key => $c_product_detail) {
                array_push($p_qty, $c_product_detail->product_qty);
            }


            for ($i = 0; $i < count($p_name); $i++) {
                if (!empty($v_name[$i])) {
                    $variantName .= $p_name[$i] . ' -> qty : (' . $p_qty[$i] . ') Specification::' . $v_name[$i] . ' : ' . $v_value[$i] . '; </br>';
                } else {
                    $variantName .= $p_name[$i] . ' -> qty : (' . $p_qty[$i] . ') ; </br>';
                }
            }
        }
        return $variantName;
    }
}
