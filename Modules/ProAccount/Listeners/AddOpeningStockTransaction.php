<?php

namespace Modules\ProAccount\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\ProAccount\Events\AddOpeningStockCreated;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\ProAccount\Repositories\JournalRepository;
use Carbon\Carbon;

class AddOpeningStockTransaction
{
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
    public function handle(AddOpeningStockCreated $event)
    {
        return true;
    }
}
