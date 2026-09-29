<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('transfer_showroom.index').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('transfer_showroom.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{ __('common.ID') }}
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
                        {{ __('common.Name') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="address" href="#">
                        @if (request('sort') == 'asc' && request('col') == "address")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "address")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('sale.Reference No')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="email" href="#">
                        @if (request('sort') == 'asc' && request('col') == "email")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "email")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Amount') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="is_approve" href="#">
                        @if (request('sort') == 'asc' && request('col') == "is_approve")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "is_approve")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('account.Approved') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{ __('common.Action') }}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $item)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td>{{ __('account.Contra Voucher') }}</x-table.td>
                <x-table.td>{{ $item->tx_id }}</x-table.td>
                <x-table.td>{{ single_price($item->amount) }}</x-table.td>
                <x-table.td>
                    @if ($item->is_approve == 0)
                        <span class="badge_3">{{ __('common.Pending') }}</span>
                    @elseif ($item->is_approve == 1)
                        <span class="badge_1">{{ __('common.Approved') }}</span>
                    @else
                        <span class="badge_4">{{ __('common.Cancelled') }}</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            <a href="#" data-toggle="modal" class="dropdown-item" onclick="voucher_detail({{ $item->id }})">{{__('account.View')}}</a>
                            @if ($item->is_approve != 1 && permissionCheck('transfer_showroom.edit'))
                                <a href="{{ route('transfer_showroom.edit', $item->id) }}" data-toggle="modal" class="dropdown-item edit_brand">{{__('common.Edit')}}</a>
                            @endif
                            @if($item->is_approve != 1 && permissionCheck('vouchers.destroy'))
                                <a onclick="confirm_modal('{{route('vouchers.destroy', $item->id)}}');" class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>
</x-table>
