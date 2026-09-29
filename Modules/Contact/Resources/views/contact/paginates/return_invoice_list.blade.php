<x-table :models="$return_invoices" filter_id='filter_id' id="return_invoice_list_in_customer_detail">
    <x-slot name='left_side_btn'>
        <input type="text" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : 'id' }}">
    </x-slot>

    <x-slot name='table_btns'>
        @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=print&purpose=return_invoice" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=csv&purpose=return_invoice" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @else
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=print&purpose=return_invoice" target="_blank" title="Print">
                <i class="ti-printer"></i>
            </a>
            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=csv&purpose=return_invoice" target="_blank" title="Export">
                <i class="ti-export"></i>
            </a>
        @endif
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn_">
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
                        {{ __('sale.Date') }}
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
                        {{ __('sale.Invoice') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="ref_no" href="#">
                        @if (request('sort') == 'asc' && request('col') == "ref_no")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "ref_no")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('sale.Reference No') }}
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
                        {{__('common.Sold By')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="return_status" href="#">
                        @if (request('sort') == 'asc' && request('col') == "return_status")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "return_status")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Approval')}}
                    </a>
                </x-table.th>
                <x-table.th width="10%" scope="col">
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
                        {{ __('common.Amount') }}
                    </a>
                </x-table.th>
            </tr>
        </x-table.head>
        <x-table.body>
            @foreach ($return_invoices as $key => $return)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td><a href="#">{{ showDate($return->date) }}</a> </x-table.td>
                <x-table.td><a onclick="getDetails({{ $return->id }})">{{$return->invoice_no}}</a></x-table.td>
                <x-table.td>{{ $return->ref_no }}</x-table.td>
                <x-table.td>{{ @$return->user->name }}</x-table.td>
                <x-table.td>
                    @if ($return->return_status == 0)
                        <h6><span class="badge_4">{{__('common.Pending')}}</span></h6>
                    @else
                        <h6><span class="badge_1">{{__('common.Approved')}}</span></h6>
                    @endif
                </x-table.td>
                <x-table.td>{{ single_price($return->total_return_amount) }}</x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
