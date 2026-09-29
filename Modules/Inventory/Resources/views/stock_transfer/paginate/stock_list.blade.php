<x-table :models="$items" filter_id='filter_id'>

    <x-slot name='left_side_btn'>

        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">

        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">

    </x-slot>



    <x-slot name='table_btns'>

        @if(strpos($_SERVER['REQUEST_URI'], '?') == true)

            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=print" target="_blank" title="Print">

                <i class="ti-printer"></i>

            </a>

            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=csv" target="_blank" title="Export">

                <i class="ti-export"></i>

            </a>

        @else

            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=print" target="_blank" title="Print">

                <i class="ti-printer"></i>

            </a>

            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=csv" target="_blank" title="Export">

                <i class="ti-export"></i>

            </a>

        @endif

        {{-- <a href="{{route('stock.report').'?import_as=print'}}" target="_blank" title="Print">

            <i class="ti-printer"></i>

        </a>

        <a href="{{route('stock.report').'?import_as=csv'}}" target="_blank" title="Export">

            <i class="ti-export"></i>

        </a> --}}

        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">

            <i class="ti-layout-column3"></i>

        </a>

    </x-slot>



    <x-slot name="table">



        <x-table.head>

            <tr>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('common.Sl')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('common.Image')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('common.Name')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('sale.SKU')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('product.Brand')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('product.Model')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('product.Branch/Warehouse')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('product.In Stock')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('product.Stock Alert')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('common.Purchase Price')}}

                    </a>

                </x-table.th>

                <x-table.th scope="col">

                    <a class="custom_thead_title" data-id="id" href="#">

                        <i class="ti-arrow-down"></i>

                        {{__('common.Selling Price')}}

                    </a>

                </x-table.th>

            </tr>

        </x-table.head>

        <x-table.body>

            @foreach ($items as $key => $item)

                <x-table.tr>

                    <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>

                    <x-table.td>

                        @if (@$item->productSku->product->product_type == "Single")

                            <img style="height: 22px;"

                                    src="{{asset(@$item->productSku->product->image_source ?? 'public/backEnd/img/no_image.png')}}">

                        @else

                            <img style="height: 22px;"

                                    src="{{asset(@$item->productSku->product_variation->image_source ?? 'public/backEnd/img/no_image.png')}}">

                        @endif

                    </x-table.td>

                    <x-table.td>

                        @if($item->productSku)

                            <a href="#" data-toggle="modal" onclick="product_detail({{ $item->productSku->product_id }} , 'null')">{{@$item->productSku->product->product_name}}</a>

                        @endif

                    </x-table.td>

                    <x-table.td>{{ $item->productSku->sku ?? '---' }}</x-table.td>

                    <x-table.td>{{@$item->productSku->product->brand->name  ?? '---'}}</x-table.td>

                    <x-table.td>{{ @$item->productSku->product->model->name ?? '---' }}</x-table.td>

                    <x-table.td>{{ @$item->houseable->name ?? '---' }}</x-table.td>

                    <x-table.td> {{ $item->stock ?? '---' }} </x-table.td>

                    <x-table.td>{{ @$item->productSku->alert_quantity ?? '---' }}</x-table.td>

                    <x-table.td>{{ single_price(@$item->productSku->cost_of_goods ?? '0') }}</x-table.td>

                    <x-table.td>{{ single_price(@$item->productSku->selling_price ?? '0') }}</x-table.td>

                </x-table.tr>

            @endforeach

        </x-table.body>

    </x-slot>



</x-table>

