<?php
namespace Modules\Account\Repositories;

use Modules\Account\Entities\ChartAccount;
use Carbon\Carbon;
use Modules\Account\Entities\Transaction;
use Modules\Contact\Entities\ContactModel;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Account\Exports\IncomeByCustomerExport;
use Modules\Account\Exports\ExpenseBySupplierExport;

class AccountBalanceRepository implements AccountBalanceRepositoryInterface
{
	public function getIncome($start_date=null, $end_date=null)
	{
		$ChartAccount = ChartAccount::where('type', 4)->get(['id','name','code']);
		$ChartAccountIds = $ChartAccount->pluck('id');

		$previousDate = Carbon::parse($start_date??Carbon::now()->format('Y-m-d'))->subDays(1)->format('Y-m-d');

		$previousIncome = Transaction::whereIn('account_id', $ChartAccountIds)->where('created_at', '<=' , $previousDate." 23:59:59")->get();



		if($start_date != null && $end_date != null)
		{
			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
		}else{

			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array(Carbon::now()->format('Y-m-d')." 00:00:00", Carbon::now()->format('Y-m-d')." 23:59:59"))->get();
		}

        $data = [
            'transaction' => $transaction,
            'previousDate' => $previousDate,
            'ChartAccount' => $ChartAccount
        ];

		return $data;
	}



	public function getExpense($start_date=null, $end_date=null)
	{
		$ChartAccount = ChartAccount::where('type', 3)->get(['id','name','code']);
		$ChartAccountIds = $ChartAccount->pluck('id');

		$previousDate = Carbon::parse($start_date??Carbon::now()->format('Y-m-d'))->subDays(1)->format('Y-m-d');

		$previous = Transaction::whereIn('account_id', $ChartAccountIds)->where('created_at', '<=' , $previousDate." 23:59:59")->get();



		if($start_date != null && $end_date != null)
		{
			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
		}else{

			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array(Carbon::now()->format('Y-m-d')." 00:00:00", Carbon::now()->format('Y-m-d')." 23:59:59"))->get();
		}

        $data = [
            'transaction' => $transaction,
            'previousDate' => $previousDate,
            'ChartAccount' => $ChartAccount
        ];

		return $data;
	}


	public function getAsset($start_date=null, $end_date=null)
	{
		$ChartAccount = ChartAccount::where('type', 1)->get(['id','name','code']);
		$ChartAccountIds = $ChartAccount->pluck('id');

		$previousDate = Carbon::parse($start_date??Carbon::now()->format('Y-m-d'))->subDays(1)->format('Y-m-d');

		$previous = Transaction::whereIn('account_id', $ChartAccountIds)->where('created_at', '<=' , $previousDate." 23:59:59")->get();



		if($start_date != null && $end_date != null)
		{
			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
		}else{

			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array(Carbon::now()->format('Y-m-d')." 00:00:00", Carbon::now()->format('Y-m-d')." 23:59:59"))->get();
		}

        $data = [
            'transaction' => $transaction,
            'previousDate' => $previousDate,
            'ChartAccount' => $ChartAccount
        ];

		return $data;
	}


	public function getLiabilities($start_date=null, $end_date=null)
	{

		$ChartAccount = ChartAccount::where('type', 2)->get(['id','name','code']);
		$ChartAccountIds = $ChartAccount->pluck('id');

		$previousDate = Carbon::parse($start_date??Carbon::now()->format('Y-m-d'))->subDays(1)->format('Y-m-d');

		$previous = Transaction::whereIn('account_id', $ChartAccountIds)->where('created_at', '<=' , $previousDate." 23:59:59")->get();



		if($start_date != null && $end_date != null)
		{
			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
		}else{

			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array(Carbon::now()->format('Y-m-d')." 00:00:00", Carbon::now()->format('Y-m-d')." 23:59:59"))->get();
		}

        $data = [
            'transaction' => $transaction,
            'previousDate' => $previousDate,
            'ChartAccount' => $ChartAccount
        ];

		return $data;
	}


	public function getEquity($start_date=null, $end_date=null)
	{

		$ChartAccount = ChartAccount::where('type', 5)->get(['id','name','code']);
		$ChartAccountIds = $ChartAccount->pluck('id');

		$previousDate = Carbon::parse($start_date??Carbon::now()->format('Y-m-d'))->subDays(1)->format('Y-m-d');

		$previous = Transaction::whereIn('account_id', $ChartAccountIds)->where('created_at', '<=' , $previousDate." 23:59:59")->get();



		if($start_date != null && $end_date != null)
		{
			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
		}else{

			$transaction = Transaction::whereIn('account_id', $ChartAccountIds)->whereBetween('created_at' , array(Carbon::now()->format('Y-m-d')." 00:00:00", Carbon::now()->format('Y-m-d')." 23:59:59"))->get();
		}

        $data = [
            'transaction' => $transaction,
            'previousDate' => $previousDate,
            'ChartAccount' => $ChartAccount
        ];
		return $data;
	}

    public function csvDownloadIncomeByCustomer($data)
    {
        if (file_exists(public_path("uploads/csv/income-by-customer.xlsx"))) {
          unlink(public_path("uploads/csv/income-by-customer.xlsx"));
        }
        return Excel::store(new IncomeByCustomerExport($data), 'uploads/csv/income-by-customer.xlsx', 'public_folder');
    }

	public function getIncomeByCustomer($row_count, $quick_search, $sort, $column, $start_date=null, $end_date=null, $relational_data = [], $selected_data = ['*'])
	{
		$items = ChartAccount::query();
        $items = $items->with($relational_data)->whereHasMorph('contactable', 'Modules\Contact\Entities\ContactModel', function ($query) {
							$query->where('contact_type',"Customer");
						});
        if ($quick_search != null) {
            $items = $items->whereLike(['name'],$quick_search);
        }
        if ($row_count == "all") {
            $total_number = ChartAccount::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            }else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            }else {
                return $items->latest()->paginate($row_count,$selected_data);
            }
        }
	}

    public function csvDownloadExpenseBySupplier($data)
    {
        if (file_exists(public_path("uploads/csv/expense-by-supplier.xlsx"))) {
          unlink(public_path("uploads/csv/expense-by-supplier.xlsx"));
        }
        return Excel::store(new ExpenseBySupplierExport($data), 'uploads/csv/expense-by-supplier.xlsx', 'public_folder');
    }

	public function getExpenseBySupplier($row_count, $quick_search, $sort, $column, $start_date=null, $end_date=null, $relational_data = [], $selected_data = ['*'])
	{
		$items = ChartAccount::query();
        $items = $items->with($relational_data)->whereHasMorph('contactable', 'Modules\Contact\Entities\ContactModel', function ($query) {
							$query->where('contact_type',"Supplier");
						});
        if ($quick_search != null) {
            $items = $items->whereLike(['name'],$quick_search);
        }
        if ($row_count == "all") {
            $total_number = ChartAccount::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            }else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            }else {
                return $items->latest()->paginate($row_count,$selected_data);
            }
        }
	}



	public function saleTax($start_date=null, $end_date=null)
	{
		$taxChartAccount = ChartAccount::where('contactable_type', 'Modules\Setup\Entities\Tax')->get()->pluck('id');

		$taxChartAccount[] = ChartAccount::where('code', '02-12-13')->first()->id;


        if($start_date==null && $end_date==null)
        {
            $transaction = Transaction::whereIn('account_id', $taxChartAccount)->where('type', 'Cr')->get();

        }else{
            $transaction = Transaction::whereIn('account_id', $taxChartAccount)->where('type', 'Cr')->whereBetween('created_at',array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
        }



		return $transaction;

	}


	public function income($start_date=null, $end_date=null){

        $ChartAccount = ChartAccount::whereIn('configuration_group_id', [1,2])->pluck('id');

        if($start_date==null && $end_date==null)
        {
            $transaction = Transaction::whereIn('account_id', $ChartAccount)->where('type', 'Dr')->get();
        }else{
            $transaction = Transaction::whereIn('account_id', $ChartAccount)->where('type', 'Dr')->whereBetween('created_at',array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
        }

        return $transaction;
    }

    public function expense($start_date=null, $end_date=null){

        $ChartAccount = ChartAccount::whereIn('configuration_group_id', [1,2])->pluck('id');
        if($start_date==null && $end_date==null)
        {
            $transaction = Transaction::whereIn('account_id', $ChartAccount)->where('type', 'Cr')->get();
        }else{
            $transaction = Transaction::whereIn('account_id', $ChartAccount)->where('type', 'Cr')->whereBetween('created_at',array($start_date." 00:00:00", $end_date." 23:59:59"))->get();
        }

        return $transaction;
    }

}
