<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Requests\CategoryFormRequest;
use Modules\Product\Repositories\CategoryRepositoryInterface;
use Brian2694\Toastr\Facades\Toastr;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->categoryRepository = $categoryRepository;
    }

    public function list_select_option(Request $request)
    {
        $data = $this->categoryRepository->listForSelect($request->search);
        return response()->json($data);
    }

    public function list_select_option_by_category(Request $request)
    {
        $data = $this->categoryRepository->listForSelectByCategory($request->category_id, $request->search);
        return response()->json($data);
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $data['items'] = $this->categoryRepository->withPaginate($row_count, $quick_search, $sort, $column, ['categories', 'parentCat', 'products:id,category_id'], ['id', 'name', 'code', 'parent_id', 'description', 'level', 'status']);
            if ($request->ajax()) {
                return view('product::category.paginate_list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->categoryRepository->withPaginate("all", $quick_search, $sort, $column, ['categories', 'parentCat', 'products:id,category_id'], ['id', 'name', 'code', 'parent_id', 'description', 'level', 'status']);
                if ($request->import_as == "print") {
                    return view('product::category.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->categoryRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/category.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-category.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('product::category.category', $data);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function parent_category()
    {
        return $this->categoryRepository->all();
    }

    public function getALl()
    {
        return $this->categoryRepository->all();
    }


    public function allSubCategory()
    {
        return $this->categoryRepository->allSubCategory();
    }

    public function create(Request $request)
    {
        try{
            $search_keyword = null;
            if ($request->input('search_keyword') != null) {
                $search_keyword = $request->input('search_keyword');
                $categories = $this->categoryRepository->serachBased($search_keyword);
            }
            else {
                $categories = $this->categoryRepository->all();
            }

            return view('product::category.category_list', [
                "categories" => $categories
            ]);

        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function store(CategoryFormRequest $request)
    {
        try {
            $item = $this->categoryRepository->create($request->except("_token"));
            if ($request->ajax()) {
                return response()->json(["message" => __('product.Category Added Successfully'), 'item' => $item], 200);
            }
            Toastr::success(__('product.Category Added Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => __('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }



    public function edit($id)
    {
        try {
            $data['item'] = $this->categoryRepository->find($id);
            return view('product::category.edit', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->categoryRepository->update($request->except("_token"), $id);
            if ($request->ajax()) {
                return response()->json(["message" => __('product.Category Updated Successfully')], 200);
            }
            Toastr::success(__('product.Category has been added Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => __('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryRepository->delete($id);
            Toastr::success(__('product.Category Deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
}
