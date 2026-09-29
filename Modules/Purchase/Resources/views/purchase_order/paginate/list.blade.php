<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('purchase_order.store'))
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("purchase_order.create")}}"><i class="ti-plus"></i>{{__('purchase.New Order')}}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        @if (auth()->user()->role->type != "normal_user")
            <a href="{{route('purchase_order.index').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('purchase_order.index').'?import_as=csv'}}" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @else
            <a href="{{route('purchase_order.self_invoices').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('purchase_order.self_invoices').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{__('common.No')}}
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
                    <a class="custom_thead_title" data-id="supplier_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "supplier_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "supplier_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('quotation.Supplier')}} {{__('common.Name')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="invoice_no" href="#">
                        @if (request('sort') == 'asc' && request('col') == "invoice_no")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "invoice_no")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('sale.Invoice No')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="payable_amount" href="#">
                        @if (request('sort') == 'asc' && request('col') == "payable_amount")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "payable_amount")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('purchase.Total Amount')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="payable_amount" href="#">
                        @if (request('sort') == 'asc' && request('col') == "payable_amount")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "payable_amount")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('purchase.Paid Amount')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="payable_amount" href="#">
                        @if (request('sort') == 'asc' && request('col') == "payable_amount")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "payable_amount")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('purchase.Due Amount')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="payable_amount" href="#">
                        @if (request('sort') == 'asc' && request('col') == "payable_amount")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "payable_amount")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('purchase.Is Approved')}}
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
                <x-table.td><a href="#">{{ showDate($item->date) }}</a> </x-table.td>
                <x-table.td>{{ @$item->supplier->name }}</x-table.td>
                <x-table.td><a href="#" onclick="getDetails({{ $item->id }})">{{$item->invoice_no}}</a></x-table.td>
                <x-table.td>{{ single_price($item->payable_amount) }}</x-table.td>
                <x-table.td>{{ single_price($item->payments()->where('payment_type','pay')->sum('amount')) }}</x-table.td>
                <x-table.td>{{ single_price($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount')) }}</x-table.td>
                <x-table.td>
                    @if ($item->status == 0)
                        <h6><span class="badge_4">{{__('purchase.No')}}</span></h6>
                    @else
                        <h6><span class="badge_1">{{__('purchase.Yes')}}</span></h6>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            {{__('common.Select')}}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="dropdownMenu2">
                            @if ($item->is_paid != 2 && $item->status == 1)
                                <a href="{{route('purchase.payment',$item->id)}}"
                                   class="dropdown-item"
                                   type="button">{{__('pos.Payment')}}</a>
                            @endif
                            @if($item->status == 0)
                                <a onclick="approve_modal('{{route('purchase.approve', $item->id)}}')"
                                   class="dropdown-item edit_brand">{{__('sale.Approve')}}</a>
                            @endif
                            @if(permissionCheck('purchase_order.edit'))
                            <a href="{{route('purchase_order.edit',$item->id)}}"
                               class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                            @endif
                            @if ($item->return_status == 2 && $item->added_to_stock == 1)
                                <a href="{{route('purchase.order.return',$item->id)}}" class="dropdown-item"
                                   type="button">{{__('purchase.Purchase Return')}}</a>
                            @endif
                            @if(permissionCheck('purchase_order.show'))
                            <a href="{{route('purchase_order.show',$item->id)}}"
                               class="dropdown-item"
                               type="button">{{__('purchase.Purchase Details')}}</a>
                            @endif

                            @if(permissionCheck('purchase.order.destroy'))
                            <a onclick="confirm_modal('{{route('purchase.order.destroy', $item->id)}}')"
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
