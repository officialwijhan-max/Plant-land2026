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

                                <label class="primary_input_label" for="">{{ __('inventory.Branch') }}/{{ __('inventory.Warehouse') }}</label>

                                <select class="primary_select mb-15" name="showroom" id="showroom">
                                    <option value="0">{{ __('attendance.Choose One') }}</option>
                                    @foreach ($stocks as $key => $stock)
                                        @php
                                            $value = $stock->id . '-' . $stock->houseable_type;
                                            $isSelected = isset($showroom)
                                                ? $value == $showroom
                                                : session()->get('showroom_id') == $stock->houseable_id && $stock->houseable_type == 'Modules\Inventory\Entities\ShowRoom';
                                        @endphp
                                        <option value="{{ $value }}" @if ($isSelected) selected @endif>
                                            {{ @$stock->name }}
                                        </option>
                                    @endforeach
                                </select>                                

                                <span class="text-danger">{{$errors->first('showroom')}}</span>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <label class="primary_input_label" for="">{{ __('purchase.Brand') }}</label>

                            <div class="primary_input mb-15">

                                <select class="select2 mb-15 single_select primary_singleSelect brand" name="brand_id">

                                    <option value="0">{{__('product.Select Brand')}}</option>

                                    @if (isset($brand) && $brand)

                                        <option value="{{$brand->id}}" selected>{{$brand->name}}</option>

                                    @endif

                                </select>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <label class="primary_input_label" for="">{{ __('purchase.Product') }}</label>

                            <div class="primary_input mb-15">

                                <select class="single_select primary_singleSelect mb-15 product_sku_id" name="product_sku_id" id="product_sku_id">

                                    <option value="0-single">{{__('quotation.Select Product')}}</option>

                                    @if (isset($product) && $product)

                                        <option value="{{$product->id}}" selected>{{$product->product->product_name}}</option>

                                    @endif

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

        @isset($items)

            <div class="col-12">

                <div class="box_header common_table_header">

                    <div class="main-title d-md-flex">

                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('common.Stock List')}}</h3>

                    </div>

                </div>

            </div>

            <div class="col-lg-12">

                <div class="QA_section QA_section_heading_custom check_box_table">

                    <div class="QA_table ">

                        <div id="item_list_tbl">

                            @include('inventory::stock_transfer.paginate.stock_list')

                        </div>

                        <!-- table-responsive -->

                        {{-- @php

                            $total = 0

                        @endphp

                        <table class="table Crm_table_active3">

                            <thead>

                            <tr>



                                <th scope="col">{{__('common.Sl')}}</th>

                                <th scope="col">{{__('common.Image')}}</th>

                                <th scope="col">{{__('common.Name')}}</th>

                                <th scope="col">{{__('common.Brand')}}</th>

                                <th scope="col">{{__('product.SKU')}}</th>

                                <th scope="col">{{__('product.Branch/Warehouse')}}</th>

                                <th scope="col">{{__('report.Supplier')}}</th>

                                <th scope="col">{{__('product.In Stock')}}</th>

                                <th scope="col">{{__('product.Stock Alert')}}</th>

                                @if (auth()->user()->role->type == "system_user")

                                    <th scope="col">{{__('common.Purchase Price')}}</th>

                                    <th scope="col">{{__('common.Purchase Value')}}</th>

                                @endif

                                <th scope="col">{{__('common.Selling Price')}}</th>

                                <th scope="col">{{__('common.Selling Value')}}</th>

                            </tr>

                            </thead>

                            <tbody>



                            @foreach($product_stocks as $key => $product_stock)

                                <tr>



                                    <th>{{$key+1}}</th>

                                    <td>

                                        @if (@$product_stock->productSku->product->product_type == "Single")

                                            <img style="height: 22px;"

                                                 src="{{asset(@$product_stock->productSku->product->image_source ?? 'public/backEnd/img/no_image.png')}}">

                                        @else

                                            <img style="height: 22px;"

                                                 src="{{asset(@$product_stock->productSku->product_variation->image_source ?? 'public/backEnd/img/no_image.png')}}">

                                        @endif

                                    </td>

                                    <td>

                                        @if($product_stock->productSku)

                                            <a href="#" data-toggle="modal"

                                               onclick="product_detail({{ $product_stock->productSku->product_id }} , 'null')">{{@$product_stock->productSku->product->product_name}}</a>

                                        @endif

                                    </td>

                                    <td>{{@$product_stock->productSku->product->brand->name}}</td>

                                    <td>{{@$product_stock->productSku->sku}}</td>

                                    <td>{{@$product_stock->houseable->name}}</td>

                                    <td>{{ !empty($req_supplier) ? $req_supplier->name : @$stock->purchase->supplier->name}}</td>

                                    <td>{{@$product_stock->stock}}</td>

                                    <td>{{@$product_stock->productSku->alert_quantity}}</td>

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

                        </table> --}}

                    </div>

                </div>

            </div>

        @endisset

    </div>

    <input type="hidden" name="products_list_select_option" id="products_list_select_option" value="{{ route('sku_product_select_list_option') }}">

    <input type="hidden" name="brand_list_select_option" id="brand_list_select_option" value="{{ route('brand.list_select_option') }}">

    <div class="product_info_det"></div>

    <div class="showModalHideColumn"></div>

    @php

        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'stock_list')->first();

    @endphp

    @if ($employee_per)

        @if ($employee_per->hide_column_no_by_self)

            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">

        @endif

    @else

        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">

    @endif

    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('common.Image') }}','image'],['3','{{ __('common.Name') }}','name'],['4','{{ __('product.SKU') }}','sku'],['5','{{ __('common.Brand') }}','brand'],['6','{{ __('common.Model') }}','model'],['7','{{ __('product.Branch/Warehouse') }}','showroom_or_wareHouse'],['8','{{ __('product.In Stock') }}','in_stock'],['9','{{ __('product.Stock Alert') }}','stock_alert'],['10','{{ __('common.Purchase Price') }}','purchase_price'],['11','{{ __('common.Selling Price') }}','selling_price']]">

    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['stock_list']) }}">



@endsection

@push("scripts")

<script src="{{ Module::asset('tables:table.js') }}"></script>

<script src="{{ Module::asset('tables:hide_show.js') }}"></script>

<script src="{{ Module::asset('core:brand_select.js') }}"></script>

<script src="{{ Module::asset('core:product_select.js') }}"></script>

    <script type="text/javascript">

        function submit() {

            let showroom = $('#showroom').val();

            let supplier = 1;

            $('#searchStock').submit();

        }



        function product_detail(el, type, range) {

            $.post('{{ route('add_product.product_Detail') }}', {

                _token: '{{ csrf_token() }}',

                id: el,

                type: type,

                range: range,

            }, function (data) {

                $('.product_info_det').html(data);

                $('#Item_Details').modal('show');

            });

        }

    </script>

@endpush

