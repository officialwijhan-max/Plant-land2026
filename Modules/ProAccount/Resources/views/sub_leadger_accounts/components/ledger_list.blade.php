
<x-table :models="$data" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none"
            value="{{ request('sort') ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none"
            value="{{ request('col') ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{ route('sub_leadger.index') . '?import_as=print' }}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{ route('sub_leadger.index') . '?import_as=csv' }}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>

    <x-slot name="table">

        <x-table.head>
            <tr>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Sl') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="type" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'type')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'type')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Type') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="ledger" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'ledger')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'ledger')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ trans('account.ledger') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="code" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'code')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'code')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Code') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'name')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'name')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Name') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="balance" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'balance')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'balance')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('account.Balance') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="status" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'status')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'status')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Status') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{ __('common.Action') }}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($data as $key => $item)
                <x-table.tr>
                    <x-table.th><a href="#">{{ $key + 1 }}</a></x-table.th>
                    <x-table.td>
                        <a href="#">{{ $item->leadger->TypeName }}</a>
                    </x-table.td>
                    <x-table.td>{{ $item->leadger->name }}</x-table.td>
                    <x-table.td>{{ $item->code }}</x-table.td>
                    <x-table.td>
                        <strong>-></strong>
                        <a class="pointer" target="_blank"
                            href="{{ route('leadger_report.sub_leadger_report_view', ['subleagerId' => $item->id]) }}">{{ $item->name }}</a>
                    </x-table.td>
                    <x-table.td>{{ single_price($item->BalanceAmount) }}</x-table.td>
                    <x-table.td>
                        @if ($item->is_active == 1)
                            <span class="badge_1">{{ __('common.Active') }}</span>
                        @else
                            <span class="badge_4">{{ __('common.Inactive') }}</span>
                        @endif
                    </x-table.td>
                    <x-table.td>
                        @if (permissionCheck('leadger_report.sub_leadger_report_view'))
                            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{ route('leadger_report.sub_leadger_report_view', ['subleagerId' => $item->id]) }}">{{ trans('account.leadger') }}</a>
                        @endif
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
