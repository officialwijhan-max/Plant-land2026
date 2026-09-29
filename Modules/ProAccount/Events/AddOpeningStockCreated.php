<?php

namespace Modules\Account\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class AddOpeningStockCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $credit_amounts,$credit_account_id,$credit_partner_id,$credit_cash_flow_account_id,$credit_narration,$debit_amounts,
    $debit_account_id,$debit_partner_id,$debit_cash_flow_account_id,$debit_narration,$productHistoryID,$productHistoryClass;


    public function __construct($credit_amounts,$credit_account_id,$credit_partner_id,$credit_cash_flow_account_id,$credit_narration,$debit_amounts,
    $debit_account_id,$debit_partner_id,$debit_cash_flow_account_id,$debit_narration, $productHistoryID, $productHistoryClass)
    {
        $this->credit_amounts = $credit_amounts;
        $this->credit_account_id = $credit_account_id;
        $this->credit_partner_id = $credit_partner_id;
        $this->credit_cash_flow_account_id = $credit_cash_flow_account_id;
        $this->credit_narration = $credit_narration;
        $this->debit_amounts = $debit_amounts;
        $this->debit_account_id = $debit_account_id;
        $this->debit_partner_id = $debit_partner_id;
        $this->debit_cash_flow_account_id = $debit_cash_flow_account_id;
        $this->debit_narration = $debit_narration;
        $this->productHistoryID = $productHistoryID;
        $this->productHistoryClass = $productHistoryClass;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
