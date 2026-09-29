<table class="table Crm_table_active5">
    <thead>
    <tr>
        <th scope="col">{{trans('account.type')}}</th>
        <th scope="col">{{trans('account.leadger')}}</th>
        <th scope="col">{{trans('account.code')}}</th>
        <th scope="col">{{trans('account.name')}}</th>
        <th scope="col">{{trans('account.balance')}}</th>
        <th scope="col">{{trans('account.status')}}</th>
        <th scope="col" width="10%">{{trans('account.action')}}</th>
    </tr>
    </thead>
    <tbody id="datas">
        @foreach ($sub_leadger_list as $sub_leadger)
            <tr>
                <td>{{ $sub_leadger->leadger->TypeName }}</td>
                <td>{{ $sub_leadger->leadger->name }}</td>
                <td>{{ $sub_leadger->code }}</td>
                <td><strong>-></strong>
                    <a class="pointer" target="_blank" href="{{ route('leadger_report.sub_leadger_report_view',['subleagerId' => $sub_leadger->id]) }}">{{ $sub_leadger->name }}</a>
                </td>
                <td>{{ single_price($sub_leadger->BalanceAmount) }}</td>
                <td>
                    @if($sub_leadger->is_active == 1)
                        <span class="badge_1">{{trans('common.Active')}}</span>
                    @else
                        <span class="badge_2">{{trans('common.Inactive')}}</span>
                    @endif
                </td>
                <td>
                    @if (permissionCheck('leadger_report.sub_leadger_report_view'))
                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" href="{{ route('leadger_report.sub_leadger_report_view',['subleagerId' => $sub_leadger->id]) }}">{{ trans('account.leadger') }}</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
