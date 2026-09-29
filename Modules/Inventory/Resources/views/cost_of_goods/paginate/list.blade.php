<x-table :models="$items" filter_id='filter_id'>
    <x-slot name='left_side_btn'>
        <input type="hidden" name="sort" class="sort d-none" value="{{ request('sort') ? request('sort') : 'asc' }}">
        <input type="hidden" name="column" class="column d-none" value="{{ request('col') ? request('col') : null }}">
    </x-slot>

    <x-slot name='table_btns'>
        <a href="{{ route('purchase_order.cost_of_goods.index') . '?import_as=print' }}" target="_blank" title="Print">
            <i class="ti-printer"></i>
        </a>
        <a href="{{ route('purchase_order.cost_of_goods.index') . '?import_as=csv' }}" target="_blank" title="Export">
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
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.ID') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('product.Image') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('sale.Invoice No') }}
                    </a>
                </x-table.th>

                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('common.Address') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('product.Product Name') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('inventory.Previous Stock') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('inventory.Newly added Stock') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('inventory.Last Costing Price (unit)') }}
                    </a>
                </x-table.th>
                <x-table.th scope="col">
                    <a class="custom_thead_title" data-id="id" href="#">
                        @if (request('sort') == 'asc' && request('col') == 'id')
                            <i class="ti-arrow-down anchor nrml"></i>
                        @elseif (request('sort') == 'desc' && request('col') == 'id')
                            <i class="ti-arrow-up anchor nrml"></i>
                        @elseif (!request('sort') && !request('col'))
                            <i class="ti-arrow-down anchor nrml"></i>
                        @else
                            <i class="ti-arrow-down anchor nrml"></i>
                        @endif
                        {{ __('inventory.New Costing Price (unit)') }}
                    </a>
                </x-table.th>
            </tr>
        </x-table.head>

        <x-table.body>
            @foreach ($items as $key => $cost_of_goods)
                <x-table.tr>
                    <x-table.th><a href="#">{{ $key + 1 }}</a></x-table.th>
                    <x-table.td>
                        @if (@$cost_of_goods->productSku->product->product_type == 'Single' && @$cost_of_goods->productSku->product->image_source != null)
                            <img style="height: 36px;" src="{{ asset($cost_of_goods->productSku->product->image_source) }}"
                                alt="{{ @$cost_of_goods->productSku->product->product_name }}">
                        @elseif(@$cost_of_goods->productSku->product->product_type == 'Variable' && @$cost_of_goods->productSku->product->image_source != null)
                            <img style="height: 36px;"
                                src="{{ asset($cost_of_goods->productSku->product_variation->image_source) }}"
                                alt="{{ @$cost_of_goods->productSku->product->product_name }}">
                        @else
                            <img style="height: 36px;" src="{{ asset($cost_of_goods->productSku->product->file_dropbox) }}"
                                alt="{{ @$cost_of_goods->productSku->product->product_name }}">
                        @endif

                    </x-table.td>
                    <x-table.td>
                        {{ $cost_of_goods->costable->invoice_no ? $cost_of_goods->costable->invoice_no : 'Begining' }}
                    </x-table.td>
                    <x-table.td>{{ @$cost_of_goods->storeable->name }}</x-table.td>
                    <x-table.td>{{ @$cost_of_goods->productSku->product->product_name }}</x-table.td>
                    <x-table.td class="text-center">{{ $cost_of_goods->previous_remaining_stock }}</x-table.td>
                    <x-table.td class="text-center">{{ $cost_of_goods->newly_stock }}</x-table.td>
                    <x-table.td>
                        {{ single_price($cost_of_goods->previous_cost_of_goods_sold) . ' /' . @$cost_of_goods->productSku->product->unit_type->name }}
                    </x-table.td>
                    <x-table.td>
                        {{ single_price($cost_of_goods->new_cost_of_goods_sold) . ' /' . @$cost_of_goods->productSku->product->unit_type->name }}
                    </x-table.td>
                </x-table.tr>
            @endforeach
        </x-table.body>
    </x-slot>

</x-table>
