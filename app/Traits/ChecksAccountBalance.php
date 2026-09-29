<?php

namespace App\Traits;

use Modules\Account\Entities\ChartAccount;
use Modules\Inventory\Entities\ShowRoom;

/**
 * Shared "would this payout overdraw the account" check for Cash Vaults
 * and Bank Accounts, per the business rule: neither may go negative.
 *
 * Used by controllers right after their existing request validation and
 * before any repository call that posts a voucher, so a blocked payment
 * never partially posts. Deliberately NOT thrown as an exception - most
 * controllers in this app catch \Exception generically in their try block
 * and show a fixed "Something Went Wrong" message, which would swallow a
 * specific insufficient-funds message. Call this before the try block and
 * Toastr::error()/return back() on a non-null result, the same way
 * $request->validate() failures are already handled.
 */
trait ChecksAccountBalance
{
    /**
     * @param array $payments Each entry: ['payment_method' => 'cash'|'bank'|'quick cash', 'account_id' => ?int, 'amount' => numeric]
     *                        This is the exact shape already built by Purchase/Sale/Payroll/Transfer controllers.
     * @param int|null $showroomId Defaults to the current session showroom.
     * @return string|null Error message if any account would go negative, otherwise null.
     */
    public function insufficientFundsMessage(array $payments, ?int $showroomId = null): ?string
    {
        $showroomId = $showroomId ?? session()->get('showroom_id', 1);
        $requestedByAccount = [];

        foreach ($payments as $payment) {
            $amount = (float) ($payment['amount'] ?? 0);
            if ($amount <= 0) {
                continue;
            }

            if (($payment['payment_method'] ?? null) === 'bank') {
                $chartAccountId = $payment['account_id'] ?? null;
            } else {
                // cash / quick cash both draw from the current branch's Cash-in-Hand account.
                $chartAccountId = ChartAccount::where('contactable_id', $showroomId)
                    ->where('contactable_type', ShowRoom::class)
                    ->value('id');
            }

            if (! $chartAccountId) {
                continue;
            }

            $requestedByAccount[$chartAccountId] = ($requestedByAccount[$chartAccountId] ?? 0) + $amount;
        }

        foreach ($requestedByAccount as $chartAccountId => $requested) {
            $message = $this->insufficientFundsForAccount((int) $chartAccountId, $requested);
            if ($message) {
                return $message;
            }
        }

        return null;
    }

    /**
     * Direct single-account check, for payout paths that already resolved
     * a specific ChartAccount id (e.g. a branch transfer's source account)
     * rather than building the cash/bank payment-line array above.
     */
    public function insufficientFundsForAccount(?int $chartAccountId, float $requested): ?string
    {
        if (! $chartAccountId || $requested <= 0) {
            return null;
        }

        $account = ChartAccount::find($chartAccountId);
        if (! $account) {
            return null;
        }

        $available = $account->getBalanceAmountAttribute();
        if ($requested > $available) {
            $short = round($requested - $available, 2);
            return sprintf(
                'Insufficient funds in "%s": available %s, this transaction needs %s (short by %s). Add a top-up deposit to this account first.',
                $account->name,
                number_format($available, 2),
                number_format($requested, 2),
                number_format($short, 2)
            );
        }

        return null;
    }
}
