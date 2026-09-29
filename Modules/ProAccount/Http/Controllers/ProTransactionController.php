<?php

namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\ShowRoom;
use Modules\ProAccount\Repositories\TransactionRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use App\Traits\PdfGenerate;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;

class ProTransactionController extends Controller
{
    use PdfGenerate;
    protected $transactionRepository;

    public function __construct(TransactionRepository $tranactionRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->transactionRepository = $tranactionRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
     public function index(Request $request)
     {
         $data['leadgerAccount'] = null;
         $data['accont_type'] = null;
         $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::today()->toDateString();
         $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::today()->toDateString();
         $data['account_id'] = ($request->leadgerId != null) ? $request->leadgerId : null;
         $data['showroom_id'] = ($request->showroom_id != null) ? $request->showroom_id : session()->get('showroom_id');
         if ($request->showroom_id != null) {
             $data['selected_showroom'] = ShowRoom::find($request->showroom_id);
         }

         if ($data['dateTo'] != null && $data['dateFrom'] == null) {
            Toastr::warning('proaccount::account.you_need_to_select_date_from_when_you_selected_date_to');
            return back();
         }
         if ($data['dateTo'] == null && $data['dateFrom'] != null) {
            Toastr::warning('proaccount::account.you_need_to_select_date_to_when_you_selected_date_from');
            return back();
         }

         if ($data['account_id'] != 0) {
             $leadgerRepo = new LeadgerRepository();
             $data['leadgerAccount'] = $leadgerRepo->find($data['account_id']);
         }
         if ($request->has('print') or $request->has('pdf')) {
             $data['vouchers'] = $this->transactionRepository->forPrint($data['dateFrom'], $data['dateTo'], $data['account_id'], $data['showroom_id']);
         }else {
             $data['vouchers'] = $this->transactionRepository->searchByAccountId($data['dateFrom'], $data['dateTo'], $data['account_id'], $data['showroom_id']);
         }
         if ($request->ajax()) {
             return view('proaccount::transaction.components.data',$data);
         }

         if ($request->has('print')) {
             return view('proaccount::transaction.print_view',$data);
         }

         if ($request->has('pdf')) {
             return $this->getPDF('proaccount::transaction.pdf',$data, 'transaction_report');
         }

         return view('proaccount::transaction.index',$data);
     }

     public function search(Request $request)
    {
        $data['leadgerAccount'] = null;
        $data['accont_type'] = null;
        $data['dateFrom'] = ($request->dateFrom != null) ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::today()->toDateString();
        $data['dateTo'] = ($request->dateTo != null) ? Carbon::parse($request->dateTo)->format('Y-m-d') : Carbon::today()->toDateString();
        $data['account_id'] = ($request->leadgerId != null) ? $request->leadgerId : null;
        $data['showroom_id'] = ($request->showroom_id != null) ? $request->showroom_id : session()->get('showroom_id');
        if ($request->showroom_id != null) {
            $data['selected_showroom'] = ShowRoom::find($request->showroom_id);
        }

        if ($data['dateTo'] != null && $data['dateFrom'] == null) {
            Toastr::warning('proaccount::account.you_need_to_select_date_from_when_you_selected_date_to');
            return back();
        }
        if ($data['dateTo'] == null && $data['dateFrom'] != null) {
            Toastr::warning('proaccount::account.you_need_to_select_date_to_when_you_selected_date_from');
            return back();
        }
        if ($data['account_id'] != 0) {
            $leadgerRepo = new LeadgerRepository();
            $data['leadgerAccount'] = $leadgerRepo->find($data['account_id']);
        }

        $data['vouchers'] = $this->transactionRepository->searchByAccountId($data['dateFrom'], $data['dateTo'], $data['account_id'], $data['showroom_id']);
        if ($request->ajax()) {
            return view('proaccount::transaction.components.data',$data);
        }
        if ($request->has('print')) {
            \LogActivity::successLog(trans('common.Print has been Done').' - Transactions', route('transaction.transactions'), "Print");
            return view('proaccount::transactions.print_view',$data);
        }
        return view('proaccount::transactions.index',$data);
    }
}
