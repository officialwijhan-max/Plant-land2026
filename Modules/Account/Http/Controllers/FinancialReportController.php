<?php

namespace Modules\Account\Http\Controllers;

use App\Traits\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\ChartAccount;

/**
 * Trial Balance, Balance Sheet, and Cost Center reports built directly
 * against the live ChartAccount/Transaction engine. The only prior
 * versions of these lived in the disabled ProAccount module, which runs
 * on a completely different model (Leadger) than everything currently
 * live - reusing that would have meant a data-model rewrite, so these
 * are new, built fresh.
 */
class FinancialReportController extends Controller
{
    use Dashboard;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Every non-group account with its raw Dr/Cr transaction totals. The
     * two grand totals must be equal in a healthy double-entry system -
     * that's the actual audit value of this report, not just a listing.
     */
    public function trialBalance()
    {
        $accounts = ChartAccount::where('is_group', 0)
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                return (object) [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'debit' => (float) $account->transactions()->where('type', 'Dr')->sum('amount'),
                    'credit' => (float) $account->transactions()->where('type', 'Cr')->sum('amount'),
                ];
            })
            ->filter(fn ($row) => $row->debit != 0 || $row->credit != 0)
            ->values();

        $data['accounts'] = $accounts;
        $data['totalDebit'] = round($accounts->sum('debit'), 2);
        $data['totalCredit'] = round($accounts->sum('credit'), 2);

        return view('account::reports.trial_balance', $data);
    }

    /**
     * Assets = Liabilities + Equity. Equity = Capital accounts (the
     * "Capital & Equity" group, code 02-09, id 9 in the default chart of
     * accounts - type=2 same as Liabilities, so it has to be split out
     * by ancestry, not by type alone) plus current Net Profit, computed
     * the same way as the dashboard's own Net Profit figure so the two
     * never disagree.
     */
    public function balanceSheet()
    {
        $capitalGroup = ChartAccount::where('code', '02-09')->first();
        $capitalAccountIds = $capitalGroup
            ? ChartAccount::where('parent_id', $capitalGroup->id)->orWhere('id', $capitalGroup->id)->pluck('id')
            : collect();

        $assetAccounts = ChartAccount::where('is_group', 0)->where('type', 1)->get();
        $liabilityAccounts = ChartAccount::where('is_group', 0)->where('type', 2)
            ->whereNotIn('id', $capitalAccountIds)->get();
        $equityAccounts = ChartAccount::where('is_group', 0)->where('type', 2)
            ->whereIn('id', $capitalAccountIds)->get();

        $data['assets'] = $assetAccounts->map(fn ($a) => (object) ['name' => $a->name, 'code' => $a->code, 'amount' => round($a->getBalanceAmountAttribute(), 2)])
            ->filter(fn ($a) => $a->amount != 0)->values();
        $data['liabilities'] = $liabilityAccounts->map(fn ($a) => (object) ['name' => $a->name, 'code' => $a->code, 'amount' => round($a->getBalanceAmountAttribute(), 2)])
            ->filter(fn ($a) => $a->amount != 0)->values();
        $data['equity'] = $equityAccounts->map(fn ($a) => (object) ['name' => $a->name, 'code' => $a->code, 'amount' => round($a->getBalanceAmountAttribute(), 2)])
            ->filter(fn ($a) => $a->amount != 0)->values();

        $data['netProfit'] = round($this->totalIncome(), 2);
        $data['totalAssets'] = round($data['assets']->sum('amount'), 2);
        $data['totalLiabilities'] = round($data['liabilities']->sum('amount'), 2);
        $data['totalEquity'] = round($data['equity']->sum('amount') + $data['netProfit'], 2);

        return view('account::reports.balance_sheet', $data);
    }

    /**
     * Transaction totals for every account flagged is_cost_center=1,
     * within an optional date range.
     */
    public function costCenters(Request $request)
    {
        $from = $request->from_date;
        $to = $request->to_date;

        $accounts = ChartAccount::costCenters()->get()->map(function ($account) use ($from, $to) {
            $query = $account->transactions();
            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
            $transactions = $query->get();

            return (object) [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'debit' => (float) $transactions->where('type', 'Dr')->sum('amount'),
                'credit' => (float) $transactions->where('type', 'Cr')->sum('amount'),
            ];
        });

        $data['accounts'] = $accounts;
        $data['from'] = $from;
        $data['to'] = $to;

        return view('account::reports.cost_centers', $data);
    }
}
