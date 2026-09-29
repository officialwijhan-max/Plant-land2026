
@if (count($child_account->transactions) > 0)
    @php
        $balance = $child_account->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
    @endphp
    <tr>
        <td class={{ ($child_account->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
            <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $child_account->id]) }}" target="_blank">{{ $child_account->name }}</a>
        </td>
        <td class="">{{ ($child_account->is_cost_center == 0) ? $child_account->code : ""}}</td>
        <td class="text-right">{{ ($child_account->is_cost_center == 0) ? single_price($balance) : '' }}</td>
    </tr>
@endif
@if ($child_account->categories)
    @foreach ($child_account->categories->whereNotIn('id', Settings('retail_earning_leadger')) as $child_account)
        @include('proaccount::reports.balance_sheet.component.child_list_others', ['child_account' => $child_account])
    @endforeach
@endif 
