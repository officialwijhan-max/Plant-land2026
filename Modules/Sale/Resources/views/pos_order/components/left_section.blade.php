<input type="hidden" id="pos_view_config" name="pos_view_config" value="{{ app('general_setting')->pos_view }}">
<div class="pos__page__products">
    <div class="pos_white_box mb_30">

        <div class="product_topbar">
            <div class="input-group primary_search_field pos_serch_field">
                <div class="input-group-prepend Search_icon">
                    <span class="input-group-text"> <i class="fas fa-search"></i> </span>
                </div>
                <input type="text" data-product="product" name="product" class="search_product"
                        id="search_keyword_id" placeholder="{{ __('common.Enter product Name, SKU, Origin, SKU, Brand or Model then hit enter or Scan bar code') }}" aria-label="Amount (to the nearest dollar)">
                <div class="input-group-append plus_button">
                    <span class="input-group-text primary-btn primary-circle fix-gr-bg reset-product-list"> <i class="fas fa-refresh "></i> </span>
                </div>
                <div class="input-group-append plus_button">
                    <span class="input-group-text primary-btn primary-circle fix-gr-bg add-product"> <i class="fas fa-plus"></i> </span>
                </div>
            </div>
            <div class="pos_tabToggle_header">
                <div class="pos_tabToggle_inner">
                    <div class="primary_input">
                        <select class="primary_select gray_select mb-15 product_category" id="category_select_id" name="product">
                            <option value="0">{{__('product.Select Category')}}</option>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="primary_input">
                        <select class="primary_select gray_select mb-15 product_brand" id="brand_select_id" name="product">
                            <option value="0">{{__('product.Select Brand')}}</option>
                            @foreach ($brands as $brand)
                                <option value="{{$brand->id}}">{{$brand->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="primary_input">
                        <select class="primary_select gray_select mb-15 product_model" id="model_select_id" name="model">
                            <option value="0">{{__('product.Select Model')}}</option>
                            @foreach ($models as $model)
                                <option value="{{$model->id}}">{{$model->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <ul class="nav mx-auto grid_list_toggler" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link pointer @if (app('general_setting')->pos_view == 2) active @endif " onclick="getPosViewUpdate(this, 2)" id="home-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pointer @if (app('general_setting')->pos_view == 1) active @endif " onclick="getPosViewUpdate(this, 1)" id="profile-tab" data-toggle="tab" role="tab" aria-controls="profile" aria-selected="false">
                            <i class="fas fa-th"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            @php
                $ProductStockList = Modules\Inventory\Entities\StockReport::whereIn('product_sku_id', collect($products['ProductList'])->pluck('product_id'))
                    ->where('houseable_type', 'Modules\Inventory\Entities\ShowRoom')
                    ->where('houseable_id', session()->get('showroom_id'))
                    ->select('id','product_sku_id','stock')
                    ->get()
            @endphp
            @if (app('general_setting')->pos_view == 2)
                <div class="tab-pane fade @if (app('general_setting')->pos_view == 2) show active @endif" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <!-- lists  -->
                    <div class="table-responsive">
                        <table class="table table-bordered product_tab_list">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('common.Name') }}</th>
                                    <th scope="col">{{ __('product.Price') }}</th>
                                    <th scope="col">{{ __('product.SKU') }}</th>
                                    <th scope="col">{{ __('product.Brand') }}</th>
                                    <th scope="col">{{ __('product.Model') }}</th>
                                </tr>
                            </thead>
                            <tbody id="all_products_tbl">
                                @foreach($products['ProductList'] as $product)
                                    <tr class="product_info" data-value="{{$product->product_id}}" data-id="{{$product->product_id}}-{{ $product->product_type }}">
                                        <th>
                                            <div class="product_image">
                                                <div class="thumb">
                                                    <img src="{{asset($product->image_source)}}" alt="">
                                                </div>
                                                <a>
                                                    <h4>{{substr($product->product_name, 0, 40)}} ({{ $product->stock }}) </h4>
                                                    @if ($product->sku)
                                                        <h5>{{ variantNameFromSku($product->sku) }}</h5>
                                                    @endif
                                                </a>
                                            </div>
                                        </th>
                                        <td>
                                            <h4 class="f_s_14 f_w_500 theme_text mb-1">{{ single_price($product->selling_price) }}</h4>
                                        </td>
                                        <td>{{ @$product->product_sku }}</td>
                                        <td>{{ @$product->brand_name }}</td>
                                        <td>{{ @$product->model_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" class="total_product_count" name="total_product_count" value="{{ $single_skip }}">
                    <input type="hidden" class="total_combo_count" name="total_combo_count" value="{{ $combo_skip }}">
                    <div class="row justify-content-center mt-30 demo_wait" style="display: none">
                        <img src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="">
                    </div>
                    <div class="row justify-content-center mt-3 tbl_load_div">
                        <a href="javascript:void(0)" class="primary_color_btn2 fix-gr-bg radius_30px loadmore_btn1">{{__('sale.Load More')}}</a>
                    </div>
                </div>
            @endif
            @if (app('general_setting')->pos_view == 1)
                <div class="tab-pane fade @if (app('general_setting')->pos_view == 1) show active @endif" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <!-- grid  -->
                    <div class="product_grid_view all_products">
                        @foreach ($products['ProductList'] as $product)
                            <div class="grid_single_product product_info pointer" data-value="{{$product->product_id}}" data-id="{{$product->product_id}}-{{ $product->product_type }}">
                                <div class="product_thumb position-relative">
                                    <span class="offer_badge">{{ $product->stock }}</span>
                                    <img src="{{asset($product->image_source)}}" alt="{{$product->product_name}}">
                                </div>
                                <div class="product_content text-center">
                                    <a href="javascript:void(0)">
                                        <h4 class="f_s_14 f_w_500 theme_text mb-1" >{{substr($product->product_name, 0, 40)}}</h4>
                                        <h4 class="f_s_14 f_w_500 theme_text mb-1">{{ single_price($product->selling_price) }}</h4>
                                        @if ($product->sku)
                                            <h4 class="f_s_14 f_w_500 theme_text mb-1">{{ variantNameFromSku($product->sku) }}</h4>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row justify-content-center mt-30 demo_wait" style="display: none">
                        <img src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="">
                    </div>
                    <input type="hidden" class="total_product_count" name="total_product_count" value="{{ $single_skip }}">
                    <input type="hidden" class="total_combo_count" name="total_combo_count" value="{{ $combo_skip }}">
                    <div class="row justify-content-center mt-3 grid_load_div">
                        <a href="javascript:void(0)" class="primary_color_btn2 fix-gr-bg radius_30px loadmore_btn">{{__('sale.Load More')}}</a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
