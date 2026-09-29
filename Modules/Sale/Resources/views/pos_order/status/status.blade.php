@if ($orders->is_approved == 0)
    <h6><span class="badge_4">{{__('sale::sale.pending')}}</span></h6>
@else
    <h6><span class="badge_1">{{__('sale::sale.approved')}}</span></h6>
@endif
