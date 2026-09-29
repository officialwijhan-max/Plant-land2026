<?php

namespace Modules\Account\Repositories;

use Modules\Account\Entities\Voucher;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Entities\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use Modules\Account\Repositories\LedgerReportRepositoryInterface;

class LedgerReportRepository implements LedgerReportRepositoryInterface
{

    public function search($dateFrom, $dateTo, $account_id)
    {
        $conditions = [];
        if ($account_id != null) {
            $conditions = array_merge($conditions, ['account_id' => $account_id]);
        }

        $query = Transaction::query();

        if ($dateFrom != null && $dateTo != null) {
            $query->whereBetween('created_at', [$dateFrom . " 00:00:00", $dateTo . " 23:59:59"]);
        }

        $results = $query->where($conditions)
            ->with([
                'voucherable:id,referable_type,referable_id,date,tx_id,narration,payment_type',
                'voucherable.referable:id', // Adjusted to avoid missing column error
                'account:id,name'
            ])
            ->latest()
            ->get(['id', 'type', 'amount', 'voucherable_id', 'voucherable_type', 'account_id']);

        return $results;
    }


    public function balanceBeforeDate($dateFrom, $beforedateAccount)
    {
        $beforeDateTransactions = Transaction::where('account_id', $beforedateAccount['id'])->where('created_at', '<', $dateFrom." 23:59:59")->get();
        if ($beforedateAccount->type == 1 || $beforedateAccount->type == 4) {
            $balance = $beforeDateTransactions->where('type', 'Dr')->sum('amount') - $beforeDateTransactions->where('type', 'Cr')->sum('amount');
        }else {
            $balance = $beforeDateTransactions->where('type', 'Cr')->sum('amount') - $beforeDateTransactions->where('type', 'Dr')->sum('amount');
        }
        return $balance;
    }
}
