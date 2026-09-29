<?php
namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Repositories\VoucherRepositoryInterface;
use Modules\Account\Repositories\TransferRepositoryInterface;
use Modules\Account\Repositories\ContraRepository;
use Modules\Account\Entities\ChartAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Toastr;
use App\Traits\ChecksAccountBalance;

class TransferController extends Controller
{
    use ChecksAccountBalance;

    protected $transferRepository;

    public function __construct(TransferRepositoryInterface $transferRepository,VoucherRepositoryInterface $voucherRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->transferRepository = $transferRepository;
        $this->voucherRepository = $voucherRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $data['items'] = $this->transferRepository->withPaginate($row_count, $quick_search, $sort, $column);
            if ($request->ajax()) {
                return view('account::transfers.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->transferRepository->withPaginate('all', $quick_search, $sort, $column);
                if ($request->import_as == "print") {
                    return view('account::transfers.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->transferRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/transfer-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-transfer-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view("account::transfers.index", $data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function showroom_create()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $data['account_categories'] = $this->voucherRepository->category();
        return view("account::transfers.transfer_to_showroom", $data);
    }

    /**
     * A real Cash <-> Bank internal transfer screen - distinct from
     * showroom_create() above, which (despite its "Transfer" menu label)
     * actually pays from cash/bank INTO an expense category, not between
     * the company's own accounts. This posts through the exact same
     * showroom_store()/update() below - only the "Transfer To" account
     * picker differs (cash_bank_account_select instead of an expense-
     * type account list), so no new store endpoint is needed.
     */
    public function treasuryTransferCreate()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $data['account_categories'] = $this->voucherRepository->category();
        return view("account::transfers.treasury_transfer", $data);
    }

    public function edit($id)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $data['payment'] = $this->transferRepository->find($id);
        $data['chartAccounts'] = $this->transferRepository->allShowroomAccounts();
        $data['payment_accounts'] = ChartAccount::PaymentAccounts()->get();
        return view('account::transfers.transfer_to_showroom_edit', $data);
    }

    public function showroom_store(Request $request)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }

        $request->validate([
            'credit_account_id' => 'required',
            'debit_account_id' => 'required|array|min:1',
            'debit_account_amount' => 'required|array|min:1',
        ], validationMessage([
            'credit_account_id' => 'required',
            'debit_account_id' => 'required|array|min:1',
            'debit_account_amount' => 'required|array|min:1',
        ]));

        $sub_amount = 0;
        foreach ($request->debit_account_amount as $key => $amount) {
            $sub_amount += $amount;
        }

        // credit_account_id is the source account this transfer draws
        // from (Cr decreases an asset account) - debit_account_id[] are
        // the destinations receiving the money.
        $insufficientFundsMessage = $this->insufficientFundsForAccount((int) $request->credit_account_id, $sub_amount);
        if ($insufficientFundsMessage) {
            Toastr::error($insufficientFundsMessage, __('common.Error'));
            return back();
        }

        DB::beginTransaction();
        try {
            $this->transferRepository->create([
                'voucher_type' => 'CRV',
                'amount'=> $sub_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=>'credit',
                'payment_type' => 'contra_voucher',
                'account_id'=> $request->credit_account_id,  //debit side and credit side shoud be same
                'main_amount'=> $sub_amount,  //debit side and credit side shoud be same
                'narration'=> $request->debit_account_narration[0],  //debit side and credit side shoud be same

                'sub_account_id'=> $request->debit_account_id,   //debit side and credit side shoud be same
                'sub_amount'=> $request->debit_account_amount,
                'sub_narration'=> $request->debit_account_narration,
                'is_approve' => (app('business_settings')->where('type', 'contra_voucher_approval')->first()->status == 1) ? 1 : 0,
                'is_transfer' => 1,
                'cheque_no' => $request->cheque_no,
                'cheque_date' => ($request->cheque_date != null) ? Carbon::parse($request->cheque_date)->format('Y-m-d') : null,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
            ]);
            DB::commit();
            \LogActivity::successLog('Money Transfer Successfully.');
            Toastr::success(__('account.Contra Voucher has been added Successfully'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Journal creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            Toastr::error("You are using Pro-Accounting. Please entry your details from that sections");
            return back();
        }
        $validate_rules = [
            "date" => "nullable",
            "voucher_type" => "required",
            "narration" => "nullable",
            "cheque_no" => "nullable",
            "cheque_date" => "nullable",
            "bank_name" => "nullable",
            "bank_branch" => "nullable",
            "credit_account_id" => "required",
            "debit_account_id" => "required|array|min:1",
            "debit_account_amount" => "required|array|min:1",
            "debit_account_narration" => "nullable"

        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        $sub_amount = 0;
        foreach ($request->debit_account_amount as $key => $amount) {
            $sub_amount += $amount;
        }

        $insufficientFundsMessage = $this->insufficientFundsForAccount((int) $request->credit_account_id, $sub_amount);
        if ($insufficientFundsMessage) {
            Toastr::error($insufficientFundsMessage, __('common.Error'));
            return back();
        }

        DB::beginTransaction();
        try {
            $voucher = $this->transferRepository->update([
                'voucher_type' => 'CRV',
                'amount'=> $sub_amount,
                'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                'account_type'=>'debit',
                'payment_type' => 'contra_voucher',
                'account_id'=> $request->credit_account_id,  //debit side and credit side shoud be same
                'main_amount'=> $sub_amount,  //debit side and credit side shoud be same
                'narration'=> $request->debit_account_narration[0],  //debit side and credit side shoud be same

                'sub_account_id'=> $request->debit_account_id,   //debit side and credit side shoud be same
                'sub_amount'=> $request->debit_account_amount,
                'sub_narration'=> $request->debit_account_narration,
                'is_approve' => (app('business_settings')->where('type', 'contra_voucher_approval')->first()->status == 1) ? 1 : 0,
                'is_transfer' => 1,
                'cheque_no' => $request->cheque_no,
                'cheque_date' => ($request->cheque_date != null) ? Carbon::parse($request->cheque_date)->format('Y-m-d') : null,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
            ], $id);

            DB::commit();
            \LogActivity::successLog('Transfer Info been updated Successfully.');
            Toastr::success(__('account.Transfer Info been updated Successfully'));
            return redirect()->route('transfer_showroom.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Voucher Payment update');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
}
