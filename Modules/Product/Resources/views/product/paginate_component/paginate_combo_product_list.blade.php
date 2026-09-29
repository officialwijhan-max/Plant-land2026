<x-table :models="$combo_items" filter_id='filter_id' id="product_combo_table">
    <x-slot name='left_side_btn'>
        <input type="text" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('add_product.index'))
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("add_product.index")}}"><i class="ti-plus"></i>{{__('product.New Product')}}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('add_product.create').'?type=combo_product&import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('add_product.create').'?type=combo_product&import_as=csv'}}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        @if(permissionCheck('add_product.csv_upload.store'))
        <a href="{{route('add_product.csv_upload.create')}}" title="Import">
            <i class="ti-import"></i>
        </a>
        @endif
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn_2">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>

    <x-slot name="table">

        <x-table.head>
            <tr>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Sl')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Image')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="name" href="#">
                        @if (request('sort') == 'asc' && request('col') == "name")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "name")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Name')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="price" href="#">
                        @if (request('sort') == 'asc' && request('col') == "price")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "price")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Price')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="total_regular_price" href="#">
                        @if (request('sort') == 'asc' && request('col') == "total_regular_price")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "total_regular_price")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Regular Price')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Total Product')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="status" href="#">
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
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="starus" href="#">
                        @if (request('sort') == 'asc' && request('col') == "starus")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "starus")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('common.Enable')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title_2" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($combo_items as $key => $comboProduct)
            <x-table.tr>
                <x-table.th>{{ $key+1 }}</x-table.th>
                <x-table.td>
                    @if ($comboProduct->image_source != null)
                        <img style="height: 36px;"
                                src="{{asset($comboProduct->image_source)}}">
                    @else
                        <img style="height: 36px;"
                                src="{{asset('public/backEnd/img/no_image.png')}}"
                                alt="{{@$comboProduct->name}}">
                    @endif
                </x-table.td>
                <x-table.td>
                    <a href="#" data-toggle="modal" onclick="product_detail({{ $comboProduct->id }} , 'combo')">{{ $comboProduct->name }}</a>
                </x-table.td>
                <x-table.td>{{ single_price($comboProduct->price) }}</x-table.td>
                <x-table.td>{{ single_price($comboProduct->total_regular_price) }}</x-table.td>
                <x-table.td>{{ count($comboProduct->combo_products) }} {{ __('product.pcs') }}</x-table.td>
                <x-table.td>
                    @if ($comboProduct->status == 0)
                        <span class="badge_4">{{ __('product.Close') }}</span>
                    @else
                        <span class="badge_1">{{ __('product.Open') }}</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    <label class="switch_toggle" for="active_checkbox{{ $comboProduct->id }}">
                        <input type="checkbox" id="active_checkbox{{ $comboProduct->id }}" {{ permissionCheck('combo_product.update_active_status') ? '' : 'disabled' }} @if ($comboProduct->status == 1) checked @endif value="{{ $comboProduct->id }}" onchange="update_active_status(this)">
                        <div class="slider round"></div>
                    </label>
                </x-table.td>
                <x-table.td>
                    <div class="dropdown CRM_dropdown">
                        <button class="btn btn-secondary dropdown-toggle"
                                type="button" id="dropdownMenu2"
                                data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"> {{ __('common.Select') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="dropdownMenu2">
                            @if (count($comboProduct->combo_products) > 0)
                                @if(permissionCheck('add_product.editCombo'))
                                    <a href="{{route('add_product.editCombo',$comboProduct->id)}}"
                                       class="dropdown-item"
                                       type="button">{{__('common.Edit')}}</a>
                                @endif
                                @if(permissionCheck('add_product.product_Detail'))
                                    <a href="#" data-toggle="modal"
                                       class="dropdown-item"
                                       onclick="product_detail({{ $comboProduct->id }} , 'combo')">{{__('common.View')}}</a>
                                @endif
                            @endif
                            @if(permissionCheck('combo_product.destroy'))
                                <a onclick="confirm_modal('{{route('combo_product.destroy',$comboProduct->id)}}');"
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
