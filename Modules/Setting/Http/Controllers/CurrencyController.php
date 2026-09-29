<?php

namespace Modules\Setting\Http\Controllers;
use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use Modules\Setting\Model\Currency;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Setting\Repositories\CurrencyRepositoryInterface;

class CurrencyController extends Controller
{
    protected $currencyRepository;

    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->middleware(['auth','permission']);
        $this->currencyRepository = $currencyRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $data['items'] = $this->currencyRepository->withPaginate($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('setting::currencies.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->currencyRepository->withPaginate("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    return view('setting::currencies.paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->currencyRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/currencies.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-currencies.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                    return redirect()->route('currencies.index');
                }
            }

            return view('setting::currencies.index', $data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }

    }

    public function store(Request $request)
    {
        $validate_rules = [
            "name" => "required",
            "code" => "required",
            "symbol" => "required"
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $this->currencyRepository->create($request->except("_token"));
            Toastr::success(__('setting.Currency Added Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        $validate_rules = [
            "name" => "required",
            "code" => "required",
            "symbol" => "required"
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $currency = $this->currencyRepository->update($request->except("_token"), $id);
            Toastr::success(__('setting.Currency Updated Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $currency = $this->currencyRepository->delete($id);
            Toastr::success(__('setting.Currency has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function edit_modal(Request $request)
    {
        try {
            $currency = $this->currencyRepository->find($request->id);
            return view('setting::currencies.edit_modal', [
                "currency" => $currency
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }
}
