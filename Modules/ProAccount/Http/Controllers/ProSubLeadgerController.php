<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Http\Requests\CreateSubLeadgerRequest;
use Brian2694\Toastr\Facades\Toastr;

class ProSubLeadgerController extends Controller
{
    protected $subLeadgerRepository;

    public function __construct(SubLeadgerRepository  $subLeadgerRepository)
    {
        $this->subLeadgerRepository = $subLeadgerRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $row_count = ($request->has('row')) ? $request->row : 10 ;
        $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
        $column = ($request->has('col')) ? $request->col : null ;
        $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
        $name = ($request->has('name')) ? $request->name : null ;
        if ($request->has("import_as")) {
            set_time_limit(-1);
            $items = $this->subLeadgerRepository->withPaginate("row",$quick_search,$name,$sort,$column,['leadger:type,id,name','transactions:id,sub_leadger_id,type,amount'],['id','leadger_id','code','name','is_active']);
            if ($request->import_as == "print") {
                return view('proaccount::sub_leadger_accounts.components.print',['items'=>$items]);
            }
            if ($request->import_as == "csv") {
                $this->subLeadgerRepository->csvDownloadPartnerAccount($items);
                $filePath = public_path("uploads/csv/partner_account_list.xlsx");
                $headers = ['Content-Type: text/csv'];
                $fileName = time().'-partner_account_list.xlsx';

                return response()->download($filePath, $fileName, $headers);
            }
        }
        $data = $this->subLeadgerRepository->withPaginate($row_count,$quick_search,$name,$sort,$column,['leadger:type,id,name','transactions:id,sub_leadger_id,type,amount'],['id','leadger_id','code','name','is_active']);
        if ($request->ajax()) {
            return view('proaccount::sub_leadger_accounts.components.ledger_list', compact('data'));
        }
        return view('proaccount::sub_leadger_accounts.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
     public function list()
     {
         $data['sub_leadger_list'] = $this->subLeadgerRepository->getAll();
         return view('proaccount::sub_leadger_accounts.page_component.ledger_list', $data);
     }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CreateSubLeadgerRequest $request)
    {
        try {
            DB::beginTransaction();
            $item = $this->subLeadgerRepository->create($request->except("_token"));
            DB::commit();
            \LogActivity::successLog("New SubLeadger Added Successfully");
            return response()->json(["message" => trans('account.Ledger Added Successfully'), "leadger" => $item->morph, "row_count" => $request->row_counts], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message" => "Something Went Wrong", "error" => $e->getMessage()], 503);
        }
    }

    public function sub_leadger_by_leadger($id)
    {
        return $this->subLeadgerRepository->subLeadgerByLeadger($id);
    }

    public function list_for_select(Request $request)
    {
        $data = $this->subLeadgerRepository->subLeadgerForSelect($request->search);
        return response()->json($data);
    }

    public function supplier_list_for_select(Request $request)
    {
        $data = $this->subLeadgerRepository->subLeadgerForSupplierSelect($request->search);
        return response()->json($data);
    }

    public function sub_leadger_by_leadger_select_option(Request $request)
    {
        try{
            $data = $this->subLeadgerRepository->getSubAccountByAjax($request->leadger_id,$request->search);
            return response()->json($data);
        }catch(Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error($e->getMessage(), 'Error!!');
            return response()->json([
                'error' => $e->getMessage()
            ],503);
        }
    }

    public function parent_account_by_id($id)
    {
        $data = $this->subLeadgerRepository->find($id);
        return response()->json($data->leadger->name);
    }
}
