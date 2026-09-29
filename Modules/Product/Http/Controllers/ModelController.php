<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Requests\ModelFormRequest;
use Modules\Product\Repositories\ModelTypeRepositoryInterface;
use Brian2694\Toastr\Facades\Toastr;

class ModelController extends Controller
{
    protected $modelTypeRepository;

    public function __construct(ModelTypeRepositoryInterface $modelTypeRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->modelTypeRepository = $modelTypeRepository;
    }

    public function index(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10;
        $sort = ($request->has('sort')) ? $request->sort : 'asc';
        $column = ($request->has('col')) ? $request->col : null;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
        $models = $this->modelTypeRepository->withPaginate($row_count, $quick_search, $sort, $column, ['products:model_id,id'], ['id', 'name', 'description', 'status']);
        if ($request->ajax()) {
            return view('product::model.model_list', compact('models'));
        }
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $data['items'] = $this->modelTypeRepository->withPaginate($row_count, $quick_search, $sort, $column, ['products:model_id,id'], ['id', 'name', 'description', 'status']);
            if ($request->import_as == "print") {
                return view('product::model.print', $data);
            }
            if ($request->import_as == "csv") {
                $this->modelTypeRepository->csvDownload($data);
                $filePath = public_path("uploads/csv/model-list.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time() . '-model-list.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        };
        return view('product::model.model', compact('models'));
    }

    public function list_select_option(Request $request)
    {
        $data = $this->modelTypeRepository->listForSelect($request);
        return response()->json($data);
    }

    public function getALl()
    {
        return $this->modelTypeRepository->all();
    }

    public function create(Request $request)
    {
        try{
            $search_keyword = null;
            if ($request->input('search_keyword') != null) {
                $search_keyword = $request->input('search_keyword');
                $models = $this->modelTypeRepository->serachBased($search_keyword);
            }
            else {
                $models = $this->modelTypeRepository->all();
            }

            return view('product::model.model_list', [
                "models" => $models
            ]);
        }catch(\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return redirect()->back();
        }
    }

    public function store(ModelFormRequest $request)
    {
        try {
            $item = $this->modelTypeRepository->create($request->except("_token"));
            return response()->json(["message" => __('product.Model Added Successfully'), 'item' => $item], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => __('common.Something Went Wrong')], 503);
        }
    }

    public function show($id)
    {
        return view('product::show');
    }

    public function edit($id)
    {
        try {
            return $this->modelTypeRepository->find($id);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }

    public function update(ModelFormRequest $request, $id)
    {
        try {
            $this->modelTypeRepository->update($request->except("_token"), $id);
            return response()->json(["message" => __('product.Model Updated Successfully')], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => __('common.Something Went Wrong')], 503);
        }
    }

    public function destroy($id)
    {

        try {
            $this->modelTypeRepository->delete($id);
            Toastr::success(__('product.Model Deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function csv_download()
    {
        try {
            return $this->modelTypeRepository->csv_download();
        } catch (\Exception $e) {
            return response()->json(["message" => __('common.Something Went Wrong')], 503);
        }
    }

    public function csv_upload()
    {
        return view('product::model.upload_via_csv.create');
    }

    public function csv_upload_store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        try {
            $this->modelTypeRepository->csv_upload_model_type($request->except("_token"));
            DB::commit();
            Toastr::success(__('common.Successfully Uploaded !!!'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                Toastr::error(__('common.Duplicate entry is exist in your file !!!'), __('common.Error'));
            }
            else {
                Toastr::error(__('common.Something went wrong. Upload again !!!'), __('common.Error'));
            }
            return back();
        }

    }
}
