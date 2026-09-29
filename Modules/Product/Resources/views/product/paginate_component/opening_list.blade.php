<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ (request('sort')) ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ (request('col')) ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{route('add_opening_stock_create').'?import_as=print'}}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{route('add_opening_stock_create').'?import_as=csv'}}" target="_blank" title="Export">
            <i class="ti-export"></i>
        </a>
        <a href="#" title="Col Show/Hide" class="hide_show_click_btn">
            <i class="ti-layout-column3"></i>
        </a>
    </x-slot>

    <x-slot name="table">

        <x-table.head>
            <tr>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('sale.Sl')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('sale.Date')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('common.Name')}}
                    </a>
                </x-table.th>
                @if (app('general_setting')->origin == 1)
                    <x-table.th scope="col" class="text-center">
                        <a class="custom_thead_title" data-id="" href="#">
                            {{__('common.Part Number')}}
                        </a>
                    </x-table.th>
                @else
                    <x-table.th scope="col" class="text-center">
                        <a class="custom_thead_title" data-id="" href="#">
                            {{__('sale.SKU')}}
                        </a>
                    </x-table.th>
                @endif

                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('product.Brand')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('product.Model')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('inventory.Branch')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('product.Purchase Price')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('product.Selling Price')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('product.Stock')}}
                    </a>
                </x-table.th>
                <x-table.th scope="col" class="text-center">
                    <a class="custom_thead_title" data-id="" href="#">
                        {{__('common.Created User')}}
                    </a>
                </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $stockProduct)
            <x-table.tr>
                <x-table.th><a href="#">{{ $key+1 }}</a></x-table.th>
                <x-table.td>{{ showDate($stockProduct->date) }}</x-table.td>
                <x-table.td>{{ @$stockProduct->productSku->product->product_name }}</x-table.td>
                <x-table.td>
                    @if (app('general_setting')->origin == 1)
                        {{ @$stockProduct->productSku->product->origin }}
                    @else
                        {{ @$stockProduct->productSku->sku }}
                    @endif
                </x-table.td>
                <x-table.td>{{ @$stockProduct->productSku->product->brand->name }}</x-table.td>
                <x-table.td>{{ @$stockProduct->productSku->product->model->name }}</x-table.td>
                <x-table.td>{{ @$stockProduct->itemable->name }}</x-table.td>
                <x-table.td>{{ single_price(@$stockProduct->productSku->purchase_price) }} / <small>{{ @$stockProduct->productSku->product->unit_type->name }}</small> </x-table.td>
                <x-table.td>{{ single_price(@$stockProduct->productSku->selling_price) }} / <small>{{ @$stockProduct->productSku->product->unit_type->name }}</small></x-table.td>
                <x-table.td>{{ $stockProduct->in_out }}</x-table.td>
                <x-table.td>{{ userName($stockProduct->created_by) }}</x-table.td>
            </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
