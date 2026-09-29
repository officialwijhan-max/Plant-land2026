<?php

namespace Modules\Setup\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Setup\Http\Requests\TaxFormRequest;
use Modules\Setup\Repositories\TaxRepositoryInterface;

class TaxController extends Controller
{
    protected $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->middleware(['auth']);
        $this->taxRepository = $taxRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'desc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->taxRepository->withPaginate($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('setup::taxes.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->taxRepository->withPaginate("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    return view('setup::taxes.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->taxRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/tax.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-tax.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('setup::taxes.index', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for District creation');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function Store(TaxFormRequest $request)
    {
        try {
            $this->taxRepository->create($request->except("_token"));
            \LogActivity::successLog('New Tax - ('.$request->name.') has been created.');
            return response()->json(["message" => __('setup.Tax has been Created Successfully')], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Tax creation');
            return response()->json(["message" => __('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function edit($id)
    {
        try {
            return $this->taxRepository->find($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function update(TaxFormRequest $request, $id)
    {
        try {
            $this->taxRepository->update($request->except("_token"), $id);
            return response()->json(["message" => __('setup.Tax has been Updated Successfully')], 200);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => __('common.Something Went Wrong'), "error" => $e->getMessage()], 503);
        }
    }

    public function destroy($id)
    {
        try {
            $tax = $this->taxRepository->delete($id);
            \LogActivity::successLog('A Tax has been destroyed.');
            Toastr::success(__('setup.Tax has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Tax Destroy');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function update_active_status(Request $request)
    {
        try {
            $tax = $this->taxRepository->update_status($request->except("_token"));
            return 1;
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return 0;
        }
    }
}
