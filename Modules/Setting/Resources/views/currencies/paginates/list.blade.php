<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('currencies.store'))
        <a data-toggle="modal"class="primary-btn radius_30px mr-10 fix-gr-bg" href="#" onclick="open_add_currency_modal()"><i class="ti-plus"></i>{{ __('common.Add New') }} {{ __('common.Currency') }}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('currencies.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('currencies.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{__('common.ID')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="title" href="#">
                        @if (request('sort') == 'asc' && request('col') == "title")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "title")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Name')}}
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
                        {{__('setting.Code')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="symbol" href="#">
                        @if (request('sort') == 'asc' && request('col') == "symbol")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "symbol")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('setting.Symbol')}}
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
                <x-table.td><a href="#">{{ $item->name }}</a> </x-table.td>
                <x-table.td>{{ $item->code }}</x-table.td>
                <x-table.td>{{ $item->symbol }}</x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            @if(permissionCheck('currencies.edit'))
                            <a href="#" class="dropdown-item edit_brand" onclick="edit_currency_modal({{ $item->id }})">{{__('common.Edit')}}</a>
                            @endif
                            @if(permissionCheck('currencies.destroy') && $item->id > 120)
                            <a onclick="confirm_modal('{{route('currencies.destroy', $item->id)}}');" class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
