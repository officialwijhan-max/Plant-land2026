<?php

namespace Modules\Account\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\Entities\AccountDeposit;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Repositories\JournalRepository;
use Toastr;

/**
 * Mandatory top-up/deposit flow for covering a Cash Vault or Bank Account
 * shortfall - see App\Traits\ChecksAccountBalance, which is what blocks
 * the original payout and points the user here.
 *
 * Restricted to Super Admin and system_user roles (CEO/GM) rather than
 * opened up to every accounting role, since committing outside capital to
 * cover a deficit is a senior-level financial decision, not a routine
 * data-entry task. Widen this later if that's not the right call for this
 * business - it would just mean registering these routes in the
 * permissions table and attaching them to the relevant roles instead of
 * this inline check.
 */
class AccountDepositController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role;
            if ($role->id !== 1 && $role->type !== 'system_user') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function create(Request $request)
    {
        $data['chartAccountId'] = $request->query('chart_account_id');
        // Cash-in-hand accounts (parent 1, "Group Cash Account") and Bank
        // accounts (parent 3, "Bank & Mobile Banking Account") - the only
        // account types a payout can be blocked on.
        $data['accounts'] = ChartAccount::whereIn('parent_id', [1, 3])->get();

        return view('account::deposits.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'source' => 'required|string|max:255',
            'date' => 'required|date',
        ], [
            'source.required' => 'A source/reason for this deposit is required.',
        ]);

        $capitalAccount = ChartAccount::where('code', '02-09-11')->first();
        if (! $capitalAccount) {
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }

        DB::beginTransaction();
        try {
            $voucher = (new JournalRepository())->create([
                'voucher_type' => 'JV',
                'amount' => $validated['amount'],
                'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
                'account_type' => 'credit',
                'payment_type' => 'journal_voucher',
                'account_id' => $capitalAccount->id,
                'main_amount' => $validated['amount'],
                'narration' => 'Deposit: ' . $validated['source'],
                'sub_account_id' => [$validated['chart_account_id']],
                'sub_amount' => [$validated['amount']],
                'sub_narration' => [$validated['source']],
                'is_approve' => 1,
            ]);

            AccountDeposit::create([
                'chart_account_id' => $validated['chart_account_id'],
                'amount' => $validated['amount'],
                'source' => $validated['source'],
                'date' => $validated['date'],
                'voucher_id' => $voucher->id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            \LogActivity::successLog('Account deposit added: ' . $validated['source']);
            Toastr::success(__('account.Deposit Added Successfully'), __('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }
}
