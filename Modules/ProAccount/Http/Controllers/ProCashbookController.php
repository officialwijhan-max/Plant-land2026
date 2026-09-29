<?php
namespace Modules\ProAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\PdfGenerate;
use Modules\ProAccount\Repositories\CashbookRepository;
use Carbon\Carbon;

class ProCashbookController extends Controller
{
    use PdfGenerate;
    protected $cashbookRepository;

    public function __construct(CashbookRepository $cashbookRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->cashbookRepository = $cashbookRepository;
    }
    public function index(Request $request)
    {
        try {
            $data['branchAccount'] = $this->cashbookRepository->branchAccount();

            if ($request->start_date != null) {
                $data['start_date'] = ($request->start_date != null) ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
                $data['end_date'] = ($request->end_date != null) ? Carbon::parse($request->end_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                $data['credit_info'] = $this->cashbookRepository->search_credit($data['start_date'],$data['end_date'], $data['branchAccount']->id);
                $data['credit_transactions'] = $data['credit_info']->unique('account_id');
                $data['credit_transactions_balance'] = $data['credit_info']->groupBy('account_id')
                                                                            ->map(function ($row) {
                                                                                    return $row->sum('amount');
                                                                                });

                $data['debit_info'] = $this->cashbookRepository->search_debit($data['start_date'],$data['end_date'], $data['branchAccount']->id);
                $data['debit_transactions'] = $data['debit_info']->unique('account_id');

                $data['debit_transactions_balance'] = $data['debit_info']->groupBy('account_id')
                                                                        ->map(function ($row) {
                                                                                return $row->sum('amount');
                                                                            });
                $data['total_transactions'] = $this->cashbookRepository->search(date('Y-m-d', strtotime('-1 day', strtotime($request->start_date))), $data['branchAccount']->id);

                if ($request->has('print')) {
                    return view('proaccount::cashbook.pdf', $data);
                }
                return view('proaccount::cashbook.index', $data);
            }else {
                $data['credit_info'] = $this->cashbookRepository->search_credit(Carbon::now()->format('Y-m-d'),Carbon::now()->format('Y-m-d'), $data['branchAccount']->id);
                $data['credit_transactions'] = $data['credit_info']->unique('account_id');
                $data['credit_transactions_balance'] = $data['credit_info']->groupBy('account_id')
                                                                            ->map(function ($row) {
                                                                                    return $row->sum('amount');
                                                                                });
                $data['debit_info'] = $this->cashbookRepository->search_debit(Carbon::now()->format('Y-m-d'),Carbon::now()->format('Y-m-d'), $data['branchAccount']->id);
                $data['debit_transactions'] = $data['debit_info']->unique('account_id');
                $data['debit_transactions_balance'] = $data['debit_info']->groupBy('account_id')
                                                                        ->map(function ($row) {
                                                                                return $row->sum('amount');
                                                                            });
                $data['total_transactions'] = $this->cashbookRepository->search(Carbon::now()->subDays(1)->format('Y-m-d'), $data['branchAccount']->id);
                if ($request->has('print')) {
                    return view('proaccount::cashbook.pdf', $data);
                }
                return view('proaccount::cashbook.index', $data);
            }
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return response()->json(["message_error" => $e->getMessage().trans('common.Something Went Wrong')]);
        }
    }
}
