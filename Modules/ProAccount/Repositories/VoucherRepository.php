<?php

namespace Modules\ProAccount\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\ProAccount\Entities\Voucher;
use Modules\ProAccount\Entities\Leadger;
use Modules\ProAccount\Entities\FinancialYearLeadgerBalance;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Exports\VoucherExport;
use update\Modules\ProAccount\Exports\PendingVoucherExport;
use Carbon\Carbon;

class VoucherRepository
{
    public function expenses($type)
    {
        return Voucher::where('is_approve', 1)->Expense($type)->where('sale_or_purchase','exp')->where('showroom_id',session()->get('showroom_id'))->sum('amount');

    }

    public function dailyProfit($id)
    {
        $main_amount = Voucher::whereHas('transactions',function ($query){
            $query->where('leadger_id',Settings('default_cost_of_goods_sold_account'))->where('type','Dr');
        })->select(DB::raw('MONTHNAME(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->whereDate('date',Carbon::today())
            ->groupBy('day')->orderBy('day','asc')->get();

        $total_amount = Voucher::whereHas('transactions',function ($query) use($id){
            $query->where('type','Dr')->whereIn('leadger_id',[$id]);
        })->select(DB::raw('MONTHNAME(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->whereDate('date',Carbon::today())
            ->groupBy('day')->orderBy('day','asc')->get();

        return [
            'main_amount' => $main_amount,
            'total_amount' => $total_amount,
        ];
    }

    public function weeklyProfit($id)
    {
        $main_amount = Voucher::whereHas('transactions',function ($query){
            $query->where('leadger_id',Settings('default_cost_of_goods_sold_account'))->where('type','Dr');
        })->select('id',DB::raw('MONTHNAME(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(amount) as sale_amount'),
            'date')->groupBy('day')->whereBetween('date',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->orderBy('day','asc')->get();

        $total_amount = Voucher::whereHas('transactions',function ($query) use($id){
            $query->where('type','Dr')->whereIn('leadger_id',[$id]);
        })->select(DB::raw('MONTHNAME(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->groupBy('day')->whereBetween('date',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->orderBy('day','asc')->get();

        return [
            'main_amount' => $main_amount,
            'total_amount' => $total_amount,
        ];
    }

    public function monthlyProfit($id)
    {
        $main_amount = Voucher::whereHas('transactions',function ($query){
            $query->where('leadger_id',Settings('default_cost_of_goods_sold_account'))->where('type','Dr');
        })->select(DB::raw('MONTHNAME(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->whereMonth('date',Carbon::now())->whereYear('date',Carbon::now())
            ->groupBy('day')->orderBy('day','asc')->get();

        $total_amount = Voucher::whereHas('transactions',function ($query) use($id){
            $query->where('type','Dr')->whereIn('leadger_id',[$id]);
        })->select(DB::raw('MONTHNAME(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('DAY(date) as day'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->whereMonth('date',Carbon::now())->whereYear('date',Carbon::now())
            ->groupBy('day')->orderBy('day','asc')->get();

        return [
            'main_amount' => $main_amount,
            'total_amount' => $total_amount,
        ];
    }

    public function yearlyProfit($id)
    {
        $main_amount = Voucher::whereHas('transactions',function ($query){
            $query->where('leadger_id',Settings('default_cost_of_goods_sold_account'))->where('type','Dr');
        })->select(DB::raw('MONTHNAME(date) as month_name'),
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->whereYear('date',Carbon::now())->groupBy('month')
            ->orderBy('month','asc')->get();

        $total_amount = Voucher::whereHas('transactions',function ($query) use($id){
            $query->where('type','Dr')->whereIn('leadger_id',[$id]);
        })->select(DB::raw('MONTHNAME(date) as month_name'),
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('SUM(amount) as sale_amount'),
            'id','date')->whereYear('date',Carbon::now())->groupBy('month')
            ->orderBy('month','asc')->get();

        return [
            'main_amount' => $main_amount,
            'total_amount' => $total_amount,
        ];
    }
    public function csvDownloadPayVoucher()
    {
        if (file_exists(public_path("uploads/csv/voucher_payments.xlsx"))) {
          unlink(public_path("uploads/csv/voucher_payments.xlsx"));
        }
        return Excel::store(new VoucherExport("voucher_payments"), 'uploads/csv/voucher_payments.xlsx', 'public_folder');
    }
    public function csvDownPendingVoucher()
    {
        if (file_exists(public_path("uploads/csv/pending-vouchers.xlsx"))) {
          unlink(public_path("uploads/csv/pending-vouchers.xlsx"));
        }
        return Excel::store(new PendingVoucherExport("pending"), 'uploads/csv/pending-vouchers.xlsx', 'public_folder');
    }
    public function csvDownApprovedVoucher()
    {
        if (file_exists(public_path("uploads/csv/approved-vouchers.xlsx"))) {
          unlink(public_path("uploads/csv/approved-vouchers.xlsx"));
        }
        return Excel::store(new PendingVoucherExport("approved"), 'uploads/csv/approved-vouchers.xlsx', 'public_folder');
    }

    public function withPaginatePayVoucher($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->whereIn('type', ['pay_cash', 'pay_bank', 'pay_in_due'])->where('showroom_id', session()->get('showroom_id'));
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','type','amount',], $quick_search)
                            ->whereIn('type', ['pay_cash', 'pay_bank', 'pay_in_due']);
        }
        if ($row_count == "all") {
            $total_number = Voucher::whereIn('type', ['pay_cash', 'pay_bank', 'pay_in_due'])->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginatePendingVoucher($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->where('is_approve',0)->where('showroom_id', session()->get('showroom_id'));
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','type','amount',], $quick_search)
                            ->where('is_approve',0);
        }
        if ($row_count == "all") {
            $total_number = Voucher::where('is_approve',0)->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function withPaginateApprovedVoucher($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->where('is_approve',1)->where('showroom_id', session()->get('showroom_id'));
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','amount'], $quick_search)
                            ->where('is_approve',1);
        }
        if ($row_count == "all") {
            $total_number = Voucher::where('is_approve',1)->count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function getAccountByCashBankOthers($type)
    {
        return Leadger::where('acc_type', $type)->where('is_active', 1)->get(['id','name']);
    }

    public function allCompletePurchasePaymentVoucherListQuery($filter_date)
    {
        return Voucher::with('referable')->where('referable_type', 'Modules\Purchase\Entities\CompletePurchase')
            ->when($filter_date, function($query) use($filter_date){
                $query->whereBetween(DB::raw('date(created_at)'),filterDateFormatingForSearchQuery($filter_date));
            })
            ->latest();
    }

    public function getAccountByAjax($type, $search)
    {
        $items = Leadger::query();

        if ($type == null) {
            $items = $items->whereIn('acc_type', ['cash', 'bank']);
        }else {
            $items = $items->where('acc_type', $type);
        }
        if ($search != '') {
            $items = $items->whereLike(['name', 'code'], $search);
        }
        $items = $items->paginate(10);

        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        } else {
            if ((auth()->user()->role->type == "system_user" || permissionCheck('leadger.store'))) {
                $response[]  =[
                    'id'    => "create_new",
                    'text'  => '<span class="add_new_icon_for_select"><i class="fa fa-plus"></i></span> Add New <span class="add_new_font_for_select"> -> '.$search.'</span>'
                ];
                $data['results'] =  $response;
            }
        }
        return $data;
    }

    public function makeVoucherNo($type)
    {
        $item = Voucher::latest()->first();
        if ($item) {
            if ($type == "purchase") {
                return 'po-pay-'.sprintf("%04d", ($item->id + 1));
            }elseif ($type == "purchased_return") {
                return 'rcpo-'.sprintf("%04d", ($item->id + 1));
            }elseif ($type == "manpower_invoice") {
                return 'mpi-'.sprintf("%04d", ($item->id + 1));
            }
        }else {
            if ($type == "purchase") {
                return 'po-pay-'.sprintf("%04d", 1);
            }elseif ($type == "purchased_return") {
                return 'rcpo-'.sprintf("%04d", 1);
            }elseif ($type == "manpower_invoice") {
                return 'mpi-'.sprintf("%04d", 1);
            }
        }
    }
    public function find($id)
    {
        return Voucher::with('transactions','transactions.leadger','transactions.sub_leadger','transactions.cash_flow_account','transactions.cash_flow_detail','approver','cash_flow_details','referable')->findOrFail($id);
    }

    public function voucherDetails($id)
    {
        return $this->find($id);
    }

    public function status_approval(array $data)
    {
        $voucher = $this->find($data['id']);
        $voucher->is_approve = $data['status'];
        $voucher->approved_by = (auth()->check()) ? auth()->user()->id : null;
        $voucher->save();
        if ($data['status'] == 1) {
            foreach ($voucher->transactions as $key => $transaction) {

                $transaction->update([
                    "is_approve" => 1
                ]);

                $financial_data = FinancialYearLeadgerBalance::where('leadger_id', $transaction->leadger_id)
                                                            ->where('accounting_period_id', $transaction->accounting_period_id)
                                                            ->where("showroom_id", $transaction->showroom_id)
                                                            ->first();

                if ($transaction->accounting_period_id != 1) {

                    $last_financial_data = FinancialYearLeadgerBalance::where('leadger_id', $transaction->leadger_id)
                                        ->whereNotIn('accounting_period_id',[$transaction->accounting_period_id])
                                        ->where("showroom_id", $transaction->showroom_id)
                                        ->latest()
                                        ->first();

                    if ($last_financial_data) {
                        $last_amount_balance = $last_financial_data->balance;
                    }else {
                        $last_amount_balance = 0;
                    }
                } else {
                    $last_amount_balance = 0;
                }

                if ($financial_data) {
                    $financial_data->update([
                        'balance' => ($transaction->leadger->GetBalanceAmount(app('financial_year')->id, $transaction->showroom_id) + $last_amount_balance)
                    ]);
                    if ($financial_data->balance == 0) {
                        $financial_data->delete();
                    }
                }else {
                    FinancialYearLeadgerBalance::create([
                                                        'leadger_id' => $transaction->leadger_id,
                                                        'showroom_id' => $transaction->showroom_id,
                                                        'accounting_period_id' => $transaction->accounting_period_id,
                                                        'balance' => ($transaction->leadger->GetBalanceAmount(app('financial_year')->id, $transaction->showroom_id) + 0),
                                                    ]);
                }
            }
        }

        return $voucher;
    }

    public function delete($id)
    {
        $voucher = $this->find($id);
        if ($voucher->is_manual_entry == 1) {
            \LogActivity::successLog('Voucher Deleted - '.$voucher->GetTypeName().'-'.$voucher->txn_id, route('activity_log'), "Voucher Deleted");
            $voucher->transactions()->forceDelete();
            $voucher->cash_flow_details()->forceDelete();
            if ($voucher->is_invoiced == 1 && $voucher->is_manual_entry == 1) {
                if ($voucher->referable_type == "Modules\Purchases\Entities\PurchaseOrder") {
                    $voucher->invoice_payments->payable->update([
                        'due_amount' => $voucher->invoice_payments->payable->due_amount + $voucher->invoice_payments->amount,
                        'is_paid' => 1
                    ]);
                } elseif ($voucher->referable_type == "Modules\Sales\Entities\Sale") {
                    $voucher->invoice_payments->payable->update([
                        'due_amount' => $voucher->invoice_payments->payable->due_amount + $voucher->invoice_payments->amount,
                        'status' => 2
                    ]);
                }
                $voucher->invoice_payments()->forceDelete();
            }
            return $voucher->forceDelete();
        }
    }

    public function deleteApproved($id, $password = null, $from = null)
    {
        $voucher = $this->find($id);
        \LogActivity::successLog('Voucher Delete Approved - '.$voucher->GetTypeName().'-'.$voucher->txn_id, route('activity_log'), "Voucher Delete Approved");
        foreach ($voucher->transactions as $key => $transaction) {
            $financial_data = FinancialYearLeadgerBalance::where('leadger_id', $transaction->leadger_id)->where('accounting_period_id', $transaction->accounting_period_id)->first();

            if ($financial_data) {
                if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3) {
                    if ($transaction->type == "Cr") {
                        $financial_data->update([
                            'balance' => $financial_data->balance + $transaction->amount
                        ]);
                    }else {
                        $financial_data->update([
                            'balance' => $financial_data->balance - $transaction->amount
                        ]);
                    }
                }else {
                    if ($transaction->type == "Cr") {
                        $financial_data->update([
                            'balance' => $financial_data->balance - $transaction->amount
                        ]);
                    }else {
                        $financial_data->update([
                            'balance' => $financial_data->balance + $transaction->amount
                        ]);
                    }
                }
                if ($financial_data->balance == 0) {
                    $financial_data->delete();
                }
            }
        }

        if ($from == "sale" || $from == "purchase") {
            $voucher->transactions()->delete();
            $voucher->cash_flow_details()->delete();
            $voucher->delete();
        } else {
            $voucher->transactions()->forceDelete();
            if ($voucher->is_invoiced == 1 && $voucher->is_manual_entry == 1) {
                $voucher->invoice_payments->payable->update([
                    'due_amount' => $voucher->invoice_payments->payable->due_amount + $voucher->invoice_payments->amount
                ]);
                $voucher->invoice_payments()->forceDelete();
            }
            $voucher->cash_flow_details()->forceDelete();
            $voucher->forceDelete();
        }
        return "success";
    }

    public function deleteApprovedPermanentLy($id)
    {
        $voucher = Voucher::with(['transactions', 'cash_flow_details'])->withTrashed()->findOrFail($id);
        $voucher->transactions()->forceDelete();
        if ($voucher->is_invoiced == 1 && $voucher->is_manual_entry == 1) {
            $voucher->invoice_payments->payable->update([
                'due_amount' => $voucher->invoice_payments->payable->due_amount + $voucher->invoice_payments->amount
            ]);
            $voucher->invoice_payments()->forceDelete();
        }
        $voucher->cash_flow_details()->forceDelete();
        $voucher->forceDelete();
    }

    public function restoreVoucher($id)
    {
        $voucher = Voucher::with(['transactions', 'cash_flow_details'])->withTrashed()->findOrFail($id);
        \LogActivity::successLog('Voucher Restored - '.$voucher->GetTypeName().'-'.$voucher->txn_id, route('activity_log'), "Voucher Restored");
        foreach ($voucher->transactionsWithTrashed as $key => $transaction) {
            $financial_data = FinancialYearLeadgerBalance::where('leadger_id', $transaction->leadger_id)->where('accounting_period_id', $transaction->accounting_period_id)->first();

            if ($financial_data) {
                if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3) {
                    if ($transaction->type == "Cr") {
                        $financial_data->update([
                            'balance' => $financial_data->balance - $transaction->amount
                        ]);
                    }else {
                        $financial_data->update([
                            'balance' => $financial_data->balance + $transaction->amount
                        ]);
                    }
                }else {
                    if ($transaction->type == "Cr") {
                        $financial_data->update([
                            'balance' => $financial_data->balance + $transaction->amount
                        ]);
                    }else {
                        $financial_data->update([
                            'balance' => $financial_data->balance - $transaction->amount
                        ]);
                    }
                }
                if ($financial_data->balance == 0) {
                    $financial_data->delete();
                }
            }
        }
        $voucher->transactions()->restore();
        $voucher->cash_flow_details()->restore();
        $voucher->restore();
    }

    public function allApproved($data)
    {
        $vouchers = Voucher::whereIn('id', $data['voucher_ids'])->where('is_approve', 0)->get();
        foreach ($vouchers as $voucher) {
            $this->status_approval(['id' => $voucher->id, 'status' => 1]);
        }
    }
}
