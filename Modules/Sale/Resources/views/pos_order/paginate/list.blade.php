<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if (auth()->user()->role->type != "normal_user" && permissionCheck('sale.store'))
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("pos-order.products")}}"><i class="ti-printer"></i>{{__('sale.POS')}}</a>
        @endif

    </x-slot>

    <x-slot name='table_btns'>
        @if (auth()->user()->role->type != "normal_user")
            <a href="{{route('pos-order.index').'?import_as=print'}}" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{route('pos-order.index').'?import_as=csv'}}" target="_blank" title="Export">
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
                    <label class="primary_checkbox d-flex ">
                        <input type="checkbox" name="check_all" class="check_all_td">
                        <span class="checkmark"></span>
                    </label>
                </x-table.th>
                <x-table.th scope="col" class="text-left">
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
                        {{__('sale.Sl')}}
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
                        {{__('sale.Date')}}
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
                        {{__('sale.Invoice')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="user_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "user_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "user_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('sale.User')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="customer_name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "customer_name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "customer_name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Customer')}}
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
                        {{__('common.Total Amount')}}
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
                        {{__('sale.Paid')}}
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
                        {{__('sale.Due')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="is_approved" href="#">
                        @if (request('sort') == 'asc' && request('col') == "is_approved")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "is_approved")
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
                <x-table.th>
                    <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                        <input name="sale_ids[]" @if ($item->is_approved == 0) class="approve" @else disabled @endif
                               data-id="{{$item->id}}" value="{{$item->id}}" type="checkbox">
                        <span class="checkmark"></span>
                    </label>
                </x-table.th>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td><a href="#">{{ showDate($item->date) }}</a> </x-table.td>
                <x-table.td><a href="#" onclick="getDetails({{ $item->id }})">{{$item->invoice_no}}</a></x-table.td>
                <x-table.td>{{ $item->user->name }}</x-table.td>
                <x-table.td>
                    @if ($item->customer_id != null || $item->customer_id === 0)
                        {{$item->customer->name}}
                    @else
                        {{$item->agentuser->name}}
                    @endif
                </x-table.td>
                <x-table.td>{{ single_price($item->payable_amount) }}</x-table.td>
                <x-table.td>{{ single_price($item->payments()->where('payment_type','pay')->sum('amount')) }}</x-table.td>
                <x-table.td>{{ ($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount') > 0) ? single_price($item->payable_amount - $item->payments()->where('payment_type','pay')->sum('amount')) : single_price(0) }}</x-table.td>
                <x-table.td>
                    @if ($item->is_approved == 0)
                        <h6><span class="badge_4">{{__('sale.Unapproved')}}</span></h6>
                    @else
                        <h6><span class="badge_1">{{__('sale.Approved')}}</span></h6>
                    @endif
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"> {{__('common.select')}}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            @if(permissionCheck('sale.edit'))
                                <a href="{{route('sale.edit',$item->id)}}" class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                            @endif
                            @if ($item->status != 1 && $item->is_approved == 1)
                                <a href="{{route('sale.payment',$item->id)}}" class="dropdown-item"
                                   type="button">{{__('pos.Payment')}}</a>
                            @endif
                            @if(permissionCheck('return.sale.approve') && $item->return_status == 0 && $item->items->sum('return_quantity') > 0)
                                <a onclick="approve_modal('{{route('return.sale.approve', $item->id)}}')"
                                   class="dropdown-item edit_brand">{{__('sale.Return Approve')}}</a>
                            @endif
                            @if (permissionCheck('sale.return') && $item->return_status != 1)
                                <a href="{{route('sale.return',$item->id)}}" class="dropdown-item"
                                   type="button">{{__('sale.Sale Return')}}</a>
                            @endif
                            @if (permissionCheck('conditional.sale.approve') && $item->is_approved == 0)
                                <a onclick="approve_modal('{{route('conditional.sale.approve', $item->id)}}')"
                                   class="dropdown-item edit_brand">{{__('sale.Approve')}}</a>
                            @endif
                            {{-- @if ($item->type == 0)
                                <a href="#" onclick="shippingInfo({{$item->id}})"
                                   data-toggle="modal" data-target="#shipping_details"
                                   class="dropdown-item"
                                   type="button">{{__('sale.Shipping Details')}}</a>
                            @endif --}}
                            @if(permissionCheck('sale.show'))
                            <a href="{{route('sale.show',$item->id)}}" class="dropdown-item"
                               type="button">{{__('sale.Order Details')}}</a>
                            @endif
                            <a href="{{route('sale.pdf',$item->id)}}" class="dropdown-item" type="button">{{__('quotation.Download')}}</a>
                            <a href="{{route('sale.challan_pdf',$item->id)}}" class="dropdown-item" type="button">{{__('common.Challan Download')}}</a>
                            <a href="{{route('sale.clone',$item->id)}}" class="dropdown-item" type="button">{{__('sale.Clone to Sale')}}</a>
                            <a href="{{route('sale.convertTosale', $item->id)}}" class="dropdown-item" type="button">{{__('quotation.Clone to Quotation')}}</a>
                            @if(permissionCheck('sale.delete'))
                                <a onclick="confirm_modal('{{route('sale.delete', $item->id)}}')" class="dropdown-item">{{__('common.Delete')}}</a>
                            @endif
                            @if ($item->is_approved == 1)
                                <a href="{{route('sale.show',$item->id)}}" class="dropdown-item edit_brand">{{__('common.Print')}}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
