<?php

namespace Modules\ProAccount\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\ProAccount\Events\SalesTransactionCreated;
use Modules\Core\Entities\Contacts\ContactModel;
use Modules\Core\Entities\Product\ProductSku;
use Modules\ProAccount\Repositories\JournalRepository;
use Modules\Core\Entities\Others\Tax;
use Modules\ProAccount\Entities\Leadger;
use App\User;
use App\Traits\Accounts;
use Carbon\Carbon;

class SalesTransactionListener
{
    use Accounts;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SalesTransactionCreated $event)
    {
        $is_approved = 1;
    }
}
