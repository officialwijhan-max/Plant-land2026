<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('stock-transfer.store'))
            <ul class="d-flex">
                <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("stock-transfer.create")}}"><i class="ti-plus"></i>{{__('product.Transfer Product')}}</a>
            </ul>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        @if (request()->is('inventory/stock-transfer-recieve'))
            <a href="{{route('stock-transfer.rcv_index').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('stock-transfer.rcv_index').'?import_as=csv'}}" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @else
            <a href="{{route('stock-transfer.index').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('stock-transfer.index').'?import_as=csv'}}" target="_blank" title="Export">
                <i class="ti-export"></i>
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
                        {{__('quotation.Date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="sendable_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "sendable_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "sendable_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.From')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="receivable_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "receivable_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "receivable_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.To')}}
                    </a>
                </x-table.th>
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
                        {{__('sale.Qty')}}
                    </a>
                </x-table.th>
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
                        {{__('sale.Total Amount')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="status" href="#">
                        @if (request('sort') == 'asc' && request('col') == "status")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "status")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Status')}}
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
                <x-table.td>{{ @$item->sendable->name }}</x-table.td>
                <x-table.td>{{ @$item->receivable->name }}</x-table.td>
                <x-table.td>{{ @$item->items->sum('quantity') }}</x-table.td>
                <x-table.td>{{ single_price(@$item->items->sum('sub_total')) }}</x-table.td>
                <x-table.td>
                    @if ($item->status == 1)
                        <h6><span class="badge_1">{{__('product.Approved')}}</span></h6>
                    @else
                        <h6><span class="badge_4">{{__('common.Pending')}}</span></h6>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            {{__('common.Select')}}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            <a href="{{route('stock-transfer.show',$item->id)}}" class="dropdown-item" type="button">{{__('sale.Details')}}</a>
                            @if (permissionCheck('stock-transfer.status') && $item->status == 0)
                                <a onclick="approve_modal('{{route('stock-transfer.status', $item->id)}}')"
                                   class="dropdown-item" type="button">{{__('sale.Approve')}}</a>
                            @endif
                            @if (permissionCheck('stock-transfer.receive') && $item->status == 1 && !$item->received_at)
                                <a onclick="approve_modal('{{route('stock-transfer.receive', $item->id)}}')"
                                   class="dropdown-item" type="button">{{__('product.Receive')}}</a>
                            @endif
                            @if(permissionCheck('stock-transfer.edit') && $item->status == 0)
                                <a href="{{route('stock-transfer.edit',$item->id)}}"
                                   class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                            @endif
                            @if(permissionCheck('stock-transfer.delete'))
                                <a onclick="confirm_modal('{{route('stock-transfer.delete', $item->id)}}')"
                                   class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                            @endif

                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
