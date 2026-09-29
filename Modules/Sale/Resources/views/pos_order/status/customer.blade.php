@if ($orders->customer_id != null)
<a href="{{route('customer.view',$orders->customer_id)}}" target="_blank" class="pointer">{{$orders->customer->name}}</a>
@else
<a href="{{route('agent.show',$orders->agent_user_id)}}" target="_blank" class="pointer">{{$orders->agentuser->name}}</a>
@endif
