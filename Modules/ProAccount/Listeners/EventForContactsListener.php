<?php

namespace Modules\ProAccount\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\ProAccount\Events\EventForContacts;

class EventForContactsListener
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
    public function handle(EventForContacts $event)
    {
        foreach($event as $item)
        {
            $sub_leadger = Subleadger::create([
                'leadger_id' => (strtolower($item->contact_type) == "supplier") ? Settings('account_payable') : Settings('account_recievable'),
                'code' => $item->contact_id,
                'name' => $item->name,
                'morphable_type' => get_class($item),
                'morphable_id' => $item->id,
                'description' => $item->contact_type.' Account Created'
            ]);
        }
    }
}
