<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="text" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('add_contact.store'))
        <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("add_contact.index")}}"><i class="ti-plus"></i>{{__('common.New Contact')}}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('customer').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('customer').'?import_as=csv'}}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        @if(permissionCheck('contact_csv_upload.store'))
            <a href="{{route('contact_csv_upload.create')}}" title="Import">
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
                        {{__('common.sl')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="contact_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "contact_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "contact_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Contact ID') }}
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
                        {{ __('setting.Active') }}
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
                        {{ __('common.Customer Name') }}
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
                        {{ __('common.Email') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="mobile" href="#">
                        @if (request('sort') == 'asc' && request('col') == "mobile")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "mobile")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Phone') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="pay_term" href="#">
                        @if (request('sort') == 'asc' && request('col') == "pay_term")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "pay_term")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Pay Term') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="tax_number" href="#">
                        @if (request('sort') == 'asc' && request('col') == "tax_number")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "tax_number")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Tax Number') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $customer_value)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td><a href="{{route('customer.view',$customer_value->id)}}">{{$customer_value->contact_id}}</a> </x-table.td>
                <x-table.td>
                    <label class="switch_toggle" for="active_checkbox{{ $customer_value->id }}">
                        <input type="checkbox" id="active_checkbox{{ $customer_value->id }}"
                               @if ($customer_value->is_active == 1) checked
                               @endif value="{{ $customer_value->id }}"
                               onchange="update_active_status(this)" {{ permissionCheck('languages.update_active_status') ? '' : 'disabled' }}>
                        <div class="slider round"></div>
                    </label>
                </x-table.td>
                <x-table.td>
                    <a href="{{route('customer.view',$customer_value->id)}}" target="_blank" class="pointer">{{$customer_value->name}}</a>
                </x-table.td>
                <x-table.td>{{$customer_value->email}}</x-table.td>
                <x-table.td>{{$customer_value->mobile}}</x-table.td>
                <x-table.td>{{ $customer_value->pay_term ?? '' }} {{ $customer_value->pay_term_condition ?? '' }}</x-table.td>
                <x-table.td>{{$customer_value->tax_number}}</x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            {{__('common.Select')}}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                            <a href="{{route('customer.view',$customer_value->id)}}" class="dropdown-item">{{__('common.View')}}</a>
                            @if(permissionCheck('add_contact.edit') && $customer_value->id > 1)
                                <a href="{{route('add_contact.edit',$customer_value->id)}}" class="dropdown-item">{{__('common.Edit')}}</a>
                            @endif

                            @if(permissionCheck('add_contact.destroy') && $customer_value->id > 1)
                                <a onclick="confirm_modal('{{route('add_contact.delete',$customer_value->id)}}');" class="dropdown-item ">{{__('common.Delete')}}</a>
                            @endif
                        </div>
                    </div>
                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
