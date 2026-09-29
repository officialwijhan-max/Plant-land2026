@foreach ($ChartOfAccountList as $charAccount)
    <tr>
        <td>{{ ($charAccount->type == 2 && in_array($charAccount->id, app('equity_account'))) ? trans('account.equity') : $charAccount->TypeName }}</td>
        <th>{{ $charAccount->code }}</th>
        <td><strong>-></strong>
            @if ($charAccount->is_cost_center == 0)
                <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $charAccount->id]) }}" class="pointer" target="_blank">{{ $charAccount->name }}</a>
            @else
                {{ $charAccount->name }}
            @endif
        </td>
        <td class="text-center">{{ $charAccount->is_cost_center  == 1 ? trans('account.yes') : trans('account.no') }}</td>
        <td class="pending">
            @if($charAccount->is_active == 1)
                <span class="badge_1">{{trans('common.Active')}}</span>
            @else
                <span class="badge_2">{{trans('common.Inactive')}}</span>
            @endif
        </td>
        <td class="text-center">{{ single_price($charAccount->BalanceAmount) }}</td>
        <td>
            <!-- shortby  -->
            <div class="dropdown CRM_dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ trans('account.select') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                    @if (permissionCheck('leadger.rename_account') && $charAccount->transactions->count() == 0 && $charAccount->is_cost_center != 1)
                        <a href="javascript:void(0)" class="dropdown-item edit_leadger" data-id="{{ $charAccount->id }}" class="dropdown-item rename_account" type="button">{{ trans('account.edit') }}</a>
                    @endif
                    @if ($charAccount->is_cost_center == 0 && permissionCheck('leadger_report.leadger_report_view'))
                        <a href="{{ route('leadger_report.leadger_report_view',['leadgerId' => $charAccount->id]) }}" class="dropdown-item" target="_blank">{{ trans('account.details') }}</a>
                    @endif
                    @if ($charAccount->id > 60 && $charAccount->transactions->count() == 0 && permissionCheck('leadger.delete'))
                        <a href="#" class="dropdown-item delete_leadger" data-id="{{ $charAccount->id }}" type="button">{{ trans('account.delete') }}</a>
                    @endif
                </div>
            </div>
            <!-- shortby  -->
        </td>
    </tr>

    @foreach ($charAccount->childrenCategories as $child_account)
        @include('proaccount::leadger_accounts.page_component.ledger_list', ['child_account' => $child_account])
    @endforeach
@endforeach
