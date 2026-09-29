<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Support\Arr;
use Modules\Account\Entities\Document;
use Modules\Account\Entities\Voucher;
use Modules\Inventory\Entities\Expense;
use Modules\Account\Entities\ChartAccount;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Account\Exports\ExpenseExport;
use Session;

class ExpenseRepository implements ExpenseRepositoryInterface
{

    public function expenceList()
    {
        if (auth()->user()->role->type == "system_user" || permissionCheck('expenses.show')) {
            return Expense::with('showroom', 'voucher')->latest()->get();
        }else {
            return Expense::with('showroom', 'voucher')->where('showroom_id', Session::get('showroom_id'))->latest()->get();
        }
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/expense-list.xlsx"))) {
            unlink(public_path("uploads/csv/expense-list.xlsx"));
        }
        return Excel::store(new ExpenseExport($data), 'uploads/csv/expense-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = Expense::query();
        $items = $items->with($relational_data);

        if (auth()->user()->role->type != "system_user") {
            $items = $items->where('showroom_id', Session::get('showroom_id'));
        }

        if ($quick_search != null) {
            $items = $items->whereLike(['voucher.tx_id','voucher.amount'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Expense::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function expenceAccount()
    {
        return ChartAccount::where('type', 3)->get();
    }

    public function create($data)
    {
        $Voucher = '';
        $transactions = $this->trranactionEntry($data);
        $Voucher = Voucher::create([
            'amount'=> $data['amount'],
            'date'=> $data['date'],
            'narration' =>  $data['narration'],
            'voucher_type' => $data['voucher_type'],
            'payment_type' => $data['payment_type'],
            'is_approve' => $data['is_approve']
        ]);


        if ($data['voucher_type'] == "BV") {
            $document = Document::create([
                'voucher_id' => $Voucher->id,
                'bank_branch' => $data['bank_branch'],
                'bank_name' => $data['bank_name'],
                'cheque_date' => $data['cheque_date'],
                'cheque_no' => $data['cheque_no']
            ]);
        }

        $Voucher->transactions()->createMany($transactions);

        foreach($Voucher->transactions as $transaction) {
            if($transaction->type == 'Cr'){
                $transaction->fromAccounts()->attach($data['sub_account_id']);
            }else{
                $transaction->fromAccounts()->attach($data['account_id']);
            }
        }
        $Voucher->update(['tx_id' => $data['voucher_type'].'-'.$Voucher->id]);

        $expense = new Expense;
        $expense->showroom_id = Session::get('showroom_id');
        $expense->voucher_id = $Voucher->id;
        $expense->status = 0;
        $expense->save();

        return $Voucher->load('transactions');
    }

    protected function trranactionEntry($data)
    {
        if ($data['account_type'] == 'debit') {
            if (is_array($data['sub_account_id'])) {
                $conver_date = [];
                for ($i = 0; $i < count($data['sub_account_id']); $i++) {
                    array_push($conver_date, [
                        'account_id' => $data['sub_account_id'][$i],
                        'type' => 'Cr',
                        'amount' => $data['sub_amount'][$i],
                        'description' => $data['description'][$i],
                        'narration' => $data['sub_narration'][$i],
                        'customer_id' => $data['customer_id'][$i],
                    ]);

                    // Additional record if customer_id is not empty
                    if (!empty($data['customer_id'][$i])) {
                        array_push($conver_date, [
                            'account_id' => $data['customer_id'][$i],
                            'type' => 'Cr',
                            'amount' => $data['sub_amount'][$i],
                            'description' => $data['description'][$i],
                            'narration' => $data['sub_narration'][$i],
                            'is_customer' => 1
                        ]);
                    }
                }
                array_push($conver_date, [
                    'account_id' => $data['account_id'],
                    'type' => 'Dr',
                    'amount' => $data['main_amount'],
                ]);
                return $conver_date;
            }
        } else {
            if (is_array($data['sub_account_id'])) {
                $conver_date = [];
                for ($i = 0; $i < count($data['sub_account_id']); $i++) {
                    array_push($conver_date, [
                        'account_id' => $data['sub_account_id'][$i],
                        'type' => 'Dr',
                        'amount' => $data['sub_amount'][$i],
                        'description' => $data['description'][$i],
                        'narration' => $data['sub_narration'][$i],
                        'customer_id' => $data['customer_id'][$i],
                    ]);

                    // Additional record if customer_id is not empty
                    if (!empty($data['customer_id'][$i])) {
                        array_push($conver_date, [
                            'account_id' => $data['customer_id'][$i],
                            'type' => 'Cr',
                            'amount' => $data['sub_amount'][$i],
                            'description' => $data['description'][$i],
                            'narration' => $data['sub_narration'][$i],
                            'is_customer' => 1
                        ]);
                    }
                }
                array_push($conver_date, [
                    'account_id' => $data['account_id'],
                    'type' => 'Cr',
                    'amount' => $data['main_amount'],
                ]);
                return $conver_date;
            }
        }
    }


    public function find($id, $relation = [])
    {
        return Expense::with($relation)->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $Voucher = '';
        $expense = Expense::findOrFail($id);;
        $transactions = $this->trranactionEntry($data);
        $Voucher = $expense->voucher;
        $Voucher->update([
            'amount'=> $data['amount'],
            'date'=> $data['date'],
            'narration' =>  $data['narration'],
            'voucher_type' => $data['voucher_type'],
            'payment_type' => $data['payment_type']
        ]);

        foreach ($Voucher->transactions as $key => $transaction) {
            $transaction->fromAccounts()->detach();
            $transaction->delete();
        }

        $Voucher->transactions()->createMany($transactions);
        foreach($Voucher->transactions as $transaction) {
            if($transaction->type == 'Cr'){
                $transaction->fromAccounts()->attach($data['sub_account_id']);
            }else{
                $transaction->fromAccounts()->attach($data['account_id']);
            }
        }

        $Voucher->update(['tx_id' => $data['voucher_type'].'-'.$Voucher->id]);

        $Voucher->document()->delete();

        if ($data['voucher_type'] == "BV") {
            $document = Document::create([
                'voucher_id' => $Voucher->id,
                'bank_branch' => $data['bank_branch'],
                'bank_name' => $data['bank_name'],
                'cheque_date' => $data['cheque_date'],
                'cheque_no' => $data['cheque_no']
            ]);
        }

        return $Voucher->load('transactions');
    }

    public function delete($id)
    {
        $expense = Expense::findOrFail($id);
        $Voucher = $expense->voucher;
        foreach ($Voucher->transactions as $transaction) {
            $transaction->fromAccounts()->detach();
            $transaction->delete();
        }
        $Voucher->document()->delete();
        $Voucher->delete();
        return $expense->delete();
    }

}
