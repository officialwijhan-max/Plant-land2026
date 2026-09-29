@extends('backEnd.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('common.Stock List')}}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="white_box_50px box_shadow_white pb-3">
                <form action="{{route("stock.report")}}" method="GET" id="searchStock">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ __('inventory.Branch') }}</label>
                                <select class="primary_select mb-15" name="showroom" id="showroom">
                                    @isset($showroom)
                                        <option value="0">{{__('attendance.Choose One')}}</option>
                                        @foreach ($stocks as $key=> $stock)
                                            <option value="{{ $stock->houseable_id }}-{{ $stock->houseable_type }}"
                                                    @if ($stock->houseable_id.'-'.$stock->houseable_type == $showroom) selected @endif>{{ @$stock->houseable->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="0">{{__('attendance.Choose One')}}</option>
                                        @foreach ($stocks as $key=> $stock)
                                            <option value="{{ $stock->houseable_id }}-{{ $stock->houseable_type }}"
                                                    @if (session()->get('showroom_id') == $stock->houseable_id && $stock->houseable_type == 'Modules\Inventory\Entities\ShowRoom') selected @endif>{{ @$stock->houseable->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                <span class="text-danger">{{$errors->first('showroom')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label class="primary_input_label" for="">{{ __('purchase.Brand') }}</label>
                            <div class="primary_input mb-15">
                                <select class="primary_select brand_id" name="brand_id">
                                    <option value="">{{__('purchase.Select Brand')}}</option>
                                    @foreach($brands as $key => $brand)
                                        <option
                                            value="{{ $brand->id }}" {{isset($brand_id) && $brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="primary_input_label" for="">{{ __('purchase.Product') }}</label>
                            <div class="primary_input mb-15">
                                <select class="primary_select product_sku_id" name="product_sku_id">
                                    <option value="">{{__('purchase.Select Product')}}</option>
                                    @foreach($products as $key => $productSku)
                                        <option
                                            value="{{ $productSku->id }}" {{isset($product_sku_id) && $product_sku_id == $productSku->id ? 'selected' : '' }}>{{ @$productSku->product->product_name }} @if (variantNameFromSku($productSku))
                                                - ({{ variantNameFromSku($productSku) }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <button class="primary_btn_2" type="submit">{{ __('report.Search') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="white_box_50px box_shadow_white">
                <div class="row">
                    @isset($product_stocks)
                        <div class="col-12">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('report.Stock Report')}}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="QA_section QA_section_heading_custom check_box_table">
                                <div class="QA_table ">
                                    <!-- table-responsive -->
                                    @php
                                        $total = 0
                                    @endphp
                                    <table class="table Crm_table_active3">
                                        <thead>
                                        <tr>

                                            <th scope="col">{{__('common.Sl')}}</th>
                                            <th scope="col">{{__('common.Name')}}</th>
                                            <th scope="col">{{__('common.Brand')}}</th>
                                            <th scope="col">{{__('product.Branch/Warehouse')}}</th>
                                            <th scope="col">{{__('product.In Qty')}}</th>
                                            <th scope="col">{{__('product.Out Qty')}}</th>
                                            <th scope="col">{{__('product.Stock')}}</th>
                                            @if (auth()->user()->role->type == "system_user")
                                                <th scope="col">{{__('common.Purchase Price')}}</th>
                                                <th scope="col">{{__('common.Purchase Value')}}</th>
                                            @endif
                                            <th scope="col">{{__('common.Selling Price')}}</th>
                                            <th scope="col">{{__('common.Selling Value')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @php
                                            $total_in = 0;
                                            $total_out = 0;
                                            $total_stock = 0;
                                            $total_purchase_price = 0;
                                            $total_purchase_value = 0;
                                            $total_selling_price = 0;
                                            $total_selling_value = 0;
                                        @endphp

                                        @foreach($product_stocks as $key => $product_stock)
                                            @php
                                                $total_in += $product_stock->in;
                                                $total_out += $product_stock->out;
                                                $total_stock += $product_stock->stock;
                                                $total_purchase_price += @$product_stock->productSku->purchase_price;
                                                $total_purchase_value = $product_stock->stock * @$product_stock->productSku->purchase_price;
                                                $total_selling_price += @$product_stock->productSku->selling_price;
                                                $total_selling_value = $product_stock->stock * @$product_stock->productSku->selling_price;;
                                            @endphp
                                            <tr>

                                                <th>{{$key+1}}</th>

                                                <td>
                                                    @if($product_stock->productSku)
                                                        <a href="#" data-toggle="modal"
                                                           onclick="product_detail({{ $product_stock->productSku->product_id }} , 'null')">{{@$product_stock->productSku->product->product_name}}</a>
                                                    @endif
                                                </td>
                                                <td>{{@$product_stock->productSku->product->brand->name}}</td>
                                                <td>{{@$product_stock->houseable->name}}</td>
                                                <td>{{@$product_stock->in}}</td>
                                                <td>{{@$product_stock->out}}</td>
                                                <td>{{@$product_stock->stock}}</td>
                                                @if (auth()->user()->role->type == "system_user")
                                                    <td>
                                                        @if (session()->get('showroom_id') == '1')
                                                            {{single_price(@$product_stock->productSku->purchase_price)}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session()->get('showroom_id') == '1')
                                                            {{single_price(@$product_stock->stock * @$product_stock->productSku->purchase_price)}}
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>
                                                    {{single_price(@$product_stock->productSku->selling_price)}}
                                                </td>
                                                <td>
                                                    {{single_price(@$product_stock->stock * @$product_stock->productSku->selling_price)}}
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-right">{{ __('common.Total') }}</th>
                                            <th>{{ $total_in }}</th>
                                            <th>{{ $total_out }}</th>
                                            <th>{{ $total_stock }}</th>
                                            <th>{{ single_price($total_purchase_price) }}</th>
                                            <th>{{ single_price($total_purchase_value) }}</th>
                                            <th>{{ single_price($total_selling_price) }}</th>
                                            <th>{{ single_price($total_selling_value) }}</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>
    <div class="product_info">

    </div>
@endsection
@push("scripts")
    <script type="text/javascript">
        function submit() {
            let showroom = $('#showroom').val();
            let supplier = $('.supplier').val();
            $('#searchStock').submit();
        }

        function product_detail(el, type, range) {
            $.post('{{ route('add_product.product_Detail') }}', {
                _token: '{{ csrf_token() }}',
                id: el,
                type: type,
                range: range,
            }, function (data) {
                $('.product_info').html(data);
                $('#Item_Details').modal('show');
            });
        }
    </script>
@endpush
