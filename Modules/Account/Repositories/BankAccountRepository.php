<?php

namespace Modules\Account\Repositories;

use Illuminate\Support\Arr;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\OpeningBalanceHistory;
use Modules\Account\Entities\TimePeriodAccount;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Account\Exports\BankAccountExport;
use Modules\Account\Entities\Transaction;
use Importer;
use Modules\Account\Imports\BankAccountImport;

class BankAccountRepository implements BankAccountRepositoryInterface
{
    public function all($start_date = null,$end_date = null)
    {
        if ($start_date == null) {
            return BankAccount::with('chartAccount')->latest()->get();
        }else {
            return BankAccount::with('chartAccount')->wherehas('transactions', function($query) use($start_date,$end_date) {
                $query->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"));
            })->with(['transactions' => function($query) use($start_date,$end_date){
                $query->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"));
                }])->latest()->get();
        }
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/bank-account-list.xlsx"))) {
            unlink(public_path("uploads/csv/bank-account-list.xlsx"));
        }
        return Excel::store(new BankAccountExport($data), 'uploads/csv/bank-account-list.xlsx', 'public_folder');
    }

    public function withPaginate($row_count, $quick_search, $sort, $column, $relational_data = [], $selected_data = ['*'])
    {
        $items = BankAccount::query();
        $items = $items->with($relational_data);

        if ($quick_search != null) {
            $items = $items->whereLike(['bank_name','account_name','account_no'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = BankAccount::count();

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

    public function create(array $data)
    {
        $chart_account = new ChartAccount();
        $chart_account->level = 2;
        $chart_account->is_group = 0;
        $chart_account->name = $data['bank_name'];
        $chart_account->type = 1; // asset
        $chart_account->parent_id = 3;
        $chart_account->status = $data['status'];
        $chart_account->configuration_group_id = 2;
        $chart_account->is_actual_bank = 1;
        $chart_account->save();

        $bankAccount = new BankAccount();
        $bankAccount->chart_account_id = $chart_account->id;
        $bankAccount->bank_name	 = !empty($data['bank_name']) ? $data['bank_name'] : '';
        $bankAccount->branch_name	 = !empty($data['branch_name']) ? $data['branch_name'] : '';
        $bankAccount->account_name	 = !empty($data['account_name']) ? $data['account_name'] : '';
        $bankAccount->account_no	 = !empty($data['account_no']) ? $data['account_no'] : '';
        $bankAccount->description	 = !empty($data['description']) ? $data['description'] : '';
        $bankAccount->save();
        $chart_account->update(['code' => '03-'.$chart_account->id]);

        // No showroom_ids sent (or an empty selection) means "shared with
        // every branch" - see BankAccount::showRooms(). Only sync when
        // branches were actually picked, so the field is optional.
        if (!empty($data['showroom_ids'])) {
            $bankAccount->showRooms()->sync($data['showroom_ids']);
        }

        // Opening balance was previously a completely dead code path -
        // create_chart_account() existed and did the right thing (a
        // proper Dr-the-bank/Cr-Capital journal entry, same pattern as
        // OpeningBalanceHistoryController::showroom_openning_balance_store())
        // but nothing ever called it or collected the field, so the
        // "Creditor Balance" was never actually settable.
        if (!empty($data['opening_balance']) && $data['opening_balance'] > 0) {
            $bankAccount->openning_balance = $data['opening_balance'];
            $this->create_chart_account($bankAccount, $chart_account);
        }

        return $bankAccount;
    }

    public function find($id)
    {
        return BankAccount::with('transactions')->findOrFail($id);
    }

    public function update(array $data)
    {
        $bankAccount =  BankAccount::find($data['id']);
        $bankAccount->bank_name	     = !empty($data['bank_name']) ? $data['bank_name'] : '';
        $bankAccount->branch_name	 = !empty($data['branch_name']) ? $data['branch_name'] : '';
        $bankAccount->account_name	 = !empty($data['account_name']) ? $data['account_name'] : '';
        $bankAccount->account_no	 = !empty($data['account_no']) ? $data['account_no'] : '';
        $bankAccount->description	 = !empty($data['description']) ? $data['description'] : '';
        $bankAccount->save();

        $bankAccount->chartAccount()->update(['name' => $data['bank_name'],'status' =>$data['status']]);

        // Unchecked HTML checkboxes submit nothing at all, so
        // showroom_ids being absent from $data doesn't distinguish "user
        // unchecked every branch" from "this request never touched
        // branches" - the form always sends showroom_ids_present=1
        // alongside the checkboxes so this can tell those apart.
        if (!empty($data['showroom_ids_present'])) {
            $bankAccount->showRooms()->sync($data['showroom_ids'] ?? []);
        }

        if (isset($data['opening_balance']) && $data['opening_balance'] !== '') {
            $existing = OpeningBalanceHistory::where('account_id', $bankAccount->chart_account_id)
                ->where('acc_type', 'asset')
                ->first();

            if ($existing) {
                // Editing an existing opening balance corrects the
                // originally recorded figure - it does not post a new
                // adjustment entry, since that's what regular vouchers/
                // transactions are for.
                $existing->update(['amount' => $data['opening_balance']]);
            } elseif ($data['opening_balance'] > 0) {
                $bankAccount->openning_balance = $data['opening_balance'];
                $this->create_chart_account($bankAccount, $bankAccount->chartAccount);
            }
        }
    }

    public function delete($id)
    {
        $bankAccount =  BankAccount::find($id);
        ChartAccount::destroy($bankAccount->chart_account_id);
        return $bankAccount->delete();
    }

    public function csv_upload_bank_account($data)
    {

        if (!empty($data['file'])) {
            $fileName = time().'_'.$data['file']->getClientOriginalName();
            request()->file('file')->storeAs('reports', $fileName, 'public');

            Excel::import(new BankAccountImport, request()->file('file'));
        }
    }

    public function create_chart_account($bankAccount, $chart_account)
    {
        if($bankAccount->openning_balance != null && $bankAccount->openning_balance > 0){
            $repo = new OpeningBalanceHistoryRepository();
            $repo->createForUser([
                'asset_account_id' => $chart_account->id,
                'asset_amount' => $bankAccount->openning_balance,
                'date' => Carbon::now()->format('Y-m-d'),
                'time_period_id' => TimePeriodAccount::where('is_closed', 0)->latest()->first()->id,
                'liability_account_id' => ChartAccount::where('code', '02-09-11')->first()->id,
                'liability_amount' => $bankAccount->openning_balance,
            ]);
            $repo->createForHistory([
                'account_id' => $chart_account->id,
                'type' => 'bank',
                'amount' => $bankAccount->openning_balance,
            ]);
        }
    }

}
