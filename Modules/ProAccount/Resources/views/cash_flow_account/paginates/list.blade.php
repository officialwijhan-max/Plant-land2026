<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if (permissionCheck('cash_flow_account.store'))
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="#"  data-toggle="modal" data-target="#create_cash_flow_account"><i class="ti-plus"></i>{{ trans('account.create_cash_flow_account') }}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('cash_flow_account.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('cash_flow_account.index').'?import_as=csv'}}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        @if (permissionCheck('cash_flow_account.import_page'))
            <a href="{{ route('cash_flow_account.import_page') }}" target="_blank" title="Import">
                <i class="ti-import"></i>
            </a>
        @endif
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>
    <x-slot name="table">
        <x-table.head>
            <tr>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Sl')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="type" href="#">
                        @if (request('sort') == 'asc' && request('col') == "type")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "type")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.type')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="code" href="#">
                        @if (request('sort') == 'asc' && request('col') == "code")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "code")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.code')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.name')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="is_active" href="#">
                        @if (request('sort') == 'asc' && request('col') == "is_active")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "is_active")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.status')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                    class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>
        <x-table.body>
            @foreach ($items as $key => $item)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td>{{ ($item->type == 3) ? trans('account.expense') : trans('account.income') }}</x-table.td>
                <x-table.td>{{ $item->code }}</x-table.td>
                <x-table.td>{{ $item->name }}</x-table.td>
                <x-table.td>
                    @if ($item->is_active == 1)
                        <h6>{{trans('common.Active')}}</h6>
                    @else
                        <h6>{{trans('common.Inactive')}}</h6>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            @if (permissionCheck('cash_flow_account.update'))
                                <a class="dropdown-item edit_cash_flow_acc" data-id="{{ $item->id }}">{{ trans('account.edit') }}</a>
                            @endif
                            @if (permissionCheck('cash_flow_report.cash_flow_view'))
                                <a href="{{ route('cash_flow_report.cash_flow_view',['cashflowId' => $item->id]) }}" class="dropdown-item" target="_blank">{{ trans('account.details') }}</a>
                            @endif
                            @if (permissionCheck('cash_flow_account.delete'))
                                <a class="dropdown-item delete_customer" data-id="{{ $item->id }}" type="button">{{ __('common.Delete') }}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
