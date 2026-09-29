<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Exports\ProductMovementExport;
use Modules\Inventory\Exports\ProductCostingSaleExport;
use Modules\Product\Entities\ProductHistory;
use Modules\Purchase\Entities\CostOfGoodHistory;
use Brian2694\Toastr\Facades\Toastr;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $search_keyword = ($request->has('quick_search')) ? $request->quick_search : null;

            $relational_data = ['productSku:id,product_id', 'productSku.product:id,product_name', 'houseable', 'itemable'];
            $selected_data = ['id', 'type', 'houseable_id', 'houseable_type', 'itemable_id', 'itemable_type', 'product_sku_id', 'in_out', 'date', 'created_by'];

            $query = ProductHistory::query();
            $query = $query->with($relational_data)
                ->where('itemable_id', session()->get('showroom_id'))
                ->where('itemable_type', "Modules\Inventory\Entities\ShowRoom");

            if (isset($search_keyword) && $search_keyword != null) {
                $like = 'LIKE';
                $productHistoryList = DB::table('product_histories')
                    ->join('product_sku', 'product_sku.id', 'product_histories.product_sku_id')
                    ->join('products', 'products.id', 'product_sku.product_id')
                    ->where('type', $like, '%' . $search_keyword . '%')
                    ->orWhere('date', $like, '%' . $search_keyword . '%')
                    ->orWhere('products.product_name', $like, '%' . $search_keyword . '%')
                    ->orWhere('in_out', $like, '%' . $search_keyword . '%')
                    ->select('product_histories.id as id')
                    ->pluck('id');
                $query = $query->whereIn('id', $productHistoryList);
            }
            if ($request->has("import_as")) {
                $data['items'] = $query->latest()->get($selected_data);
            } else {
                if ($row_count == "all") {
                    $total_row = ProductHistory::count();
                    if ($column != null) {
                        $data['items'] = $query->orderBy($column, $sort)->paginate($total_row, $selected_data);
                    } else {
                        $data['items'] = $query->select($selected_data)->latest()->paginate($total_row);
                    }
                } else {
                    if ($column != null) {
                        $data['items'] = $query->orderBy($column, $sort)->paginate($row_count, $selected_data);
                    } else {
                        $data['items'] = $query->latest()->paginate($row_count, $selected_data);
                    }
                }
            }

            if ($request->ajax()) {
                return view('inventory::product_movements.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->import_as == "print") {
                    return view('inventory::product_movements.print', $data);
                }
                if ($request->import_as == "csv") {
                    if (file_exists(public_path("uploads/csv/product-movements.xlsx"))) {
                        unlink(public_path("uploads/csv/product-movements.xlsx"));
                    }
                    Excel::store(new ProductMovementExport($data), 'uploads/csv/product-movements.xlsx', 'public_folder');
                    $filePath = public_path("uploads/csv/product-movements.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-product-movements.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('inventory::product_movements.index', $data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }

    }

    public function cost_of_goods_index(Request $request)
    {
        try {$row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $search_keyword = ($request->has('quick_search')) ? $request->quick_search : null;

            $relational_data = ['productSku:id,product_id', 'productSku.product:id,product_name,image_source,product_type', 'costable', 'storeable'];
            $selected_data = ['id', 'costable_type', 'costable_id', 'storeable_type', 'storeable_id', 'product_sku_id', 'previous_remaining_stock', 'newly_stock', 'previous_cost_of_goods_sold', 'new_cost_of_goods_sold'];

            $query = CostOfGoodHistory::query();
            $query = $query->with($relational_data);

            if ($search_keyword) {
                $query = $query->whereLike(['previous_remaining_stock'], $search_keyword);
            }

            if ($row_count == "all") {
                if ($column != null) {
                    $data['items'] = $query->orderBy($column, $sort)->paginate(CostOfGoodHistory::count(), $selected_data);
                } else {
                    $data['items'] = $query->latest()->paginate(CostOfGoodHistory::count(), $selected_data);
                }
            } elseif ($request->has("import_as") && $request->import_as == "print") {
                $data['items'] = $query->latest()->paginate(CostOfGoodHistory::count());
            } else {
                if ($column != null) {
                    $data['items'] = $query->orderBy($column, $sort)->paginate($row_count, $selected_data);
                } else {
                    $data['items'] = $query->latest()->paginate($row_count, $selected_data);
                }
            }
            if ($request->ajax()) {
                return view('inventory::cost_of_goods.paginate.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->import_as == "print") {
                    return view('inventory::cost_of_goods.paginate.print', $data);
                }
                if ($request->import_as == "csv") {
                    if (file_exists(public_path("uploads/csv/product-costing-sales.xlsx"))) {
                        unlink(public_path("uploads/csv/product-costing-sales.xlsx"));
                    }
                    Excel::store(new ProductCostingSaleExport($data), 'uploads/csv/product-costing-sales.xlsx', 'public_folder');
                    $filePath = public_path("uploads/csv/product-costing-sales.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-product-costing-sales.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('inventory::cost_of_goods.index', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
}
