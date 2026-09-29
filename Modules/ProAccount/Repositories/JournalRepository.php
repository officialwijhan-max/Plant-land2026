<?php

namespace Modules\ProAccount\Repositories;

use Carbon\Carbon;
use Modules\ProAccount\Entities\Voucher;
use Modules\ProAccount\Entities\Transaction;
use Modules\ProAccount\Entities\Leadger;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\ProAccount\Entities\CashFlowDetail;
use Modules\Sales\Repositories\SaleRepository;
use Modules\Purchases\Repositories\PurchaseOrderRepository;
use Modules\Sales\Entities\PosPayment;
use Modules\ProAccount\Repositories\VoucherRepository;
use Maatwebsite\Excel\Facades\Excel;
use update\Modules\ProAccount\Exports\VoucherExport;

class JournalRepository
{
    public function csvDownloadJournalVoucher()
    {
        if (file_exists(public_path("uploads/csv/journal_vouchers.xlsx"))) {
          unlink(public_path("uploads/csv/journal_vouchers.xlsx"));
        }
        return Excel::store(new VoucherExport(null), 'uploads/csv/journal_vouchers.xlsx', 'public_folder');
    }

    public function withPaginateJournalVoucher($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = Voucher::query();
        $items = $items->with($relational_data)->where('showroom_id', session()->get('showroom_id'));
        if ($quick_search != null) {
            $items = $items->whereLike(['txn_id','narration','date','amount'], $quick_search)
                            ;
        }
        if (Settings('accounting_entry_system') == "single_entry") {
            $items = $items->whereIn('type', ['pay_cash','pay_bank','rec_cash','rec_bank','rec','pay'])

                            ->orWhereIn('sale_or_purchase', ['exp','inc'])
                            ;
        }
        if ($row_count == "all") {
            $total_number = Voucher::count();

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
    public function transactionalAccounts()
    {
        return Leadger::with("chart_accounts")
            ->where('is_cost_center', 0)->where('is_active', 1)
            ->get();
    }

    public function create($data)
    {
        $Voucher = '';
        $transactions = $this->dataEntry($data);
        $cash_v = Voucher::where('type', strtolower($data['type']))->orderBy('id', 'DESC')->first();
        if ($cash_v) {
            $n_data = explode('-', $cash_v->txn_id);
            if (count($n_data) > 1) {
                 $txn_num = (int) $n_data[1] + 1;
             } else {
                 $txn_num = $n_data[0] + 1;
             }
        }else{
            $txn_num = 100;
        }

        $Voucher = Voucher::create([
            'amount' => $data['amount'],
            'date' => $data['date'],
            'showroom_id' => (!empty($data['showroom_id'])) ? $data['showroom_id'] : session()->get('showroom_id'),
            'narration' => $data['narration_voucher'],
            'is_cash_flow_journal' => $data['is_cash_flow_journal'],
            'type' => $data['type'],
            'is_approve' => $data['is_approve'],
            'referable_type' => (!empty($data['referable_type'])) ? $data['referable_type'] : null,
            'referable_id' => (!empty($data['referable_id'])) ? $data['referable_id'] : null,
            'is_invoiced' => (!empty($data['is_invoiced'])) ? $data['is_invoiced'] : 0,
            'is_advanced' => (!empty($data['is_advanced'])) ? $data['is_advanced'] : 0,
            'sale_or_purchase' => (!empty($data['sale_or_purchase'])) ? $data['sale_or_purchase'] : null,
            'ref_no' => (!empty($data['ref_no'])) ? $data['ref_no'] : null,
            'is_manual_entry' => (!empty($data['is_manual_entry'])) ? $data['is_manual_entry'] : 0,
            'created_by' => (auth()->check()) ? auth()->user()->id : null
        ]);
        foreach ($transactions as $key => $transaction) {
            $transactionEntry = Transaction::create([
                'voucher_id' => $Voucher->id,
                'showroom_id' => (!empty($data['showroom_id'])) ? $data['showroom_id'] : session()->get('showroom_id'),
                'leadger_id' => $transaction['leadger_id'],
                'sub_leadger_id' => $transaction['sub_leadger_id'],
                'narration' => $transaction['narration'],
                'type' => $transaction['type'],
                'amount' => $transaction['amount'],
                'date' => $data['date'],
                'is_cash_flow_journal' => $data['is_cash_flow_journal'],
                'accounting_period_id' => app('financial_year')->id,
            ]);

            if ($transaction['cash_flow_account_id'] > 0) {
                CashFlowDetail::create([
                    'voucher_id' => $Voucher->id,
                    'cash_flow_account_id' => $transaction['cash_flow_account_id'],
                    'transaction_id' => $transactionEntry->id,
                    'type' => $transaction['type'],
                    'amount' => $transaction['amount'],
                ]);
            }
            if ($transaction['sub_leadger_id'] != 0) {
                SubLeadger::find($transaction['sub_leadger_id'])->update(['is_blocked'=>1]);
            }
            if ($transaction['leadger_id'] != 0) {
                Leadger::find($transaction['leadger_id'])->update(['is_blocked'=>1]);
            }
        }
        $Voucher->update(['txn_id' => $txn_num]);

        if ($Voucher->is_approve == 1) {
            $voucherRepo = new VoucherRepository();
            $voucherRepo->status_approval([
                'id' => $Voucher->id,
                'status' => 1,
            ]);
        }
        if(!empty($data['is_invoiced']) && $data['is_invoiced'] == 1)
        {
            if($data['referable_type'] == "Modules\Sales\Entities\Sale")
            {
                $saleRepo = new SaleRepository;
                $sale = $saleRepo->find($data['referable_id']);
                $paid_amount = $sale->payments()->where('payment_type','pay')->sum('amount') + $data['amount'];
                if ($sale->payable_amount <= $paid_amount) {
                    $sale->status = 1;
                }else {
                    $sale->status = 2;
                }
                $sale_payment = new PosPayment([
                    'voucher_id' => $Voucher->id,
                    'date' => $Voucher->date,
                    'payment_method' => 'cash',
                    'amount' => $data['amount'],
                    'has_discount_on_payment' => (!empty($data['discount_percentage']) && $data['discount_percentage'] > 0) ? 1 : 0,
                    'amount_after_discount' => !empty($data['discount_amount']) ? $data['amount'] - $data['discount_amount'] : $data['amount'],
                    'payable_id' => $data['referable_id'],
                    'payable_type' => 'Modules\Sales\Entities\Sale',
                ]);
                $sale->due_amount = $sale->due_amount - $data['amount'];
                $sale->payments()->save($sale_payment);
                $sale->save();
            }
            if($data['referable_type'] == "Modules\Purchases\Entities\PurchaseOrder")
            {
                $orderRepo = new PurchaseOrderRepository;
                $order = $orderRepo->find($data['referable_id']);
                $paid_amount = $order->payments()->where('payment_type','pay')->sum('amount') + $data['amount'];
                if ($order->payable_amount <= $paid_amount) {
                    $order->is_paid = 2;
                }else {
                    $order->is_paid = 1;
                }
                $order_payment = new PosPayment([
                    'voucher_id' => $Voucher->id,
                    'payment_method' => 'cash',
                    'date' => $Voucher->date,
                    'amount' => $data['amount'],
                    'has_discount_on_payment' => (!empty($data['discount_percentage']) && $data['discount_percentage'] > 0) ? 1 : 0,
                    'amount_after_discount' => !empty($data['discount_amount']) ? $data['amount'] - $data['discount_amount'] : $data['amount'],
                    'payable_id' => $data['referable_id'],
                    'payable_type' => 'Modules\Purchases\Entities\PurchaseOrder',
                ]);
                $order->due_amount = $order->due_amount - $data['amount'];
                $order->payments()->save($order_payment);
                $order->save();
            }
        }

        return $Voucher->load('transactions');
    }

    public function find($id)
    {
        return Voucher::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $Voucher = '';
        $transactions = $this->dataEntry($data);
        $Voucher = Voucher::findOrFail($id);
        $Voucher->update([
            'amount' => $data['amount'],
            'date' => $data['date'],
            'narration' => $data['narration_voucher'],
            'is_cash_flow_journal' => $data['is_cash_flow_journal'],
            'type' => $data['type'],
            'is_approve' => $data['is_approve'],
            'referable_type' => (!empty($data['referable_type'])) ? $data['referable_type'] : null,
            'referable_id' => (!empty($data['referable_id'])) ? $data['referable_id'] : null,
            'is_invoiced' => (!empty($data['is_invoiced'])) ? $data['is_invoiced'] : 0,
            'is_advanced' => (!empty($data['is_advanced'])) ? $data['is_advanced'] : 0,
            'updated_by' => auth()->user()->id
        ]);

        if(!empty($data['is_invoiced']) && $data['is_invoiced'] == 1)
        {
            if($data['referable_type'] == "Modules\Sales\Entities\Sale")
            {
                $prev_amount = $Voucher->invoice_payments->sum('amount');
                $Voucher->invoice_payments()->forceDelete();
                $saleRepo = new SaleRepository;
                $sale = $saleRepo->find($data['referable_id']);
                $paid_amount = $sale->payments()->where('payment_type','pay')->sum('amount') + $data['amount'];
                if ($sale->payable_amount <= $paid_amount) {
                    $sale->status = 1;
                }else {
                    $sale->status = 2;
                }
                $sale_payment = new PosPayment([
                    'voucher_id' => $Voucher->id,
                    'payment_method' => 'cash',
                    'date' => $Voucher->date,
                    'amount' => $data['amount'],
                    'has_discount_on_payment' => ($data['discount_percentage'] > 0) ? 1 : 0,
                    'amount_after_discount' => $data['amount'] - $data['discount_amount'],
                    'payable_id' => $data['referable_id'],
                    'payable_type' => 'Modules\Sales\Entities\Sale',
                ]);
                $sale->due_amount = $sale->due_amount + $prev_amount - $data['amount'];
                $sale->payments()->save($sale_payment);
                $sale->save();
            }
            if($data['referable_type'] == "Modules\Purchases\Entities\PurchaseOrder")
            {
                $prev_amount = $Voucher->invoice_payments->sum('amount');
                $Voucher->invoice_payments()->forceDelete();
                $orderRepo = new PurchaseOrderRepository;
                $order = $orderRepo->find($data['referable_id']);
                $paid_amount = $order->payments()->where('payment_type','pay')->sum('amount') + $data['amount'];
                if ($order->payable_amount <= $paid_amount) {
                    $order->is_paid = 2;
                }else {
                    $order->is_paid = 1;
                }
                $order_payment = new PosPayment([
                    'voucher_id' => $Voucher->id,
                    'payment_method' => 'cash',
                    'date' => $Voucher->date,
                    'amount' => $data['amount'],
                    'has_discount_on_payment' => ($data['discount_percentage'] > 0) ? 1 : 0,
                    'amount_after_discount' => $data['amount'] - $data['discount_amount'],
                    'payable_id' => $data['referable_id'],
                    'payable_type' => 'Modules\Purchases\Entities\PurchaseOrder',
                ]);
                $order->due_amount = $order->due_amount + $prev_amount - $data['amount'];
                $order->payments()->save($order_payment);
                $order->save();
            }
        }
        foreach ($Voucher->transactions as $key => $transaction) {
            $transaction->cash_flow_detail->delete();
            $transaction->delete($transaction->id);
        }

        foreach ($transactions as $key => $transaction) {
            $transactionEntry = Transaction::create([
                'voucher_id' => $Voucher->id,
                'leadger_id' => $transaction['leadger_id'],
                'sub_leadger_id' => $transaction['sub_leadger_id'],
                'narration' => $transaction['narration'],
                'type' => $transaction['type'],
                'amount' => $transaction['amount'],
                'date' => Carbon::now()->format('Y-m-d'),
                'is_cash_flow_journal' => $data['is_cash_flow_journal'],
                'accounting_period_id' => app('financial_year')->id,
            ]);

            if ($transaction['cash_flow_account_id'] > 0) {
                CashFlowDetail::create([
                    'voucher_id' => $Voucher->id,
                    'cash_flow_account_id' => $transaction['cash_flow_account_id'],
                    'transaction_id' => $transactionEntry->id,
                    'type' => $transaction['type'],
                    'amount' => $transaction['amount'],
                ]);
            }
        }

        $txn_data = explode('-', $Voucher->txn_id);
        if (count($txn_data) > 1) {
            $txn_num = $txn_data[1];
        } else {
            $txn_num = $txn_data[0];
        }

        $Voucher->update(['txn_id' => $txn_num]);

        return $Voucher->load('transactions');
    }

    protected function dataEntry($data)
    {
        $convert_data = [];
        if (array_key_exists('direct_manupulate',$data) && $data['direct_manupulate'] == 0) {
            for ($i=0; $i < count($data['debit_account_id']); $i++) {
                if (array_key_exists($i, $data['debit_account_id']) && is_numeric ( $data['debit_account_id'][$i] ) && $data['debit_account_id'][$i] > 0) {
                    array_push($convert_data,[
                        'leadger_id' => $data['debit_account_id'][$i],
                        'sub_leadger_id' => $data['debit_sub_account_id'][$i],
                        'cash_flow_account_id' => $data['debit_cash_flow_account_id'][$i],
                        'type' => 'Dr',
                        'amount' => $data['debit_account_amount'][$i],
                        'narration' => $data['debit_narration'][$i],
                    ]);
                }
            }
            for ($i=0; $i < count($data['credit_account_id']); $i++) {
                if (array_key_exists($i, $data['credit_account_id']) && is_numeric ( $data['credit_account_id'][$i] ) && $data['credit_account_id'][$i] > 0) {
                    array_push($convert_data,[
                        'leadger_id' => $data['credit_account_id'][$i],
                        'sub_leadger_id' => $data['credit_sub_account_id'][$i],
                        'cash_flow_account_id' => $data['credit_cash_flow_account_id'][$i],
                        'type' => 'Cr',
                        'amount' => $data['credit_account_amount'][$i],
                        'narration' => $data['credit_narration'][$i],
                    ]);
                }
            }
        } else {
            if (count($data['debit_account_id']) >= count($data['credit_account_id'])) {
                for ($i=0; $i < count($data['debit_account_id']); $i++) {
                    if (array_key_exists($i, $data['debit_account_id']) && is_numeric ( $data['debit_account_id'][$i] ) && $data['debit_account_id'][$i] > 0) {
                        array_push($convert_data,[
                            'leadger_id' => $data['debit_account_id'][$i],
                            'sub_leadger_id' => $data['debit_sub_account_id'][$i],
                            'cash_flow_account_id' => $data['debit_cash_flow_account_id'][$i],
                            'type' => 'Dr',
                            'amount' => $data['debit_account_amount'][$i],
                            'narration' => $data['debit_narration'][$i],
                        ]);
                    }
                    if (array_key_exists($i, $data['credit_account_id']) && is_numeric ( $data['credit_account_id'][$i] ) && $data['credit_account_id'][$i] > 0) {
                        array_push($convert_data,[
                            'leadger_id' => $data['credit_account_id'][$i],
                            'sub_leadger_id' => $data['credit_sub_account_id'][$i],
                            'cash_flow_account_id' => $data['credit_cash_flow_account_id'][$i],
                            'type' => 'Cr',
                            'amount' => $data['credit_account_amount'][$i],
                            'narration' => $data['credit_narration'][$i],
                        ]);
                    }
                }
            } else {
                for ($i=0; $i < count($data['credit_account_id']); $i++) {
                    if (array_key_exists($i, $data['debit_account_id']) && is_numeric ( $data['debit_account_id'][$i] ) && $data['debit_account_id'][$i] > 0) {
                        array_push($convert_data,[
                            'leadger_id' => $data['debit_account_id'][$i],
                            'sub_leadger_id' => $data['debit_sub_account_id'][$i],
                            'cash_flow_account_id' => $data['debit_cash_flow_account_id'][$i],
                            'type' => 'Dr',
                            'amount' => $data['debit_account_amount'][$i],
                            'narration' => $data['debit_narration'][$i],
                        ]);
                    }
                    if (array_key_exists($i, $data['credit_account_id']) && is_numeric ( $data['credit_account_id'][$i] ) && $data['credit_account_id'][$i] > 0) {
                        array_push($convert_data,[
                            'leadger_id' => $data['credit_account_id'][$i],
                            'sub_leadger_id' => $data['credit_sub_account_id'][$i],
                            'cash_flow_account_id' => $data['credit_cash_flow_account_id'][$i],
                            'type' => 'Cr',
                            'amount' => $data['credit_account_amount'][$i],
                            'narration' => $data['credit_narration'][$i],
                        ]);
                    }
                }
            }
        }
        return $convert_data;
    }
}
