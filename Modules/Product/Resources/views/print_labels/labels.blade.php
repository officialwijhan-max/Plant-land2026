@extends('backEnd.master')
@section('page-title',__('common.Print Label'))
@section('mainContent')
   <section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
         <div class="row justify-content-center">
            <div class="col-12">
               <div class="box_header">
                  <div class="main-title d-flex">
                     <h3 class="mb-0 mr-30">{{__('common.Print Label')}}</h3>
                  </div>
               </div>
            </div>
            <div class="col-12">
               <div class="white_box_50px box_shadow_white">
                  <form action="{{route("print.labels")}}" method="GET" enctype="multipart/form-data" class="create_forms">
                      <div class="row">
                        <div class=" col-sm-12">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px">{{ __('sale.Select Product') }}</h3>
                                </div>
                            </div>
                        </div>
                         <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="primary_input mb-15">
                                <div class="double_label d-flex justify-content-between">
                                    <label class="primary_input_label" for="">{{__('product.Select Brand')}}</label>
                                </div>
                                <select class="select2 mb-15 single_select primary_singleSelect product-load-filter brand" name="brand_id" id="brand_id">
                                    <option value="0">{{__('product.Select Brand')}}</option>
                                </select>
                                <span class="text-danger">{{$errors->first('brand_id')}}</span>
                            </div>
                        </div>
                    
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="primary_input mb-15">
                                <div class="double_label d-flex justify-content-between">
                                    <label class="primary_input_label" for="">{{__('product.Select Model')}}</label>
                                </div>
                                <select class="select2 mb-15 single_select primary_singleSelect model product-load-filter" name="model_id" id="model_id">
                                    <option value="0">{{__('product.Select Model')}}</option>
                                </select>
                                <span class="text-danger">{{$errors->first('product_id')}}</span>
                            </div>
                        </div>

                         <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <div class="double_label d-flex justify-content-between">
                                    <label class="primary_input_label" for="">{{__('sale.Select Product')}} * [{{__('product.Select Brand or Model for load product list')}}]</label>
                                </div>
                                <select class="select2 mb-15 single_select primary_single product_info" name="product_sku_id" id="product_info"></select>
                               <span class="text-danger">{{$errors->first('product_sku_id')}}</span>
                            </div>
                         </div>
                     </div>
                     <div class="row">
                        <div class="col-12">
                            <table class="table table_modal table-bordered">
                                <tbody id="product_details">
                                    <tr>
                                        <td>{{__('product.Product')}}</td>
                                        <td>{{__('product.SKU')}}</td>
                                        <td>{{__('product.No of Label')}}</td>
                                        <td width="7%">{{__('common.Action')}}</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-12">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('product.Info to Show in Label') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <ul id="theme_nav" class="permission_list sms_list ">
                                <li>
                                    <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                        <input name="name" id="name" type="checkbox" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <p>{{ __('product.Product Name') }}</p>
                                </li>
                            </ul>
                           <div class="primary_input mb-15">
                              <label class="primary_input_label" for=""> {{__("product.Product Name") .' '. __('product.Font Size')}} *</label>
                              <input class="primary_input_field" name="product_name_font_size" type="text" value="12" min="5" required="1">
                              <span class="text-danger">{{$errors->first('product_name_font_size')}}</span>
                           </div>
                        </div>
                        <div class="col-lg-3">
                            <ul id="theme_nav" class="permission_list sms_list ">
                                <li>
                                    <label data-id="color_option" class="primary_checkbox d-flex mr-12">
                                        <input name="variation" id="variation" type="checkbox" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <p>{{ __('product.Product Variation (Recommended)') }}</p>
                                </li>
                            </ul>
                           <div class="primary_input mb-15">
                              <label class="primary_input_label" for=""> {{__("product.Variant") .' '. __('product.Font Size')}} *</label>
                              <input class="primary_input_field" name="variant_font_size" type="text" value="12" min="5" required="1">
                              <span class="text-danger">{{$errors->first('variant_font_size')}}</span>
                           </div>
                        </div>
                        <div class="col-lg-3">
                            <ul id="theme_nav" class="permission_list sms_list justify-content-between">
                                <li>
                                    <label class="primary_checkbox d-flex mr-12">
                                        <input name="product_price" id="price" type="checkbox" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <p>{{ __('product.Price') }}</p>
                                </li>
                                <li class="mb-0">
                                    <select class="primary_select price_tax" id="tax_option" name="tax_option">
                                        <option value="none">{{ __('common.Excluding Tax') }}</option>
                                        <option value="plain_tax">{{ __('common.Including Tax') }}</option>
                                    </select>
                                </li>
                            </ul>
                           <div class="primary_input mb-15">
                              <label class="primary_input_label" for=""> {{__("product.Price") .' '. __('product.Font Size')}} *</label>
                              <input class="primary_input_field" name="price_font_size" type="text" value="12" min="5" required="1">
                              <span class="text-danger">{{$errors->first('price_font_size')}}</span>
                           </div>
                        </div>
                        <div class="col-lg-3">
                            <ul id="theme_nav" class="permission_list sms_list ">
                                <li>
                                    <label data-id="color_option" class="primary_checkbox d-flex mr-12">
                                    <input name="business_name" id="business_name" type="checkbox" checked>
                                    <span class="checkmark"></span>
                                    </label>
                                    <p>{{ __('common.Business Name') }}</p>
                                </li>
                            </ul>
                           <div class="primary_input mb-15">
                              <label class="primary_input_label" for=""> {{__("common.Business Name") .' '. __('product.Font Size')}} *</label>
                              <input class="primary_input_field" name="business_name_font_size" type="text" value="12" min="5" required="1">
                              <span class="text-danger">{{$errors->first('business_name_font_size')}}</span>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-12">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('product.Barcode Settings') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <select class="primary_select page" id="page" name="page">
                               <option value="20">{{ __('product.20 Labels per Sheet, Sheet Size: 8.5" x 11", Label Size: 4" x 1", Labels per sheet: 20') }}</option>
                                <option value="30">{{ __('product.30 Labels per sheet, Sheet Size: 8.5" x 11", Label Size: 2.625" x 1", Labels per sheet: 30') }}</option>
                                <option value="32">{{ __('product.32 Labels per sheet, Sheet Size: 8.5" x 11", Label Size: 2" x 1.25", Labels per sheet: 32') }}</option>
                                <option value="40">{{ __('product.40 Labels per sheet, Sheet Size: 8.5" x 11", Label Size: 2" x 1", Labels per sheet: 40') }}</option>
                                <option value="50">{{ __('product.50 Labels per Sheet, Sheet Size: 8.5" x 11", Label Size: 1.5" x 1", Labels per sheet: 50') }}</option>
                                <option value="0">{{ __('product.Continuous Rolls - 31.75mm x 25.4mm, Label Size: 31.75mm x 25.4mm, Gap: 3.18mm') }}</option>

                            </select>
                        </div>
                     </div>
                     <div class="row mt-4">
                        <div class="col-12">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-2 mb_xs_15px mb_sm_20px">{{ __('product.Barcode Size') }}</h3>
                                    <img src="data:image/png;base64, {{DNS1D::getBarcodePNG('416', 'C39')}}" alt="barcode"style="max-width:50px !important;height: 0.2in !important; display: block; margin-left:2px"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="primary_input_label" for=""> {{__("product.Max Width") .' : '. __('product.Inches')}} *</label>
                              <input class="primary_input_field" name="max_width" type="text" value="1" min="0.5" type="number" step="0.001" required="1">
                              <span class="text-danger">{{$errors->first('max_width')}}</span>
                        </div>
                        <div class="col-lg-6">
                            <label class="primary_input_label" for=""> {{__("product.Height") .' : '. __('product.Inches')}} *</label>
                              <input class="primary_input_field" name="height" type="text" value="0.2" min="0.2" type="number" step="0.001" required="1">
                              <span class="text-danger">{{$errors->first('height')}}</span>
                        </div>
                     </div>
                     <div class="row mt-5">
                          <div class="col-12">
                             <div class="submit_btn text-center ">
                                <button class="primary-btn semi_large2 submit_button_form fix-gr-bg"><i
                                   class="ti-check"></i>{{__("product.Generate")}}
                                </button>
                             </div>
                          </div>
                       </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="brand_list_select_option" id="brand_list_select_option" value="{{ route('brand.list_select_option') }}">
    <input type="hidden" name="model_list_select_option" id="model_list_select_option" value="{{ route('model.list_select_option') }}">
    <input type="hidden" name="products_list_select_option" id="products_list_select_option" value="{{ route('products.list_select_option_opening_stock') }}">
</section>
@endsection
@push("scripts")

<script src="{{ Module::asset('core:brand_select.js') }}"></script>
<script src="{{ Module::asset('core:model_select.js') }}"></script>
<script src="{{ Module::asset('core:product_select_with_type_purchase.js') }}"></script>
<script type="text/javascript">
$(document).on('change', '.product_info', function () {
    var sku_id = $('#product_info').val();
    $.post('{{ route('product.product_detail_json') }}', {_token:'{{ csrf_token() }}', id:sku_id}, function(data){
        $('#product_details').append(data);
    });
});
$(document).on('click', '.delete_product', function () {
    $(this).closest('tr').remove();
    // console.log($(this).closest('tr'))
});
</script>
@endpush
