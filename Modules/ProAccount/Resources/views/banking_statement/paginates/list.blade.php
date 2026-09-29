<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('banking_statement.create') && Settings('accounting_entry_system') != "single_entry")
            <a class="primary-btn radius_30px mr-10 create_link fix-gr-bg" href="{{ route('banking_statement.create') }}"><i class="ti-plus"></i>{{ trans('account.upload_banking_transaction') }}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('banking_statement.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('banking_statement.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                    <a class="custom_thead_title" data-id="date" href="#">
                        @if (request('sort') == 'asc' && request('col') == "date")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "date")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="leadger_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "leadger_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "leadger_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.account')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="balance" href="#">
                        @if (request('sort') == 'asc' && request('col') == "balance")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "balance")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.balance')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="re_conciled" href="#">
                        @if (request('sort') == 'asc' && request('col') == "re_conciled")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "re_conciled")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.reconciled')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="created_by" href="#">
                        @if (request('sort') == 'asc' && request('col') == "created_by")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "created_by")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{trans('account.uploaded_by')}}
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
                <x-table.td>{{ showDate($item->date) }}</x-table.td>
                <x-table.td>{{ $item->leadger->name }}</x-table.td>
                <x-table.td>{{ single_price($item->balance) }}</x-table.td>
                <x-table.td>
                    @if ($item->re_conciled == 1)
                        <span class="badge_1">{{__('account.Closed')}}</span>
                    @else
                        <span class="badge_3">{{__('account.Open')}}</span>
                    @endif
                </x-table.td>
                <x-table.td>{{ $item->user->name }}</x-table.td>
                <x-table.td>
                    <!-- shortby  -->
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            @if (permissionCheck('banking_statement.show'))
                                <a class="dropdown-item" href="{{ route('banking_statement.show',$item->id) }}">{{trans('account.view')}}</a>
                            @endif
                            @if (permissionCheck('banking_statement.reconciled'))
                                <a class="dropdown-item" href="{{ route('banking_statement.reconciled',$item->id) }}">{{trans('account.reconciled')}}</a>
                            @endif
                            @if (permissionCheck('banking_statement.done') && $item->re_conciled != 1)
                                <a class="dropdown-item reconcilation_done" href="#" data-id="{{ $item->id }}">{{trans('account.check_complete')}}</a>
                            @endif
                            @if (permissionCheck('banking_statement.destroy') && $item->re_conciled != 1)
                                <a class="dropdown-item delete_leadger" onclick="confirm_modal('{{route('banking_statement.destroy', $item->id)}}');" data-id="{{ $item->id }}" type="button">{{ __('common.Delete') }}</a>
                            @endif
                        </div>
                    </div>
                    <!-- shortby  -->
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
