
@if (count($child_account->transactionsForFinancialYear()) > 0)
    <tr>
        <td class={{ ($child_account->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
            <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $child_account->id]) }}" target="_blank">{{ $child_account->name }}</a>
        </td>
        <td class="">{{ ($child_account->is_cost_center == 0) ? $child_account->code : ""}}</td>
        @foreach ($financial_years as $l => $financial_year)
            @if (Settings('income_summary_debit_leadger') != $child_account->id)
                <td class="text-right">{{ ($child_account->is_cost_center == 0) ? single_price($child_account->FinancialYearBalance($financial_year->id, $showroom_id)) : '' }}</td>
            @endif
        @endforeach
    </tr>
@endif
@if ($child_account->categories)
    @foreach ($child_account->categories as $child_account)
        @include('proaccount::reports.income_statement_report.component.child_list', ['child_account' => $child_account])
    @endforeach
@endif
