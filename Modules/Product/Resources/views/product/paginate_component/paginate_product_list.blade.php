<x-table :models="$items" filter_id='filter_id' id="product_table">
    <x-slot name='left_side_btn'>
        <input type="text" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
        @if(permissionCheck('add_product.index'))
            <a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("add_product.index")}}"><i class="ti-plus"></i>{{__('product.New Product')}}</a>
        @endif
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('add_product.create').'?type=product&import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('add_product.create').'?type=product&import_as=csv'}}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        @if(permissionCheck('add_product.csv_upload.store'))
        <a href="{{route('add_product.csv_upload.create')}}" title="Import">
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
                    <a class="custom_thead_title_2" data-id="product_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "product_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "product_id")
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
                    <a class="custom_thead_title_2" data-id="product_id" href="#">
                        @if (request('sort') == 'asc' && request('col') == "product_id")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "product_id")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Name')}}
                    </a>
                </x-table.th>
                @if (app('general_setting')->origin == 1)
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
                            {{__('common.Part Number')}}
                        </a>
                    </x-table.th>
                @else
                    <x-table.th scope="col">
                        <a class="custom_thead_title_2" data-id="sku" href="#">
                            @if (request('sort') == 'asc' && request('col') == "sku")
                                <i class="ti-arrow-down anchor nrml"></i>
                            @elseif (request('sort') == 'desc' && request('col') == "sku")
                                <i class="ti-arrow-up anchor nrml"></i>
                            @elseif (!request('sort') && !request('col'))
                                <i class="ti-arrow-down anchor nrml"></i>
                            @else
                                <i class="ti-arrow-down anchor nrml"></i>
                            @endif
                            {{__('sale.SKU')}}
                        </a>
                    </x-table.th>
                @endif
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
                        {{__('product.Brand')}}
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
                        {{__('product.Model')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="purchase_price" href="#">
                        @if (request('sort') == 'asc' && request('col') == "purchase_price")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "purchase_price")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Purchase Price')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="selling_price" href="#">
                        @if (request('sort') == 'asc' && request('col') == "selling_price")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "selling_price")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Selling Price')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title_2" data-id="min_selling_price" href="#">
                        @if (request('sort') == 'asc' && request('col') == "min_selling_price")
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == "min_selling_price")
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{__('product.Min Price')}}
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
                        {{__('product.Stock')}}
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
                        {{__('product.Supplier')}}
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
                        {{__('product.Product Type')}}
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
                        {{__('product.Category')}}
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
                        {{__('product.Stock Alert')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" width="10%"> <a class="custom_thead_title_2" data-id="" href="#"> <i
                            class="ti-arrow-down"></i>{{__('common.Action')}}</a> </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key=>$productSkus)
            <x-table.tr>
                <x-table.th>{{ $key+1 }}</x-table.th>
                <x-table.td>
                    @if (@$productSkus->product->product_type == "Single" && @$productSkus->product->image_source != null)
                        <img style="height: 36px;"
                            src="{{asset(@$productSkus->product->image_source ?? 'public/backEnd/img/no_image.png')}}"
                            alt="{{@$productSkus->product->product_name}}">
                    @elseif(@$productSkus->product->product_type == "Variable" && @$productSkus->product->image_source != null)
                        <img style="height: 36px;"
                            src="{{asset(@$productSkus->product_variation->image_source ?? 'public/backEnd/img/no_image.png')}}"
                            alt="{{@$productSkus->product->product_name}}">
                    @else
                        <img style="height: 36px;"
                            src="{{asset('public/backEnd/img/no_image.png')}}"
                            alt="{{@$productSkus->product->product_name}}">
                    @endif
                </x-table.td>
                <x-table.td><a href="#" data-toggle="modal" onclick="product_detail({{ $productSkus->product_id }} , 'null')">{{@$productSkus->product->product_name}}</a></x-table.td>
                @if (app('general_setting')->origin == 1)
                    <x-table.td>{{ $productSkus->product->origin }}</x-table.td>
                @else
                    <x-table.td>{{ $productSkus->sku }}</x-table.td>
                @endif
                <x-table.td>{{ @$productSkus->product->brand->name }}</x-table.td>
                <x-table.td>{{ @$productSkus->product->model->name }}</x-table.td>
                <x-table.td>{{ single_price($productSkus->purchase_price) }}</x-table.td>
                <x-table.td>{{ single_price($productSkus->selling_price) }}</x-table.td>
                <x-table.td>{{ single_price($productSkus->min_selling_price) }}</x-table.td>
                <x-table.td>{{ ($productSkus->stock()->exists()) ? $productSkus->stock->stock : 0 }}</x-table.td>
                <x-table.td>{{ ($productSkus->item()->exists()) ? @$productSkus->item->itemable->supplier->name : 'X' }}</x-table.td>
                <x-table.td>{{ (@$productSkus->product->product_type == 'Variable') ? 'Variant' : 'Single' }}</x-table.td>
                <x-table.td>{{ @$productSkus->product->category->name }}</x-table.td>
                <x-table.td>{{ $productSkus->alert_quantity.' '.@$productSkus->product->unit_type->name }}</x-table.td>
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
                                <a href="{{route('add_product.edit',$productSkus->product_id)}}"
                                   class="dropdown-item"
                                   type="button">{{__('common.Edit')}}</a>

                                @if ($productSkus->suggested()->exists())
                                    <a href="#"
                                       class="dropdown-item"
                                       type="button">{{__('purchase.Added To Suggested')}}</a>
                                @endif

                            @endif
                            @php
                                $image = $productSkus->product->product_type == 'Variable' ? asset($productSkus->product_variation->image_source) : asset($productSkus->product->image_source);
                            @endphp
                            @if ($productSkus->barcode_type)
                                <a href="#" data-id="{{$productSkus->id}}"
                                   data-toggle="modal"
                                   onclick="barcodeGenerator('{{$image}}','{{$productSkus->product->product_name}}','{{$productSkus->sku}}','{{$productSkus->id}}','{{@$productSkus->stock->stock}}')"
                                   class="dropdown-item generate_barcode"
                                   data-target="#generate_barcode">{{__('product.Generate Barcode')}}</a>
                            @endif
                            @if(permissionCheck('add_product.index'))
                                <a href="#" data-toggle="modal"
                                   class="dropdown-item"
                                   onclick="product_detail({{ $productSkus->product_id }} , 'null')">{{__('common.View')}}</a>
                            @endif
                            @if(permissionCheck('add_product.destroy') && $productSkus->sku_products->count() == 0)
                                <a onclick="confirm_modal('{{route('add_product.destroy',$productSkus->product_id)}}');"
                                   class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                            @endif
                            <a href="{{route('add_product.selling_price_history',$productSkus->id)}}" class="dropdown-item" type="button">{{__('product.Selling Price History')}}</a>
                        </div>
                    </div>

                </x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
