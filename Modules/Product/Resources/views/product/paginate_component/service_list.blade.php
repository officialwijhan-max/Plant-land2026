<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('add_product.index'))
        <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("add_product.index")}}"><i class="ti-plus"></i>{{__('product.New Product')}}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('add_product.service').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('add_product.service').'?import_as=csv'}}" target="_blank" title="Export">
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
                        {{__('product.Image')}}
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
                        {{__('product.Name')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="sku" href="#">
                        @if (request('sort') == 'asc' && request('col') == "sku")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "sku")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        @if (app('general_setting')->origin == 1)
                            {{__('common.Part Number')}}
                        @else
                            {{__('sale.SKU')}}
                        @endif
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="selling_price" href="#">
                        @if (request('sort') == 'asc' && request('col') == "selling_price")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "selling_price")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Hourly Rate')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $productSku)
                <x-table.tr>
                    <x-table.td>{{$key+1}}</x-table.td>
                    <x-table.td>
                        @if (@$productSku->product->product_type == "Single" && @$productSku->product->image_source != null)
                            <img style="height: 36px;"
                                src="{{asset(@$productSku->product->image_source ?? 'public/backEnd/img/no_image.png')}}"
                                alt="{{@$productSku->product->product_name}}">
                        @elseif(@$productSku->product->product_type == "Variable" && @$productSku->product->image_source != null)
                            <img style="height: 36px;"
                                src="{{asset(@$productSku->product_variation->image_source ?? 'public/backEnd/img/no_image.png')}}"
                                alt="{{@$productSku->product->product_name}}">
                        @else
                            <img style="height: 36px;"
                                src="{{asset('public/backEnd/img/no_image.png')}}"
                                alt="{{@$productSku->product->product_name}}">
                        @endif
                    </x-table.td>
                    <x-table.td><a href="#" data-toggle="modal" onclick="product_detail({{ $productSku->product_id }} , 'null')">{{@$productSku->product->product_name}}</a></x-table.td>
                    <x-table.td>
                        @if (app('general_setting')->origin == 1)
                            {{$productSku->product->origin}}
                        @else
                            {{ $productSku->sku }}
                        @endif
                    </x-table.td>
                    <x-table.td>{{single_price($productSku->selling_price)}}</x-table.td>
                    <x-table.td>
                        <div class="dropdown CRM_dropdown">
                            <button class="btn btn-secondary dropdown-toggle"
                                    type="button" id="dropdownMenu2"
                                    data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"> {{__('common.select')}}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenu2">
                                @if(permissionCheck('add_product.edit'))
                                    <a href="{{route('add_product.edit',$productSku->product_id)}}" class="dropdown-item" type="button">{{__('common.Edit')}}</a>
                                @endif

                                @if(permissionCheck('add_product.index'))
                                    <a href="#" data-toggle="modal" class="dropdown-item" onclick="product_detail({{ $productSku->product_id }} , 'null')">{{__('common.View')}}</a>
                                @endif

                                @if(permissionCheck('add_product.destroy') && $productSku->sku_products->count() == 0)
                                    <a onclick="confirm_modal('{{route('add_product.destroy',$productSku->product_id)}}');" class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                                @endif
                            </div>
                        </div>
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
