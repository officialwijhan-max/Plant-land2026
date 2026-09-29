<tr>
    <td>{{ ($child_account->type == 2 && in_array($child_account->id, app('equity_account')['equity_account'])) ? trans('account.equity') : $child_account->TypeName }}</td>
    <th>{{ $child_account->code }}</th>
    <td>
        @for ($i = 0; $i < $child_account->level; $i++)
            <strong>-</strong>
        @endfor
        <strong>-></strong>
        @if ($child_account->is_cost_center == 0)
            <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $child_account->id]) }}" class="pointer" target="_blank">{{ $child_account->name }}</a>
        @else
            {{ $child_account->name }}
        @endif
    </td>
        <td class="text-center">{{ $child_account->is_cost_center == 1 ? trans('account.yes') : trans('account.no') }}</td>
        <td class="pending">
            @if($child_account->is_active == 1)
                <span class="badge_1">{{trans('common.Active')}}</span>
            @else
                <span class="badge_2">{{trans('common.Inactive')}}</span>
            @endif
        </td>
        <td>{{ single_price($child_account->BalanceAmount) }}</td>
    <td>
        <!-- shortby  -->
        <div class="dropdown CRM_dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ trans('account.select') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                @if (permissionCheck('leadger.rename_account') && $child_account->transactions->count() == 0 && $child_account->is_cost_center != 1)
                    <a href="javascript:void(0)" class="dropdown-item edit_leadger" data-id="{{ $child_account->id }}" class="dropdown-item rename_account" type="button">{{ trans('account.edit') }}</a>
                @endif
                @if ($child_account->is_cost_center == 0 && permissionCheck('leadger_report.leadger_report_view'))
                    <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $child_account->id]) }}" class="dropdown-item" target="_blank">{{ trans('account.details') }}</a>
                @endif
                @if ($child_account->id > 60 && $child_account->transactions->count() == 0 && permissionCheck('leadger.delete'))
                    <a href="#" class="dropdown-item delete_leadger" data-id="{{ $child_account->id }}" type="button">{{ trans('account.delete') }}</a>
                @endif
            </div>
        </div>
        <!-- shortby  -->
    </td>
</tr>
@if ($child_account->categories)
    @foreach ($child_account->categories as $child_account)
        @include('proaccount::leadger_accounts.page_component.child_ledger_list', ['child_account' => $child_account])
    @endforeach
@endif
